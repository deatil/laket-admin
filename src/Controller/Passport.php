<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use think\facade\Route;
use think\facade\Session;

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;

use Laket\Admin\Facade\Admin;
use Laket\Admin\Support\Screen;

/**
 * 登录
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
     * 登录
     */
    public function login()
    {
        if (Admin::isLogin()) {
            return $this->error("你已经登录", laket_route("admin.index.index"));
        }

        do_action('admin_passport_login_before');
        
        // 使用 RSA 方法
        $private = RSA::createKey(1024)->withPadding(RSA::ENCRYPTION_PKCS1);
        $public = $private->getPublicKey();
        
        // 私钥
        $privateKey = $private->toString('PKCS8');
        
        // 公钥
        $publicKey = $public->toString('PKCS8');
        
        // 缓存私钥
        $prikeyCacheKey = config('laket.passport.prikey_cache_key');
        Session::set($prikeyCacheKey, $privateKey);
        
        // 过滤公钥多余字符
        $publicKeyBase64 = str_replace([
            "-----BEGIN PUBLIC KEY-----", 
            "-----END PUBLIC KEY-----", 
            "\r\n",
            "\r",
            "\n",
        ], "", $publicKey);
        
        $this->assign("publicKey", $publicKeyBase64);

        do_action('admin_passport_login_after', $privateKey, $publicKey);

        return $this->fetch('laket-admin::passport.login');
    }
    
    /**
     * 提交登录
     */
    public function loginCheck()
    {
        if (Admin::isLogin()) {
            return $this->error('你已经登录！');
        }
        
        do_action('admin_passport_login_check_before');
        
        $verify = request()->post('verify');
        
        // 验证码
        if (! captcha_check($verify)) {
            return $this->error('验证码输入错误！');
        }
        
        $data = request()->post();
        
        // 验证数据
        $rule = [
            'name|用户名'   => 'require|alphaDash|length:3,20',
            'password|密码' => 'require',
        ];
        $message = [
            'name.require'     => '用户名不能为空！',
            'name.alphaDash'   => '用户名格式错误！',
            'name.length'      => '用户名字符长度错误！',
            'password.require' => '密码不能为空！',
        ];
        $result = $this->validate($data, $rule, $message);
        if (true !== $result) {
            return $this->error($result);
        }
        
        // 密码
        $password = base64_decode($data['password']);
        if (empty($password)) {
            return $this->error('用户名或者密码错误！');
        }

        try {
            // 私钥
            $prikeyCacheKey = config('laket.passport.prikey_cache_key');
            $prikey = Session::get($prikeyCacheKey);
            
            // 导入私钥
            $rsakey = PublicKeyLoader::load($prikey);
            
            // RSA 解出密码
            $password = $rsakey->withPadding(RSA::ENCRYPTION_PKCS1)
                ->decrypt($password);
        } catch(\Exception $e) {
            do_action('admin_passport_login_check_password_decrypt_fail', $e->getMessage());
            
            return $this->error('用户名或者密码错误！');
        }

        $adminInfo = Admin::login($data['name'], $password);
        if (empty($adminInfo)) {
            do_action('admin_passport_login_check_password_error');
            
            return $this->error('用户名或者密码错误！');
        }
        
        // 是否锁定
        if ($adminInfo['status'] != 1) {
            return $this->error('帐号不存在或者已被锁定！');
        }
        
        // 清除数据
        Session::delete($prikeyCacheKey);
        
        do_action('admin_passport_login_check_after', $adminInfo);
        
        return $this->success('登录成功！', laket_route('admin.index.index'));
    }
    
    /**
     * 退出登录
     */
    public function logout()
    {
        if (! Admin::isLogin()) {
            return $this->error("你还没有登录", laket_route("admin.passport.login"));
        }
        
        // 登录账号信息
        $adminInfo = Admin::getData();
        
        if (Admin::logout()) {
            return $this->success('退出成功', laket_route("admin.passport.login"));
        }
        
        do_action('admin_passport_logout_after', $adminInfo);
        
        return $this->error("退出失败", laket_route("admin.passport.login"));
    }
    
    /**
     * 锁定
     */
    public function lockscreen()
    {
        $url = request()->url();
        
        make(Screen::class)->lock($url);

        do_action('admin_passport_lockscreen_after', $url);
        
        return $this->success('屏幕锁定成功');
    }
    
    /**
     * 解除锁定
     */
    public function unlockscreen()
    {
        $adminInfo = Admin::getData();
        $password = request()->post('password');
        
        if (!Admin::checkPassword($adminInfo['name'], $password)) {
            return $this->error("密码错误，解除锁定失败");
        }
        
        make(Screen::class)->unlock();

        do_action('admin_passport_unlockscreen_after', $password);
        
        return $this->success('屏幕解除锁定成功');
    }

}
