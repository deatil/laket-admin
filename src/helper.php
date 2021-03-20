<?php

use think\facade\Event;
use think\helper\Arr;

use Laket\Admin\Model\Flash as FlashModel;
use Laket\Admin\Model\Attachment as AttachmentModel;

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
     */
    function laket_flash_setting($name, $key = null, $default = null) {
        $data = FlashModel::where([
                'name' => $name,
            ])
            ->find();
        
        $setting = $data['setting_datalist'];
        
        if (! empty($key)) {
            return Arr::get($setting, $key, $default);
        }
        
        return $setting;
    }
}
