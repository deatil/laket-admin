<?php

namespace Laket\Admin\Validate;

use think\Validate;

/**
 * 管理员验证器
 *
 * @create 2021-3-19
 * @author deatil
 */
class Admin extends Validate
{
    // 定义验证规则
    protected $rule = [
        'name|用户名' => 'require|alphaDash|length:3,20|unique:laket_admin',
        'password|密码' => 'require|length:3,20|confirm',
        'email|邮箱' => 'email',
    ];

    // 登录验证场景定义
    public function sceneUpdate()
    {
        return $this->only(['name', 'password', 'email'])
            ->remove('password', 'require');
    }

    // 定义验证场景
    protected $scene = [
        'insert' => [
            'name', 
            'email',
        ],
    ];

}
