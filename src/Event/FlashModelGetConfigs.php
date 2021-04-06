<?php

declare (strict_types = 1);

namespace Laket\Admin\Event;

/**
 * 闪存插件获取配置
 */
class FlashModelGetConfigs
{
    /**
     * data
     */
    public $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }

}
