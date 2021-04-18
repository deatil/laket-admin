<?php

use think\facade\Route;
use Laket\Admin\Controller;

Route::group(config('laket.route.group'), function() {
    // 登陆
    Route::get('passport/captcha', Controller\Passport::class . '@captcha')->name('admin.passport.captcha');
    Route::get('passport/login', Controller\Passport::class . '@getLogin')->name('admin.passport.login');
    Route::post('passport/login', Controller\Passport::class . '@postLogin')->name('admin.passport.login-post');
    Route::get('passport/logout', Controller\Passport::class . '@logout')->name('admin.passport.logout');
    Route::post('passport/lockscreen', Controller\Passport::class . '@lockscreen')->name('admin.passport.lockscreen');
    Route::post('passport/unlockscreen', Controller\Passport::class . '@unlockscreen')->name('admin.passport.unlockscreen');

    // 首页
    Route::get('index', Controller\Index::class . '@index')->name('admin.index.index');
    Route::get('main', Controller\Index::class . '@main')->name('admin.index.main');
    Route::post('clear', Controller\Index::class . '@clear')->name('admin.index.clear');
    
    // 个人信息
    Route::get('profile/index', Controller\Profile::class . '@getIndex')->name('admin.profile.index');
    Route::post('profile/index', Controller\Profile::class . '@postIndex')->name('admin.profile.index-post');
    Route::get('profile/password', Controller\Profile::class . '@getPassword')->name('admin.profile.password');
    Route::post('profile/password', Controller\Profile::class . '@postPassword')->name('admin.profile.password-post');
    
    // 上传
    Route::post('upload/file', Controller\Upload::class . '@file')->name('admin.upload.file');

    // 附件
    Route::get('attachment/index', Controller\Attachment::class . '@index')->name('admin.attachment.index');
    Route::get('attachment/index-data', Controller\Attachment::class . '@indexData')->name('admin.attachment.index-data');
    Route::get('attachment/view', Controller\Attachment::class . '@view')->name('admin.attachment.view');
    Route::post('attachment/delete', Controller\Attachment::class . '@delete')->name('admin.attachment.delete');

    // 管理员
    Route::get('admin/index', Controller\Admin::class . '@index')->name('admin.admin.index');
    Route::get('admin/index-data', Controller\Admin::class . '@indexData')->name('admin.admin.index-data');
    Route::get('admin/add', Controller\Admin::class . '@add')->name('admin.admin.add');
    Route::post('admin/add', Controller\Admin::class . '@add')->name('admin.admin.add-post');
    Route::get('admin/edit', Controller\Admin::class . '@edit')->name('admin.admin.edit');
    Route::post('admin/edit', Controller\Admin::class . '@edit')->name('admin.admin.edit-post');
    Route::post('admin/delete', Controller\Admin::class . '@delete')->name('admin.admin.delete');
    Route::get('admin/view', Controller\Admin::class . '@view')->name('admin.admin.view');
    Route::get('admin/password', Controller\Admin::class . '@password')->name('admin.admin.password');
    Route::post('admin/password', Controller\Admin::class . '@password')->name('admin.admin.password-post');
    Route::get('admin/access', Controller\Admin::class . '@access')->name('admin.admin.access');
    Route::post('admin/access', Controller\Admin::class . '@access')->name('admin.admin.access-post');

    // 用户组
    Route::get('auth-group/index', Controller\AuthGroup::class . '@index')->name('admin.auth-group.index');
    Route::get('auth-group/index-data', Controller\AuthGroup::class . '@indexData')->name('admin.auth-group.index-data');
    Route::get('auth-group/create', Controller\AuthGroup::class . '@create')->name('admin.auth-group.create');
    Route::post('auth-group/write', Controller\AuthGroup::class . '@write')->name('admin.auth-group.write');
    Route::get('auth-group/edit', Controller\AuthGroup::class . '@edit')->name('admin.auth-group.edit');
    Route::post('auth-group/update', Controller\AuthGroup::class . '@update')->name('admin.auth-group.update');
    Route::post('auth-group/delete', Controller\AuthGroup::class . '@delete')->name('admin.auth-group.delete');
    Route::get('auth-group/access', Controller\AuthGroup::class . '@access')->name('admin.auth-group.access');
    Route::post('auth-group/access', Controller\AuthGroup::class . '@access')->name('admin.auth-group.access-post');
    Route::post('auth-group/listorder', Controller\AuthGroup::class . '@listorder')->name('admin.auth-group.listorder');

    // 权限菜单
    Route::get('auth-rule/index', Controller\AuthRule::class . '@index')->name('admin.auth-rule.index');
    Route::get('auth-rule/index-data', Controller\AuthRule::class . '@indexData')->name('admin.auth-rule.index-data');
    Route::get('auth-rule/all', Controller\AuthRule::class . '@all')->name('admin.auth-rule.all');
    Route::get('auth-rule/all-data', Controller\AuthRule::class . '@allData')->name('admin.auth-rule.all-data');
    Route::get('auth-rule/add', Controller\AuthRule::class . '@add')->name('admin.auth-rule.add');
    Route::post('auth-rule/add', Controller\AuthRule::class . '@add')->name('admin.auth-rule.add-post');
    Route::get('auth-rule/edit', Controller\AuthRule::class . '@edit')->name('admin.auth-rule.edit');
    Route::post('auth-rule/edit', Controller\AuthRule::class . '@edit')->name('admin.auth-rule.edit-post');
    Route::post('auth-rule/delete', Controller\AuthRule::class . '@delete')->name('admin.auth-rule.delete');
    Route::post('auth-rule/listorder', Controller\AuthRule::class . '@listorder')->name('admin.auth-rule.listorder');
    Route::post('auth-rule/setmenu', Controller\AuthRule::class . '@setmenu')->name('admin.auth-rule.setmenu');
    Route::post('auth-rule/setstate', Controller\AuthRule::class . '@setstate')->name('admin.auth-rule.setstate');

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
