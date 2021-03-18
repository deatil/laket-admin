<?php

declare (strict_types = 1);

namespace Laket\Admin\Exception;

use Throwable;

use think\exception\Handle;

/**
 * 异常
 *
 * @create 2021-3-18
 * @author deatil
 */
class Handler extends Handle
{
    /**
     * 覆盖为自定义处理方式
     */
    protected function renderExceptionContent(Throwable $exception): string
    {
        ob_start();
        $data = $this->convertExceptionToArray($exception);
        extract($data);
        
        $exceptionTmpl = $this->app->config->get('laket.exception_tmpl');
        include $exceptionTmpl;

        return ob_get_clean();
    }
}
