<?php

declare (strict_types = 1);

namespace Laket\Admin\Event;

use think\App;

/**
 * 事件管理类
 */
class Events
{
    /**
     * 动作事件
     */
    protected Action $action;

    /**
     * 过滤事件
     */
    protected Filter $filter;
    
    public function __construct(App $app)
    {
        $this->action = new Action($app);
        $this->filter = new Filter($app);
    }

    /**
     * 获取动作事件
     */
    public function getAction(): Action
    {
        return $this->action;
    }

    /**
     * 获取过滤事件
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

}
