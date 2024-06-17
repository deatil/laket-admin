<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use Composer\Semver\Semver;
use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;

use think\helper\Arr;
use think\helper\Str;

use Laket\Admin\Facade\Flash as Flasher;
use Laket\Admin\Model\Flash as FlashModel;
use Laket\Admin\Support\PclZip;

/**
 * 插件管理
 *
 * @create 2021-3-19
 * @author deatil
 */
class Flash extends Base
{
    /**
     * 已安装插件
     */
    public function index()
    {
        return $this->fetch('laket-admin::flash.index');
    }
    
    /**
     * 已安装插件数据
     */
    public function indexData()
    {
        $limit = $this->request->param('limit/d', 10);
        $page = $this->request->param('page/d', 1);
        
        $searchField = $this->request->param('search_field/s', '', 'trim');
        $keyword = $this->request->param('keyword/s', '', 'trim');
        
        $map = [];
        if (!empty($searchField) && !empty($keyword)) {
            $map[] = [$searchField, 'like', "%$keyword%"];
        }

        $list = FlashModel::where($map)
            ->page($page, $limit)
            ->order([
                'listorder' => 'DESC',
                'name' => 'ASC',
            ])
            ->select()
            ->toArray();
        $total = FlashModel::where($map)->count();

        // 添加icon图标
        $list = collect($list)
            ->each(function($data, $key) {
                $icon = '';
                if (class_exists($data['bind_service'])) {
                    app()->register($data['bind_service']);
                    $newClass = app()->getService($data['bind_service']);
                    
                    $icon = Flasher::getFlashIcon($newClass);
                }
                
                $data['icon'] = $icon;
                
                return $data;
            })
            ->toArray();
        
        return $this->success('获取成功', '', [
            "count" => $total, 
            "list"  => $list,
        ]);
    }

    /**
     * 本地插件列表
     */
    public function local()
    {
        $searchField = $this->request->param('search_field/s', '', 'trim');
        $keywords = $this->request->param('keywords/s', '', 'trim');
        
        Flasher::loadFlash();
        $flashs = Flasher::getFlashs();
        
        $installFlashs = FlashModel::getFlashs();
        
        $flashs = collect($flashs);
        if (!empty($searchField) && !empty($keywords)) {
            $flashs = $flashs->whereLike($searchField, $keywords);
        }
        
        $flashs = $flashs
            ->each(function($data, $key) use($installFlashs) {
                if (isset($installFlashs[$data['name']])) {
                    $data['install'] = $installInfo = $installFlashs[$data['name']];
                    
                    $infoVersion = Arr::get($data, 'version', 0);
                    $installVersion = Arr::get($installInfo, 'version', 0);
                    if (Comparator::greaterThan($infoVersion, $installVersion)) {
                        $data['upgrade'] = 1;
                    } else {
                        $data['upgrade'] = 0;
                    }
                    
                    $data['status'] = Arr::get($installInfo, 'status', 0);;
                } else {
                    $data['install'] = [];
                    $data['upgrade'] = 0;
                    $data['status'] = 0;
                }
                
                return $data;
            });
        
        $this->assign('searchField', $searchField);
        $this->assign('keywords', $keywords);
        $this->assign('list', $flashs);
        
        return $this->fetch('laket-admin::flash.local');
    }
    
    /**
     * 刷新本地插件
     */
    public function refreshLocal()
    {
        Flasher::refresh();
        FlashModel::clearCahce();
        
        return $this->success('插件刷新成功');
    }
    
    /**
     * 插件安装
     */
    public function install()
    {
        $name = $this->request->param('name');
        if (empty($name)) {
            return $this->error('请选择需要安装的插件！');
        }
        
        $installInfo = FlashModel::where(['name' => $name])
            ->find();
        if (! empty($installInfo)) {
            return $this->error('插件已经安装过了！');
        }
        
        Flasher::loadFlash();
        $info = Flasher::getFlash($name);
        if (empty($info)) {
            return $this->error('插件不存在！');
        }
        
        $checkInfo = Flasher::validateInfo($info);
        if (! $checkInfo) {
            return $this->error('插件信息错误！');
        }
        
        try {
            $infoVersion = (new VersionParser())->normalize($info['version']);
        } catch(\Exception $e) {
            return $this->error('插件版本信息错误！');
        }
        
        $adminVersion = config('laket.admin.version');
        
        try {
            $versionCheck = Semver::satisfies($adminVersion, $info['adaptation']);
        } catch(\Exception $e) {
            return $this->error('插件适配系统版本错误！');
        }
        
        if (! $versionCheck) {
            return $this->error('插件适配系统版本错误，当前系统版本：' . $adminVersion);
        }
        
        $requireExtensions = FlashModel::checkRequireExtension($info['require']);
        if (! empty($requireExtensions)) {
            foreach ($requireExtensions as $re) {
                if (! $re['match']) {
                    return $this->error('依赖插件(' . $re['name'] . ')错误，已安装版本: ' . $re['install_version']);
                }
            }
        }
        
        $flash = FlashModel::create([
            'name'         => Arr::get($info, 'name'),
            'title'        => Arr::get($info, 'title'),
            'description'  => Arr::get($info, 'description'),
            'keywords'     => json_encode(Arr::get($info, 'keywords')),
            'homepage'     => Arr::get($info, 'homepage'),
            'authors'      => json_encode(Arr::get($info, 'authors', [])),
            'version'      => Arr::get($info, 'version'),
            'adaptation'   => Arr::get($info, 'adaptation'),
            'require'      => json_encode(Arr::get($info, 'require')),
            'bind_service' => Arr::get($info, 'bind_service'),
            'setting'      => json_encode(Arr::get($info, 'setting', [])),
            'listorder'    => Arr::get($info, 'sort'),
        ]);
        if ($flash === false) {
            return $this->error('安装失败！');
        }
        
        Flasher::getNewClassMethod($flash['bind_service'], 'install');
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        return $this->success('安装成功！');
    }

    /**
     * 插件卸载
     */
    public function uninstall()
    {
        $name = $this->request->param('name');
        if (empty($name)) {
            return $this->error('请选择需要卸载的插件！');
        }
        
        $installInfo = FlashModel::where(['name' => $name])
            ->find();
        if (empty($installInfo)) {
            return $this->error('插件还没有安装！');
        }
        
        if ($installInfo['status'] == 1) {
            return $this->error('请禁用插件后再卸载！');
        }

        $status = FlashModel::where(['name' => $name])->delete();
        if ($status === false) {
            return $this->error("卸载失败！");
        }
        
        Flasher::loadFlash();
        Flasher::getNewClassMethod($installInfo['bind_service'], 'uninstall');
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        return $this->success("卸载成功！");
    }
    
    /**
     * 插件更新
     */
    public function upgrade()
    {
        $name = $this->request->param('name');
        if (empty($name)) {
            return $this->error('请选择需要更新的插件！');
        }
        
        $installInfo = FlashModel::where(['name' => $name])
            ->find();
        if (empty($installInfo)) {
            return $this->error('插件还没有安装！');
        }
        
        if ($installInfo['status'] == 1) {
            return $this->error('请禁用插件后再更新！');
        }

        Flasher::loadFlash();
        $info = Flasher::getFlash($name);
        if (empty($info)) {
            return $this->error('本地插件不存在！');
        }
        
        $checkInfo = Flasher::validateInfo($info);
        if (! $checkInfo) {
            return $this->error('插件信息错误！');
        }
        
        $adminVersion = config('laket.admin.version');
        
        try {
            $versionCheck = Semver::satisfies($adminVersion, $info['adaptation']);
        } catch(\Exception $e) {
            return $this->error('插件适配系统版本错误！');
        }
        
        if (! $versionCheck) {
            return $this->error('插件适配系统版本错误，当前系统版本：' . $adminVersion);
        }
        
        try {
            $infoVersion = (new VersionParser())->normalize($info['version']);
        } catch(\Exception $e) {
            return $this->error('插件版本信息不正确！');
        }
        
        $infoVersion = Arr::get($info, 'version', 0);
        $installVersion = Arr::get($installInfo, 'version', 0);
        if (! Comparator::greaterThan($infoVersion, $installVersion)) {
            return $this->error('插件不需要更新！');
        }
        
        $requireExtensions = FlashModel::checkRequireExtension($info['require']);
        if (! empty($requireExtensions)) {
            foreach ($requireExtensions as $re) {
                if (! $re['match']) {
                    return $this->error('依赖插件(' . $re['name'] . ')错误，已安装版本: ' . $re['install_version']);
                }
            }
        }

        $status = FlashModel::update([
                'title'        => Arr::get($info, 'title'),
                'description'  => Arr::get($info, 'description'),
                'keywords'     => json_encode(Arr::get($info, 'keywords')),
                'homepage'     => Arr::get($info, 'homepage'),
                'authors'      => json_encode(Arr::get($info, 'authors', [])),
                'version'      => Arr::get($info, 'version'),
                'adaptation'   => Arr::get($info, 'adaptation'),
                'require'      => json_encode(Arr::get($info, 'require')),
                'bind_service' => Arr::get($info, 'bind_service'),
                'setting'      => json_encode(Arr::get($info, 'setting', [])),
                'listorder'    => Arr::get($info, 'sort'),
                'upgrade_time' => time(),
            ], [
                'name' => $name
            ]);
        if ($status === false) {
            return $this->error('更新失败！');
        }
        
        Flasher::getNewClassMethod(Arr::get($info, 'bind_service'), 'upgrade');
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        return $this->success('更新成功！');
    }
    
    /**
     * 插件详情
     */
    public function view()
    {
        $name = $this->request->param('name/s');
        if (empty($name)) {
            return $this->error('请选择需要的插件！');
        }
        
        $data = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($data)) {
            return $this->error('信息不存在！');
        }
        
        $icon = '';
        if (class_exists($data['bind_service'])) {
            app()->register($data['bind_service']);
            $newClass = app()->getService($data['bind_service']);
            if (isset($newClass->composer)) {
                $icon = dirname($newClass->composer) . '/icon.png';
            }
        }
        
        $data['icon'] = Flasher::getIcon($icon);
        
        $this->assign("data", $data);
        
        return $this->fetch('laket-admin::flash.view');
    }
    
    /**
     * 插件设置
     */
    public function setting()
    {
        $name = $this->request->param('name/s');
        if (empty($name)) {
            return $this->error('请选择需要的插件！');
        }
        
        $info = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($info)) {
            return $this->error('插件不存在！');
        }
        
        $settinglist = $info['settinglist'];
        $setting_datalist = $info['setting_datalist'];
        
        foreach ($settinglist as &$value) {
            if (isset($setting_datalist[$value['name']])) {
                $value['value'] = $setting_datalist[$value['name']];
            }
            
            if (isset($value['type'])) {
                if ($value['type'] == 'checkbox') {
                    $value['value'] = empty($value['value']) ? [] : explode(',', $value['value']);
                }
                
                if ($value['type'] == 'date') {
                    $value['value'] = empty($value['value']) ? date('Y-m-d') : $value['value'];
                }
                
                if ($value['type'] == 'datetime') {
                    $value['value'] = empty($value['value']) ? date('Y-m-d H:i:s') : $value['value'];
                }
            }
        }
        
        $this->assign("info", $info);
        
        // 通用设置
        $this->assign("fields", $settinglist);
        
        return $this->fetch('laket-admin::flash.setting');
    }
    
    /**
     * 插件设置保存
     */
    public function settingSave()
    {
        $name = $this->request->param('name/s');
        if (empty($name)) {
            return $this->error('请选择需要的插件！');
        }
        
        $info = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($info)) {
            return $this->error('插件不存在！');
        }
        
        $data = $this->request->post('item/a');
        
        $settinglist = $info['settinglist'];
        
        foreach ($settinglist as $setting) {
            $name = $setting['name'];
            $type = $setting['type'];
            $title = $setting['title'];
            
            // 查看是否赋值
            if (! isset($data[$name])) {
                switch ($type) {
                    // 开关
                    case 'switch':
                        $data[$name] = 0;
                        break;
                    case 'checkbox':
                        $data[$name] = '';
                        break;
                }
            } else {
                if (is_array($data[$name])) {
                    $data[$name] = implode(',', $data[$name]);
                }
                switch ($type) {
                    case 'switch':
                        $data[$name] = 1;
                        break;
                }
            }
        }
        
        $status = FlashModel::where([
            'id' => $info['id'],
        ])->update([
            'setting_data' => json_encode($data),
        ]);
        
        if ($status === false) {
            return $this->error('保存设置失败！');
        }
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        return $this->success('保存设置成功！');
    }
    
    /**
     * 启用插件
     */
    public function enable()
    {
        $name = $this->request->param('name/s');
        if (empty($name)) {
            return $this->error('请选择需要的插件！');
        }
        
        $installInfo = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($installInfo)) {
            return $this->error('插件还没有安装！');
        }
        
        $status = FlashModel::where([
            'name' => $name,
        ])->update([
            'status' => 1,
        ]);
        if ($status === false) {
            return $this->error('启用失败！');
        }
        
        Flasher::loadFlash();
        Flasher::getNewClassMethod($installInfo['bind_service'], 'enable');
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        return $this->success('启用成功！');
    }
    
    /**
     * 禁用插件
     */
    public function disable()
    {
        $name = $this->request->param('name/s');
        if (empty($name)) {
            return $this->error('请选择需要的插件！');
        }
        
        $installInfo = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($installInfo)) {
            return $this->error('插件还没有安装！');
        }
        
        $status = FlashModel::where([
            'name' => $name,
        ])->update([
            'status' => 0,
        ]);
        
        if ($status === false) {
            return $this->error('禁用失败！');
        }
        
        Flasher::getNewClassMethod($installInfo['bind_service'], 'disable');
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        return $this->success('禁用成功！');
    }

    /**
     * 插件排序
     */
    public function listorder()
    {
        $name = $this->request->param('name/s', 0);
        if (empty($name)) {
            return $this->error('请选择需要的插件！');
        }
        
        $listorder = $this->request->param('value/d', 100);
        
        $rs = FlashModel::where([
            'name' => $name,
        ])->update([
            'listorder' => $listorder,
        ]);
        if ($rs === false) {
            return $this->error("排序失败！");
        }
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        return $this->success("排序成功！");
    }
    
    /**
     * 上传插件
     */
    public function upload()
    {
        $requestFile = $this->request->file('file');
        if (empty($requestFile)) {
            return $this->error('上传插件文件不能为空');
        }
        
        // 插件压缩包后缀
        $extension = $requestFile->extension();
        if ($extension != 'zip') {
            return $this->error('上传的插件文件格式只支持zip格式');
        }
        
        // 缓存目录
        if (! defined('PCLZIP_TEMPORARY_DIR')) {
            define('PCLZIP_TEMPORARY_DIR', runtime_path('cache'));
        }
        
        // 解析composer.json
        $filename = $requestFile->getPathname();
        $zip = new PclZip($filename);
        
        $list = $zip->listContent();
        if ($list == 0) {
            return $this->error('上传的插件文件错误');
        }
        
        $composer = collect($list)
            ->map(function($item) {
                if (strpos($item['filename'], 'composer.json') !== false) {
                    return $item;
                }
            })
            ->filter(function($data) {
                return !empty($data);
            })
            ->sort(function($item) {
                $item['filename'] = str_replace('\\', '/', $item['filename']);
                return count(explode('/', $item['filename']));
            })
            ->values()
            ->toArray();
        
        if (empty($composer)) {
            return $this->error('插件composer.json不存在');
        }
        
        $data = $zip->extractByIndex($composer[0]['index'], PCLZIP_OPT_EXTRACT_AS_STRING);
        if ($data == 0) {
            return $this->error('上传的插件文件错误');
        }
        
        try {
            $composerInfo = json_decode($data[0]['content'], true);
        } catch(\Exception $e) {
            return $this->error('插件composer.json格式错误');
        }
        
        if (! isset($composerInfo['name']) 
            || empty($composerInfo['name'])
        ) {
            return $this->error('插件composer.json格式错误');
        }
        
        if (! preg_match('/^[a-zA-Z][a-zA-Z0-9\_\-\/]+$/', $composerInfo['name'])) {
            return $this->error('插件包名格式错误');
        }
        
        $extensionDirectory = Flasher::getFlashPath('');
        $extensionPath = Flasher::getFlashPath($composerInfo['name']);
        
        $force = $this->request->param('force');
        
        // 检查插件目录是否存在
        if (file_exists($extensionPath) && !$force) {
            return $this->error('插件('.$composerInfo['name'].')已经存在');
        }
        
        $extensionRemovePath = str_replace('composer.json', '', $composer[0]['filename']);
        $extensionPregPath = '/^'.str_replace(['\\', '/'], ['\\\\', '\\/'], $extensionRemovePath).'.*?/';
        
        // 解压文件
        $list = $zip->extract(
            PCLZIP_OPT_PATH, $extensionPath,
            PCLZIP_OPT_REMOVE_PATH, $extensionRemovePath,
            PCLZIP_OPT_EXTRACT_DIR_RESTRICTION, $extensionDirectory,
            PCLZIP_OPT_BY_PREG, $extensionPregPath,
            PCLZIP_OPT_REPLACE_NEWER,
        );
        
        if ($list == 0) {
            return $this->error('插件('.$composerInfo['name'].')解压失败');
        }
        
        // 上传后刷新本地缓存
        Flasher::refresh();
        
        return $this->success('插件('.$composerInfo['name'].')上传成功');
    }

}
