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
    protected Action $actionHandle;

    /**
     * 过滤事件
     */
    protected Filter $filterHandle;
    
    public function __construct(App $app)
    {
        $this->actionHandle = new Action($app);
        $this->filterHandle = new Filter($app);
    }

    /**
     * 获取动作事件
     */
    public function action(): Action
    {
        return $this->actionHandle;
    }

    /**
     * 获取过滤事件
     */
    public function filter(): Filter
    {
        return $this->filterHandle;
    }

}
