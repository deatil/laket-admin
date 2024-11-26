## 插件结构目录

### 结构目录

~~~
flashs 目录
├─ author                   作者目录
│  ├─ package               插件名称
│  │  ├─ resources          资源目录
│  │  ├─ src                代码目录
│  │  │  ├─ Service.php     插件服务，根据自定义加载设置放置
│  │  ├─ composer.json      composer加载文件
│  │  ├─ icon.png           插件icon文件
│  │  ├─ LICENSE            许可协议
│  │  ├─ README.md          插件说明文件
│  │  └─ ...                其他
│  │
│  └─ ...                   同一作者其他插件
│
└─ ...                      其他插件
~~~

### composer

~~~json
{
    "name": "laket/laket-settings", # 插件包名，必填
    "description": "The settings is a laket-admin'extension.", # 插件描述，必填
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
            "Laket\\Admin\\Settings\\": "src" # 插件自动加载配置，必填
        }
    },
    "laket" : {
        "title": "系统设置",   # 插件名称，必填
        "version": "1.0.1",    # 插件版本号，必填
        "adaptation": "1.0.*", # 插件适配系统版本号，必填
        "require": {           # 依赖插件，选填
            "laket/laket-settings":"1.2.*",
            "laket/laket-operation-log":"1.3.*"
        },
        "sort": 200 # 排序，选填
    },
    "extra": {
        "think":{
            "services":[
                "Laket\\Admin\\Settings\\Service" # 插件服务，必填
            ]
        }
    }
}
~~~


### 插件服务

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
     * 引导，必填
     */
    public function boot()
    {
        // 插件注册，必须设置
        Flash::extend('laket/laket-settings'/*插件包名*/, __CLASS__/*当前插件服务类名*/);
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


### 插件服务默认提供的一些方法

* `loadRoutesFrom($path)` 加载路由

* `registerRoutes(Closure $closure)` 注册路由

* `commands($commands)` 添加指令

* `loadViewsFrom($path, $namespace)` 加载视图

* `mergeConfigFrom($path, $key)` 加载配置文件

* `loadFilesFrom($path)` 导入文件

* `publishes(array $paths, $groups = null)` 添加脚本推送


### 插件设置支持的类型

`hidden`, `password`, `text`, `number`, `switch`, 
`array`, `checkbox`, `radio`, `select`, `color`, 
`date`, `datetime`, `textarea`, `image`, `images`, 
`tags`, `file`, `files`


### 其他文件

*  其他文件可根据需要自行加载
