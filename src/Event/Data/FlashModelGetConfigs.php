<?php

declare (strict_types = 1);

namespace Laket\Admin\Event\Data;

/**
 * 闪存插件获取配置
 */
class FlashModelGetConfigs
{
    public $settinglist;

    public $settingDatalist;

    public function __construct(array $settinglist, array $settingDatalist)
    {
        $this->settinglist = $settinglist;
        
        $this->settingDatalist = $settingDatalist;
    }

}
