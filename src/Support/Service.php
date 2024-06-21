<?php

declare (strict_types = 1);

namespace Laket\Admin\Support;

use think\Service as BaseService;

use Laket\Admin\Traits\Macroable;
use Laket\Admin\Facade\View as LaketView;

/**
 * 服务
 *
 * @create 2021-3-17
 * @author deatil
 */
class Service extends BaseService
{
    use Macroable;
    
    /**
     * 配置信息
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app->config;

        $config->set(Arr::merge(
            require $path, 
            $config->get($key, [])
        ), $key);
    }
    
    /**
     * 注册视图
     *
     * @param  string|array  $path
     * @param  string  $namespace
     * @return void
     */
    protected function loadViewsFrom($path, $namespace)
    {
        $viewFinder = app('laket-admin.view-finder');
        
        // 设置配置的视图路径
        $config = config('laket.view');
        if (isset($config['paths']) 
            && is_array($config['paths'])
        ) {
            foreach ($config['paths'] as $viewPath) {
                if (is_dir($appPath = $viewPath.'/vendor/'.$namespace)) {
                    $viewFinder->addNamespace($namespace, $appPath);
                }
            }
        }
        
        // 设置自定义路径
        $viewFinder->addNamespace($namespace, $path);
    }

    /**
     * 注册视图标签
     *
     * @param string|array $path
     * @param bool         $prepend
     * @return void
     */
    protected function registerViewTaglib($taglib, bool $prepend = false)
    {
        $taglibs = is_array($taglib) ? $taglib : func_get_args();
        
        $viewTaglib = app('laket-admin.view-taglib');
        foreach ((array) $taglibs as $taglib) {
            if ($prepend) {
                $viewTaglib->prependTaglib($taglib);
            } else {
                $viewTaglib->addTaglib($taglib);
            }
        }
        
        // 配置视图标签
        $viewTaglibs = (array) LaketView::getConfig('taglib_build_in');
        $taglibs = (array) $viewTaglib->getTaglibs();
        
        $newTaglibs = array_filter(array_merge($viewTaglibs, $taglibs));
        LaketView::config([
            'taglib_build_in' => $newTaglibs,
        ]);
    }
    
    /**
     * 注册多语言
     *
     * @param  string $path
     * @return void
     */
    protected function loadLangsFrom($path)
    {
        if (!is_dir($path) || !file_exists($path)) {
            return ;
        }
        
        $langset = app()->lang->getLangSet();
        
        $path = realpath($path);
        
        // 多语言文件
        app()->lang->load($path . '/' . $langset . '.php');
        
        // 文件夹
        $files = glob($path . '/' . $langset . '.*');
        app()->lang->load($files);
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
        app('laket-admin.publish')->publishes(static::class, $paths, $groups);
    }

}
