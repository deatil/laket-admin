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
        $this->app  = $app;
    }
    
    /**
     * @var
     */
    public function handle($request, Closure $next)
    {
        $this->checkPermission($request);
        
        return $next($request);
    }
    
    /**
     * 检测权限
     */
    protected function checkPermission($request)
    {
        $excepts = array_merge([
            'get:admin.passport.captcha',
            'get:admin.passport.login',
            'post:admin.passport.login-post',
        ], (array) config('larket.auth.permission_excepts', []));
        
        $requestMethod = $request->rule()->getMethod();
        $requestName = $request->rule()->getName();
        
        $rule = strtolower($requestMethod . ':' . $requestName);
        if (in_array($rule, $excepts)) {
            return ;
        }
        
        // 超级管理员
        $isSuperAdmin = Admin::isSuperAdmin();
        if ($isSuperAdmin) {
            return ;
        }
        
        // 检测访问权限
        if (Admin::checkPermission($rule)) {
            return ;
        }
        
        $this->error('未授权访问!');
    }
}
