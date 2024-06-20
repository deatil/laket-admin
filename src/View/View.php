<?php

declare (strict_types = 1);

namespace Laket\Admin\View;

use think\helper\Arr;
use think\View as BaseView;

/**
 * 视图类
 */
class View extends BaseView
{
    protected function resolveConfig(string $name)
    {
        $config = $this->app->config->get('laket.views', []);
        Arr::forget($config, 'type');
        return $config;
    }

    /**
     * 默认驱动
     * @return string|null
     */
    public function getDefaultDriver()
    {
        return $this->app->config->get('laket.views.type', 'php');
    }
}
