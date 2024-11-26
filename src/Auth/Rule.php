<?php

declare (strict_types = 1);

namespace Laket\Admin\Auth;

use think\facade\Session;

use Laket\Admin\Model\AuthGroup as AuthGroupModel;
use Laket\Admin\Model\AuthRule as AuthRuleModel;
use Laket\Admin\Model\AuthRuleAccess as AuthRuleAccessModel;

/**
 * 数据规则
 *
 * @create 2021-3-18
 * @author deatil
 */
class Rule
{    
    // 认证方式，1为实时认证；2为登录认证。
    protected $type = 2; 
    
    // 模式, slug 或者 url
    protected $mode = 'slug'; 

    /**
     * 初始化
     */
    public function __construct($type = 1, $mode = 'slug')
    {
        $this->type = $type;
        $this->mode = $mode;
    }

    /**
     * 获得权限列表
     *
     * @param integer $uid  用户id
     * @param integer $type 选择类型
     */
    public function getAuthList($uid)
    {
        $cacheId = '_AUTH_LIST_' . $uid . '_' . $this->mode;
        
        if ($this->type == 2  && Session::has($cacheId)) {
            return Session::get($cacheId);
        }
        
        // 读取用户所属用户组
        $groups = $this->getGroups($uid);
        $gids = [];
        if (!empty($groups)) {
            foreach ($groups as $g) {
                $gids[] = $g['id'];
            }
        }
        
        // 保存用户所属用户组设置的所有权限规则id
        $ids = $this->getGroupRuleidList($gids); 
        $ids = array_unique($ids);
        if (empty($ids)) {
            return [];
        }
        
        $map = [
            ['id', 'in', $ids],
            ['status', '=', 1],
        ];
        
        // 读取用户组所有权限规则
        $rules = AuthRuleModel::where($map)
            ->field('url,slug,method')
            ->select();
            
        // 循环规则，判断结果。
        $authList = [];
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                if (! empty($rule['slug'])) {
                    if ($this->mode == 'slug') {
                        $authList[] = $rule['slug'];
                    } else {
                        $authList[] = strtoupper($rule['method']).':'.$rule['url'];
                    }
                }
            }
        }
        
        $authList = array_unique($authList);
        
        if ($this->type == 2) {
            // 规则列表结果保存到session
            Session::set($cacheId, $authList);
        }
        
        return $authList;
    }
    
    /**
     * 获得权限ID列表
     *
     * @param array $gids 分组id列表
     * @return array
     */
    public function getGroupRuleidList($gids = [])
    {
        $rules = AuthRuleAccessModel::where([
            ['group_id', 'in', $gids],
        ])
        ->column('rule_id');
        
        return $rules;
    }    

    /**
     * 根据用户id获取用户组,返回值为数组
     *
     * @param integer uid  用户id
     * @return array       用户所属的用户组 
     *  array(
     *      array(
     *          'id' => '用户组id',
     *          'title' => '用户组名称',
     *          'uid' => '用户id',
     *      ),
     *      ...
     *   )
     */
    public function getGroups($uid)
    {
        static $groups = [];
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }
        
        $userGroups = AuthGroupModel::withJoin(['groupAccess'])
            ->where([
                ['status', '=', 1],
                ['groupAccess.admin_id', '=', $uid],
            ])
            ->field('id, title')
            ->select()
            ->toArray();
        $groups[$uid] = $userGroups ?: [];

        return $groups[$uid];
    }
}
