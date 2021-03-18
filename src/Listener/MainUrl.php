<?php

declare (strict_types = 1);

namespace Laket\Admin\Listener;

use Laket\Admin\Event;

/**
 * 主页链接
 */
class MainUrl
{
    /**
     * 构造方法
     */
    public function handle(Event\MainUrl $event)
    {
        $event->data->url = laket_route('admin.index.main');
    }

}
