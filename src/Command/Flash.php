<?php

declare (strict_types = 1);

namespace Laket\Admin\Command;

use Composer\Semver\Semver;
use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;

use think\helper\Arr;
use think\helper\Str;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\console\Table;

use Laket\Admin\Admin;
use Laket\Admin\Facade\Flash as Flasher;
use Laket\Admin\Model\Flash as FlashModel;

/**
 * 插件管理脚本
 *
 * > php think laket-admin:flash [--package=package_name] [--action=install]
 *
 * @create 2021-4-7
 * @author deatil
 */
class Flash extends Command
{
    /**
     * 配置
     */
    protected function configure()
    {
        $this
            ->setName('laket-admin:flash')
            ->addOption('package', 'p', Option::VALUE_OPTIONAL, 'flash package name.')
            ->addOption('action', 'a', Option::VALUE_OPTIONAL, 'flash command action.')
            ->setDescription('The command is some flash tools.');
    }

    /**
     * 执行
     */
    protected function execute(Input $input, Output $output)
    {
        $package = $input->getOption('package');
        if (empty($package)) {
            $package = $output->ask($input, 'Please enter an flash package name');
            if (empty($package)) {
                $output->error("Enter flash is empty !");
                return;
            }
        }
        
        $action = $input->getOption('action');
        if (empty($action)) {
            $output->info("Flash action list: ");
            
            $table = new Table();
            
            $header = ['No.', 'Action'];
            $table->setHeader($header, Table::ALIGN_LEFT);
            
            $actions = [
                '[1]' => 'Install',
                '[2]' => 'Uninstall',
                '[3]' => 'Upgrade',
                '[4]' => 'Enable',
                '[5]' => 'Disable',
            ];
            $rows = [];
            foreach ($actions as $no => $action) {
                $rows[] = [$no, $action];
            }
            $table->setRows($rows, Table::ALIGN_LEFT);
            $table->setStyle('default'); // default,compact,markdown,borderless,box,box-double
            
            $this->table($table);
            
            $action = $output->ask($input, 'Please enter an action or action line');
            if (empty($action)) {
                $action = 'install';
            }
        }
        
        $actions = [
            '1' => 'install',
            '2' => 'uninstall',
            '3' => 'upgrade',
            '4' => 'enable',
            '5' => 'disable',
        ];
        
        if (isset($actions[$action])) {
            $action = $actions[$action];
        }
        
        if (! in_array($action, $actions)) {
            $output->error("Enter action '{$action}' is error !");
            return;
        }
        
        $status = $this->{$action}($package);
        if ($status === false) {
            return;
        }
        
        $output->info($action . ' successfully!');
    }
    
    /**
     * 安装
     */
    public function install($name)
    {
        $installInfo = FlashModel::where(['name' => $name])
            ->find();
        if (! empty($installInfo)) {
            $this->output->error('Flash installed.');
            return false;
        }
        
        Flasher::loadFlash();
        $info = Flasher::getFlash($name);
        if (empty($info)) {
            $this->output->error('Flash dont exists');
            return false;
        }
        
        $checkInfo = Flasher::validateInfo($info);
        if (! $checkInfo) {
            $this->output->error('Flash info error.');
            return false;
        }
        
        try {
            $infoVersion = (new VersionParser())->normalize($info['version']);
        } catch(\Exception $e) {
            $this->output->error('Flash version error.');
            return false;
        }
        
        $adminVersion = Admin::VERSION;
        
        try {
            $versionCheck = Semver::satisfies($adminVersion, $info['adaptation']);
        } catch(\Exception $e) {
            $this->output->error('Flash adaptation version is error.');
            return false;
        }
        
        if (! $versionCheck) {
            $this->output->error('Flash adaptation version is error and system version: ' . $adminVersion);
            return false;
        }
        
        $requireExtensions = FlashModel::checkRequireExtension($info['require']);
        if (! empty($requireExtensions)) {
            foreach ($requireExtensions as $re) {
                if (! $re['match']) {
                    $this->output->error('Flash require\'flash [' . $re['name'] . '] error, installed version: ' . $re['install_version']);
                    return false;
                }
            }
        }
        
        Flasher::callClassMethod($info['bind_service'], 'action');
        
        // 安装前
        do_action('admin_install_flash', $name);
        
        // 安装当前插件时
        do_action('admin_install_' . $name);

        $flash = FlashModel::create([
            'name'         => Arr::get($info, 'name'),
            'title'        => Arr::get($info, 'title'),
            'description'  => Arr::get($info, 'description'),
            'keywords'     => json_encode(Arr::get($info, 'keywords')),
            'homepage'     => Arr::get($info, 'homepage'),
            'authors'      => json_encode(Arr::get($info, 'authors', [])),
            'version'      => Arr::get($info, 'version'),
            'adaptation'   => Arr::get($info, 'adaptation'),
            'require'      => json_encode(Arr::get($info, 'require')),
            'bind_service' => Arr::get($info, 'bind_service'),
            'setting'      => json_encode(Arr::get($info, 'setting', [])),
            'listorder'    => Arr::get($info, 'sort'),
        ]);
        if ($flash === false) {
            $this->output->error('Install error.');
            return false;
        }
        
        // 安装后
        do_action('admin_installed_flash', $name);
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
    }

    /**
     * 卸载
     */
    public function uninstall($name)
    {
        $installInfo = FlashModel::where(['name' => $name])
            ->find();
        if (empty($installInfo)) {
            $this->output->error('Flash dont install.');
            return false;
        }
        
        if ($installInfo['status'] == 1) {
            $this->output->error('Please enable flash and uninstall.');
            return false;
        }
        
        Flasher::loadFlash();
        Flasher::callClassMethod($installInfo['bind_service'], 'action');
        
        // 卸载前
        do_action('admin_uninstall_flash', $name);

        $status = FlashModel::where(['name' => $name])->delete();
        if ($status === false) {
            $this->output->error("Flash unstall error.");
            return false;
        }
        
        // 卸载当前插件时
        do_action('admin_uninstall_' . $name);
        
        // 卸载后
        do_action('admin_uninstalled_flash', $name);
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
    }
    
    /**
     * 模块更新
     */
    public function upgrade($name)
    {
        $installInfo = FlashModel::where(['name' => $name])
            ->find();
        if (empty($installInfo)) {
            $this->output->error('Flash dont install.');
            return false;
        }
        
        if ($installInfo['status'] == 1) {
            $this->output->error('Please enable flash and upgrade.');
            return false;
        }

        Flasher::loadFlash();
        $info = Flasher::getFlash($name);
        if (empty($info)) {
            $this->output->error('Flash dont exists.');
            return false;
        }
        
        $checkInfo = Flasher::validateInfo($info);
        if (! $checkInfo) {
            $this->output->error('Flash info is error.');
            return false;
        }
        
        $adminVersion = Admin::VERSION;
        
        try {
            $versionCheck = Semver::satisfies($adminVersion, $info['adaptation']);
        } catch(\Exception $e) {
            $this->output->error('Flash adaptation version is error.');
            return false;
        }
        
        if (! $versionCheck) {
            $this->output->error('Flash adaptation version is error and system version：' . $adminVersion);
            return false;
        }
        
        try {
            $infoVersion = (new VersionParser())->normalize($info['version']);
        } catch(\Exception $e) {
            $this->output->error('Flash version is error.');
            return false;
        }
        
        $infoVersion = Arr::get($info, 'version', 0);
        $installVersion = Arr::get($installInfo, 'version', 0);
        if (! Comparator::greaterThan($infoVersion, $installVersion)) {
            $this->output->error('Flash dont need upgrade.');
            return false;
        }
        
        $requireExtensions = FlashModel::checkRequireExtension($info['require']);
        if (! empty($requireExtensions)) {
            foreach ($requireExtensions as $re) {
                if (! $re['match']) {
                    $this->output->error('Flash require\'flash [' . $re['name'] . '] error, installed version: ' . $re['install_version']);
                    return false;
                }
            }
        }
        
        Flasher::callClassMethod($info['bind_service'], 'action');
        
        // 更新前
        do_action('admin_upgrade_flash', $name);
        
        // 更新当前插件时
        do_action('admin_upgrade_' . $name);

        $status = FlashModel::update([
                'title'        => Arr::get($info, 'title'),
                'description'  => Arr::get($info, 'description'),
                'keywords'     => json_encode(Arr::get($info, 'keywords')),
                'homepage'     => Arr::get($info, 'homepage'),
                'authors'      => json_encode(Arr::get($info, 'authors', [])),
                'version'      => Arr::get($info, 'version'),
                'adaptation'   => Arr::get($info, 'adaptation'),
                'require'      => json_encode(Arr::get($info, 'require')),
                'bind_service' => Arr::get($info, 'bind_service'),
                'setting'      => json_encode(Arr::get($info, 'setting', [])),
                'listorder'    => Arr::get($info, 'sort'),
                'upgrade_time' => time(),
            ], [
                'name' => $name
            ]);
        if ($status === false) {
            $this->output->error('Upgrade error.');
            return false;
        }
        
        // 更新后
        do_action('admin_upgraded_flash', $name);
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
    }
    
    /**
     * 启用
     */
    public function enable($name)
    {
        $installInfo = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($installInfo)) {
            $this->output->error('Flash dont install.');
            return false;
        }
        
        Flasher::loadFlash();
        Flasher::callClassMethod($installInfo['bind_service'], 'action');
        
        // 启用前
        do_action('admin_enable_flash', $name);
        
        $status = FlashModel::where([
            'name' => $name,
        ])->update([
            'status' => 1,
        ]);
        if ($status === false) {
            $this->output->error('Enable error.');
            return false;
        }
        
        // 启用当前插件时
        do_action('admin_enable_' . $name);
        
        // 启用后
        do_action('admin_enabled_flash', $name);
        
        // 清除缓存
        Flasher::forgetFlashCache($name);
    }
    
    /**
     * 禁用
     */
    public function disable($name)
    {
        $installInfo = FlashModel::where([
                "name" => $name,
            ])->find();
        if (empty($installInfo)) {
            $this->output->error('Flash dont install.');
            return false;
        }
        
        Flasher::callClassMethod($installInfo['bind_service'], 'action');
        
        // 禁用前
        do_action('admin_disable_flash', $name);
        
        $status = FlashModel::where([
            'name' => $name,
        ])->update([
            'status' => 0,
        ]);
        
        if ($status === false) {
            $this->output->error('Disable error.');
            return false;
        }
        
        // 禁用当前插件时
        do_action('admin_disable_' . $name);
        
        // 禁用后
        do_action('admin_disabled_flash', $name);

        // 清除缓存
        Flasher::forgetFlashCache($name);
    }

}
