<?php

declare (strict_types = 1);

namespace Laket\Admin\Flash;

use Laket\Admin\Support\Service as BaseService;
use Laket\Admin\Traits\FlashService as FlashServiceTrait;

/**
 * 闪存服务
 *
 * @create 2021-3-19
 * @author deatil
 */
class Service extends BaseService
{
    use FlashServiceTrait;
    
    /**
     * 闪存composer.json文件
     */
    public $composer = '';
    
    /**
     * 启动，只有启用后加载
     */
    public function start()
    {}
    
    /**
     * 安装后
     */
    public function install()
    {}
    
    /**
     * 卸载后
     */
    public function uninstall()
    {}
    
    /**
     * 更新后
     */
    public function upgrade()
    {}
    
    /**
     * 启用后
     */
    public function enable()
    {}
    
    /**
     * 禁用后
     */
    public function disable()
    {}
}
