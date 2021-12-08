<?php

declare (strict_types = 1);

namespace Laket\Admin\Command;

use think\facade\Db;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\console\Table;
use think\facade\Console;

use Laket\Admin\Support\File;
use Laket\Admin\Support\Sql;

/**
 * 系统安装
 *
 * > php think laket-admin:install
 *
 * @create 2020-3-20
 * @author deatil
 */
class Install extends Command
{
    /**
     * 配置
     */
    protected function configure()
    {
        $this
            ->setName('laket-admin:install')
            ->setDescription('You will install Laket-admin.');
    }

    /**
     * 执行
     */
    protected function execute(Input $input, Output $output)
    {
        $output->newLine();
        $output->highlight('Laket-admin version v' . config('laket.admin.version') . "\n");
        
        $isCheckFunc = $output->ask($input, '> Before install, you need check system\'fuctions (Y/n)?', 'y');
        if ($isCheckFunc != 'y') {
            $output->error('> You don\'t check and don\'t install! ');
            return false;
        }
        
        $this->checkFunction($input, $output);
        
        $isStart = $this->output->ask($input, '> You will install laket-admin (Y/n)?', 'y');
        if ($isStart != 'y') {
            $output->error('> You don\'t install! ');
            return false;
        }
        
        $this->runInstall($input, $output);
    }

    /**
     * 检测扩展
     */
    protected function checkFunction(Input $input, Output $output)
    {
        $output->info("> System's functions is checking...");
        $output->newLine();
        
        $table = new Table();
        
        $header = ['Function', 'Status'];
        $table->setHeader($header, Table::ALIGN_LEFT);
        
        $rows = [];
        
        $items = $this->checkFunc();
        foreach ($items as $v) {
            if ($v[2] == 'no') {
                $rows[] = [$v[0], 'No'];
            } else {
                $rows[] = [$v[0], 'Yes'];
            }
        }
        $table->setRows($rows, Table::ALIGN_LEFT);
        $table->setStyle('default'); // default,compact,markdown,borderless,box,box-double
        
        $this->table($table);
    }

    /**
     * 执行
     */
    protected function runInstall(Input $input, Output $output)
    {
        $output->info("> Laket-admin is installing...");
        $output->newLine();
        
        $installLockFile = root_path() . 'install.lock';
        if (file_exists($installLockFile)) {
            $output->warning("Laket-admin is installed! Please unlink root 'install.lock' file.");
            return false;
        }
        
        // 当前连接数据库配置
        $dbConfig = app()->db->connect()->getConfig();
        
        // 数据库配置
        $database = $dbConfig['database'];
        $databaseCharset = $dbConfig['charset'];
        
        if (empty($database)) {
            $output->warning("Place set database config!");
            return false;
        }
        if (empty($databaseCharset)) {
            $output->warning("Place set database charset config!");
            return false;
        }
        
        // 创建数据库
        $dbConfig1 = $dbConfig;
        unset($dbConfig1['database']);
        app()->config->set([
            'connections' => [
                'laket-admin-db1' => $dbConfig1,
            ],
        ], 'database');
        $db = Db::connect('laket-admin-db1');
        $db->execute("CREATE DATABASE IF NOT EXISTS `".$database."` DEFAULT CHARACTER SET ".$databaseCharset." COLLATE ".$databaseCharset."_unicode_ci;");
        
        // 导入数据库
        $sqlFile = __DIR__ . '/../../resources/database/laketadmin.sql';
        if (! file_exists($sqlFile)) {
            $output->warning("Sql file don't exists!");
            return false;
        }
        
        $sqlStatement = Sql::getSqlFromFile($sqlFile);
        if (empty($sqlStatement)) {
            $output->warning("Sql is empty!");
            return false;
        }
        
        // 执行sql
        $dbConfig2 = $dbConfig;
        app()->config->set([
            'connections' => [
                'laket-admin-db2' => $dbConfig2,
            ],
        ], 'database');
        $db2 = Db::connect('laket-admin-db2');
        
        $dbPrefix = $dbConfig2['prefix'];
        foreach ($sqlStatement as $value) {
            try {
                $value = str_replace([
                    'pre__',
                ], [
                    $dbPrefix,
                ], trim($value));
                $db2->execute($value);
            } catch (\Exception $e) {
                $output->warning($e->getMessage());
                return false;
            }
        }
        
        // 复制静态文件
        Console::call('laket-admin:publish', [
            '--tag=laket-admin-assets',
            '--force',
        ]);
        
        // 复制配置文件
        Console::call('laket-admin:publish', [
            '--tag=laket-admin-config',
            '--force',
        ]);

        // 添加安装锁定文件
        file_put_contents($installLockFile, '');
        
        $output->info("Install lake-admin successfully!");
    }
    
    /**
     * 函数及扩展检查
     *
     * @return array
     */
    private function checkFunc()
    {
        $items = [
            ['pdo', '支持', 'yes', '类'],
            ['pdo_mysql', '支持', 'yes', '模块'],
            ['zip', '支持', 'yes', '模块'],
            ['fileinfo', '支持', 'yes', '模块'],
            ['curl', '支持', 'yes', '模块'],
            ['xml', '支持', 'yes', '函数'],
            ['file_get_contents', '支持', 'yes', '函数'],
            ['mb_strlen', '支持', 'yes', '函数'],
            ['gzopen', '支持', 'yes', '函数'],
        ];

        foreach ($items as &$v) {
            if (('类' == $v[3] && !class_exists($v[0])) 
                || ('模块' == $v[3] && !extension_loaded($v[0])) 
                || ('函数' == $v[3] && !function_exists($v[0])) 
            ) {
                $v[1] = '不支持';
                $v[2] = 'no';
            }
        }

        return $items;
    }

}
