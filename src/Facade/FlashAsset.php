<?php

declare (strict_types = 1);

namespace Laket\Admin\Facade;

use think\Facade;

/**
 * 插件静态文件
 *
 * @create 2024-7-10
 * @author deatil
 */
class FlashAsset extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'laket-admin.flash-asset';
    }
}
