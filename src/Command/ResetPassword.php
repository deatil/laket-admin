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

use Laket\Admin\Facade\Admin as Adminer;
use Laket\Admin\Model\Admin as AdminModel;

/**
 * 重设密码
 *
 * php think laket-admin:reset-password
 *
 * @create 2021-3-20
 * @author deatil
 */
class ResetPassword extends Command
{
    /**
     * 配置
     */
    protected function configure()
    {
        $this
            ->setName('laket-admin:reset-password')
            // 配置一个参数
            // ->addArgument('password', Argument::REQUIRED, 'password')
            // 配置一个选项
            // ->addOption('password', null, Option::VALUE_REQUIRED, 'password')
            ->setDescription('You will reset an admin password.');
    }

    /**
     * 执行
     */
    protected function execute(Input $input, Output $output)
    {
        // 使用 getArgument() 取出参数值 -key value
        // $password = $input->getArgument('password');
        
        // 使用 getOption() 取出选项值 --key value
        // $password = $input->getOption('password');
        
        $output->newLine();
        
        $admin = $output->ask($input, '> Before, you need enter an adminid or an admin\'name');
        if (empty($admin)) {
            $output->error('> Admin is empty!');
            return false;
        }
        
        $password = $this->output->ask($input, '> Please enter a password');
        if (empty($password)) {
            $output->error('> Password is empty!');
            return false;
        }
        
        $password = md5($password);
        $passwordInfo = Adminer::encryptPassword($password); 
        
        $data = [];
        $data['password'] = $passwordInfo['password'];
        $data['password_salt'] = $passwordInfo['encrypt'];
        
        $status = AdminModel::where([
                'id' => $admin,
            ])
            ->whereOr([
                'name' => $admin,
            ])
            ->update($data);
        
        if ($status === false) {
            $output->error('> Reset password is error!');
            return false;
        }
        
        $output->info('Reset password successfully!');
    }

}
