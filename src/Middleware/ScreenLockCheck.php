<?php

declare (strict_types = 1);

namespace Laket\Admin\Middleware;

use Closure;
use think\App;

use Laket\Admin\Http\Traits\Jump as JumpTrait;
use Laket\Admin\Support\Screen;

/**
 * 锁屏检测
 *
 * @create 2021-3-18
 * @author deatil
 */
class ScreenLockCheck
{
    use JumpTrait;
    
    /** @var App */
    protected $app;
    
    public function __construct(App $app)
    {
        $this->app  = $app;
    }
    
    /**
     * 入口
     */
    public function handle($request, Closure $next)
    {
        $this->checkScreenLock();
        
        return $next($request);
    }
    
    /**
     * 检测锁屏
     */
    protected function checkScreenLock()
    {
        // 过滤的行为
        $allowUrl = array_merge([
            'get:admin.passport.captcha',
            'get:admin.passport.login',
            'post:admin.passport.login-check',
            'get:admin.passport.logout',
            'post:admin.passport.lockscreen',
            'post:admin.passport.unlockscreen',
            'get:admin.index.index',
            'get:admin.index.main',
        ], (array) config('laket.auth.screenlock_excepts', []));
        
        $requestMethod = request()->rule()->getMethod();
        $requestName = request()->rule()->getName();
        
        $rule = strtolower($requestMethod . ':' . $requestName);
        if (! in_array($rule, $allowUrl)) {
            $check = make(Screen::class)->check();
            if ($check !== false) {
                $url = laket_route('admin.index.index');
                $this->error('后台已锁定，请先解锁', $url);
            }
        }
    }
    
}
