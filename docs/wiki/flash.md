## flash闪存插件结构目录

### composer

~~~json
{
    "name": "laket/laket-settings", # 闪存插件包名，必填
    "description": "The settings is a laket-admin'extension.", # 闪存插件描述，必填
    "keywords": [
        "laket",
        "settings",
        "admin",
        "laket-admin"
    ],
    "homepage": "https://github.com/deatil/laket-settings",
    "type": "library",
    "license": "Apache-2.0",
    "authors": [
        {
            "name": "deatil",
            "email": "deatil@github.com",
            "homepage": "https://github.com/deatil",
            "role": "Developer"
        }
    ],
    "require": {
        "php": "^7.3",
        "laket/laket-admin": "1.0.*"
    },
    "autoload": {
        "psr-4": {
            "Laket\\Admin\\Settings\\": "src" # 闪存插件自动加载配置，必填
        }
    },
    "laket" : {
        "title": "系统设置", # 闪存插件名称，必填
        "version": "1.0.1", # 闪存插件版本号，必填
        "adaptation": "1.0.*" # 闪存插件适配系统版本号，必填
    },
    "extra": {
        "think":{
            "services":[
                "Laket\\Admin\\Settings\\Service" # 闪存插件服务，必填
            ]
        }
    }
}
~~~


### 闪存插件服务

~~~php
<?php

declare (strict_types = 1);

namespace Laket\Admin\Settings;

use Laket\Admin\Flash\Menu;
use Laket\Admin\Facade\Flash;
use Laket\Admin\Flash\Service as BaseService;

class Service extends BaseService
{
    /**
     * composer文件地址，必填
     */
    public $composer = __DIR__ . '/../composer.json';
    
    /**
     * 图标，选填。没有填写默认加载根目录 `icon.png` 文件
     */
    public $icon = '';
    
    /**
     * 设置，设置文件或者数组，选填。没有填写默认加载根目录 `setting.php` 文件
     */
    public $setting = '';
    
    protected $slug = 'laket-admin.flash.settings';
    
    /**
     * 引导，选填
     */
    public function boot()
    {
        Flash::extend('laket/laket-settings', __CLASS__);
    }
    
    /**
     * 开始，只有启用后加载，选填
     */
    public function start()
    {
        // 配置
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/field_type.php', 'field_type');
        
        // 路由
        $this->loadRoutesFrom(__DIR__ . '/../resources/routes/admin.php');
        
        // 视图
        $this->loadViewsFrom(__DIR__ . '/../resources/view', 'laket-settings');
        
        // 引入函数
        $this->loadFilesFrom(__DIR__ . "/helper.php");
    }
    
    /**
     * 安装后，选填
     */
    public function install()
    {
        $menus = include __DIR__ . '/../resources/menus/menus.php';
        
        // 添加菜单
        Menu::create($menus);
        
        // 数据库
        Flash::executeSql(__DIR__ . '/../resources/database/install.sql');
    }
    
    /**
     * 卸载后，选填
     */
    public function uninstall()
    {
        Menu::delete($this->slug);
        
        // 数据库
        Flash::executeSql(__DIR__ . '/../resources/database/uninstall.sql');
    }
    
    /**
     * 更新后，选填
     */
    public function upgrade()
    {}
    
    /**
     * 启用后，选填
     */
    public function enable()
    {
        Menu::enable($this->slug);
    }
    
    /**
     * 禁用后，选填
     */
    public function disable()
    {
        Menu::disable($this->slug);
    }
    
}
~~~


### 闪存插件服务默认提供的一些方法

* `loadRoutesFrom($path)` 加载路由

* `registerRoutes(Closure $closure)` 添加指令

* `commands($commands)` 注册路由

* `loadViewsFrom($path, $namespace)` 加载视图

* `mergeConfigFrom($path, $key)` 加载配置文件

* `loadFilesFrom($path)` 导入文件

* `publishes(array $paths, $groups = null)` 添加脚本推送文件


### 其他文件

*  其他文件可根据需要自行加载
