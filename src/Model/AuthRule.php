<?php

namespace Laket\Admin\Model;

use think\facade\Cache;

use Laket\Admin\Facade\Admin as AuthAdmin;

/**
 * 权限规则模型
 *
 * @create 2021-3-18
 * @author deatil
 */
class AuthRule extends ModelBase
{
    // 设置当前模型对应的数据表名称
    protected $name = 'laket_auth_rule';
    
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
     * 规则的分组列表
     */
    public function groups()
    {
        return $this->belongsToMany(AuthGroup::class, AuthRuleAccess::class, 'group_id', 'rule_id');
    }
    
    /**
     * 获取菜单列表
     *
     * @param type $data
     * @return type
     */
    public static function getMenusList()
    {
        $menus =  Cache::remember(md5('laket.admin.menus'), function() {
            $menus = [];
            
            $data = static::select()->toArray();
            if (!empty($data)) {
                foreach ($data as $rs) {
                    $menus[$rs['id']] = $rs;
                }
            }
            
            return $menus;
        }, 0);
        
        return $menus;
    }
    
    /**
     * 获取菜单
     * @return type
     */
    public static function getMenuList()
    {
        $data = static::getTree(0);
        return $data;
    }

    /**
     * 取得树形结构的菜单
     * @param type $mid
     * @param type $parent
     * @param type $level
     * @return type
     */
    public static function getTree($mid, $parent = "", $level = 1)
    {
        $data = static::adminMenu($mid);
        
        $level++;
        if (is_array($data)) {
            $ret = null;
            foreach ($data as $a) {
                $id = $a['id'];
                $url = $a['url'];
                $slug = $a['slug'];
                
                if (strpos($url, '://') || 0 === strpos($url, '/')) {
                    $url = $url;
                } else {
                    $url = laket_route($slug);
                }
                
                $array = [
                    "menuid" => $id,
                    "id" => $id,
                    "title" => $a['title'],
                    "icon" => $a['icon'],
                    "parent" => $parent,
                    "url" => $url,
                ];
                $ret[$id ] = $array;
                $child = static::getTree($a['id'], $id, $level);
                // 只考虑5层结构
                if ($child && $level <= 5) {
                    $ret[$id]['items'] = $child;
                }
            }
        }
        
        return $ret;
    }

    /**
     * 按父ID查找菜单子项
     * @param integer $parentid   父菜单ID
     * @param integer $withSelf  是否包括他自己
     */
    public static function adminMenu($parentid, $withSelf = false)
    {
        $result = static::where([
                'parentid' => $parentid, 
                'menu_show' => 1,
                'status' => 1,
            ])
            ->order('listorder ASC')
            ->select()
            ->toArray();
        if (empty($result)) {
            $result = [];
        }
        
        if ($withSelf) {
            $parentInfo = static::where(['id' => $parentid])->find();
            $result2[] = $parentInfo ? $parentInfo : array();
            $result = array_merge($result2, $result);
        }
        
        // 是否超级管理员
        if (AuthAdmin::isSuperAdmin()) {
            return $result;
        }
        
        $authIdList = static::getAuthIdList();
        
        $array = [];
        if (!empty($result)) {
            foreach ($result as $v) {
                if (in_array($v['id'], $authIdList)) {
                    $array[] = $v;
                }
            }
        }
        
        return $array;
    }

    /**
     * 获取权限ID列表
     */
    public static function getAuthIdList()
    {
        static $authIdList = [];
        if (!empty($authIdList)) {
            return $authIdList;
        }
        
        $admins = Admin::with(['groups'])
            ->where([
                'id' => AuthData::getId(),
            ])
            ->select()
            ->toArray();
            
        $groupIds = [];
        foreach ($admins as $admin) {
            if (!empty($admin['groups'])) {
                foreach ($admin['groups'] as $group) {
                    $groupIds[] = $group['id'];
                }
            }
        }
        
        $authIdList = AuthRuleAccess::where([
            ['group_id', 'in', $groupIds],
        ])
        ->column('rule_id');
        
        return $authIdList;
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree 是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     */
    public static function returnNodes($tree = true)
    {
        static $tree_nodes = [];
        if ($tree && !empty($tree_nodes[(int) $tree])) {
            return $tree_nodes[$tree];
        }
        
        if ((int) $tree) {
            $list = static::order('listorder ASC,id ASC')->select()->toArray();
            foreach ($list as $key => $value) {
                $list[$key]['url'] = $value['url'];
            }
            $nodes = Arr::listToTree($list, $pk = 'id', $pid = 'parentid', $child = 'operator', $root = 0);
            foreach ($nodes as $key => $value) {
                if (!empty($value['operator'])) {
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        } else {
            $nodes = static::order('listorder ASC,id ASC')->select()->toArray();
            foreach ($nodes as $key => $value) {
                $nodes[$key]['url'] = $value['url'];
            }
        }
        $tree_nodes[(int) $tree] = $nodes;
        return $nodes;
    }

}
