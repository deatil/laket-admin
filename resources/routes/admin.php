<?php

use think\facade\Route;
use Laket\Admin\Controller;

Route::group(config('laket.route.group'), function() {
    // 登陆
    Route::get('passport/captcha', Controller\Passport::class . '@captcha')->name('admin.passport.captcha');
    Route::get('passport/login', Controller\Passport::class . '@login')->name('admin.passport.login');
    Route::post('passport/login', Controller\Passport::class . '@loginCheck')->name('admin.passport.login-check');
    Route::get('passport/logout', Controller\Passport::class . '@logout')->name('admin.passport.logout');
    Route::post('passport/lockscreen', Controller\Passport::class . '@lockscreen')->name('admin.passport.lockscreen');
    Route::post('passport/unlockscreen', Controller\Passport::class . '@unlockscreen')->name('admin.passport.unlockscreen');

    // 首页
    Route::get('index', Controller\Index::class . '@index')->name('admin.index.index');
    Route::get('main', Controller\Index::class . '@main')->name('admin.index.main');
    Route::post('clear', Controller\Index::class . '@clear')->name('admin.index.clear');
    
    // 个人信息
    Route::get('profile/setting', Controller\Profile::class . '@setting')->name('admin.profile.setting');
    Route::post('profile/setting', Controller\Profile::class . '@settingSave')->name('admin.profile.setting-save');
    Route::get('profile/password', Controller\Profile::class . '@password')->name('admin.profile.password');
    Route::post('profile/password', Controller\Profile::class . '@passwordSave')->name('admin.profile.password-save');
    
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
    Route::post('admin/add', Controller\Admin::class . '@addSave')->name('admin.admin.add-save');
    Route::get('admin/edit', Controller\Admin::class . '@edit')->name('admin.admin.edit');
    Route::post('admin/edit', Controller\Admin::class . '@editSave')->name('admin.admin.edit-save');
    Route::post('admin/delete', Controller\Admin::class . '@delete')->name('admin.admin.delete');
    Route::get('admin/view', Controller\Admin::class . '@view')->name('admin.admin.view');
    Route::get('admin/password', Controller\Admin::class . '@password')->name('admin.admin.password');
    Route::post('admin/password', Controller\Admin::class . '@passwordSave')->name('admin.admin.password-save');
    Route::get('admin/access', Controller\Admin::class . '@access')->name('admin.admin.access');
    Route::post('admin/access', Controller\Admin::class . '@accessSave')->name('admin.admin.access-save');

    // 用户组
    Route::get('auth-group/index', Controller\AuthGroup::class . '@index')->name('admin.auth-group.index');
    Route::get('auth-group/index-data', Controller\AuthGroup::class . '@indexData')->name('admin.auth-group.index-data');
    Route::get('auth-group/add', Controller\AuthGroup::class . '@add')->name('admin.auth-group.add');
    Route::post('auth-group/add', Controller\AuthGroup::class . '@addSave')->name('admin.auth-group.add-save');
    Route::get('auth-group/edit', Controller\AuthGroup::class . '@edit')->name('admin.auth-group.edit');
    Route::post('auth-group/edit', Controller\AuthGroup::class . '@editSave')->name('admin.auth-group.edit-save');
    Route::post('auth-group/delete', Controller\AuthGroup::class . '@delete')->name('admin.auth-group.delete');
    Route::get('auth-group/access', Controller\AuthGroup::class . '@access')->name('admin.auth-group.access');
    Route::post('auth-group/access', Controller\AuthGroup::class . '@accessSave')->name('admin.auth-group.access-save');
    Route::post('auth-group/listorder', Controller\AuthGroup::class . '@listorder')->name('admin.auth-group.listorder');

    // 权限菜单
    Route::get('auth-rule/index', Controller\AuthRule::class . '@index')->name('admin.auth-rule.index');
    Route::get('auth-rule/index-data', Controller\AuthRule::class . '@indexData')->name('admin.auth-rule.index-data');
    Route::get('auth-rule/all', Controller\AuthRule::class . '@all')->name('admin.auth-rule.all');
    Route::get('auth-rule/all-data', Controller\AuthRule::class . '@allData')->name('admin.auth-rule.all-data');
    Route::get('auth-rule/add', Controller\AuthRule::class . '@add')->name('admin.auth-rule.add');
    Route::post('auth-rule/add', Controller\AuthRule::class . '@addSave')->name('admin.auth-rule.add-save');
    Route::get('auth-rule/edit', Controller\AuthRule::class . '@edit')->name('admin.auth-rule.edit');
    Route::post('auth-rule/edit', Controller\AuthRule::class . '@editSave')->name('admin.auth-rule.edit-save');
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
    Route::post('flash/setting', Controller\Flash::class . '@setting')->name('admin.flash.setting-save');
    Route::post('flash/upload', Controller\Flash::class . '@upload')->name('admin.flash.upload-save');
})
->middleware(config('laket.route.middleware'));
