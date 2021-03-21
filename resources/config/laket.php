<?php

return [
    'admin' => [
        "name" => "LaketAdmin",
        "name_mini" => "Laket",
        "version" => "1.0.0",
        "release" => "1.0.0.20210321",
    ],
    
    'password' => [
        'salt' => env('laket.admin_salt', 'd,d7ja0db1a974;38cE84976abbac2cd'),
        'super_id' => env('laket.admin_super_id', 'e92ba0a3f86f4a5693d8487eb8c632b5'),
    ],
    
    'route' => [
        'group' => env('laket.route_group', 'admin'),
        'middleware' => env('laket.route_middleware', 'laket-admin'),
    ],
    
    'auth' => [
        // 认证方式，1为实时认证；2为登录认证。
        'type' => env('laket.auth_type', 1),
        'authenticate_excepts' => ($authenticateExceptsEnv = env('laket.auth_authenticate_excepts', '')) ? explode(',', $authenticateExceptsEnv) : [],
        'permission_excepts' => ($permissionExceptsEnv = env('laket.auth_permission_excepts', 'public')) ? explode(',', $permissionExceptsEnv) : [],
    ],
    
    'flash' => [
        'directory' => env('laket.flash_directory', 'flashs'),
    ],
    
    'upload' => [
        'disk' => env('laket.upload_disk', 'public'),
    ],
    
    'response' => [
        'json' => [
            'is_allow_origin' => env('laket.response_json_is_allow_origin', 1),
            'allow_origin' => env('laket.response_json_allow_origin', '*'),
            'allow_credentials' => env('laket.response_json_allow_credentials', 0),
            'allow_methods' => env('laket.response_json_allow_methods', 'GET,POST,PATCH,PUT,DELETE,OPTIONS'),
            'allow_headers' => env('laket.response_json_allow_headers', 'X-Requested-With,X_Requested_With,Content-Type,Authorization'),
            'expose_headers' => env('laket.response_json_expose_headers', 'Larke-Admin-Captcha-Id'),
            'max_age' => env('laket.response_json_max_age', ''),
        ],
    ],
];
