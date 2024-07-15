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
    
    public function __construct(App $app)
    {
        $this->app  = $app;
    }
    
    /**
     * @var
     */
    public function handle($request, Closure $next)
    {
        $pathinfo = $request->pathinfo();
        $routeGroup = config('laket.route.group');
        if (Str::startsWith($pathinfo, $routeGroup.'/')) {
            $handler = apply_filters('admin_middleware_exception_handler', Handler::class);
            
            $this->app->bind(Handle::class, $handler);
        }
        
        return $next($request);
    }
}
