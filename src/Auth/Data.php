<?php

declare (strict_types = 1);

namespace Laket\Admin\Auth;

use think\facade\Session;
use think\facade\Config;

use Laket\Admin\Facade\Password;
use Laket\Admin\Model\Admin as AdminModel;
use Laket\Admin\Model\AuthGroup as AuthGroupModel;

/**
 * 登录信息
 *
 * @create 2024-6-6
 * @author deatil
 */
class Data
{
    /**
     * 账号 ID
     */
    protected string $id;
    
    /**
     * 检查当前用户是否超级管理员
     */
    protected bool $isRoot;
    
    /**
     * 登陆数据
     */
    protected array $info = [];

    /**
     * 设置账号 ID
     *
     * @param string $id 登陆数据
     * @return void
     */
    public function withId(string $id)
    {
        $this->id = $id;
    }

    /**
     * 设置是否超级管理员
     *
     * @param array $isRoot 是否超级管理员
     * @return void
     */
    public function withIsRoot(bool $isRoot)
    {
        $this->isRoot = $isRoot;
    }

    /**
     * 设置登陆数据
     *
     * @param array $info 登陆数据
     * @return void
     */
    public function withInfo(array $info)
    {
        $this->info = $info;
    }
    
    /**
     * 登陆ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * 是否超级管理员
     */
    public function isRoot(): bool
    {
        return $this->isRoot;
    }

    /**
     * 登陆数据
     */
    public function getInfo(): array
    {
        return $this->info;
    }

}
