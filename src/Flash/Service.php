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
     * composer.json文件地址
     */
    public $composer = '';
    
    /**
     * 图标
     */
    public $icon = '';
    
    /**
     * 设置，设置文件或者数组
     */
    public $setting = '';
    
    /**
     * 在插件安装、插件卸载等操作时有效
     */
    public function action()
    {}
    
    /**
     * 启动，只有启用后加载
     */
    public function start()
    {}

}
