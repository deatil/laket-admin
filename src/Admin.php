<?php

declare (strict_types = 1);

namespace Laket\Admin;

use Laket\Admin\Flash\Manager;
use Laket\Admin\Auth\Admin as AuthAdmin;

/**
 * Admin
 *
 * @create 2024-7-31
 * @author deatil
 */
class Admin
{
    /**
     * 版本号
     */
    const VERSION = "1.5.11";
    
    /**
     * 发布号
     */
    const RELEASE = "20240806";
    
    /**
     * 插件
     *
     * @return string
     */
    public static function flash(): Manager
    {
        return app("laket-admin.flash");
    }
    
    /**
     * 登录信息
     *
     * @return string
     */
    public static function authAdmin(): AuthAdmin
    {
        return app("laket-admin.auth-admin");
    }

}