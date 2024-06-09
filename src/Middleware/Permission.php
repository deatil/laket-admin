<?php

declare (strict_types = 1);

namespace Laket\Admin\Middleware;

use Closure;
use think\App;

use Laket\Admin\Facade\Admin;
use Laket\Admin\Http\Traits\Jump as JumpTrait;

/**
 * 权限
 *
 * @create 2021-3-18
 * @author deatil
 */
class Permission
{
    use JumpTrait;
    
    /** 
     * @var App 
     */
    protected $app;
    
    public function __construct(App $app)
    {
        $this->app = $app;
    }
    
    /**
     * @var
     */
    public function handle($request, Closure $next)
    {
        if (! $this->checkPermission($request)) {
            return $this->error('未授权访问!');
        }
        
        return $next($request);
    }
    
    /**
     * 检测权限
     */
    protected function checkPermission($request)
    {
        $excepts = array_merge([
            'admin.passport.captcha',
            'admin.passport.login',
            'admin.passport.login-check',
            'admin.passport.logout',
        ], (array) config('laket.auth.permission_excepts', []));
        
        $rule = $request->rule()->getName();
        if (in_array($rule, $excepts)) {
            return true;
        }
        
        // 超级管理员
        $isSuperAdmin = Admin::isSuperAdmin();
        if ($isSuperAdmin) {
            return true;
        }
        
        // 检测访问权限
        if (Admin::checkPermission($rule)) {
            return true;
        }
        
        return false;
    }
}
