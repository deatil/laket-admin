<?php

declare (strict_types = 1);

namespace Laket\Admin\Event;

use think\App;

/**
 * 事件管理类
 */
abstract class Event
{
    /**
     * 监听者
     * @var array
     */
    protected $listener = [];

    /**
     * 应用对象
     * @var App
     */
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 注册事件监听
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @param bool   $sort     排序
     * @return $this
     */
    public function listen(string $event, $listener, int $sort = 1)
    {
        $this->listener[$event][] = [
            'listener' => $listener,
            'sort'     => $sort,
            'key'      => $this->filterBuildUniqueId($listener),
        ];

        return $this;
    }
    
    /**
     * 移除监听事件
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @param bool   $sort     排序
     * @return bool
     */
    public function removeListener(string $event, $listener, int $sort = 1): bool
    {
        $key = $this->filterBuildUniqueId($listener);

        $exists = isset($this->listener[$event]);
        if ($exists) {
            foreach ($this->listener[$event] as $k => $v) {
                if ($v['key'] == $key && $v['sort'] == $sort) {
                    unset($this->listener[$event][$k]);
                }
            }
        }

        return $exists;
    }
    
    /**
     * 事件是否在监听
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @return bool
     */
    public function hasListener(string $event, $listener = false): bool
    {
        if (false === $listener) {
            return $this->hasListeners();
        }

        $key = $this->filterBuildUniqueId($listener);

        if (! $key) {
            return false;
        }
        
        if (! isset($this->listener[$event])) {
            return false;
        }

        foreach ($this->listener[$event] as $listen) {
            if ($listen['key'] == $key) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * 是否有事件监听
     * 
     * @return bool
     */
    public function hasListeners(): bool 
    {
        foreach ($this->listener as $listener) {
            if ($listener) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * 获取所有事件监听
     * 
     * @return array
     */
    public function getListeners()
    {
        return $this->listener;
    }
    
    /**
     * 是否存在事件监听点
     * 
     * @param string $event 事件名称
     * @return bool
     */
    public function exists(string $event): bool
    {
        return isset($this->listener[$event]);
    }

    /**
     * 移除事件监听点
     * 
     * @param string $event 事件名称
     * @return void
     */
    public function remove(string $event): void
    {
        unset($this->listener[$event]);
    }

    /**
     * 清空
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->listener = [];
    }
    
    /**
     * 执行事件调度
     * 
     * @param mixed $event  事件方法
     * @param mixed $params 参数
     * @return mixed
     */
    protected function dispatch($event, array $params = [])
    {
        if (!is_string($event)) {
            $call = $event;
        } elseif (str_contains($event, '::')) {
            $call = $event;
        } elseif (function_exists($event)) {
            $call = $event;
        } else {
            $obj  = $this->app->make($event);
            $call = [$obj, 'handle'];
        }

        return $this->app->invoke($call, $params);
    }
    
    /**
     * 排序
     */
    protected function arraySort(array $arr, string $key, string $type = 'desc')
    {
        $keyValue = [];
        foreach ($arr as $k => $v) {
            $keyValue[$k] = $v[$key];
        }
        
        if (strtolower($type) == 'asc') {
            asort($keyValue);
        } else {
            arsort($keyValue);
        }
        
        reset($keyValue);
        
        $newArray = [];
        foreach ($keyValue as $k => $v) {
            $newArray[$k] = $arr[$k];
        }
        
        return $newArray;
    }
    
    /**
     * 生成唯一值
     */
    protected function filterBuildUniqueId($callback) 
    {
        if (is_string($callback)) {
            return $callback;
        }

        if (is_object($callback)) {
            $callback = array($callback, '');
        } else {
            $callback = (array) $callback;
        }

        if (is_object($callback[0])) {
            return spl_object_hash($callback[0]) . $callback[1];
        } elseif (is_string($callback[0])) {
            return $callback[0] . '::' . $callback[1];
        }
    }
}
