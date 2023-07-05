# laket-admin 通用PHP后台管理系统


## 项目介绍

*  `laket-admin` 是基于 `Thinkphp` 的PHP通用后台管理系统
*  使用 `layui` 搭建的后台管理界面
*  通过系统构建的插件系统完成对项目的开发
*  插件说明文档 `docs/wiki/flash.md`


## 环境要求

 - PHP >= 7.1.0
 - Thinkphp >= 8.0
 - Fileinfo PHP Extension


## 截图预览

![LaketAdmin](https://user-images.githubusercontent.com/24578855/118827262-cc0c0880-b8ee-11eb-9e5f-6c9c5adc24db.png)
![LaketAdmin-admin](https://user-images.githubusercontent.com/24578855/118827273-cf06f900-b8ee-11eb-8cde-40c85d83ca94.png)
![LaketAdmin-flash](https://user-images.githubusercontent.com/24578855/118827296-d3331680-b8ee-11eb-87eb-bcdf5c8cdd3d.png)
![LaketAdmin-group](https://user-images.githubusercontent.com/24578855/118827300-d4644380-b8ee-11eb-99a3-6e7f8faec8ff.png)
![LaketAdmin-rule](https://user-images.githubusercontent.com/24578855/118827328-d928f780-b8ee-11eb-996a-211bd7bff961.png)


## 安装步骤

1. 首先安装 `thinkphp`

```php
composer create-project topthink/think laket-admin 8.* && cd laket-admin
```

2. 配置数据库的连接信息，并确认能够正常连接数据库

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

运行完命令后，你可以找到 `config/laket.php` 配置文件

5. 后台地址 `http://yourdomain.com/admin/index`, 登录账号：`admin` 及密码 `123456`


## 闪存推荐

| 名称 | 描述 |
| --- | --- |
| [系统设置](https://github.com/deatil/laket-settings) | 添加系统的设置功能 |
| [操作日志](https://github.com/deatil/laket-operation-log) | 记录管理员在后台的操作日志 |
| [数据库管理](https://github.com/deatil/laket-admin-database) | 数据库备份、优化、修复及还原 |
| [百度编辑器](https://github.com/deatil/laket-ueditor) | 增强闪存插件设置，增强设置闪存插件 |
| [闪存插件禁用](https://github.com/deatil/laket-flash-disable) | 禁用后插件将不能进行相关的操作 |

注：闪存插件目录默认为 `/flashs` 目录


## 问题反馈

在使用中有任何问题，请使用以下联系方式联系我们

Github: https://github.com/deatil/laket-admin


## 特别鸣谢

感谢以下的项目,排名不分先后

 - topthink/framework
 
 - composer/semver
 
 - layui


## 开源协议

*  `laket-admin` 遵循 `Apache2` 开源协议发布，在保留本系统版权的情况下提供个人及商业免费使用。 


## 版权

*  该系统所属版权归 deatil(https://github.com/deatil) 所有。
