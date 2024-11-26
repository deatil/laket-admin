<?php

declare (strict_types = 1);

namespace Laket\Admin\Middleware;

use Closure;
use think\App;

use Laket\Admin\Facade\Admin;
use Laket\Admin\Http\Traits\Jump as JumpTrait;
use Laket\Admin\Model\AuthRule as AuthRuleModel;

/**
 * 登录检测
 *
 * @create 2021-3-18
 * @author deatil
 */
class Auth
{
    use JumpTrait;
    
    /** @var App */
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
        $loginUrl = laket_route("admin.passport.login");

        // 地址检测
        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }
        
        // 登录检测
        if (! Admin::check()) {
            return $this->error('请先登录！', $loginUrl);
        }

        // 检测账号状态
        if (! $this->checkStatus()) {
            return $this->error('帐号不存在或者已被锁定！', $loginUrl);
        }

        return $next($request);
    }
    
    /**
     * 检测账号状态
     *
     * @return boolean
     */
    protected function checkStatus()
    {
        // 获取当前登录用户信息
        $adminInfo = Admin::getData();
        
        // 是否锁定
        if ($adminInfo['status'] != 1) {
            Admin::logout();
            return false;
        }
        
        return true;
    }
    
    /**
     * 检测权限
     */
    protected function shouldPassThrough($request)
    {
        // 过滤不需要登录的行为
        $excepts = array_merge([
            'admin.passport.captcha',
            'admin.passport.login',
            'admin.passport.login-check',
        ], (array) config('laket.auth.authenticate_excepts', []));
        
        $requestName = $request->rule()->getName();
        if (in_array($requestName, $excepts)) {
            return true;
        }
        
        return false;
    }
}
