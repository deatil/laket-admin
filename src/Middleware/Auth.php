<?php

declare (strict_types = 1);

namespace Laket\Admin\Middleware;

use Closure;
use think\App;

use Laket\Admin\Facade\Admin;
use Laket\Admin\Http\Traits\Jump as JumpTrait;
use Laket\Admin\Model\AuthRule as AuthRuleModel;

/**
 * 登陆检测
 *
 * @create 2021-3-18
 * @author deatil
 */
class Auth
{
    use JumpTrait;
    
    /** @var App */
    protected $app;
    
    protected $loginUrl = '';
    
    public function __construct(App $app)
    {
        $this->app  = $app;
        $this->loginUrl = laket_route("admin.passport.login");
    }
    
    /**
     * @var
     */
    public function handle($request, Closure $next)
    {
        // 登陆检测
        $this->checkLogin($request);
        
        // 地址检测
        $this->checkAuth($request);
        
        return $next($request);
    }
    
    /**
     * 登陆检测
     *
     * @return boolean
     */
    protected function checkLogin($request)
    {
        // 检查是否登录
        $check = Admin::check();
        if (empty($check)) {
            return false;
        }
        
        // 获取当前登录用户信息
        $adminInfo = Admin::getData();
        
        // 是否锁定
        if (! $adminInfo['status']) {
            Admin::logout();
            $this->error('您的帐号已经被锁定！', $this->loginUrl);
            return false;
        }
        
        // 是否是超级管理员
        $adminIsRoot = Admin::isSuperAdmin();
        
        // 设置环境变量
        $this->app->env->set('admin_id', $adminInfo['id']);
        $this->app->env->set('admin_is_root', $adminIsRoot);
        $this->app->env->set('admin_info', $adminInfo);
        
        return true;
    }
    
    /**
     * 检测权限
     */
    protected function checkAuth($request)
    {
        // 过滤不需要登陆的行为
        $excepts = array_merge([
            'get:admin.passport.captcha',
            'get:admin.passport.login',
            'post:admin.passport.login-check',
        ], (array) config('laket.auth.authenticate_excepts', []));
        
        $requestMethod = $request->rule()->getMethod();
        $requestName = $request->rule()->getName();
        
        $rule = strtolower($requestMethod . ':' . $requestName);
        if (in_array($rule, $excepts)) {
            return;
        }
        
        $adminId = $this->app->env->get('admin_id');
        if (! empty($adminId)) {
            return;
        }
        
        $this->error('请先登陆', $this->loginUrl);
    }
}
