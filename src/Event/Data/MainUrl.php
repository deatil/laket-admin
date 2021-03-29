<?php

declare (strict_types = 1);

namespace Laket\Admin\Event\Data;

/**
 * 主页链接
 */
class MainUrl
{
    public $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

}
