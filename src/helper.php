<?php

use think\facade\Event;
use think\facade\View;
use think\helper\Arr;

use Laket\Admin\Model\Flash as FlashModel;
use Laket\Admin\Model\Attachment as AttachmentModel;
use Laket\Admin\Facade\Flash as Flasher;
use Laket\Admin\Facade\Admin as AuthAdmin;
use Laket\Admin\Http\Traits\View as ViewTrait;

if (! function_exists('laket_url')) {
    /**
     * Url生成
     * @param string      $url    路由地址
     * @param array       $vars   变量
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     * @return UrlBuild
     */
    function laket_url(string $url = '', array $vars = [], $suffix = true, $domain = false) {
        $newUrl = url($url, $vars, $suffix, $domain);
        return (string) $newUrl;
    }
}

if (! function_exists('laket_route')) {
    /**
     * Url生成
     * @param string      $name   路由名称
     * @param array       $vars   变量
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     * @return UrlBuild
     */
    function laket_route(string $name = '', array $vars = [], $suffix = true, $domain = false) {
        $newUrl = url($name, $vars, $suffix, $domain);
        return (string) $newUrl;
    }
}

if (! function_exists('laket_view')) {
    /**
     * 解析和获取模板内容 用于输出
     *
     * @access protected
     * @param string $template 模板文件名或者内容
     * @param array  $vars     模板变量
     *
     * @return string
     * @throws \Exception
     */
    function laket_view($template, $vars = []) {
        return (new class {
            use ViewTrait;
        })->fetch($template, $vars);
    }
}

if (! function_exists('laket_check_permission')) {
    /**
     * 权限检测
     * @param string $rule slug名称
     * @return bool
     */
    function laket_check_permission($rule = '', $relation = 'or', $mode = 'slug', $type = null) {
        return AuthAdmin::checkPermission($rule, $mode, $type, $relation);
    }
}

if (!function_exists('laket_runhook')) {
    /**
     * 行为
     * @param  string $tag    标签名称
     * @param  mixed  $params 传入参数
     * @param  bool   $once   只获取一个有效返回值
     * @return mixed
     */
    function laket_runhook($tag, $params = null, $once = false)
    {
        $event = Event::trigger($tag, $params, $once);
        if ($once) {
            return $event;
        } else {
            $html = join("", $event);;
            return $html;
        }
        
    }
}

if (!function_exists('laket_file_name')) {
    /**
     * 根据附件id获取文件名
     * @param string $id 附件id
     * @return string
     */
    function laket_file_name($id = '')
    {
        $data = AttachmentModel::where([
                'id' => $id
            ])
            ->find();
        $name = $data['name'];
        return $name ? $name : '没有找到文件';
    }
}

if (!function_exists('laket_attachment_url')) {
    /**
     * 获取附件路径
     * @param int $id 附件id
     * @param bool $domain 是否添加域名
     * @return string
     */
    function laket_attachment_url($id, $domain = false)
    {
        $data = AttachmentModel::where([
                'id' => $id
            ])
            ->find();
        $path = $data['uri'];
        return ($path !== false) ? 
            ($domain ? request()->domain() . $path : $path)
            : "";
    }
}

if (!function_exists('laket_attachment_url_list')) {
    /**
     * 获取多附件地址
     * @param string $ids 附件id列表
     * @param bool $domain 是否添加域名
     * @return 返回附件列表
     */
    function laket_attachment_url_list($ids, $domain = false) {
        if ($ids == '') {
            return false;
        }
        
        $id_list = explode(',', $ids);
        foreach ($id_list as $id) {
            $list[] = laket_attachment_url($id, $domain);
        }
        return $list;
    }
}

if (! function_exists('laket_flash_setting')) {
    /**
     * 闪存插件配置信息
     * @param string $name 闪存插件包名
     * @param string|null $key 取值
     * @param mix|null $default 默认值
     * @return 闪存插件设置值
     */
    function laket_flash_setting($name, $key = null, $default = null) {
        $flashs = FlashModel::getFlashs();
        
        $data = Arr::get($flashs, $name, []);
        $setting = Arr::get($data, 'setting_datalist', []);
        
        if (! empty($key)) {
            return Arr::get($setting, $key, $default);
        }
        
        return $setting;
    }
}

if (! function_exists('laket_admin_authenticate_excepts')) {
    /**
     * 登陆验证过滤
     */
    function laket_admin_authenticate_excepts(array $excepts)
    {
        return Flasher::authenticateExcepts($excepts);
    }
}

if (! function_exists('laket_admin_permission_excepts')) {
    /**
     * 权限过滤
     */
    function laket_admin_permission_excepts(array $excepts)
    {
        return Flasher::permissionExcepts($excepts);
    }
}
