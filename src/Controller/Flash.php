<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use Composer\Semver\Semver;
use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;

use think\facade\Db;
use think\helper\Arr;

use Laket\Admin\Facade\Flash as Flasher;
use Laket\Admin\Model\Flash as FlashModel;

/**
 * 闪存
 *
 * @create 2021-3-19
 * @author deatil
 */
class Flash extends Base
{
    /**
     * 已安装
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            
            $list = FlashModel::order('listorder ASC, name ASC')
                ->paginate([
                    'list_rows' => $limit,
                    'page' => $page
                ])
                ->toArray();
            
            // 添加icon图标
            $list['data'] = collect($list['data'])
                ->each(function($data, $key) {
                    $icon = '';
                    if (class_exists($data['bind_service'])) {
                        app()->register($data['bind_service']);
                        $newClass = app()->getService($data['bind_service']);
                        if (isset($newClass->composer)) {
                            $icon = dirname($newClass->composer) . '/icon.png';
                        }
                    }
                    
                    $data['icon'] = Flasher::getIcon($icon);
                    
                    return $data;
                })
                ->toArray();
            
            return $this->json([
                "code" => 0, 
                'msg' => '获取成功！',
                'data' => $list['data'],
                'count' => $list['total'],
            ]);
        } else {
            return $this->fetch('laket-admin::flash.index');
        }
    }

    /**
     * 本地列表
     */
    public function local()
    {
        Flasher::loadFlash();
        $flashs = Flasher::getFlashs();
        
        $installFlashs = FlashModel::getFlashs();
        $flashs = collect($flashs)->each(function($data, $key) use($installFlashs) {
            if (isset($installFlashs[$data['name']])) {
                $data['install'] = $installInfo = $installFlashs[$data['name']];
                
                $infoVersion = Arr::get($data, 'version', 0);
                $installVersion = Arr::get($installInfo, 'version', 0);
                if (Comparator::greaterThan($infoVersion, $installVersion)) {
                    $data['upgrade'] = 1;
                } else {
                    $data['upgrade'] = 0;
                }
            } else {
                $data['install'] = [];
                $data['upgrade'] = 0;
            }
            
            return $data;
        });
        
        $this->assign('list', $flashs);
        
        return $this->fetch('laket-admin::flash.local');
    }
    
    /**
     * 刷新本地闪存
     */
    public function refreshLocal()
    {
        Flasher::refresh();
        
        return $this->success('闪存刷新成功');
    }
    
    /**
     * 安装
     */
    public function install()
    {
        $name = $this->request->param('name');
        if (empty($name)) {
            $this->error('请选择需要安装的闪存！');
        }
        
        $installInfo = FlashModel::where(['name' => $name])
            ->find();
        if (! empty($installInfo)) {
            $this->error('闪存已经安装过了！');
        }
        
        Flasher::loadFlash();
        $info = Flasher::getFlash($name);
        if (empty($info)) {
            $this->error('闪存不存在！');
        }
        
        $checkInfo = Flasher::validateInfo($info);
        if (! $checkInfo) {
            return $this->error('闪存信息错误！');
        }
        
        try {
            $infoVersion = (new VersionParser())->normalize($info['version']);
        } catch(\Exception $e) {
            return $this->error('闪存版本信息错误！');
        }
        
        $adminVersion = config('laket.admin.version');
        
        try {
            $versionCheck = Semver::satisfies($adminVersion, $info['adaptation']);
        } catch(\Exception $e) {
            return $this->error('闪存适配系统版本错误！');
        }
        
        if (! $versionCheck) {
            return $this->error('闪存适配系统版本错误，当前系统版本：' . $adminVersion);
        }
        
        $flash = FlashModel::create([
            'name' => Arr::get($info, 'name'),
            'title' => Arr::get($info, 'title'),
            'description' => Arr::get($info, 'description'),
            'keywords' => json_encode(Arr::get($info, 'keywords')),
            'homepage' => Arr::get($info, 'homepage'),
            'authors' => json_encode(Arr::get($info, 'authors', [])),
            'version' => Arr::get($info, 'version'),
            'adaptation' => Arr::get($info, 'adaptation'),
            'bind_service' => Arr::get($info, 'bind_service'),
            'setting' => json_encode(Arr::get($info, 'setting', [])),
        ]);
        if ($flash === false) {
            $this->error('安装失败！');
        }
        
        Flasher::getNewClassMethod($flash['bind_service'], 'install');
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        $this->success('安装成功！');
    }

    /**
     * 卸载
     */
    public function uninstall()
    {
        $name = $this->request->param('name');
        if (empty($name)) {
            $this->error('请选择需要卸载的闪存！');
        }
        
        $installInfo = FlashModel::where(['name' => $name])
            ->find();
        if (empty($installInfo)) {
            $this->error('闪存还没有安装！');
        }
        
        $status = FlashModel::where(['name' => $name])->delete();
        if ($status === false) {
            $this->error("卸载失败！");
        }
        
        Flasher::loadFlash();
        Flasher::getNewClassMethod($installInfo['bind_service'], 'uninstall', [
            'flash' => $installInfo,
        ]);
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        $this->success("卸载成功！");
    }
    
    /**
     * 模块更新
     */
    public function upgrade()
    {
        $name = $this->request->param('name');
        if (empty($name)) {
            $this->error('请选择需要更新的闪存！');
        }
        
        $installInfo = FlashModel::where(['name' => $name])
            ->find();
        if (empty($installInfo)) {
            $this->error('闪存还没有安装！');
        }
        
        Flasher::loadFlash();
        $info = Flasher::getFlash($name);
        if (empty($info)) {
            $this->error('本地闪存不存在！');
        }
        
        $checkInfo = Flasher::validateInfo($info);
        if (! $checkInfo) {
            return $this->error('闪存信息错误！');
        }
        
        $adminVersion = config('laket.admin.version');
        
        try {
            $versionCheck = Semver::satisfies($adminVersion, $info['adaptation']);
        } catch(\Exception $e) {
            return $this->error('闪存适配系统版本错误！');
        }
        
        if (! $versionCheck) {
            return $this->error('闪存适配系统版本错误，当前系统版本：' . $adminVersion);
        }
        
        try {
            $infoVersion = (new VersionParser())->normalize($info['version']);
        } catch(\Exception $e) {
            return $this->error('闪存版本信息不正确！');
        }
        
        $infoVersion = Arr::get($info, 'version', 0);
        $installVersion = Arr::get($installInfo, 'version', 0);
        if (! Comparator::greaterThan($infoVersion, $installVersion)) {
            return $this->error('闪存不需要更新！');
        }
        
        $status = FlashModel::update([
                'title' => Arr::get($info, 'title'),
                'description' => Arr::get($info, 'description'),
                'keywords' => json_encode(Arr::get($info, 'keywords')),
                'homepage' => Arr::get($info, 'homepage'),
                'authors' => json_encode(Arr::get($info, 'authors', [])),
                'version' => Arr::get($info, 'version'),
                'adaptation' => Arr::get($info, 'adaptation'),
                'bind_service' => Arr::get($info, 'bind_service'),
                'setting' => json_encode(Arr::get($info, 'setting', [])),
                'upgrade_time' => time(),
            ], [
                'name' => $name
            ]);
        if ($status === false) {
            $this->error('更新失败！');
        }
        
        Flasher::getNewClassMethod(Arr::get($info, 'bind_service'), 'upgrade');
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        $this->success('更新成功！');
    }
    
    /**
     * 详情
     */
    public function view()
    {
        $name = $this->request->param('name/s');
        if (empty($name)) {
            $this->error('请选择需要的闪存！');
        }
        
        $data = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($data)) {
            $this->error('信息不存在！');
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
     * 设置
     */
    public function setting()
    {
        $name = $this->request->param('name/s');
        if (empty($name)) {
            $this->error('请选择需要的闪存！');
        }
        
        $info = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($info)) {
            $this->error('信息不存在！');
        }
        
        if ($this->request->isPost()) {
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
                $this->error('设置失败！');
            }
            
            $this->success('设置成功！');
        } else {
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
    }
    
    /**
     * 启用
     */
    public function enable()
    {
        $name = $this->request->param('name/s');
        if (empty($name)) {
            $this->error('请选择需要的闪存！');
        }
        
        $installInfo = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($installInfo)) {
            $this->error('闪存还没有安装！');
        }
        
        $status = FlashModel::where([
            'name' => $name,
        ])->update([
            'status' => 1,
        ]);
        if ($status === false) {
            $this->error('启用失败！');
        }
        
        Flasher::loadFlash();
        Flasher::getNewClassMethod($installInfo['bind_service'], 'enable');
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        $this->success('启用成功！');
    }
    
    /**
     * 禁用
     */
    public function disable()
    {
        $name = $this->request->param('name/s');
        if (empty($name)) {
            $this->error('请选择需要的闪存！');
        }
        
        $installInfo = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($installInfo)) {
            $this->error('闪存还没有安装！');
        }
        
        $status = FlashModel::where([
            'name' => $name,
        ])->update([
            'status' => 0,
        ]);
        
        if ($status === false) {
            $this->error('禁用失败！');
        }
        
        Flasher::getNewClassMethod($installInfo['bind_service'], 'disable');
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        $this->success('禁用成功！');
    }

    /**
     * 排序
     */
    public function listorder()
    {
        $name = $this->request->param('name/s', 0);
        if (empty($name)) {
            $this->error('请选择需要的闪存！');
        }
        
        $listorder = $this->request->param('value/d', 100);
        
        $rs = FlashModel::where([
            'name' => $name,
        ])->update([
            'listorder' => $listorder,
        ]);
        if ($rs === false) {
            $this->error("排序失败！");
        }
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
        
        $this->success("排序成功！");
    }

}
