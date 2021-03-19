<?php

return [
    'admin' => [
        "name" => "LaketAdmin",
        "name_mini" => "Laket",
        "version" => "1.0.0",
        "release" => "1.0.0.20210318",
    ],
    
    'password' => [
        'salt' => env('laket.admin_salt', 'd,d7ja0db1a974;38cE84976abbac2cd'),
        'super_id' => env('laket.admin_super_id', 'e92ba0a3f86f4a5693d8487eb8c632b5'),
    ],
    
    'route' => [
        'group' => env('laket.route_group', 'admin'),
        'middleware' => env('laket.route_middleware', 'larke-admin'),
    ],
    
    'auth' => [
        'authenticate_excepts' => ($authenticateExceptsEnv = env('laket.auth_authenticate_excepts', '')) ? explode(',', $authenticateExceptsEnv) : [],
        'permission_excepts' => ($permissionExceptsEnv = env('laket.auth_permission_excepts', 'public')) ? explode(',', $permissionExceptsEnv) : [],
    ],
    
    'flash' => [
        'directory' => env('laket.flash_directory', 'flash'),
    ],
    
    'upload' => [
        'disk' => env('laket.upload_disk', 'public'),
    ],
    
    // 异常页面的模板文件
    'exception_tmpl'   => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'exception.tpl',
    
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',
    'dispatch_error_tmpl' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',

];
