<?php

use think\facade\Route;

use Laket\Admin\Controller;
use Laket\Admin\Middleware\AuthCheck as AuthCheckMiddleware;
use Laket\Admin\Middleware\ScreenLockCheck as ScreenLockCheckMiddleware;

Route::group('admin', function() {
    // 登陆部分
    Route::get('passport/captcha', Controller\Passport::class . '@captcha')->name('admin.passport.captcha');
    Route::get('passport/login', Controller\Passport::class . '@getLogin')->name('admin.passport.login');
    Route::post('passport/login', Controller\Passport::class . '@postLogin')->name('admin.passport.login-save');
    Route::get('passport/logout', Controller\Passport::class . '@logout')->name('admin.passport.logout');
    Route::post('passport/lockscreen', Controller\Passport::class . '@lockscreen')->name('admin.passport.lockscreen');
    Route::post('passport/unlockscreen', Controller\Passport::class . '@postLogin')->name('admin.passport.unlockscreen');

    // 首页
    Route::get('index', Controller\Index::class . '@index')->name('admin.index.index');
    Route::get('main', Controller\Index::class . '@main')->name('admin.index.main');
    Route::post('clear', Controller\Index::class . '@clear')->name('admin.index.clear');

    // 个人信息
    Route::get('profile/index', Controller\Profile::class . '@getIndex')->name('admin.profile.index');
    Route::post('profile/index', Controller\Profile::class . '@postIndex')->name('admin.profile.index-post');
    Route::get('profile/password', Controller\Profile::class . '@getPassword')->name('admin.profile.password');
    Route::post('profile/password', Controller\Profile::class . '@postPassword')->name('admin.profile.password-post');
})->middleware([
    AuthCheckMiddleware::class,
    ScreenLockCheckMiddleware::class,
]);
