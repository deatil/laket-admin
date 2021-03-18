<?php

declare (strict_types = 1);

namespace Laket\Admin\Facade;

use think\Facade;

/**
 * 管理员
 *
 * @create 2021-3-18
 * @author deatil
 */
class Password extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'laket-admin.password';
    }
}
