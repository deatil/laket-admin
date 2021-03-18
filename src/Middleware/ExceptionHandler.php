<?php

declare (strict_types = 1);

namespace Laket\Admin\Middleware;

use Closure;

use think\App;
use think\helper\Str;
use think\exception\Handle;

use Laket\Admin\Exception\Handler;

/**
 * 异常设置
 *
 * @create 2021-3-18
 * @author deatil
 */
class ExceptionHandler
{
    /** @var App */
    protected $app;
    
    protected $loginUrl = '';
    
    public function __construct(App $app)
    {
        $this->app  = $app;
    }
    
    /**
     * 行为扩展的执行入口必须是run
     */
    public function handle($request, Closure $next)
    {
        $pathinfo = $request->pathinfo();
        if (Str::startsWith($pathinfo, 'admin/')) {
            $this->app->bind(Handle::class, Handler::class);
        }
        
        return $next($request);
    }
}
