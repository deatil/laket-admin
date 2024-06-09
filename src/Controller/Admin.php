<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use Laket\Admin\Facade\Admin as AdminData;
use Laket\Admin\Model\Admin as AdminModel;
use Laket\Admin\Model\AuthGroup as AuthGroupModel;
use Laket\Admin\Model\AuthGroupAccess as AuthGroupAccessModel;

/**
 * 管理员管理
 *
 * @create 2021-3-18
 * @author deatil
 */
class Admin extends Base
{
    /**
     * 管理员管理列表
     */
    public function index()
    {
        return $this->fetch('laket-admin::admin.index');
    }
    
    /**
     * 管理员管理列表
     */
    public function indexData()
    {
        $limit = $this->request->param('limit/d', 10);
        $page = $this->request->param('page/d', 1);

        $map = $this->buildparams();
        
        if (! AdminData::isSuperAdmin()) {
            $userChildGroupIds = (new AdminModel)->getUserChildGroupIds(AdminData::getId());
            $adminIds = AuthGroupAccessModel::where([
                    ['group_id', 'in', $userChildGroupIds],
                ])
                ->column('admin_id');
            $map[] = ['id', 'in', $adminIds];
        }
        
        $list = AdminModel::with(['groups'])
            ->where($map)
            ->page($page, $limit)
            ->select()
            ->visible([
                'groups' => [
                    'title',
                ]
            ])
            ->toArray();
        $total = AdminModel::where($map)
            ->count();
        
        return $this->success('获取成功', '', [
            "count" => $total, 
            "list"  => $list,
        ]);
    }

    /**
     * 添加管理员
     */
    public function add()
    {
        return $this->fetch('laket-admin::admin.add');
    }

    /**
     * 添加管理员
     */
    public function addSave()
    {
        $data = $this->request->post('');
        
        $result = $this->validate($data, 'Laket\\Admin\\Validate\\Admin.insert');
        if (true !== $result) {
            return $this->error($result);
        }
        
        if (isset($data['status'])) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        
        $status = AdminModel::create($data);
        if ($status === false) {
            return $this->error('添加失败！');
        }
       
        return $this->success("添加管理员成功！");
    }

    /**
     * 管理员编辑
     */
    public function edit()
    {
        $id = $this->request->param('id/s');
        if (empty($id)) {
            return $this->error('参数错误！');
        }
        
        $data = AdminModel::where([
                "id" => $id,
            ])
            ->find();
        if (empty($data)) {
            return $this->error('账号不存在！');
        }
        
        $this->assign("data", $data);
        
        return $this->fetch('laket-admin::admin.edit');
    }

    /**
     * 管理员编辑
     */
    public function editSave()
    {
        $data = $this->request->post('');
        
        $result = $this->validate($data, 'Laket\\Admin\\Validate\\Admin.update');
        if (true !== $result) {
            return $this->error($result);
        }
        
        if (empty($data['id'])) {
            return $this->error('参数错误！');
        }
        
        $adminInfo = AdminModel::where([
                "id" => $data['id'],
            ])
            ->find();
        if (empty($adminInfo)) {
            return $this->error('账号信息不存在！');
        }
        
        if ($adminInfo['id'] == AdminData::getId()) {
            return $this->error('你不能修改自己的账号！');
        }
        
        if (AdminData::isSuperAdmin($adminInfo['id'])) {
            return $this->error('超级管理员不能被修改！');
        }
        
        if (isset($data['status'])) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        
        $status = AdminModel::update($data, [
                'id' => $data['id'],
            ]);
        if ($status === false) {
            return $this->error('修改失败！');
        }
        
        return $this->success("修改成功！");
    }
    
    /**
     * 管理员删除
     */
    public function delete()
    {
        $id = $this->request->param('id');
        if (empty($id)) {
            return $this->error('参数错误！');
        }
        
        $adminInfo = AdminModel::where([
                "id" => $id,
            ])
            ->find();
        if (empty($adminInfo)) {
            return $this->error('信息不存在！');
        }
        
        if ($adminInfo['id'] == AdminData::getId()) {
            return $this->error('你不能删除自己的账号！');
        }
        
        if (AdminData::isSuperAdmin($adminInfo['id'])) {
            return $this->error('超级管理员不能被删除！');
        }
        
        $status = AdminModel::where([
                'id' => $id,
            ])
            ->delete();
        if ($status === false) {
            return $this->error('删除失败！');
        }
        
        AuthGroupAccessModel::where([
                'admin_id' => $id,
            ])
            ->delete();
        
        return $this->success("删除成功！");
    }
    
    /**
     * 管理员详情
     */
    public function view()
    {
        $id = $this->request->param('id/s');
        if (empty($id)) {
            return $this->error('参数错误！');
        }
        
        $data = AdminModel::where([
            "id" => $id,
        ])->find();
        if (empty($data)) {
            return $this->error('账号不存在！');
        }
        
        $gids = AuthGroupAccessModel::where([
                'admin_id' => $id,
            ])
            ->column('group_id');
        $authGroups = AuthGroupModel::getGroups();
        
        $groups = [];
        foreach ($authGroups as $authGroup) {
            if (in_array($authGroup['id'], $gids)) {
                $groups[] = $authGroup['title'];
            }
        }
        
        $data['groups'] = $groups;
        
        $this->assign("data", $data);
        
        return $this->fetch('laket-admin::admin.view');
    }
    
    /**
     * 管理员更新密码
     */
    public function password()
    {
        $id = $this->request->param('id/s');
        $data = AdminModel::where([
                "id" => $id,
            ])
            ->find();
        if (empty($data)) {
            return $this->error('账号不存在！');
        }
        
        $this->assign("data", $data);
        
        return $this->fetch('laket-admin::admin.password');
    }
    
    /**
     * 管理员更新密码
     */
    public function passwordSave()
    {
        $post = $this->request->post('');
        
        if (empty($post) || !isset($post['id'])) {
            return $this->error('没有修改的数据！');
        }
        
        if (empty($post['password'])) {
            return $this->error('新密码不能为空！');
        }
        if (empty($post['password_confirm'])) {
            return $this->error('确认密码不能为空！');
        }
        
        if ($post['password'] != $post['password_confirm']) {
            return $this->error('两次密码不一致！');
        }
        
        if ($post['id'] == AdminData::getId()) {
            return $this->error('你不能修改自己账号的密码！');
        }
        
        // 对密码进行处理
        $passwordinfo = AdminData::encryptPassword($post['password']); 
        
        $data = [];
        $data['password'] = $passwordinfo['password'];
        $data['password_salt'] = $passwordinfo['encrypt'];
        
        $status = AdminModel::where([
                'id' => $post['id'],
            ])
            ->update($data);
        if ($status === false) {
            return $this->error('修改密码失败！');
        }
        
        return $this->success("修改密码成功！");
    }
    
    /**
     * 授权
     */
    public function access()
    {
        $id = $this->request->param('id/s');
        if (empty($id)) {
            return $this->error('参数错误！');
        }
        
        $data = AdminModel::where([
                "id" => $id,
            ])
            ->find();
        if (empty($data)) {
            return $this->error('该信息不存在！');
        }
        
        $data['gids'] = AuthGroupAccessModel::where([
                'admin_id' => $id,
            ])
            ->column('group_id');
        
        $this->assign("data", $data);
        
        if (! AdminData::isSuperAdmin()) {
            $userChildGroupIds = (new AdminModel)->getUserChildGroupIds(AdminData::getId());
            $roles = AuthGroupModel::getGroups([
                    ['id', 'in', $userChildGroupIds],
                ]);
        } else {
            $roles = AuthGroupModel::getGroups();
        }
        $this->assign("roles", $roles);
        
        return $this->fetch('laket-admin::admin.access');
    }
    
    /**
     * 授权
     */
    public function accessSave()
    {
        $data = $this->request->post('');
        
        if (empty($data['id'])) {
            return $this->error('参数错误！');
        }
        
        if ($data['id'] == AdminData::getId()) {
            return $this->error('你不能修改自己的账号！');
        }
        
        $adminInfo = AdminModel::where([
                "id" => $data['id'],
            ])
            ->find();
        if (empty($adminInfo)) {
            return $this->error('信息不存在！');
        }
        
        // 清除
        AuthGroupAccessModel::where([
                'admin_id' => $data['id'],
            ])
            ->delete();
        
        $newRoles = [];
        if (isset($data['roleid']) && !empty($data['roleid'])) {
            if (! AdminData::isSuperAdmin()) {
                $childGroupIds = (new AdminModel)->getUserChildGroupIds(AdminData::getId());
                $roleids = explode(',', $data['roleid']);
                
                $newRoles = array_intersect_assoc($childGroupIds, $roleids);
            } else {
                $newRoles = explode(',', $data['roleid']);
            }
        }
        
        $groupAccess = [];
        foreach ($newRoles as $role) {
            AuthGroupAccessModel::create([
                'admin_id' => $data['id'],
                'group_id' => $role,
            ]);
        }
        
        return $this->success("授权成功！");
    }

}
