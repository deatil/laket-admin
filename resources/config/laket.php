<?php

return [
    'admin' => [
        "name"      => "LaketAdmin",
        "name_mini" => "Laket",
        "version"   => "1.2.11",
        "release"   => "1.2.11.20240609",
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
    
    'route' => [
        'group'      => env('laket.route_group', 'admin'),
        'middleware' => env('laket.route_middleware', 'laket-admin'),
    ],
    
    'auth' => [
        // 认证方式，1为实时认证；2为登录认证。
        'type'                 => 1,
        // 登录认证过滤，格式: requestMethod:routeName
        'authenticate_excepts' => [],
        // 权限认证过滤，格式: requestMethod:routeName
        'permission_excepts'   => [],
        // 锁屏认证过滤，格式: requestMethod:routeName
        'screenlock_excepts'   => [],
    ],
    
    'flash' => [
        'directory' => env('laket.flash_directory', 'flashs'),
    ],
    
    'upload' => [
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
            "\\Laket\\Admin\\Template\\Taglib\\Laket",
        ],
        
        // 资源
        'assets' => env('laket.view_assets', "/static"),
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
