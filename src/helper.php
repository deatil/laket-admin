<?php

use think\helper\Arr;
use think\helper\Str;

use Laket\Admin\Facade\Event;
use Laket\Admin\Facade\Admin as AuthAdmin;
use Laket\Admin\Facade\Flash as FlashManager;
use Laket\Admin\Facade\ViewFinder as ViewPathFinder;
use Laket\Admin\Http\Traits\View as ViewTrait;
use Laket\Admin\Support\Form;
use Laket\Admin\Model\Flash as FlashModel;
use Laket\Admin\Model\Attachment as AttachmentModel;

if (! function_exists('make')) {
    /**
     * 实例化一个类
     *
     * @param string $name 类名或标识
     * @param array  $args 参数
     * @return object
     */
    function make(string $name, array $args = []) 
    {
        return app($name, $args, true);
    }
}

if (! function_exists('route')) {
    /**
     * 根据路由名称生成 Url
     *
     * @param string      $name   路由名称
     * @param array       $vars   变量
     * @param bool|string $suffix 生成的 URL 后缀
     * @param bool|string $domain 域名
     * @return string
     */
    function route(
        string $name = '', 
        array $vars  = [], 
        $suffix = true, 
        $domain = false
    ) {
        $url = url($name, $vars, $suffix, $domain);
        return (string) $url;
    }
}

if (! function_exists('add_action')) {
    /**
     * 注册操作
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @param int    $sort     排序, 值越大越靠前
     * @return void
     */
    function add_action(string $event, $listener, int $sort = 1): void
    {
        Event::action()->listen($event, $listener, $sort);
    }
}

if (! function_exists('do_action')) {
    /**
     * 触发操作
     * 
     * @param string|object $event 事件名称
     * @param mixed         $var   更多数据
     * @return void
     */
    function do_action($event, ...$var): void
    {
        Event::action()->trigger($event, ...$var);
    }
}

if (! function_exists('remove_action')) {
    /**
     * 移除操作
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @param int    $sort     排序
     * @return bool
     */
    function remove_action(string $event, $listener, int $sort = 1): bool
    {
        return Event::action()->removeListener($event, $listener, $sort);
    }
}

if (! function_exists('has_action')) {
    /**
     * 是否有操作
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @return bool
     */
    function has_action(string $event, $listener): bool
    {
        return Event::action()->hasListener($event, $listener);
    }
}

if (! function_exists('add_filter')) {
    /**
     * 注册过滤器
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @param int    $sort     排序, 值越大越靠前
     * @return void
     */
    function add_filter(string $event, $listener, int $sort = 1): void
    {
        Event::filter()->listen($event, $listener, $sort);
    }
}

if (! function_exists('apply_filters')) {
    /**
     * 触发过滤器
     * 
     * @param string|object $event 事件名称
     * @param mixed         $value 需要过滤的数据
     * @param mixed         $var   更多数据
     * @return mixed
     */
    function apply_filters($event, $value = null, ...$var)
    {
        return Event::filter()->trigger($event, $value, ...$var);
    }
}

if (! function_exists('remove_filter')) {
    /**
     * 移除过滤器
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @param int    $sort     排序
     * @return bool
     */
    function remove_filter(string $event, $listener, int $sort = 1): bool
    {
        return Event::filter()->removeListener($event, $listener, $sort);
    }
}

if (! function_exists('has_filter')) {
    /**
     * 是否有过滤器
     * 
     * @param string $event    事件名称
     * @param mixed  $listener 监听操作
     * @return bool
     */
    function has_filter(string $event, $listener): bool
    {
        return Event::filter()->hasListener($event, $listener);
    }
}

if (! function_exists('register_install_hook')) {
    /**
     * 注册安装操作
     * 
     * @param string $name     插件包名
     * @param mixed  $callback 回调函数
     * @return void
     */
    function register_install_hook(string $name, $callback): void
    {
        add_action('admin_install_' . $name, $callback);
    }
}

if (! function_exists('register_uninstall_hook')) {
    /**
     * 注册卸载操作
     * 
     * @param string $name     插件包名
     * @param mixed  $callback 回调函数
     * @return void
     */
    function register_uninstall_hook(string $name, $callback): void
    {
        add_action('admin_uninstall_' . $name, $callback);
    }
}

if (! function_exists('register_upgrade_hook')) {
    /**
     * 注册更新操作
     * 
     * @param string $name     插件包名
     * @param mixed  $callback 回调函数
     * @return void
     */
    function register_upgrade_hook(string $name, $callback): void
    {
        add_action('admin_upgrade_' . $name, $callback);
    }
}

if (! function_exists('register_enable_hook')) {
    /**
     * 注册启用操作
     * 
     * @param string $name     插件包名
     * @param mixed  $callback 回调函数
     * @return void
     */
    function register_enable_hook(string $name, $callback): void
    {
        add_action('admin_enable_' . $name, $callback);
    }
}

if (! function_exists('register_disable_hook')) {
    /**
     * 注册禁用操作
     * 
     * @param string $name     插件包名
     * @param mixed  $callback 回调函数
     * @return void
     */
    function register_disable_hook(string $name, $callback): void
    {
        add_action('admin_disable_' . $name, $callback);
    }
}

if (! function_exists('is_admin')) {
    /**
     * 是否为后台 uri
     *
     * @param string $url 
     * @return bool
     */
    function is_admin(string $url = '') 
    {
        if (empty($url)) {
            $url = request()->pathinfo();
        }
        
        $url = ltrim($url, '/');
        
        $routeGroup = config('laket.route.group');
        if (Str::startsWith($url, ltrim($routeGroup, '/') . '/')) {
            return true;
        }
        
        return false;
    }
}

if (! function_exists('assets')) {
    /**
     * 资源 uri
     *
     * @param string $path 资源路径
     * @return string
     */
    function assets($path = '') 
    {
        $root = config('laket.view.assets');
        return rtrim($root, '/') . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (! function_exists('laket_url')) {
    /**
     * Url生成
     *
     * @param string      $url    路由地址
     * @param array       $vars   变量
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     * @return string
     */
    function laket_url(
        string $url = '', 
        array $vars = [], 
        $suffix = true, 
        $domain = false
    ) {
        $newUrl = url($url, $vars, $suffix, $domain);
        return (string) $newUrl;
    }
}

if (! function_exists('laket_route')) {
    /**
     * Url生成
     *
     * @param string      $name   路由名称
     * @param array       $vars   变量
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     * @return string
     */
    function laket_route(
        string $name = '', 
        array $vars  = [], 
        $suffix = true, 
        $domain = false
    ) {
        $url = url($name, $vars, $suffix, $domain);
        return (string) $url;
    }
}

if (! function_exists('laket_admin_url')) {
    /**
     * 后台 Url 生成
     *
     * @param string      $url    路由地址
     * @param array       $vars   变量
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     * @return string
     */
    function laket_admin_url(
        string $url = '', 
        array $vars = [], 
        $suffix = true, 
        $domain = false
    ) {
        $group = config('laket.route.group');
        $url = '/' . $group . '/' . ltrim($url, '/');
        
        $newUrl = url($url, $vars, $suffix, $domain);
        return (string) $newUrl;
    }
}

if (! function_exists('laket_view_path')) {
    /**
     * 获取视图路径
     *
     * @param string $template 模板路径
     * @return string
     */
    function laket_view_path($template) 
    {
        return ViewPathFinder::find($template);
    }
}

if (! function_exists('laket_view')) {
    /**
     * 解析和获取模板内容 用于输出
     *
     * @param string $template 模板文件名或者内容
     * @param array  $vars     模板变量
     * @param int    $code     状态码
     * @return string
     */
    function laket_view($template = '', $vars = [], $code = 200) 
    {
        return (new class {
            use ViewTrait;
            
            public function view($template, $vars, $code = 200)
            {
                $data = $this->fetch($template, $vars);
                return response($data, $code);
            }
        })->view($template, $vars, $code);
    }
}

if (! function_exists('laket_assets')) {
    /**
     * 后台资源 uri
     *
     * @param string $path 资源路径
     * @return string
     */
    function laket_assets($path = '') 
    {
        $root = config('laket.view.admin_assets');
        return rtrim($root, '/') . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (! function_exists('laket_auth')) {
    /**
     * 权限检测
     *
     * 检测链接: 
     * laket_auth("GET:admin/auth-group/access", 'or', 'url')
     * laket_auth("admin.auth-group.access", 'or')
     *
     * @param string $rule slug名称
     * @param string $relation
     * @param string $mode
     * @param string|null $type
     * @return bool
     */
    function laket_auth(
        $rule     = '', 
        $relation = 'or', 
        $mode     = 'slug', 
        $type     = null
    ) {
        return AuthAdmin::checkPermission($rule, $mode, $type, $relation);
    }
}

if (! function_exists('laket_file_name')) {
    /**
     * 根据附件id获取文件名
     *
     * @param string $id 附件id
     * @return string
     */
    function laket_file_name($id)
    {
        $data = AttachmentModel::where([
                'id' => $id
            ])
            ->find();
        return $data['name'] ?? '没有找到文件';
    }
}

if (! function_exists('laket_attachment_url')) {
    /**
     * 获取附件路径
     *
     * @param int $id 附件id
     * @param bool $domain 是否添加域名
     * @return string
     */
    function laket_attachment_url($id, $domain = false)
    {
        return AttachmentModel::getAttachmentUrl($id, $domain);
    }
}

if (! function_exists('laket_attachment_urls')) {
    /**
     * 获取多附件地址
     *
     * @param string|array $ids    附件id列表
     * @param bool         $domain 是否添加域名
     * @return array
     */
    function laket_attachment_urls($ids, $domain = false)
    {
        if (empty($ids)) {
            return [];
        }
        
        if (! is_array($ids)) {
            $ids = explode(',', $ids);
        }
        
        $ids = array_unique($ids);
        
        foreach ($ids as $id) {
            $list[$id] = laket_attachment_url($id, $domain);
        }
        
        return $list;
    }
}

if (! function_exists('laket_flash_static')) {
    /**
     * 插件静态文件
     *
     * @param string $name 插件包名
     * @param string $name 文件路径
     * @return string
     */
    function laket_flash_static(string $name, string $path) {
        $url = url('flash.assets', [
            'flash' => $name, 
            'path'  => ltrim($path, '/'),
        ]);
        
        return (string) $url;
    }
}

if (! function_exists('laket_flash_setting')) {
    /**
     * 插件配置信息
     *
     * @param string      $name     插件包名
     * @param string|null $key     取值
     * @param mix|null    $default 默认值
     * @return mixed
     */
    function laket_flash_setting(string $name, $key = null, $default = null)
    {
        $flashs = FlashModel::getFlashs();
        
        $data = Arr::get($flashs, $name, []);
        $setting = Arr::get($data, 'setting_datalist', []);
        
        if (! empty($key)) {
            return Arr::get($setting, $key, $default);
        }
        
        return $setting;
    }
}

if (! function_exists('laket_flash_installed')) {
    /**
     * 插件是否安装
     *
     * @param string $name 插件包名
     * @return bool
     */
    function laket_flash_installed(string $name)
    {
        $flashs = FlashModel::getFlashs();
        
        $info = Arr::get($flashs, $name, []);
        if (empty($info)) {
            return false;
        }
        
        return true;
    }
}

if (! function_exists('laket_flash_enabled')) {
    /**
     * 插件是否启用
     *
     * @param string $name 插件包名
     * @return bool
     */
    function laket_flash_enabled(string $name)
    {
        $flashs = FlashModel::getFlashs();
        
        $info = Arr::get($flashs, $name, []);
        if (empty($info)) {
            return false;
        }
        
        $status = Arr::get($info, 'status', 0);
        if ($status != 1) {
            return false;
        }
        
        return true;
    }
}

if (! function_exists('laket_authenticate_excepts')) {
    /**
     * 登录过滤
     *
     * @param array $excepts 过滤规则
     * @return mixed
     */
    function laket_authenticate_excepts(array $excepts)
    {
        FlashManager::authenticateExcepts($excepts);
    }
}

if (! function_exists('laket_permission_excepts')) {
    /**
     * 权限过滤
     *
     * @param array $excepts 过滤规则
     * @return mixed
     */
    function laket_permission_excepts(array $excepts)
    {
        FlashManager::permissionExcepts($excepts);
    }
}

if (! function_exists('laket_screenlock_excepts')) {
    /**
     * 锁屏过滤
     *
     * @param array $excepts 过滤规则
     * @return mixed
     */
    function laket_screenlock_excepts(array $excepts)
    {
        FlashManager::screenlockExcepts($excepts);
    }
}

if (! function_exists('form_select')) {
    /**
     * 下拉选择框
     *
     * @param type $array 数据
     * @param type $id 默认选择
     * @param type $str 属性
     * @param type $defaultOption 默认选项
     * @return string
     */
    function form_select($array = [], $id = 0, $str = '', $defaultOption = '')
    {
        return Form::select($array, $id, $str, $defaultOption);
    }
}

if (! function_exists('form_checkbox')) {
    /**
     * 复选框
     *
     * @param $array        选项 二维数组
     * @param $id           默认选中值，多个用 '逗号'分割
     * @param $str          属性
     * @param $defaultvalue 是否增加默认值 默认值为 -99
     * @param $field 
     * @return string
     */
    function form_checkbox($array = [], $id = '', $str = '', $defaultvalue = '', $field = '')
    {
        return Form::checkbox($array, $id, $str, $defaultvalue, $field);
    }
}

if (! function_exists('form_images')) {
    /**
     * 图片上传
     *
     * @param string $name     表单名称
     * @param int    $id       表单id
     * @param string $value    表单默认值
     * @param bool   $mult     是否多图片
     * @param string $alowexts 允许图片格式
     * @param int    $size     图片大小限制
     * @return string
     */
    function form_images($name, $id = '', $value = '', $mult = false, $size = 0) {
        return Form::images($name, $id, $value, $mult, $size);
    }
}
