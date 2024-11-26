<?php

declare (strict_types = 1);

namespace Laket\Admin\Support;

use think\facade\Route as Router;

/**
 * 路由
 *
 * @create 2021-3-18
 * @author deatil
 */
class Route
{
    /**
     * 获取路由信息
     *
     * @return mixed
     */
    public function getRoutes()
    {
        $ruleList = Router::getRuleList();
        
        $ruleList = collect($ruleList)
            ->map(function ($route) {
                return $this->getRouteInformation($route);
            })
            ->all();
        
        return $ruleList;
    }
    
    protected function getRouteInformation($route)
    {
        return [
            'name'    => $route['name'],
            'rule'    => $route['rule'],
            'route'   => $this->getRouteInfo($route['route']),
            'method'  => $route['method'],
            'vars'    => $route['vars'],
            'option'  => $route['option'],
            'pattern' => $route['pattern'],
        ];
    }
    
    /**
     * 格式化路由信息
     *
     * @return string
     */
    protected function getRouteInfo($route)
    {
        return ($route instanceof \Closure) ? 'Closure' : $route;;
    }
}
