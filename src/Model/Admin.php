<?php

declare (strict_types = 1);

namespace Laket\Admin\Model;

use Laket\Admin\Support\Tree;

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
        $id = md5(mt_rand(100000, 999999).microtime().uniqid());
        $model->setAttr('id', $id);
        
        $model->setAttr('add_time', time());
        $model->setAttr('add_ip', request()->ip());
    }
    
    public static function onBeforeUpdate($model)
    {
        $model->setAttr('update_time', time());
        $model->setAttr('update_ip', request()->ip());
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
    
    /**
     * 获取用户的用户组ID列表
     */
    public static function getUserGroupIds($uid)
    {
        // 当前用户组ID列表
        $userGroupIds = $this->getGroupIdList($uid);
        return $userGroupIds;
    }
    
    /**
     * 获取用户的父级用户组ID列表
     */
    public function getUserParentGroupIds($uid)
    {
        // 当前用户组ID列表
        $userGroupIds = $this->getGroupIdList($uid);
        $userParentGroupIds = $this->getParentGroupIdList($userGroupIds);
        
        return $userParentGroupIds;
    }
    
    /**
     * 获取用户的子级用户组ID列表
     */
    public function getUserChildGroupIds($uid)
    {
        if (empty($uid)) {
            return [];
        }
        
        // 用户组列表
        $authGroupList = AuthGroup::where([
                'module' => 'admin',
            ])
            ->order([
                'id' => 'ASC',
            ])
            ->select();
        
        // 当前用户组ID列表
        $userGroupIds = $this->getGroupIdList($uid);
        
        $Tree = make(Tree::class);
        
        $userChildGroupIds = [];
        if (!empty($userGroupIds)) {
            foreach ($userGroupIds as $user_group_id) {
                $getChildGroupIds = $Tree->getListChildsId($authGroupList, $user_group_id);
                $userChildGroupIds = array_merge($userChildGroupIds, $getChildGroupIds);
            }
        }
        
        return $userChildGroupIds;
    }
    
    /**
     * 获得用户权限ID列表
     * @param integer $adminId  用户id
     * @return array
     */
    public function getAuthIdList($adminId)
    {
        $groupIds = $this->getGroupIdList($adminId);
        
        $authIds = AuthRuleAccess::where([
                ['group_id', 'in', $groupIds],
            ])
            ->column('rule_id');

        return $authIds;
    }
    
    /**
     * 分组ID列表
     */
    public function getGroupIdList($adminId)
    {
        $admins = static::with(['groups'])
            ->where([
                'id' => $adminId,
            ])
            ->select()
            ->visible([
                'groups' => [
                    'id',
                ]
            ])
            ->toArray();
        $groupIds = [];
        foreach ($admins as $admin) {
            foreach ($admin['groups'] as $group) {
                $groupIds[] = $group['id'];
            }
        }
        
        return $groupIds;
    }
    
    /**
     * 父级分组ID列表
     */
    public function getParentGroupIdList($gids = [])
    {
        $map = [
            ['id', 'in', $gids],
        ];
        
        $ids = AuthGroup::where($map)
            ->column('parentid');
        
        return $ids;
    }

}
