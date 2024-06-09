<?php

declare (strict_types = 1);

namespace Laket\Admin\Auth;

/**
 * 权限认证类
 *
 * @create 2021-3-18
 * @author deatil
 */
class Permission
{
    /**
     * 检查权限
     *
     * @param string|array name  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param integer uid        认证用户的id
     * @param string relation    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * @param array type         认证方式
     * @param string mode        执行check的模式
     * @return boolean           通过验证返回true;失败返回false
     */
    public function check(
        $name, 
        $uid = '', 
        $relation = 'or', 
        $type = 2, 
        $mode = 'slug'
    ) {
        $authList = $this->getAuthList($uid, $type, $mode);
        
        $checkAuthList = (new Check)->withAuths($authList)->check($name, $relation, $mode);
        if ($checkAuthList !== false) {
            return $checkAuthList;
        }
        
        return false;
    }

    /**
     * 获得权限列表
     *
     * @param integer $uid  用户id
     */
    public function getAuthList($uid, $type, $mode)
    {
        // 获取用户需要验证的所有有效规则列表
        $authList = (new Rule($type, $mode))->getAuthList($uid); 
        
        return $authList;
    }
    
}
