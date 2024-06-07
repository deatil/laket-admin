<?php

return [
    'admin' => [
        "name"      => "LaketAdmin",
        "name_mini" => "Laket",
        "version"   => "1.2.5",
        "release"   => "1.2.5.20240607",
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
        'type'                 => env('laket.auth_type', 1),
        // 登陆认证过滤，格式: requestMethod:requestName
        'authenticate_excepts' => ($authenticateExceptsEnv = env('laket.auth_authenticate_excepts', '')) ? explode(',', $authenticateExceptsEnv) : [],
        // 权限认证过滤，格式: requestMethod:requestName
        'permission_excepts'   => ($permissionExceptsEnv = env('laket.auth_permission_excepts', '')) ? explode(',', $permissionExceptsEnv) : [],
        // 锁屏认证过滤，格式: requestMethod:requestName
        'screenlock_excepts'   => ($screenlockExceptsEnv = env('laket.auth_screenlock_excepts', '')) ? explode(',', $screenlockExceptsEnv) : [],
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
            app_path().'view',
        ],
        
        // 标签
        'taglib_build_in' => 
            ($envViewTaglib = env('laket.view_taglib_build_in', '')) 
            ? explode(',', $envViewTaglib) 
            : ["\\Laket\\Admin\\Template\\Taglib\\Laket"],
        
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
