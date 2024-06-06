<?php

declare (strict_types = 1);

namespace Laket\Admin\Facade;

use think\Facade;

/**
 * 登录信息
 *
 * @create 2024-6-6
 * @author deatil
 */
class AuthData extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'laket-admin.auth-data';
    }
}
