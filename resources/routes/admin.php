<?php

use think\facade\Route;
use Laket\Admin\Controller;

Route::group(config('laket.route.group'), function() {
    // 登陆
    Route::get('passport/captcha', Controller\Passport::class . '@getCaptcha')->name('admin.passport.captcha');
    Route::get('passport/login', Controller\Passport::class . '@getLogin')->name('admin.passport.login');
    Route::post('passport/login', Controller\Passport::class . '@postLogin')->name('admin.passport.login-post');
    Route::get('passport/logout', Controller\Passport::class . '@getLogout')->name('admin.passport.logout');
    Route::post('passport/lockscreen', Controller\Passport::class . '@postLockscreen')->name('admin.passport.lockscreen');
    Route::post('passport/unlockscreen', Controller\Passport::class . '@postUnlockscreen')->name('admin.passport.unlockscreen');

    // 首页
    Route::get('index', Controller\Index::class . '@getIndex')->name('admin.index.index');
    Route::get('main', Controller\Index::class . '@getMain')->name('admin.index.main');
    Route::post('clear', Controller\Index::class . '@postClear')->name('admin.index.clear');
    
    // 个人信息
    Route::get('profile/setting', Controller\Profile::class . '@getSetting')->name('admin.profile.setting');
    Route::post('profile/setting', Controller\Profile::class . '@postSetting')->name('admin.profile.setting-post');
    Route::get('profile/password', Controller\Profile::class . '@getPassword')->name('admin.profile.password');
    Route::post('profile/password', Controller\Profile::class . '@postPassword')->name('admin.profile.password-post');
    
    // 上传
    Route::post('upload/file', Controller\Upload::class . '@file')->name('admin.upload.file');

    // 附件
    Route::get('attachment/index', Controller\Attachment::class . '@getIndex')->name('admin.attachment.index');
    Route::get('attachment/index-data', Controller\Attachment::class . '@getIndexData')->name('admin.attachment.index-data');
    Route::get('attachment/view', Controller\Attachment::class . '@getView')->name('admin.attachment.view');
    Route::post('attachment/delete', Controller\Attachment::class . '@postDelete')->name('admin.attachment.delete');

    // 管理员
    Route::get('admin/index', Controller\Admin::class . '@getIndex')->name('admin.admin.index');
    Route::get('admin/index-data', Controller\Admin::class . '@getIndexData')->name('admin.admin.index-data');
    Route::get('admin/add', Controller\Admin::class . '@getAdd')->name('admin.admin.add');
    Route::post('admin/add', Controller\Admin::class . '@postAdd')->name('admin.admin.add-post');
    Route::get('admin/edit', Controller\Admin::class . '@getEdit')->name('admin.admin.edit');
    Route::post('admin/edit', Controller\Admin::class . '@postEdit')->name('admin.admin.edit-post');
    Route::post('admin/delete', Controller\Admin::class . '@postDelete')->name('admin.admin.delete');
    Route::get('admin/view', Controller\Admin::class . '@getView')->name('admin.admin.view');
    Route::get('admin/password', Controller\Admin::class . '@getPassword')->name('admin.admin.password');
    Route::post('admin/password', Controller\Admin::class . '@postPassword')->name('admin.admin.password-post');
    Route::get('admin/access', Controller\Admin::class . '@getAccess')->name('admin.admin.access');
    Route::post('admin/access', Controller\Admin::class . '@postAccess')->name('admin.admin.access-post');

    // 用户组
    Route::get('auth-group/index', Controller\AuthGroup::class . '@getIndex')->name('admin.auth-group.index');
    Route::get('auth-group/index-data', Controller\AuthGroup::class . '@getIndexData')->name('admin.auth-group.index-data');
    Route::get('auth-group/add', Controller\AuthGroup::class . '@getAdd')->name('admin.auth-group.add');
    Route::post('auth-group/add', Controller\AuthGroup::class . '@postAdd')->name('admin.auth-group.add-post');
    Route::get('auth-group/edit', Controller\AuthGroup::class . '@getEdit')->name('admin.auth-group.edit');
    Route::post('auth-group/edit', Controller\AuthGroup::class . '@postEdit')->name('admin.auth-group.edit-post');
    Route::post('auth-group/delete', Controller\AuthGroup::class . '@postDelete')->name('admin.auth-group.delete');
    Route::get('auth-group/access', Controller\AuthGroup::class . '@getAccess')->name('admin.auth-group.access');
    Route::post('auth-group/access', Controller\AuthGroup::class . '@postAccess')->name('admin.auth-group.access-post');
    Route::post('auth-group/listorder', Controller\AuthGroup::class . '@postListorder')->name('admin.auth-group.listorder');

    // 权限菜单
    Route::get('auth-rule/index', Controller\AuthRule::class . '@getIndex')->name('admin.auth-rule.index');
    Route::get('auth-rule/index-data', Controller\AuthRule::class . '@getIndexData')->name('admin.auth-rule.index-data');
    Route::get('auth-rule/all', Controller\AuthRule::class . '@getAll')->name('admin.auth-rule.all');
    Route::get('auth-rule/all-data', Controller\AuthRule::class . '@getAllData')->name('admin.auth-rule.all-data');
    Route::get('auth-rule/add', Controller\AuthRule::class . '@getAdd')->name('admin.auth-rule.add');
    Route::post('auth-rule/add', Controller\AuthRule::class . '@postAdd')->name('admin.auth-rule.add-post');
    Route::get('auth-rule/edit', Controller\AuthRule::class . '@getEdit')->name('admin.auth-rule.edit');
    Route::post('auth-rule/edit', Controller\AuthRule::class . '@postEdit')->name('admin.auth-rule.edit-post');
    Route::post('auth-rule/delete', Controller\AuthRule::class . '@postDelete')->name('admin.auth-rule.delete');
    Route::post('auth-rule/listorder', Controller\AuthRule::class . '@postListorder')->name('admin.auth-rule.listorder');
    Route::post('auth-rule/setmenu', Controller\AuthRule::class . '@postSetmenu')->name('admin.auth-rule.setmenu');
    Route::post('auth-rule/setstate', Controller\AuthRule::class . '@postSetstate')->name('admin.auth-rule.setstate');

    // 闪存
    Route::get('flash/index', Controller\Flash::class . '@index')->name('admin.flash.index');
    Route::get('flash/index-data', Controller\Flash::class . '@indexData')->name('admin.flash.index-data');
    Route::get('flash/local', Controller\Flash::class . '@local')->name('admin.flash.local');
    Route::post('flash/refresh', Controller\Flash::class . '@refreshLocal')->name('admin.flash.refresh');
    Route::post('flash/install', Controller\Flash::class . '@install')->name('admin.flash.install');
    Route::post('flash/uninstall', Controller\Flash::class . '@uninstall')->name('admin.flash.uninstall');
    Route::post('flash/upgrade', Controller\Flash::class . '@upgrade')->name('admin.flash.upgrade');
    Route::get('flash/view', Controller\Flash::class . '@view')->name('admin.flash.view');
    Route::post('flash/enable', Controller\Flash::class . '@enable')->name('admin.flash.enable');
    Route::post('flash/disable', Controller\Flash::class . '@disable')->name('admin.flash.disable');
    Route::post('flash/listorder', Controller\Flash::class . '@listorder')->name('admin.flash.listorder');
    Route::get('flash/setting', Controller\Flash::class . '@setting')->name('admin.flash.setting');
    Route::post('flash/setting', Controller\Flash::class . '@setting')->name('admin.flash.setting-post');
    Route::post('flash/upload', Controller\Flash::class . '@upload')->name('admin.flash.upload-post');
})
->middleware(config('laket.route.middleware'));
