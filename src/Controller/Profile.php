<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use Laket\Admin\Model\Admin as AdminModel;
use Laket\Admin\Facade\Admin as AdminFacade;

/**
 * 账号信息
 *
 * @create 2021-3-18
 * @author deatil
 */
class Profile extends Base
{
    /**
     * 管理员账号修改
     */
    public function setting()
    {
        $adminInfo = env('admin_info');
        
        $id = $adminInfo['id'];
        $data = AdminModel::where([
                "id" => $id,
            ])->find();
        
        if (empty($data)) {
            $this->error('该信息不存在！');
        }
        
        $this->assign("data", $data);
        
        return $this->fetch('laket-admin::profile.setting');
    }

    /**
     * 管理员账号修改
     */
    public function settingSave()
    {
        $adminInfo = env('admin_info');
        
        $post = $this->request->post();
        
        $data = [];
        $data['email'] = $post['email'];
        $data['nickname'] = $post['nickname'];
        $data['avatar'] = $post['avatar'];

        $status = AdminModel::where([
                'id' => $adminInfo['id'],
            ])
            ->data($data)
            ->update();
        
        if ($status === false) {
            $this->error('修改失败！');
        }
        
        $this->success("修改成功！");
    }

    /**
     * 管理员密码修改
     */
    public function password()
    {
        $adminInfo = env('admin_info');
        
        $data = AdminModel::where([
                "id" => $adminInfo['id'],
            ])->find();
        
        if (empty($data)) {
            $this->error('信息不存在！');
        }
        
        $this->assign("data", $data);
        
        return $this->fetch('laket-admin::profile.password');
    }

    /**
     * 管理员密码修改
     */
    public function passwordSave()
    {
        $adminInfo = env('admin_info');
        
        $post = $this->request->post();
        
        // 验证数据
        $rule = [
            'password|旧密码' => 'require|length:32',
            'password2|新密码' => 'require|length:32',
            'password2_confirm|确认新密码' => 'require|length:32',
        ];
        $result = $this->validate($post, $rule);
        if (true !== $result) {
            return $this->error($result);
        }
        
        if (!isset($post['password']) || empty($post['password'])) {
            $this->error('请填写旧密码！');
        }

        if (!isset($post['password2']) || empty($post['password2'])) {
            $this->error('请填写新密码！');
        }

        if (!isset($post['password2_confirm']) || empty($post['password2_confirm'])) {
            $this->error('请填写确认密码！');
        }
        
        if ($post['password2'] != $post['password2_confirm']) {
            $this->error('确认密码错误！');
        }
        
        if ($post['password2'] == $post['password']) {
            $this->error('请确保新密码与旧密码不同');
        }
        
        if (! AdminFacade::checkPassword($adminInfo['name'], $post['password'])) {
            $this->error('旧密码错误！');
        }

        $passwordinfo = AdminFacade::encryptPassword($post['password2']); //对密码进行处理
        
        $data = [];
        $data['password'] = $passwordinfo['password'];
        $data['password_salt'] = $passwordinfo['encrypt'];

        $status = AdminModel::where([
            'id' => $adminInfo['id'],
        ])->update($data);
        
        if ($status === false) {
            $this->error('修改密码失败！');
        }
        
        AdminFacade::logout();
        
        $this->success("修改密码成功！");
    }

}
