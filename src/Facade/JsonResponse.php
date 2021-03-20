<?php

declare (strict_types = 1);

namespace Laket\Admin\Facade;

use think\Facade;

/**
 * json响应
 *
 * @create 2021-3-20
 * @author deatil
 */
class JsonResponse extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'laket-admin.response';
    }
}
