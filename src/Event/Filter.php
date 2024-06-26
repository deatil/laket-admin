<?php

declare (strict_types = 1);

namespace Laket\Admin\Event;

/**
 * 过滤事件
 */
class Filter extends Event
{
    /**
     * 触发事件
     * 
     * @param string|object $event 事件名称
     * @param mixed         $value 需要过滤的数据
     * @param mixed         $var   更多数据
     * @return mixed
     */
    public function trigger($event, $value = null, ...$var)
    {
        if (is_object($event)) {
            $value = $event;
            $event = $event::class;
        }

        $result    = [];
        $listeners = $this->listener[$event] ?? [];

        if (str_contains($event, '.')) {
            [$prefix, $event] = explode('.', $event, 2);
            
            foreach ($this->listener as $e => $listener) {
                if ($event == '*' && str_starts_with($e, $prefix . '.')) {
                    $listeners = array_merge($listeners, $listener);
                }
            }
        }

        $listeners = $this->arraySort($listeners, 'sort');

        $tmp = $var;
        $result = $value;
        foreach ($listeners as $key => $listener) {
            array_unshift($tmp, $result);
            
            $result = $this->dispatch($listener['listener'], $tmp);
            $tmp = $var;
        }

        return $result;
    }
}
