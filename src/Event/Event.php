<?php

declare (strict_types = 1);

namespace Laket\Admin\Event;

use think\App;

/**
 * 事件管理类
 * @package think
 */
class Event
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
     * 注册动作
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @param bool   $sort     排序
     * @return $this
     */
    public function addAction(string $event, $listener, int $sort = 1)
    {
        $this->listener[$event][] = [
            'listener' => $listener,
            'sort'     => $sort,
            'key'      => $this->filterBuildUniqueId($listener),
        ];

        return $this;
    }

    /**
     * 触发动作
     * 
     * @param string|object $event 事件名称
     * @param mixed         $var   更多参数
     * @return void
     */
    public function doAction($event, ...$var): void
    {
        if (is_object($event)) {
            $event = $event::class;
        }

        $result    = [];
        $listeners = $this->listener[$event] ?? [];

        if (str_contains($event, '.')) {
            [$prefix, $event] = explode('.', $event, 2);
            if (isset($this->listener[$prefix . '.*'])) {
                $listeners = array_merge($listeners, $this->listener[$prefix . '.*']);
            }
        }

        $listeners = $this->arraySort($listeners, 'sort');

        foreach ($listeners as $key => $listener) {
            $this->dispatch($listener['listener'], $var);
        }
    }

    /**
     * 添加过滤器
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @param bool   $sort     排序
     * @return $this
     */
    public function addFilter(string $event, $listener, int $sort = 1)
    {
        $this->listener[$event][] = [
            'listener' => $listener,
            'sort'     => $sort,
            'key'      => $this->filterBuildUniqueId($listener),
        ];

        return $this;
    }

    /**
     * 触发过滤器
     * 
     * @param string|object $event  事件名称
     * @param mixed         $params 传入参数
     * @param mixed         $var    更多参数
     * @return mixed
     */
    public function applyFilters($event, $params = null, ...$var)
    {
        if (is_object($event)) {
            $params = $event;
            $event  = $event::class;
        }

        $result    = [];
        $listeners = $this->listener[$event] ?? [];

        if (str_contains($event, '.')) {
            [$prefix, $event] = explode('.', $event, 2);
            if (isset($this->listener[$prefix . '.*'])) {
                $listeners = array_merge($listeners, $this->listener[$prefix . '.*']);
            }
        }

        $listeners = $this->arraySort($listeners, 'sort');

        if (count($var) == 0) {
            $var = [];
        }
        
        $tmp = $var;
        $result = $params;
        foreach ($listeners as $key => $listener) {
            array_unshift($tmp, $result);
            
            $result = $this->dispatch($listener['listener'], $tmp);
        }

        return $result;
    }
    
    /**
     * 移除过滤器
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @return bool
     */
    public function removeFilter(string $event, $listener): bool
    {
        $key = $this->filterBuildUniqueId($listener);

        $exists = isset($this->listener[$event]);
        if ($exists) {
            foreach ($this->listener[$event] as $k => $v) {
                if ($v['key'] == $key) {
                    unset($this->listener[$event][$k]);
                }
            }
        }

        return $exists;
    }
    
    /**
     * 是否有过滤器
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @return bool
     */
    public function hasFilter(string $event, $listener = false): bool
    {
        if (false === $listener) {
            return $this->hasFilters();
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
    public function hasFilters(): bool 
    {
        foreach ($this->listener as $listener) {
            if ($listener) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * 是否存在事件监听
     * 
     * @param string $event 事件名称
     * @return bool
     */
    public function exists(string $event): bool
    {
        return isset($this->listener[$event]);
    }

    /**
     * 移除事件监听
     * 
     * @param string $event 事件名称
     * @return void
     */
    public function remove(string $event): void
    {
        unset($this->listener[$event]);
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
