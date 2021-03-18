<?php

declare (strict_types = 1);

namespace Laket\Admin\Event;

/**
 * 主页链接
 */
class MainUrl
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
