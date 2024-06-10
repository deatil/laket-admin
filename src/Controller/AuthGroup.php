<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use Laket\Admin\Support\Tree;
use Laket\Admin\Model\AuthGroup as AuthGroupModel;
use Laket\Admin\Model\AuthRule as AuthRuleModel;
use Laket\Admin\Model\AuthRuleAccess as AuthRuleAccessModel;

/**
 * 用户组
 *
 * @create 2021-3-18
 * @author deatil
 */
class AuthGroup extends Base
{
    /**
     * 用户组
     */
    public function index()
    {
        return $this->fetch('laket-admin::auth-group.index');
    }
    
    /**
     * 用户组数据
     */
    public function indexData()
    {
        $list = AuthGroupModel::order([
                'listorder' => 'DESC',
                'add_time' => 'ASC',
            ])
            ->select()
            ->toArray();
        $total = AuthGroupModel::count();
        
        $tree = make(Tree::class);
        $tree->withData($list);
        $list = $tree->buildArray(0);
        $list = $tree->buildFormatList($list, 'title');
        
        return $this->success('获取成功', '', [
            "count" => $total, 
            "list"  => $list
        ]);
    }

    /**
     * 全部用户组
     */
    public function all()
    {
        return $this->fetch('laket-admin::auth-group.all');
    }
    
    /**
     * 全部用户组数据
     */
    public function allData()
    {
        $limit = $this->request->param('limit/d', 20);
        $page = $this->request->param('page/d', 1);
        
        $searchField = $this->request->param('search_field/s', '', 'trim');
        $keyword = $this->request->param('keyword/s', '', 'trim');
        
        $map = [];
        if (!empty($searchField) && !empty($keyword)) {
            $map[] = [$searchField, 'like', "%$keyword%"];
        }
        
        $list = AuthGroupModel::where($map)
            ->page($page, $limit)
            ->order([
                'listorder' => 'DESC',
                'add_time' => 'ASC',
            ])
            ->select()
            ->toArray();
        $total = AuthGroupModel::where($map)->count();
        
        return $this->success('获取成功', '', [
            "count" => $total, 
            "list"  => $list,
        ]);
    }
    
    /**
     * 添加用户组
     */
    public function add()
    {
        $Tree = make(Tree::class);
        $list = AuthGroupModel::order([
                'id' => 'ASC',
            ])
            ->column('*', 'id');
        
        $Tree->withData($list);
        $groupData = $Tree->buildArray(0);
        $groupData = $Tree->buildFormatList($groupData, 'title');
        
        $this->assign("group_data", $groupData);
        
        return $this->fetch('laket-admin::auth-group.add');
    }
    
    /**
     * 添加用户组保存
     */
    public function addSave()
    {
        $data = $this->request->post();
        if (empty($data['parentid'])) {
            return $this->error('父级不能为空');
        }
        
        $result = $this->validate($data, 'Laket\\Admin\\Validate\\AuthGroup');
        if (true !== $result) {
            return $this->error($result);
        }
        
        $r = AuthGroupModel::create($data);
        if ($r === false) {
            return $this->error('添加失败！');
        }
        
        return $this->success('添加成功！');
    }
    
    /**
     * 编辑用户组
     */
    public function edit()
    {
        $id = $this->request->param('id');
        if (empty($id)) {
            return $this->error('用户组不存在！');
        }
        
        $authGroup = AuthGroupModel::where([
                'id' => $id,
            ])
            ->find();
        if (empty($authGroup)) {
            return $this->error('用户组不存在！');
        }
        
        $Tree = make(Tree::class);
        
        $list = AuthGroupModel::order([
                'id' => 'ASC',
            ])
            ->column('*', 'id');
            
        $childsId = $Tree->getListChildsId($list, $authGroup['id']);
        $childsId[] = $authGroup['id'];
        
        if (!empty($list)) {
            foreach ($list as $key => $val) {
                if (in_array($val['id'], $childsId)) {
                    unset($list[$key]);
                }
            }
        }
        
        $Tree->withData($list);
        $groupData = $Tree->buildArray(0);
        $groupData = $Tree->buildFormatList($groupData, 'title');
        
        $this->assign("group_data", $groupData);
        $this->assign('auth_group', $authGroup);
        
        return $this->fetch('laket-admin::auth-group.edit');
    }
    
    /**
     * 编辑用户组保存
     */
    public function editSave()
    {
        $data = $this->request->post();
        if (empty($data['parentid'])) {
            return $this->error('父级不能为空');
        }
        
        if (!isset($data['id']) || empty($data['id'])) {
            return $this->error('用户组ID不存在！');
        }
        
        $authGroup = AuthGroupModel::where([
                'id' => $data['id'],
            ])
            ->find();
        if (empty($authGroup)) {
            return $this->error('用户组不存在！');
        }
        
        // 更新
        $r = AuthGroupModel::update($data, [
                'id' => $data['id'],
            ]);
        
        if ($r === false) {
            return $this->error('更新失败！');
        }
        
        return $this->success('更新成功！');
    }
    
    /**
     * 删除用户组
     */
    public function delete()
    {
        $groupId = $this->request->param('id');
        if (empty($groupId)) {
            return $this->error('用户组不存在！');
        }
        
        $authGroupCount = AuthGroupModel::count();
        if ($authGroupCount <= 1) {
            return $this->error('用户组至少保留一个！');
        }
        
        $authGroup = AuthGroupModel::where([
                'id' => $groupId,
            ])
            ->find();
        if (empty($authGroup)) {
            return $this->error('用户组不存在！');
        }
        
        // 子用户组检测
        $childGroupCount = AuthGroupModel::where([
                ['parentid', '=', $groupId],
            ])
            ->count();
        if ($childGroupCount > 0) {
            return $this->error('删除失败，请删除子用户后再删除！');
        }
        
        $rs = AuthGroupModel::where(['id' => $groupId])->delete();
        if ($rs === false) {
            return $this->error('删除失败！');
        }
        
        // 删除权限
        AuthRuleAccessModel::where([
            'group_id' => $groupId,
        ])->delete();
        
        return $this->success("删除成功！");
    }
    
    /**
     * 访问授权页面
     */
    public function access()
    {
        $groupId = $this->request->param('group_id');
        if (empty($groupId)) {
            return $this->error('用户组ID不能为空！');
        }
        
        $rules = AuthGroupModel::withJoin(['ruleAccess'])
            ->where([
                'auth_group.id' => $groupId,
            ])
            ->visible([
                'ruleAccess' => [
                    'rule_id',
                ]
            ])
            ->column('ruleAccess.rule_id');
        $this->assign('rules', $rules);
    
        $result = AuthRuleModel::returnNodes(false);
        
        $json = [];
        if (!empty($result)) {
            foreach ($result as $rs) {
                $data = [
                    'id' => $rs['id'],
                    'parentid' => $rs['parentid'],
                    'title' => (empty($rs['method']) ? $rs['title'] : ($rs['title'] . '[' . strtoupper($rs['method']) . ']')),
                    // 'checked' => in_array($rs['id'], $rules) ? true : false,
                    'field' => 'roleid',
                    'spread' => false,
                ];
                $json[] = $data;
            }
        }
        
        $json = make(Tree::class)
            ->withConfig('buildChildKey', 'children')
            ->withData($json)
            ->buildArray(0);
        
        $this->assign('group_id', $groupId);
        $this->assign('json', $json);
        
        $authGroup = AuthGroupModel::where([
            'id' => $groupId,
        ])->find();
        $this->assign('auth_group', $authGroup);
        
        return $this->fetch('laket-admin::auth-group.access');
    }
    
    /**
     * 访问授权保存
     */
    public function accessSave()
    {
        $groupId = $this->request->post('id');
        if (empty($groupId)) {
            return $this->error('用户组不存在！');
        }
    
        $authGroup = AuthGroupModel::where([
                'id' => $groupId,
            ])
            ->find();
        if (empty($authGroup)) {
            return $this->error('用户组不存在！');
        }
        
        $newRules = $this->request->post('rules');
        
        $rules = [];
        if (!empty($newRules)) {
            $rules = explode(',', $newRules);
        }
        
        // 删除权限
        AuthRuleAccessModel::where([
            'group_id' => $groupId,
        ])->delete();
        
        // 权限添加
        if (isset($rules) && !empty($rules)) {
            foreach ($rules as $rule) {
                AuthRuleAccessModel::create([
                    'group_id' => $groupId,
                    'rule_id' => $rule,
                ]);
            }
        }
        
        return $this->success('授权成功！');
    }

    /**
     * 用户组排序
     */
    public function listorder()
    {
        $id = $this->request->param('id/s', 0);
        if (empty($id)) {
            return $this->error('参数不能为空！');
        }
        
        $listorder = $this->request->param('value/d', 100);
        
        $rs = AuthGroupModel::where([
            'id' => $id,
        ])->update([
            'listorder' => $listorder,
        ]);
        if ($rs === false) {
            return $this->error("排序失败！");
        }
        
        return $this->success("排序成功！");
    }
    
}
