<?php

declare (strict_types = 1);

namespace Laket\Admin\Flash;

use Laket\Admin\Facade\Event as AdminEvent;

/**
 * 事件
 *
 * @create 2024-7-23
 * @author deatil
 */
class Event
{
    /**
     * 类型
     *
     * @var string
     */
    private string $type;

    /**
     * 唯一句柄
     *
     * @var string
     */
    private string $handle;

    /**
     * 初始化
     *
     * @param string $type 
     * @param string $handle 
     */
    public function __construct(string $type, string $handle)
    {
        $this->type   = $type;
        $this->handle = $this->nativeClassName($handle);
    }

    /**
     * 操作
     *
     * @param string $handle 标识
     * @return Plugin
     */
    public static function action(string $handle): Event
    {
        return new self('action', $handle);
    }

    /**
     * 过滤器
     *
     * @param string $handle 标识
     * @return Plugin
     */
    public static function filter(string $handle): Event
    {
        return new self('filter', $handle);
    }

    /**
     * 设置回调函数
     *
     * @param string $component 当前组件
     * @param callable $value 回调函数
     */
    public function __set(string $component, callable $value)
    {
        $weight = 1;

        if (strpos($component, '_') > 0) {
            $parts = explode('_', $component, 2);
            [$component, $weight] = $parts;
            $weight = intval($weight);
        }

        $component = $this->handle . ':' . $component;
        
        if ($this->type == 'action') {
            AdminEvent::action()->listen($component, $value, $weight);
        } else {
            AdminEvent::filter()->listen($component, $value, $weight);
        }
    }
    
    /**
     * 回调函数
     *
     * @param string $component 当前组件
     * @param array $args 参数
     */
    public function __call(string $component, array $args)
    {
        $component = $this->handle . ':' . $component;

        if ($this->type == 'action') {
            AdminEvent::action()->trigger($component, ...$args);
        } else {
            return AdminEvent::filter()->trigger($component, ...$args);
        }
    }
    
    /**
     * @param string $className
     * @return string
     */
    public static function nativeClassName(string $className): string
    {
        return trim(str_replace('\\', '_', $className), '_');
    }
}
