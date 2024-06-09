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
        $this->app = $app;
    }
    
    /**
     * 入口
     */
    public function handle($request, Closure $next)
    {
        if (! $this->checkScreenLock()) {
            return $this->error('后台已锁定，请先解锁', laket_route('admin.index.index'));
        }
        
        return $next($request);
    }
    
    /**
     * 检测锁屏
     */
    protected function checkScreenLock()
    {
        // 过滤的行为
        $excepts = array_merge([
            'admin.passport.captcha',
            'admin.passport.login',
            'admin.passport.login-check',
            'admin.passport.logout',
            'admin.passport.lockscreen',
            'admin.passport.unlockscreen',
            'admin.index.index',
            'admin.index.main',
        ], (array) config('laket.auth.screenlock_excepts', []));
        
        $requestName = request()->rule()->getName();
        if (! in_array($requestName, $excepts)) {
            $check = make(Screen::class)->check();
            if ($check !== false) {
                return false;
            }
        }
        
        return true;
    }
    
}
