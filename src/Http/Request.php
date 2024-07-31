<?php

declare (strict_types = 1);

namespace Laket\Admin\Http;

use think\helper\Str;

/*
 * 扩展请求相关
 *
 * @create 2021-3-29
 * @author deatil
 */
class Request
{
    /**
     * 匹配当前请求路径
     *
     * eg:
     * get,post:admin.index.index
     *
     * @param string $path
     * @param null|string $current
     * @return bool
     */
    public static function matchPath($path, ?string $current = null)
    {
        $request = request();
        $current = $current ?: $request->baseUrl();

        if (Str::contains($path, ':')) {
            [$methods, $path] = explode(':', $path);

            $methods = array_map('strtoupper', explode(',', $methods));

            if (! empty($methods) 
                && ! in_array($request->method(), $methods)
            ) {
                return false;
            }
        }

        // 判断路由名称
        if (static::routeIs($path)) {
            return true;
        }

        if (! Str::contains($path, '*')) {
            return $path === $current;
        }

        $path = str_replace(['*', '/'], ['([0-9a-z-_,])*', "\/"], $path);

        return preg_match("/$path/i", $current);
    }
    
    /**
     * 判断路由名称是否存在
     * 
     * @param string $name 路由标识
     * @return bool
     */
    public static function routeIs(string $name)
    {
        // 当前请求路由标识
        $requestName = request()->rule()->getName();
        
        // 判断路由名称
        if ($requestName == $name) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 获取路由标识设置的路由
     * 
     * @param string $name   路由标识
     * @param string $domain 域名
     * @param string $method 请求类型
     * @return bool
     */
    public static function getNamesFromRoute(string $name, string $domain = null, string $method = '*')
    {
        $name = request()->rule()->getRouter()->getName($name, $domain, $method);
        
        return $name;
    }

}
