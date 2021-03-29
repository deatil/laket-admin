<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use think\facade\Db;
use think\facade\Cache;
use think\captcha\Captcha;

use Laket\Admin\Support\File;
use Laket\Admin\Event as AdminEvent;
use Laket\Admin\Model\AuthRule as AuthRuleModel;
use Laket\Admin\Model\Flash as FlashModel;
use Laket\Admin\Model\Attachment as AttachmentModel;

/**
 * 后台首页
 *
 * @create 2021-3-18
 * @author deatil
 */
class Index extends Base
{
    /**
     * 后台首页
     */
    public function index()
    {
        // 用户信息
        $this->assign('user_info', env('admin_info'));

        // 左侧菜单
        $menus = AuthRuleModel::getMenuList();
        $this->assign("menus", $menus);
        
        // 后台首页数据
        $mainUrl = laket_route('admin.index.main');
        $mainUrlData = new AdminEvent\Data\MainUrl($mainUrl);
        
        // 自定义后台首页
        event(new AdminEvent\MainUrl($mainUrlData));
        
        $this->assign("main_url", $mainUrlData->url);
        
        return $this->fetch('laket-admin::index.index');
    }
    
    /**
     * 欢迎首页
     */
    public function main()
    {
        $this->assign('user_info', env('admin_info'));
        
        // 模型数量
        $flashCount = FlashModel::count();
        $this->assign('flash_count', $flashCount);
        
        // 附件数量
        $attachmentCount = AttachmentModel::count();
        $this->assign('attachment_count', $attachmentCount);
        
        $this->assign('sys_info', $this->getSysInfo());
        
        return $this->fetch('laket-admin::index.main');
    }

    /**
     * phpinfo信息 按需显示在前台
     */
    protected function getSysInfo()
    {
        //$sys_info['os'] = PHP_OS; //操作系统
        $sys_info['ip'] = GetHostByName($_SERVER['SERVER_NAME']); //服务器IP
        $sys_info['php_uname'] = php_uname();
        $sys_info['web_server'] = $_SERVER['SERVER_SOFTWARE']; //服务器环境
        $sys_info['phpv'] = phpversion(); //php版本
        $sys_info['fileupload'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknown'; //文件上传限制
        //$sys_info['memory_limit'] = ini_get('memory_limit'); //最大占用内存
        //$sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false; //最大执行时间
        //$sys_info['zlib'] = function_exists('gzclose') ? 'YES' : 'NO'; //Zlib支持
        //$sys_info['safe_mode'] = (boolean) ini_get('safe_mode') ? 'YES' : 'NO'; //安全模式
        //$sys_info['timezone'] = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl'] = function_exists('curl_init') ? 'YES' : 'NO'; //Curl支持
        //$sys_info['max_ex_time'] = @ini_get("max_execution_time") . 's';
        $sys_info['domain'] = $_SERVER['HTTP_HOST']; //域名
        //$sys_info['remaining_space'] = round((disk_free_space(".") / (1024 * 1024)), 2) . 'M'; //剩余空间
        //$sys_info['user_ip'] = $_SERVER['REMOTE_ADDR']; //用户IP地址
        $sys_info['beijing_time'] = gmdate("Y年n月j日 H:i:s", time() + 8 * 3600); //北京时间
        $sys_info['time'] = date("Y年n月j日 H:i:s"); //服务器时间
        //$sys_info['web_directory'] = $_SERVER["DOCUMENT_ROOT"]; //网站目录
        $mysqlinfo = Db::query("SELECT VERSION() as version");
        $sys_info['mysql_version'] = $mysqlinfo[0]['version'];
        if (function_exists("gd_info")) {
            //GD库版本
            $gd = gd_info();
            $sys_info['gdinfo'] = $gd['GD Version'];
        } else {
            $sys_info['gdinfo'] = __("未知");
        }
        return $sys_info;
    }
    
    /**
     * 清空缓存
     */
    public function clear()
    {
        $type = $this->request->request("type", 'all');
        switch ($type) {
            case 'data':
                File::delDir(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'cache');
                Cache::clear();
                break;
                
            case 'template':
                File::delDir(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'temp');
                break;
                
            case 'all':
                File::delDir(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'cache');
                Cache::clear();
                File::delDir(root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'temp');
                break;
        }
        
        $this->success('清理缓存成功');
    }

}
