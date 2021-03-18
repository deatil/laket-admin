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
class AuthCheck
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
     * 行为扩展的执行入口必须是run
     */
    public function handle($request, Closure $next)
    {
        // 登陆检测
        $this->checkAdminLogin();
        
        $response = $next($request);
        
        // 地址检测
        $this->checkAdminRuleAuth();
        
        return $response;
    }
    
    /**
     * 检测登陆权限
     */
    protected function checkAdminLogin()
    {
        // 重复检测跳过
        if ($this->app->env->get('admin_id')) {
            return;
        }
        
        // 检测登陆
        $this->competence();
    }
    
    /**
     * 检测权限
     */
    protected function checkAdminRuleAuth()
    {
        // 过滤不需要登陆的行为
        $allowUrl = [
            'get:admin.passport.captcha',
            'get:admin.passport.login',
            'post:admin.passport.login-save',
            'delete:admin.passport.logout',
        ];
        
        $requestMethod = request()->rule()->getMethod();
        $requestName = request()->rule()->getName();
        
        $rule = strtolower(
            $requestMethod . 
            ':' . $requestName
        );
        
        if (! in_array($rule, $allowUrl)) {
            $adminId = $this->app->env->get('admin_id');
            if (empty($adminId)) {
                // 跳转到登录界面
                $this->error('请先登陆', $this->loginUrl);
            }
            
            // 是否是超级管理员
            $adminIsRoot = $this->app->env->get('admin_is_root');

            // 超级管理员跳过
            if ($adminIsRoot) {
                return;
            }
            
            // 检测访问权限
            if (! $this->checkRule($rule)) {
                $this->error('未授权访问!');
            }
        }
        
    }
    
    /**
     * 验证登录
     * @return boolean
     */
    private function competence()
    {
        // 检查是否登录
        $check = Admin::check();
        if (empty($check)) {
            return false;
        }
        
        // 获取当前登录用户信息
        $adminInfo = Admin::getData();
        if (empty($adminInfo)) {
            Admin::logout();
            return false;
        }
        
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
        
        return $adminInfo;
    }
    
    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     */
    final private function checkPermission($rule)
    {
        if (!Admin::checkPermission($rule)) {
            return false;
        }
        return true;
    }
}
