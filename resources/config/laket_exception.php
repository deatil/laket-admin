<?php

return [
    // 异常页面的模板文件
    'exception_tmpl'   => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'exception.tpl',
    
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',
    'dispatch_error_tmpl' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',
];
