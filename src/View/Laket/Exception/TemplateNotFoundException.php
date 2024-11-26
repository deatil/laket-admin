<?php

declare (strict_types = 1);

namespace Laket\Admin\View\Laket\Exception;

use RuntimeException;

class TemplateNotFoundException extends RuntimeException
{
    protected $template;

    public function __construct(string $message, string $template = '')
    {
        $this->message  = $message;
        $this->template = $template;
    }

    /**
     * 获取模板文件
     * @access public
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }
}
