<?php

return [
    'admin' => [
        "name"      => "LaketAdmin",
        "name_mini" => "Laket",
    ],
    
    // 登录
    'passport' => [
        // 全局盐
        'salt'             => env('laket.admin_salt', 'd,d7ja0db1a974;38cE84976abbac2cd'),
        
        // 超级管理员ID
        'super_id'         => env('laket.admin_super_id', 'e92ba0a3f86f4a5693d8487eb8c632b5'),
        
        // RSA 相关
        'prikey_cache_key' => env('laket.prikey_cache_key', 'laket-admin-prikey-key'),
    ],
    
    // 路由
    'route' => [
        'group'      => env('laket.route_group', 'admin'),
        'middleware' => env('laket.route_middleware', 'laket-admin'),
    ],
    
    'auth' => [
        // 认证方式，1为实时认证；2为登录认证。
        'type'                 => 1,
        // 登录认证过滤，格式: routeName
        'authenticate_excepts' => [],
        // 权限认证过滤，格式: routeName
        'permission_excepts'   => [],
        // 锁屏认证过滤，格式: routeName
        'screenlock_excepts'   => [],
    ],
    
    // 插件
    'flash' => [
        // 插件目录
        'directory' => env('laket.flash_directory', 'flashs'),
    ],
    
    // 上传
    'upload' => [
        // 上传驱动
        'disk' => env('laket.upload_disk', 'public'),
    ],
    
    // 视图
    'view' => [
        // 视图位置
        'paths' => [
            root_path('view'),
        ],
        
        // 标签
        'taglib_build_in' => [
            "\\Laket\\Admin\\Template\\TagLib\\Laket",
        ],
        
        // 资源
        'assets' => env('laket.view_assets', "/static"),
        
        // 后台资源
        'admin_assets' => env('laket.view_admin_assets', "/static/admin"),
        
        // 后台资源路径
        'admin_assets_path' => public_path('static/admin'),
    ],

    // 视图配置
    'views' => [
        // 模板引擎类型使用 Laket
        'type'          => '\\Laket\\Admin\\View\\Laket\\Laket',
        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
        'auto_rule'     => 1,
        // 模板目录名
        'view_dir_name' => 'view',
        // 模板后缀
        'view_suffix'   => 'html',
        // 模板文件名分隔符
        'view_depr'     => DIRECTORY_SEPARATOR,
        // 模板引擎普通标签开始标记
        'tpl_begin'     => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'       => '}',
        // 标签库标签开始标记
        'taglib_begin'  => '{',
        // 标签库标签结束标记
        'taglib_end'    => '}',
    ],
    
    // 响应
    'response' => [
        'json' => [
            'is_allow_origin'   => env('laket.response_json_is_allow_origin', 1),
            'allow_origin'      => env('laket.response_json_allow_origin', '*'),
            'allow_credentials' => env('laket.response_json_allow_credentials', 0),
            'allow_methods'     => env('laket.response_json_allow_methods', 'GET,POST,PATCH,PUT,DELETE,OPTIONS'),
            'allow_headers'     => env('laket.response_json_allow_headers', 'X-Requested-With,X_Requested_With,Content-Type,Authorization'),
            'expose_headers'    => env('laket.response_json_expose_headers', ''),
            'max_age'           => env('laket.response_json_max_age', ''),
        ],
    ],
];
