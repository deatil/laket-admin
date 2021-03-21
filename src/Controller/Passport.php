<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use think\facade\Route;

use Laket\Admin\Facade\Admin;
use Laket\Admin\Support\Screen;

/**
 * 登陆
 *
 * @create 2021-3-18
 * @author deatil
 */
class Passport extends Base
{
    /**
     * 验证码
     */
    public function captcha()
    {
        return captcha('laket');
    }
    
    /**
     * 登陆
     */
    public function getLogin()
    {
        if (Admin::isLogin()) {
            $this->error("你已经登陆", laket_route("admin.index.index"));
        }
        
        return $this->fetch('laket-admin::passport.login');
    }
    
    /**
     * 提交登陆
     */
    public function postLogin()
    {
        $verify = request()->post('verify');
        
        // 验证码
        if (! captcha_check($verify)) {
            return $this->error('验证码输入错误！');
        }
        
        $data = request()->post();
        
        // 验证数据
        $rule = [
            'name|用户名' => 'require|alphaDash|length:3,20',
            'password|密码' => 'require|length:32',
        ];
        $message = [
            'name.require' => '用户名不能为空',
            'password.require' => '密码不能为空',
            'password.length' => '密码错误',
        ];
        $result = $this->validate($data, $rule, $message);
        if (true !== $result) {
            return $this->error($result);
        }
        
        if (! Admin::login($data['name'], $data['password'])) {
            $this->error("用户名或者密码错误", laket_route("admin.passport.login"));
        }
        
        $this->success('登陆成功', laket_route('admin.index.index'));
    }
    
    /**
     * 退出登录
     */
    public function logout()
    {
        if (! Admin::isLogin()) {
            $this->error("你还没有登陆", laket_route("admin.passport.login"));
        }
        
        if (Admin::logout()) {
            $this->success('注销成功', laket_route("admin.passport.login"));
        }
    }
    
    /**
     * 锁定
     */
    public function lockscreen()
    {
        $url = request()->url();
        
        (new Screen())->lock($url);
        
        $this->success('屏幕锁定成功');
    }
    
    /**
     * 解除锁定
     */
    public function unlockscreen()
    {
        $adminInfo = env('admin_info');
        $password = request()->post('password');
        
        if (!Admin::checkPassword($adminInfo['name'], $password)) {
            $this->error("密码错误，解除锁定失败");
        }
        
        (new Screen())->unlock();
        
        $this->success('屏幕解除锁定成功');
    }

}
