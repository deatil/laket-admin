# laket-admin 通用PHP后台管理系统


## 项目介绍

*  `laket-admin` 是基于 `Thinkphp` 的 PHP 通用后台管理系统
*  使用 `layui` 搭建的后台管理界面
*  通过系统构建的插件系统完成对项目的开发
*  插件说明文档 [`docs/wiki/flash.md`](docs/wiki/flash.md)


## 环境要求

 - PHP >= 8.0.0
 - Thinkphp >= 8.0
 - Fileinfo PHP Extension


## 截图预览

<table>
    <tr>
        <td width="50%">
            <center>
                <img alt="LaketAdmin" src="https://github.com/deatil/laket-admin/assets/24578855/fcc7187d-5f41-4f24-a0be-76083905902b" />
            </center>
        </td>
        <td width="50%">
            <center>
                <img alt="menus" src="https://github.com/deatil/laket-admin/assets/24578855/431cfba5-e1b7-459b-bba9-b7370375de18" />
            </center>
        </td>
    </tr>
    <tr>
        <td width="50%">
            <center>
                <img alt="attach" src="https://github.com/deatil/laket-admin/assets/24578855/948ce7db-2ffa-4f9f-8b60-45b8606cac1b" />
            </center>
        </td>
        <td width="50%">
            <center>
                <img alt="flash" src="https://github.com/deatil/laket-admin/assets/24578855/d8fa5b9e-1d1c-4e33-ade4-e834d04c4109" />
            </center>
        </td>
    </tr>
</table>

更多截图 
[Laket Admin 系统截图](https://github.com/deatil/laket-admin/issues/1)


## 安装步骤

1. 首先安装 `thinkphp`

```php
composer create-project topthink/think laket-admin 8.* && cd laket-admin
```

2. 配置数据库信息，并确认能够正常连接数据库

```
config/database.php
```

3. 执行以下命令下载系统

```php
composer require laket/laket-admin
```

4. 然后运行下面的命令安装系统

```php
php think laket-admin:install
```

运行完命令后，你可以找到 `config/laket_conf.php` 配置文件，重命名为 `config/laket.php`

5. 后台地址 `http://yourdomain.com/admin/index`, 登录账号：`admin` 及密码 `123456`


## 插件推荐

| 名称 | 描述 |
| --- | --- |
| [系统通用配置](https://github.com/deatil/laket-config) | 自定义数据配置，将配置信息统一到一个表 |
| [系统设置](https://github.com/deatil/laket-settings) | 添加系统的设置功能 |
| [操作日志](https://github.com/deatil/laket-operation-log) | 记录管理员在后台的操作日志 |
| [数据库管理](https://github.com/deatil/laket-admin-database) | 数据库备份、优化、修复及还原 |
| [百度编辑器](https://github.com/deatil/laket-ueditor) | 设置添加百度编辑器使用 |
| [插件禁用](https://github.com/deatil/laket-flash-disable) | 禁用后插件将不能进行相关的操作 |
| [表单提交](https://github.com/deatil/laket-pushbook) | 简单的表单提交，包括 Admin 页面和用户端页面 |
| [CMS](https://github.com/deatil/laket-cms) | CMS 内容管理系统 |

注：插件目录默认为 `/flashs` 目录


## 特别鸣谢

感谢以下的项目,排名不分先后

 - topthink/framework
 
 - layui

 - composer/semver


## 开源协议

*  `laket-admin` 遵循 `Apache2` 开源协议发布，在保留本系统版权的情况下提供个人及商业免费使用。 


## 版权

*  该系统所属版权归 deatil(https://github.com/deatil) 所有。
