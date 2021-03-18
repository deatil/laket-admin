<?php

declare (strict_types = 1);

namespace Laket\Admin\Model;

/**
 * 管理员
 *
 * @create 2021-3-18
 * @author deatil
 */
class Admin extends ModelBase
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'laket_admin';
    
    // 设置主键名
    protected $pk = 'id';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;

    public static function onBeforeInsert($model)
    {
        $id = md5(mt_rand(10000, 99999) . microtime());
        $model->setAttr('id', $id);
        
        $model->setAttr('add_time', time());
        $model->setAttr('add_ip', request()->ip());
    }
    
    /**
     * 管理员的分组列表
     */
    public function groups()
    {
        return $this->belongsToMany(AuthGroup::class, AuthGroupAccess::class, 'group_id', 'admin_id');
    }
    
    /**
     * 管理员的附件列表
     * @param string $type 关联类型
     * @return array
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, [
            'type',
            'type_id', 
        ], 'admin');
    }
    
}
