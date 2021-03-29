## laket-admin 通用PHP后台开源管理系统


### 项目介绍

*  `laket-admin` 是基于 `thinkphp6` 版本的通用PHP后台开源管理系统
*  使用 `layui` 搭建的后台管理页面
*  通过系统构建的闪存插件系统完成对项目的开发
*  闪存插件说明文档 `docs/wiki/flash.md`


### 环境要求

 - PHP >= 7.1.0
 - thinkphp ^6.0.0
 - Fileinfo PHP Extension


### 截图预览

![LaketAdmin](https://user-images.githubusercontent.com/24578855/111893388-652cb900-8a3d-11eb-9f09-3983de3aefa5.png)


### 安装步骤

1. 首先安装 `thinkphp 6.*`，并确认连接数据库的配置没有问题，开始执行以下命令

```php
composer require laket/laket-admin
```

2. 然后运行下面的命令安装系统

```php
php think laket-admin:install
```

运行完命令后，你可以找到 `config/laket.php` 配置文件

3. 后台地址 `http://yourdomain.com/admin/index`, 登录账号：`admin` 及密码 `123456`


### 闪存推荐

| 名称 | 描述 |
| --- | --- |
| [系统设置](https://github.com/deatil/laket-settings) | 添加系统的设置功能 |
| [操作日志](https://github.com/deatil/laket-operation-log) | 记录管理员在后台的操作日志 |
| [数据库管理](https://github.com/deatil/laket-admin-database) | 数据库备份、优化、修复及还原 |

注：闪存插件目录默认为 `/flashs` 目录


### 问题反馈

在使用中有任何问题，请使用以下联系方式联系我们

Github: https://github.com/deatil/laket-admin


### 特别鸣谢

感谢以下的项目,排名不分先后

 - topthink/framework
 
 - composer/semver
 
 - layui


### 开源协议

*  `laket-admin` 遵循 `Apache2` 开源协议发布，在保留本系统版权的情况下提供个人及商业免费使用。 


### 版权

*  该系统所属版权归 deatil(https://github.com/deatil) 所有。
