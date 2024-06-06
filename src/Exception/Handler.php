<?php

declare (strict_types = 1);

namespace Laket\Admin\Exception;

use Throwable;

use think\Response;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;

/**
 * 异常
 *
 * @create 2021-3-18
 * @author deatil
 */
class Handler extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     *
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
    
    /**
     * 覆盖为自定义处理方式
     */
    protected function renderExceptionContent(Throwable $exception): string
    {
        ob_start();
        $data = $this->convertExceptionToArray($exception);
        extract($data);
        
        $exceptionTmpl = $this->app->config->get('laket_exception.exception_tmpl');
        include $exceptionTmpl;

        return ob_get_clean();
    }
}
