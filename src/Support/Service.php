<?php

declare (strict_types = 1);

namespace Laket\Admin\Support;

use think\Service as BaseService;

/**
 * 服务
 *
 * @create 2021-3-17
 * @author deatil
 */
class Service extends BaseService
{
    /**
     * 注册视图命名空间
     *
     * @param  string|array  $path
     * @param  string  $namespace
     * @return void
     */
    protected function loadViewsFrom($path, $namespace)
    {
        app('laket-admin.view-finder')->addNamespace($namespace, $path);
    }
    
    /**
     * 配置信息
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app->config;

        $config->set(array_merge(
            require $path, 
            $config->get($key, [])
        ), $key);
    }
    
    /**
     * 导入文件
     *
     * @param string|array $path
     * @return void
     */
    protected function loadFilesFrom($path)
    {
        $path = is_array($path) ? $path : func_get_args();
        
        foreach ((array) $path as $file) {
            if (file_exists($file)) {
                include_once $file;
            }
        }
    }
    
    /**
     * 设置推送
     *
     * @param  array  $paths
     * @param  mixed  $groups
     * @return void
     */
    protected function publishes(array $paths, $groups = null)
    {
        app('laket-admin.publish')->publishes(__CLASS__, $paths, $groups);
    }

}
