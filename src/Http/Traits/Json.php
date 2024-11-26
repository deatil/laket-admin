<?php

declare (strict_types = 1);

namespace Laket\Admin\Http\Traits;

use think\Response;

/**
 * JSON数据返回
 *
 * @create 2021-3-18
 * @author deatil
 *
 * 用法：
 * class index
 * {
 *     use \Laket\Admin\Http\Traits\Json;
 *     public function index(){
 *         return $this->errorJson();
 *     }
 *
 *     public function index2(){
 *         return $this->successJson();
 *     }
 * }
 */
trait Json
{
    /*
     * 返回错误json
     */
    protected function errorJson(
        $msg = null, 
        $code = 1, 
        $data = [],
        $header = []
    ) {
        return app('laket-admin.response')->json(false, $code, $msg, $data, $header);
    }
    
    /*
     * 返回成功json
     */
    protected function successJson(
        $msg = '获取成功', 
        $data = [], 
        $code = 0,
        $header = []
    ) {
        return app('laket-admin.response')->json(true, $code, $msg, $data, $header);
    }
}
