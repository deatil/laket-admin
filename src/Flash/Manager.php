<?php

declare (strict_types = 1);

namespace Laket\Admin\Flash;

use ReflectionClass;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use think\facade\Db;
use think\facade\Route;
use think\facade\Cache;
use think\helper\Arr;

use Laket\Admin\Support\File;
use Laket\Admin\Support\Sql;
use Laket\Admin\Model\Flash as FlashModel;
use Laket\Admin\Flash\Service as FlashService;

/**
 * 闪存管理
 *
 * @create 2021-3-19
 * @author deatil
 */
class Manager
{
    /**
     * @var array
     */
    public $flashs = [];
    
    /**
     * @var string 本地闪存缓存id
     */
    public $flashsCacheId = 'laket-admin-local-flashs';

    /**
     * 添加闪存
     *
     * @param string $name 包名
     * @param string $class 绑定服务类
     *
     * @return self
     */
    public function extend($name, $class = null)
    {
        if (!empty($name) && !empty($class)) {
            $this->forget($name);
            
            $this->flashs[$name] = $class;
        }
        
        return $this;
    }
    
    /**
     * 获取添加的闪存
     *
     * @param string $name
     *
     * @return string
     */
    public function getExtend($name = null)
    {
        if (isset($this->flashs[$name])) {
            return $this->flashs[$name];
        }
        
        return $this->flashs;
    }
    
    /**
     * 移除添加的闪存
     *
     * @param string $name
     *
     * @return string|null
     */
    public function forget($name)
    {
        if (isset($this->flashs[$name])) {
            $flash = $this->flashs[$name];
            unset($this->flashs[$name]);
            return $flash;
        }
        
        return null;
    }
    
    /**
     * 检测非compoer闪存是否存在
     *
     * @param string $name 闪存包名
     *
     * @return bool
     */
    public function checkLocal($name)
    {
        $flashDirectory = $this->getFlashPath($name);
        
        if (file_exists($flashDirectory)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 设置闪存路由
     *
     * @param $callback
     * 
     * @return self
     */
    public function routes($callback)
    {
        Route::group(config('laket.route.group'), $callback)
            ->middleware(config('laket.route.middleware'));
        
        return $this;
    }
    
    /**
     * 设置命名空间
     *
     * @param $prefix
     * @param $paths
     * 
     * @return self
     */
    public function namespaces($prefix, $paths = [])
    {
        app('laket-admin.loader', [], true)
            ->setPsr4($prefix, $paths)
            ->register();
        
        return $this;
    }
    
    /**
     * 添加登陆过滤
     *
     * @param array $excepts
     * 
     * @return void
     */
    public function authenticateExcepts(array $excepts = [])
    {
        if (empty($excepts)) {
            return ;
        }
        
        $auth = config('laket.auth', []);
        foreach ($excepts as $except) {
            $auth['authenticate_excepts'][] = $except;
        }
        
        config([
            'auth' => $auth,
        ], 'laket');
    }
    
    /**
     * 添加权限过滤
     *
     * @param array $excepts
     * 
     * @return void
     */
    public function permissionExcepts(array $excepts = [])
    {
        if (empty($excepts)) {
            return ;
        }
        
        $auth = config('laket.auth', []);
        foreach ($excepts as $except) {
            $auth['permission_excepts'][] = $except;
        }
        
        config([
            'auth' => $auth,
        ], 'laket');
    }
    
    /**
     * 执行sql
     *
     * @param string $sql sql字符或者文件
     * 
     * @return boolen
     */
    public function executeSql(string $sql)
    {
        if (file_exists($sql)) {
            $sqlStatement = Sql::getSqlFromFile($sql);
        } else {
            $sqlStatement = $sql;
        }
        
        if (empty($sqlStatement)) {
            return false;
        }
        
        $dbPrefix = app()->db->connect()->getConfig('prefix');
        foreach ($sqlStatement as $value) {
            $value = str_replace([
                'laket__',
                'pre__',
                'prefix__',
                '__PRE__',
                '__PREFIX__',
            ], $dbPrefix, trim($value));
            Db::execute($value);
        }
        
        return true;
    }
    
    /**
     * 加载闪存
     *
     * @return void
     */
    public function bootFlash()
    {
        // 检测
        try {
            Db::query("show databases");
        } catch(\Exception $e) {
            return ;
        }
        
        $dbPrefix = app()->db->connect()->getConfig('prefix');
        $modelName = $dbPrefix.'laket_flash';
        if (! Db::execute("SHOW TABLES LIKE '%{$modelName}%'")) {
            return ;
        }

        $list = FlashModel::getFlashs();
        $flashDirectory = $this->getFlashPath();

        $services = collect($list)->map(function($data) use($flashDirectory) {
            if ($data['status'] != 1) {
                return null;
            }

            if (empty($data['name'])) {
                return null;
            }
            
            // 闪存绑定类
            if (empty($data['bind_service'])) {
                return null;
            }
            
            $directory = realpath($flashDirectory . DIRECTORY_SEPARATOR . $data['name']);
            
            if (! class_exists($data['bind_service']) 
                && file_exists($directory)
            ) {
                // 绑定非composer闪存
                $cacheId = md5(str_replace('\\', '/', $data['name']));
                
                $composerData = Cache::get($cacheId);
                if (! $composerData) {
                    $composerData = $this->parseComposer($directory . '/composer.json');
                    Cache::set($cacheId, $composerData, 10080);
                }
                
                $this->registerPsr4(Arr::get($composerData, 'psr-4', []));
                $this->registerService(Arr::get($composerData, 'services', []));
            }
            
            if (! class_exists($data['bind_service'])) {
                return null;
            }
            
            // 获取绑定服务
            $newClass = app()->getService($data['bind_service']);
            
            if (! $newClass) {
                return null;
            }
            
            return $newClass;
        })->filter(function($data) {
            return !empty($data);
        })->toArray();

        array_walk($services, function ($s) {
            $this->startService($s);
        });
    }
    
    /**
     * 启动闪存服务
     *
     * @return void
     */
    protected function startService(FlashService $service)
    {
        $service->callStartingCallbacks();

        if (method_exists($service, 'start')) {
            app()->invoke([$service, 'start']);
        }

        $service->callStartedCallbacks();
    }
    
    /**
     * 解析composer
     *
     * @return array
     */
    public function parseComposer($composer)
    {
        if (! file_exists($composer)) {
            return [];
        }
        
        try {
            $composerData = (array) json_decode(file_get_contents($composer), true);
        } catch (\Throwable $e) {
            return [];
        }
        
        $psr4 = Arr::get($composerData, 'autoload.psr-4', []);
        $newPsr4 = [];
        foreach ($psr4 as $key => $value) {
            $newPsr4[$key] = realpath(dirname($composer) . '/' . $value);
        }
        
        $newData = [
            'psr-4' => $newPsr4,
            'services' => Arr::get($composerData, 'extra.think.services', []),
        ];
        
        return $newData;
    }
    
    /**
     * 注册命名空间
     */
    public function registerPsr4($data)
    {
        foreach ($data as $namespace => $path) {
            $this->namespaces($namespace, $path);
        }
    }
    
    /**
     * 注册服务
     */
    public function registerService($services, $boot = false)
    {
        foreach ($services as $service) {
            if (! class_exists($service)) {
                continue;
            }
            
            $registerService = app()->register($service);
            
            if (! $registerService && $boot) {
                $newService = app()->getService($service);
                app()->bootService($newService);
            }
        }
    }
    
    /**
     * 加载本地闪存
     *
     * @return self
     */
    public function loadFlash()
    {
        $flashs = Cache::get($this->flashsCacheId);
        if (! $flashs) {
            $directory = $this->getFlashPath();
            $directories = $this->getDirectories($directory);
            
            $flashs = collect($directories)
                ->sort()
                ->map(function($path) {
                    $composerData = $this->parseComposer($path . '/composer.json');
                    return $composerData;
                })
                ->values()
                ->toArray();
            
            Cache::set($this->flashsCacheId, $flashs, 10080);
        }
        
        collect($flashs)->each(function($flash) {
            $services = Arr::get($flash, 'services', []);
            
            $this->registerPsr4(Arr::get($flash, 'psr-4', []));
            $this->registerService(Arr::get($flash, 'services', []), true);
        });
        
        return $this;
    }
    
    /**
     * 刷新本地加载闪存
     *
     * @return self
     */
    public function refresh()
    {
        Cache::delete($this->flashsCacheId);
        
        return $this;
    }
    
    /**
     * 移除闪存信息缓存
     *
     * @param string $name
     *
     * @return self
     */
    public function forgetFlashCache(string $name)
    {
        // 清除缓存
        $cacheId = md5(str_replace('\\', '/', $name));
        Cache::delete($cacheId);
        
        // 清空安装缓存
        FlashModel::clearCahce();
        
        return $this;
    }
    
    /**
     * 闪存存放文件夹
     *
     * @param string $path
     *
     * @return string
     */
    public function getFlashDirectory()
    {
        return config('laket.flash.directory');
    }
    
    /**
     * 闪存存放目录
     *
     * @param string $path
     *
     * @return string
     */
    public function getFlashPath(string $path = '')
    {
        $flashPath = root_path($this->getFlashDirectory());
        return $flashPath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
    
    /**
     * 闪存绑定类
     *
     * @param string|null $name
     *
     * @return string
     */
    public function getFlashClass(?string $name = null)
    {
        if (empty($name)) {
            return '';
        }
        
        $className = Arr::get($this->flashs, $name, '');
        
        return $className;
    }
    
    /**
     * 实例化类
     *
     * @param string|null $className
     *
     * @return object
     */
    public function getNewClass(?string $className = null)
    {
        if (! class_exists($className)) {
            return false;
        }
        
        app()->register($className);
        
        $newClass = app()->getService($className);
        if (! ($newClass instanceof FlashService)) {
            return false;
        }
        
        return $newClass;
    }
    
    /**
     * 实例化类方法
     *
     * @param string|null $className 
     * @param string|null $method 
     * @param array $param 
     *
     * @return mixed
     */
    public function getNewClassMethod(?string $className = null, ?string $method = null, array $param = [])
    {
        if (empty($className) || empty($method)) {
            return false;
        }
        
        $newClass = $this->getNewClass($className);
        if (! $newClass) {
            return false;
        }
        
        if (! method_exists($newClass, $method)) {
            return false;
        }
        
        $res = call_user_func_array([$newClass, $method], $param);
        return $res;
    }
    
    /**
     * 闪存的实例化类
     *
     * @param string|null $name
     *
     * @return mixed|object
     */
    public function getFlashNewClass(?string $name = null)
    {
        $className = $this->getFlashClass($name);
        
        return $this->getNewClass($className);
    }
    
    /**
     * 闪存信息
     *
     * @param string|null $name
     *
     * @return array
     */
    public function getFlash(?string $name = null)
    {
        $newClass = $this->getFlashNewClass($name);
        if ($newClass === false) {
            return [];
        }
        
        if (! isset($newClass->composer)) {
            return [];
        }
        
        try {
            $info = (array) json_decode(file_get_contents($newClass->composer), true);
        } catch (\Throwable $e) {
            $info = [];
        }
        
        // 图标
        if (! empty($newClass->icon)) {
            $iconPath = $newClass->icon;
        } else {
            $iconPath = dirname($newClass->composer) . '/icon.png';
        }
        $icon = $this->getIcon($iconPath);

        // 设置
        $setting = [];
        if (! empty($newClass->setting)) {
            if (is_array($newClass->setting)) {
                $setting = $newClass->setting;
            } elseif (is_string($newClass->setting) 
                && file_exists($newClass->setting)
            ) {
                $setting = include $newClass->setting;
            }
        } else {
            $settingPath = dirname($newClass->composer) . '/setting.php';
            if (file_exists($settingPath)) {
                $setting = include $settingPath;
            }
        }
        
        return [
            'icon' => $icon,
            'name' => $name,
            'title' => Arr::get($info, 'laket.title'),
            'description' => Arr::get($info, 'description'),
            'keywords' => Arr::get($info, 'keywords'),
            'homepage' => Arr::get($info, 'homepage'),
            'authors' => Arr::get($info, 'authors', []), 
            'version' => Arr::get($info, 'laket.version'),
            'adaptation' => Arr::get($info, 'laket.adaptation'),
            'bind_service' => Arr::get($this->flashs, $name, ''),
            'setting' => (array) $setting,
        ];
    }
    
    /**
     * 全部添加的闪存
     *
     * @return array
     */
    public function getFlashs()
    {
        $flashs = $this->flashs;
        
        $thiz = $this;
        
        $list = collect($flashs)->each(function($className, $name) use($thiz) {
            $info = $thiz->getFlash($name);
            if (!empty($info)) {
                return $info;
            }
        })->filter(function($data) {
            return !empty($data);
        })->toArray();
        
        return $list;
    }
    
    /**
     * 闪存标识图片
     *
     * @param string|null $icon
     *
     * @return string
     */    
    public function getIcon($icon = '')
    {
        if (! file_exists($icon) || ! is_file($icon)) {
            return '';
        }
        
        $data = file_get_contents($icon);
        $base64Data = base64_encode($data);
        
        $iconData = "data:image/png;base64,{$base64Data}";
        
        return $iconData;
    }
    
    /**
     * 验证闪存信息
     *
     * @param array $info
     *
     * @return boolen
     */
    public function validateInfo(array $info)
    {
        $mustInfo = [
            'title',
            'description',
            'keywords',
            'authors',
            'version',
            'adaptation',
        ];
        if (empty($info)) {
            return false;
        }
        
        foreach ($mustInfo as $item) {
            if (!isset($info[$item]) || empty($info[$item])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 获取满足条件的闪存文件夹
     *
     * @param string|null $dirPath
     *
     * @return array
     */
    public function getDirectories(?string $dirPath = null)
    {
        $flashs = [];
        
        if (empty($dirPath) || ! is_dir($dirPath)) {
            return $flashs;
        }

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::FOLLOW_SYMLINKS)
        );
        $it->setMaxDepth(2);
        $it->rewind();

        while ($it->valid()) {
            if ($it->getDepth() > 1 
                && $it->isFile()
                && $it->getFilename() === 'composer.json'
            ) {
                $flashs[] = dirname($it->getPathname());
            }

            $it->next();
        }

        return $flashs;
    }
    
}
