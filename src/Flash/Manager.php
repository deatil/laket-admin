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
 * 插件管理
 *
 * @create 2021-3-19
 * @author deatil
 */
class Manager
{
    /**
     * @var \Closure(string):void
     */
    private static $includeFile;
    
    /**
     * @var array
     */
    public $flashs = [];
    
    /**
     * 本地插件缓存id
     * 
     * @var string 
     */
    public $flashsCacheId = 'laket-admin-local-flashs';

    /**
     * 构造函数
     */
    public function __construct()
    {
        self::initializeIncludeClosure();
    }
    
    /**
     * 添加插件
     *
     * @param string $name 包名
     * @param string $class 绑定服务类
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
     * 获取添加的插件
     *
     * @param string $name
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
     * 移除添加的插件
     *
     * @param string $name
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
     * 检测非compoer插件是否存在
     *
     * @param string $name 插件包名
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
     * 设置插件路由
     *
     * @param $callback
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
     * 登录过滤
     *
     * @param array $excepts
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
     * 权限过滤
     *
     * @param array $excepts
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
     * 锁屏过滤
     *
     * @param array $excepts
     * @return void
     */
    public function screenlockExcepts(array $excepts = [])
    {
        if (empty($excepts)) {
            return ;
        }
        
        $auth = config('laket.auth', []);
        foreach ($excepts as $except) {
            $auth['screenlock_excepts'][] = $except;
        }
        
        config([
            'auth' => $auth,
        ], 'laket');
    }
    
    /**
     * 执行sql
     *
     * @param string $sql sql字符或者文件
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
     * 加载插件
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
            
            // 插件绑定类
            if (empty($data['bind_service'])) {
                return null;
            }
            
            $directory = realpath($flashDirectory . DIRECTORY_SEPARATOR . $data['name']);
            
            if (! class_exists($data['bind_service']) 
                && file_exists($directory)
            ) {
                // 绑定非composer插件
                $cacheId = md5(str_replace('\\', '/', $data['name']));
                
                $composerData = Cache::get($cacheId);
                if (! $composerData) {
                    $composerData = $this->parseComposer($directory . '/composer.json');
                    Cache::set($cacheId, $composerData, 10080);
                }
                
                $this->registerPsr4(Arr::get($composerData, 'psr-4', []));
                $this->loadFile(Arr::get($composerData, 'files', []));
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
     * 启动插件服务
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
        
        $files = Arr::get($composerData, 'autoload.files', []);
        $newFiles = [];
        foreach ($files as $key => $value) {
            $newFiles[] = realpath(dirname($composer) . '/' . $value);
        }
        
        $newData = [
            'psr-4' => $newPsr4,
            'files' => $newFiles,
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
     * 加载文件
     */
    public function loadFile($files)
    {
        $files = is_array($files) ? $files : [$files];
        
        foreach ($files as $file) {
            $this->includeFile($file);
        }
    }
    
    /**
     * 引入文件
     *
     * @param  string    $class 文件
     * @return true|null True if loaded, false otherwise
     */
    protected function includeFile($file)
    {
        if (file_exists($file)) {
            $includeFile = self::$includeFile;
            $includeFile($file);

            return true;
        }

        return false;
    }
    
    /**
     * 加载本地插件
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
                ->map(function($path) {
                    $composerData = $this->parseComposer($path . '/composer.json');
                    return $composerData;
                })
                ->order('name')
                ->values()
                ->toArray();
            
            Cache::set($this->flashsCacheId, $flashs, 10080);
        }
        
        collect($flashs)->each(function($flash) {
            $services = Arr::get($flash, 'services', []);
            
            $this->registerPsr4(Arr::get($flash, 'psr-4', []));
            $this->loadFile(Arr::get($flash, 'files', []));
            $this->registerService(Arr::get($flash, 'services', []), true);
        });
        
        return $this;
    }
    
    /**
     * 刷新本地加载插件
     *
     * @return self
     */
    public function refresh()
    {
        Cache::delete($this->flashsCacheId);
        
        return $this;
    }
    
    /**
     * 移除插件信息缓存
     *
     * @param string $name
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
     * 插件存放文件夹
     *
     * @param string $path
     * @return string
     */
    public function getFlashDirectory()
    {
        return config('laket.flash.directory');
    }
    
    /**
     * 插件存放目录
     *
     * @param string $path
     * @return string
     */
    public function getFlashPath(string $path = '')
    {
        $flashPath = root_path($this->getFlashDirectory());
        return $flashPath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
    
    /**
     * 插件绑定类
     *
     * @param string|null $name
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
     * @param string $className 
     * @param string $method 
     * @param array  $param 
     * @return mixed
     */
    public function getNewClassMethod(string $className, string $method, array $param = [])
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
        
        return call_user_func_array([$newClass, $method], $param);
    }
    
    /**
     * 插件的实例化类
     *
     * @param string|null $name
     * @return mixed|object
     */
    public function getFlashNewClass(?string $name = null)
    {
        $className = $this->getFlashClass($name);
        
        return $this->getNewClass($className);
    }
    
    /**
     * 插件标识
     *
     * @param object|null $newClass
     * @return string
     */
    public function getFlashIcon(?object $newClass = null)
    {
        if (! is_object($newClass)) {
            return '';
        }
        
        if (! empty($newClass->icon)) {
            $icon = $newClass->icon;
        } else {
            if (empty($newClass->composer)) {
                $composer = $this->getPathFromClass($newClass) . '/../composer.json';
            } else {
                $composer = $newClass->composer;
            }
            
            $icon = dirname($composer) . '/icon.png';
        }
        
        return $this->getIcon($icon);
    }
    
    /**
     * 插件信息
     *
     * @param string|null $name
     * @return array
     */
    public function getFlash(?string $name = null)
    {
        $newClass = $this->getFlashNewClass($name);
        if ($newClass === false) {
            return [];
        }
        
        if (empty($newClass->composer)) {
            $composer = $this->getPathFromClass($newClass) . '/../composer.json';
        } else {
            $composer = $newClass->composer;
        }
        
        if (! file_exists($composer)) {
            return [];
        }
        
        try {
            $info = (array) json_decode(file_get_contents($composer), true);
        } catch (\Exception $e) {
            $info = [];
        }
        
        if (empty($info)) {
            return [];
        }
        
        // 图标
        if (! empty($newClass->icon)) {
            $iconPath = $newClass->icon;
        } else {
            $iconPath = dirname($composer) . '/icon.png';
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
            $settingPath = dirname($composer) . '/setting.php';
            if (file_exists($settingPath)) {
                $setting = include $settingPath;
            }
        }
        
        return [
            'icon'         => $icon,
            'name'         => $name,
            'title'        => Arr::get($info, 'laket.title'),
            'description'  => Arr::get($info, 'description'),
            'keywords'     => Arr::get($info, 'keywords'),
            'homepage'     => Arr::get($info, 'homepage'),
            'authors'      => Arr::get($info, 'authors', []), 
            'version'      => Arr::get($info, 'laket.version'),
            'adaptation'   => Arr::get($info, 'laket.adaptation'),
            'require'      => Arr::get($info, 'laket.require', []),
            'bind_service' => Arr::get($this->flashs, $name, ''),
            'setting'      => (array) $setting,
            'sort'         => Arr::get($info, 'laket.sort', 100),
        ];
    }
    
    /**
     * 全部添加的插件
     *
     * @return array
     */
    public function getFlashs()
    {
        $thiz = $this;
        
        $flashs = $this->flashs;
        $list = collect($flashs)->each(function($className, $name) use($thiz) {
            $info = $thiz->getFlash($name);
            if (! empty($info)) {
                return $info;
            }
            
            return [];
        })->filter(function($data) {
            return !empty($data);
        })->toArray();
        
        return $list;
    }
    
    /**
     * 插件标识图片
     *
     * @param string|null $icon
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
     * 验证插件信息
     *
     * @param array $info
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
     * 获取满足条件的插件文件夹
     *
     * @param string|null $dirPath
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
    
    /**
     * 根据类名获取类所在文件夹
     *
     * @param string|object|null $class
     * @return string|bool
     */
    public function getPathFromClass($class = null)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        
        $reflection = new ReflectionClass($class);
        $filePath = dirname($reflection->getFileName());

        return $filePath;
    }
    
    /**
     * @return void
     */
    private static function initializeIncludeClosure()
    {
        if (self::$includeFile !== null) {
            return;
        }

        /**
         * Scope isolated include.
         *
         * Prevents access to $this/self from included files.
         *
         * @param  string $file
         * @return void
         */
        self::$includeFile = \Closure::bind(static function($file) {
            include $file;
        }, null, null);
    }

}
