<?php

declare (strict_types = 1);

namespace Laket\Admin\Auth;

use think\facade\Session;
use think\facade\Config;

use Laket\Admin\Facade\Password;
use Laket\Admin\Model\Admin as AdminModel;
use Laket\Admin\Model\AuthGroup as AuthGroupModel;

/**
 * 管理员
 *
 * @create 2021-3-18
 * @author deatil
 */
class Admin
{
    /**
     * 登陆数据
     */
    protected $data = [];
    
    /**
     * 用户登录
     * @param string $name 账户名
     * @param string $password (md5值) 密码
     * @return bool|mixed
     */
    public function login($name = '', $password = '')
    {
        $name = trim($name);
        $password = trim($password);
        $passwordPass = $this->checkPassword($name, $password);
        if ($passwordPass === false) {
            return false;
        }
        
        $info = $this->getInfo($name);
        
        $this->loginStatus($info['id']);
        
        Session::set('laket_admin_adminid', $info['id']);
        
        return true;
    }

    /**
     * 更新登录状态信息
     */
    public function loginStatus($id)
    {
        $data = [
            'last_login_time' => time(), 
            'last_login_ip' => request()->ip()
        ];
        return AdminModel::where([
            'id' => $id,
        ])->update($data);
    }

    /**
     * 获取用户信息
     */
    public function getInfo($id)
    {
        if (empty($id)) {
            return false;
        }

        $info = AdminModel::where([
                'id' => $id,
            ])
            ->whereOr([
                'name' => $id,
            ])
            ->find();
        if (empty($info)) {
            return false;
        }
        
        return $info->toArray();
    }

    /**
     * 检测密码
     * @param type $identifier 用户名或者用户ID
     * @param type $password 密码
     * @return boolean
     */
    public function checkPassword($identifier, $password)
    {
        if (empty($identifier) || empty($password)) {
            return false;
        }

        $info = $this->getInfo($identifier);
        if ($info === false) {
            return false;
        }
        
        // 密码验证
        $encryptPassword = $this->encryptPassword($password, $info['password_salt']);
        if ($encryptPassword != $info['password']) {
            return false;
        }
        
        return true;
    }

    /**
     * 检验用户是否已经登陆
     * @return boolean
     */
    public function check()
    {
        $adminid = Session::get('laket_admin_adminid');
        if (empty($adminid)) {
            return false;
        }
        
        $info = $this->getInfo($adminid);
        if ($info === false) {
            return false;
        }
        
        $this->data = $info;
        
        return true;
    }
    
    /**
     * 登陆数据
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * 登陆ID
     */
    public function getId()
    {
        return $this->data['id'] ?? false;
    }
    
    /**
     * 是否登陆
     */
    public function isLogin()
    {
        return $this->getId() !== false;
    }

    /**
     * 检查当前用户是否超级管理员
     * @return boolean
     */
    public function isSuperAdmin($id = null)
    {
        if (empty($id)) {
            $id = $this->getId();
        }
        
        if (empty($id)) {
            return false;
        }
        
        if (Config::get('laket.password.super_id') != $id) {
            return false;
        }
        
        return true;
    }

    /**
     * 注销登录状态
     * @return boolean
     */
    public function logout()
    {
        Session::clear();
        
        $this->data = [];
        
        return true;
    }
    
    /**
     * 管理员密码加密
     * @param $password
     * @param $encrypt //传入加密串，在修改密码时做认证
     * @return array/password
     */
    public function encryptPassword($password, $encrypt = '')
    {
        $pwd = Password::setSalt(Config::get("laket.password.salt"))
            ->encrypt($password, $encrypt);
        return $pwd;
    }
    
    /**
     * 权限检测
     */
    public function checkPermission(
        $rule, 
        $mode = 'slug', 
        $type = null, 
        $relation = 'or'
    ) {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        if (empty($type)) {
            $type = config('laket.auth.type');
        }
        
        $auth = app('laket-admin.auth-permission');
        $checkStatus = $auth->check($rule, $this->getId(), $relation, $type, $mode);
        if (false === $checkStatus) {
            return false;
        }
        
        return true;
    }

}
