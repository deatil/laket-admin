<?php

namespace Laket\Admin\Model;

use think\model\Pivot;

/**
 * 用户组授权
 *
 * @create 2021-3-18
 * @author deatil
 */
class AuthGroupAccess extends Pivot
{
    // 设置当前模型对应的数据表名称
    protected $name = 'laket_auth_group_access';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;

    public static function onBeforeInsert($model)
    {
        $id = md5(mt_rand(10000, 99999) . microtime());
        $model->setAttr('id', $id);
    }

}
