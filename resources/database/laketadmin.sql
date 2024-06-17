DROP TABLE IF EXISTS `pre__laket_admin`;
CREATE TABLE `pre__laket_admin` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '管理账号',
  `password` char(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '管理密码',
  `password_salt` char(6) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '加密因子',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `email` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `avatar` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `last_login_time` int(10) DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `update_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`),
  KEY `username` (`name`),
  KEY `email` (`email`),
  KEY `nickname` (`nickname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

DROP TABLE IF EXISTS `pre__laket_attachment`;
CREATE TABLE `pre__laket_attachment` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '附件关联类型',
  `type_id` char(32) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '关联类型ID',
  `name` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文件名',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件路径',
  `mime` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `ext` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件类型',
  `size` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'sha1 散列值',
  `driver` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public' COMMENT '上传驱动',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '上传时间',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`type_id`),
  KEY `md5` (`md5`),
  KEY `sha1` (`sha1`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='附件表';

DROP TABLE IF EXISTS `pre__laket_auth_group`;
CREATE TABLE `pre__laket_auth_group` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户组id',
  `parentid` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '父组别',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '描述信息',
  `listorder` int(10) DEFAULT '100' COMMENT '排序ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `update_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`),
  KEY `parentid` (`parentid`),
  KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限组表';

DROP TABLE IF EXISTS `pre__laket_auth_group_access`;
CREATE TABLE `pre__laket_auth_group_access` (
  `admin_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `group_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  UNIQUE KEY `admin_id` (`admin_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员与用户组关联表';

DROP TABLE IF EXISTS `pre__laket_auth_rule`;
CREATE TABLE `pre__laket_auth_rule` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规则id',
  `parentid` char(32) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '上级ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '访问地址',
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GET' COMMENT '请求类型',
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `icon` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图标',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '提示',
  `listorder` int(10) DEFAULT '100' COMMENT '排序ID',
  `menu_show` tinyint(1) DEFAULT '1' COMMENT '菜单显示',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `update_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`),
  KEY `parentid` (`parentid`),
  KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='规则表';

DROP TABLE IF EXISTS `pre__laket_auth_rule_access`;
CREATE TABLE `pre__laket_auth_rule_access` (
  `group_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `rule_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  UNIQUE KEY `rule_id` (`rule_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='用户组与权限关联表';

DROP TABLE IF EXISTS `pre__laket_flash`;
CREATE TABLE `pre__laket_flash` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件包名',
  `title` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `homepage` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '插件地址',
  `keywords` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '关键字',
  `description` mediumtext COLLATE utf8mb4_unicode_ci COMMENT '描述',
  `authors` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '作者',
  `version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '版本',
  `adaptation` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '适配系统版本',
  `require` text COLLATE utf8mb4_unicode_ci COMMENT '依赖插件',
  `bind_service` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '绑定服务',
  `setting` mediumtext COLLATE utf8mb4_unicode_ci COMMENT '配置设置信息',
  `setting_data` text COLLATE utf8mb4_unicode_ci COMMENT '配置结果信息',
  `listorder` int(10) DEFAULT '100' COMMENT '排序',
  `install_time` int(10) DEFAULT '0' COMMENT '安装时间',
  `upgrade_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `update_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='已安装闪存列表';

REPLACE INTO `pre__laket_admin` (`id`,`name`,`password`,`password_salt`,`nickname`,`email`,`avatar`,`status`,`last_login_time`,`last_login_ip`,`update_time`,`update_ip`,`add_time`,`add_ip`) VALUES ('dbe97f21a69f67fb361b0be64988ee59','laket','c37866fe5f87adae565cb915d579f216','HwOgkR','Lake','laket@qq.com','d0633455bf755b408cbc4a6b4fe2400c',1,1616257042,'127.0.0.1',1616240760,'127.0.0.1',1564667927,'127.0.0.1'),('e92ba0a3f86f4a5693d8487eb8c632b5','admin','82b73cc50afcfdd146cc20d631864390','PaBQfr','管理员','lake-admin@qq.com','531f329e1a237dc26d9e1aabdd39cd1f',1,1616257118,'127.0.0.1',0,'0',1564667925,'127.0.0.1');
REPLACE INTO `pre__laket_auth_group` (`id`,`parentid`,`title`,`description`,`listorder`,`status`,`update_time`,`update_ip`,`add_time`,`add_ip`) VALUES ('26d9697f66e341d56af023423d8718b3','538a712299e0ba6011aaf63f2a1317f4','编辑','网站编辑，包括对文章的添加编辑等',105,1,1616240778,'127.0.0.1',0,''),('538a712299e0ba6011aaf63f2a1317f4','0','超级管理员','拥有所有权限',95,1,0,'0',0,'');
REPLACE INTO `pre__laket_auth_rule` (`id`,`parentid`,`title`,`url`,`method`,`slug`,`icon`,`remark`,`listorder`,`menu_show`,`status`,`update_time`,`update_ip`,`add_time`,`add_ip`) VALUES ('011fb80f96970904d07725d7587d0047','ef07d2b56a46a1b656689093060ca242','权限列表','auth-rule/index','GET','admin.auth-rule.index','','',50,0,1,1717690739,'127.0.0.1',0,''),('03ed3c06c2ceae21d9d639594924d39a','79a1c5e8d0109f00257b84472e13efd1','刷新缓存','flash/refresh','POST','admin.flash.refresh','','',57,0,1,0,'0',1717868331,'127.0.0.1'),('0c744d1fbfa4155b89124a789bee65a3','157155dd3aa0a5dd5d218b21cea203ea','控制面板','index/main','GET','admin.index.main','','',15,0,1,0,'0',0,''),('0f95a02350a57a0d7601c17830be880f','e0bd1dabdf99bd59a4b0d044902a123b','更改密码','profile/password','POST','admin.profile.password-save','','',5,0,1,1717654257,'127.0.0.1',0,''),('11db4925d60d0744426847b66071e593','637324c3794352e2ce554aa9869f365e','附件删除','attachment/delete','POST','admin.attachment.delete','','',9,0,1,0,'0',0,''),('127f14e56512a555e228214ca10ca692','ef07d2b56a46a1b656689093060ca242','全部权限数据','auth-rule/all-data','GET','admin.auth-rule.all-data','','',47,0,1,0,'0',1717662731,'127.0.0.1'),('1312e1dcbd1860723805e9a332a4b884','ef07d2b56a46a1b656689093060ca242','权限状态','auth-rule/setstate','POST','admin.auth-rule.setstate','','',32,0,1,0,'0',0,''),('1329fe0776cdfa8cc43b8fdf3c79ce26','ef07d2b56a46a1b656689093060ca242','添加权限保存','auth-rule/add','POST','admin.auth-rule.add-save','','',44,0,1,1717662559,'127.0.0.1',0,''),('13d503862b23386e01bb576800f7245f','a7e2a306ff2204effe9f76620f452f39','用户组','auth-group/index','GET','admin.auth-group.index','icon-group','',20,1,1,0,'0',0,''),('157155dd3aa0a5dd5d218b21cea203ea','0','控制台','','OPTIONS','','icon-homepage','',20,0,1,1718081330,'127.0.0.1',0,''),('175029224681e70ffa83d1a2b3afc47f','1a831aa1506502eb0c5cd6bdf395b833','附件上传','upload/file','POST','admin.upload.file','','',25,0,1,1717663643,'127.0.0.1',0,''),('183844a1ba05e08872679737123c82e3','79a1c5e8d0109f00257b84472e13efd1','插件详情','flash/view','GET','admin.flash.view','','',53,0,1,1717663285,'127.0.0.1',0,''),('1a831aa1506502eb0c5cd6bdf395b833','0','后台设置','','OPTIONS','','icon-neirongguanli','',15,0,1,1717663576,'127.0.0.1',1717663527,'127.0.0.1'),('22267f1859bb6d54f104075342166fde','157155dd3aa0a5dd5d218b21cea203ea','缓存更新','index/clear','POST','admin.index.clear','','',10,0,1,0,'0',0,''),('24d3c7e1f6b81c2ac19550ff83318ba8','637324c3794352e2ce554aa9869f365e','附件列表','attachment/index','GET','admin.attachment.index','','',15,0,1,0,'0',0,''),('24ee1bcb5c59d23ef80dee1318ca038e','ebff38dd2f5cdd54761f855ebdc9074a','更改密码保存','admin/password','POST','admin.admin.password-save','','',41,0,1,1717662268,'127.0.0.1',0,''),('2585f778538bfc82540effb40264d2d0','ebff38dd2f5cdd54761f855ebdc9074a','管理员列表数据','admin/index-data','GET','admin.admin.index-data','','',49,0,1,1718075062,'127.0.0.1',0,''),('291c040f603086f5130d2ea5caac2853','79a1c5e8d0109f00257b84472e13efd1','插件排序','flash/listorder','POST','admin.flash.listorder','','',50,0,1,1717663318,'127.0.0.1',1616173423,'127.0.0.1'),('30e18040ab03310a2d5de28ff1fe7969','79a1c5e8d0109f00257b84472e13efd1','插件禁用','flash/disable','POST','admin.flash.disable','','',51,0,1,1717663307,'127.0.0.1',0,''),('3761fc2344c50bc9174e40125a0c3976','79a1c5e8d0109f00257b84472e13efd1','本地插件','flash/local','GET','admin.flash.local','','',58,0,1,1717663242,'127.0.0.1',0,''),('37aa3b209596f6773651441276e2e92d','ebff38dd2f5cdd54761f855ebdc9074a','添加管理员保存','admin/add','POST','admin.admin.add-save','','',47,0,1,1717662302,'127.0.0.1',0,''),('3e275652992372c676f74a25614c1f22','79a1c5e8d0109f00257b84472e13efd1','插件卸载','flash/uninstall','POST','admin.flash.uninstall','','',55,0,1,1717663262,'127.0.0.1',0,''),('407b20d09bc520e8db63309643bf2ac6','637324c3794352e2ce554aa9869f365e','附件列表','attachment/index-data','GET','admin.attachment.index-data','','',14,0,1,1717654662,'127.0.0.1',0,''),('414f55a603a8374e85fb19cc2b7b2735','637324c3794352e2ce554aa9869f365e','附件详情','attachment/view','GET','admin.attachment.view','','',10,0,1,0,'0',0,''),('47b837912a59370eced195809212cb47','ef07d2b56a46a1b656689093060ca242','权限排序','auth-rule/listorder','POST','admin.auth-rule.listorder','','',34,0,1,0,'0',0,''),('50ed938a3f32d701e1144c3a5f59ca29','ebff38dd2f5cdd54761f855ebdc9074a','管理员列表','admin/index','GET','admin.admin.index','','',50,0,1,0,'0',0,''),('58b79db312db5ee3e93101b3acaa601c','ef07d2b56a46a1b656689093060ca242','权限列表数据','auth-rule/index-data','GET','admin.auth-rule.index-data','','',49,0,1,1717654582,'127.0.0.1',0,''),('637324c3794352e2ce554aa9869f365e','f99ce69498e9bb3e12d7f18f2b8d603a','附件管理','attachment/index','GET','admin.attachment.index','icon-accessory','',30,1,1,0,'0',0,''),('663fbcc0ce5b06c1ff2ed24a06d757b7','13d503862b23386e01bb576800f7245f','全部用户组','auth-group/all','GET','admin.auth-group.all','','',48,0,1,1717690650,'127.0.0.1',1717690440,'127.0.0.1'),('68335126b4d2f63e7202c9774b2c15ad','13d503862b23386e01bb576800f7245f','编辑用户组','auth-group/edit','GET','admin.auth-group.edit','','',40,0,1,0,'0',0,''),('68f003a9f280ffe8bea9a2100a363723','1a831aa1506502eb0c5cd6bdf395b833','账号信息','profile/index','GET','admin.profile.index','','',50,0,1,1717663607,'127.0.0.1',0,''),('6927499efce07e4939ec634cce0fa480','7beb74362d2c7363d01c1f0134115585','解锁屏幕','passport/unlockscreen','POST','admin.passport.unlockscreen','','',5,0,1,0,'0',0,''),('79a1c5e8d0109f00257b84472e13efd1','f99ce69498e9bb3e12d7f18f2b8d603a','闪存插件','flash/index','GET','admin.flash.index','icon-mokuaishezhi','',10,1,1,0,'0',0,''),('7a360a4393162d6ffb44c3ee452acc8c','13d503862b23386e01bb576800f7245f','用户组列表','auth-group/index','GET','admin.auth-group.index','','',50,0,1,0,'0',0,''),('7beb74362d2c7363d01c1f0134115585','1a831aa1506502eb0c5cd6bdf395b833','锁定屏幕','passport/lockscreen','POST','admin.passport.lockscreen','','',30,0,1,1717663632,'127.0.0.1',0,''),('7c5eb4613d03da930e61d003b27b0cad','ef07d2b56a46a1b656689093060ca242','全部权限','auth-rule/all','GET','admin.auth-rule.all','','',48,0,1,0,'0',1717662694,'127.0.0.1'),('87abee44293a676dc0ad98419c0a54f6','79a1c5e8d0109f00257b84472e13efd1','插件启用','flash/enable','POST','admin.flash.enable','','',52,0,1,1717663296,'127.0.0.1',0,''),('89bdfb77700ac902108a2139ee610cd0','79a1c5e8d0109f00257b84472e13efd1','插件更新','flash/upgrade','POST','admin.flash.upgrade','','',54,0,1,1717663273,'127.0.0.1',0,''),('8b9844f730c66fa81c3b3f85fc28449c','68f003a9f280ffe8bea9a2100a363723','账号信息','profile/setting','GET','admin.profile.setting','','',10,0,1,1717654237,'127.0.0.1',0,''),('9364ab35a553feb99bfdde72fbdc229e','13d503862b23386e01bb576800f7245f','删除用户组','auth-group/delete','POST','admin.auth-group.delete','','',35,0,1,0,'0',0,''),('997bfac935e680bba80121dbf1f6a8e0','ebff38dd2f5cdd54761f855ebdc9074a','添加管理员','admin/add','GET','admin.admin.add','','',48,0,1,0,'0',0,''),('9caf84fe6f59d57924c7f4a89cf6fa19','ebff38dd2f5cdd54761f855ebdc9074a','管理员详情','admin/view','GET','admin.admin.view','','',43,0,1,0,'0',0,''),('a7e2a306ff2204effe9f76620f452f39','0','权限管理','','OPTIONS','','icon-guanliyuan','',10,1,1,1616512987,'127.0.0.1',0,''),('b232de38130130dcaffb9aa8006c18e1','ef07d2b56a46a1b656689093060ca242','编辑权限','auth-rule/edit','GET','admin.auth-rule.edit','','',40,0,1,0,'0',0,''),('bb917176938d7801df81964ad0b45179','79a1c5e8d0109f00257b84472e13efd1','插件上传','flash/upload','POST','admin.flash.upload','','',47,0,1,0,'0',1717663193,'127.0.0.1'),('c0e834dc0bec7e69f15d1d967c577253','79a1c5e8d0109f00257b84472e13efd1','插件列表','flash/index','GET','admin.flash.index','','',60,0,1,1717663219,'127.0.0.1',0,''),('c125678e6db8a78c7f9cfebad79f915c','79a1c5e8d0109f00257b84472e13efd1','设置保存','flash/setting','POST','admin.flash.setting-save','','',48,0,1,0,'0',1717663136,'127.0.0.1'),('c5db8934a7efb788655f360380725cc8','7beb74362d2c7363d01c1f0134115585','锁定屏幕','passport/lockscreen','POST','admin.passport.lockscreen','','',15,0,1,0,'0',0,''),('c7742f8c6567af98670ba88810e97cc1','ebff38dd2f5cdd54761f855ebdc9074a','编辑管理员保存','admin/edit','POST','admin.admin.edit-save','','',45,0,1,1717662289,'127.0.0.1',0,''),('c943c6346550e302c72ca8c7332b05c4','13d503862b23386e01bb576800f7245f','访问授权保存','auth-group/access','POST','admin.auth-group.access-save','','',29,0,1,1717662342,'127.0.0.1',0,''),('cb7c976ff6e113556688563e3b72290b','ebff38dd2f5cdd54761f855ebdc9074a','账号授权','admin/access','GET','admin.admin.access','','',40,0,1,0,'0',1717662157,'127.0.0.1'),('ce37c3ca21be4695556e360083852416','157155dd3aa0a5dd5d218b21cea203ea','管理首页','index/index','GET','admin.index.index','','',20,0,1,0,'0',0,''),('d1dd4f64e1c34ec7f6daabb9a5763232','ebff38dd2f5cdd54761f855ebdc9074a','更改密码','admin/password','GET','admin.admin.password','','',42,0,1,0,'0',0,''),('d1f58d220f2dbb9daebf7a165de71c2c','13d503862b23386e01bb576800f7245f','添加用户组','auth-group/add','GET','admin.auth-group.add','','',45,0,1,1717654498,'127.0.0.1',0,''),('d1fc5dda9550ae957c22fc19ce1eaabe','ef07d2b56a46a1b656689093060ca242','删除权限','auth-rule/delete','POST','admin.auth-rule.delete','','',35,0,1,0,'0',0,''),('d64fe3d5c8b8bc7459e04c7875874b0c','79a1c5e8d0109f00257b84472e13efd1','设置','flash/setting','GET','admin.flash.setting','','',49,0,1,0,'0',1717662932,'127.0.0.1'),('d86dfd1f85e0c3c692c32cdbb554b702','13d503862b23386e01bb576800f7245f','用户组列表数据','auth-group/index-data','GET','admin.auth-group.index-data','','',49,0,1,1718075078,'127.0.0.1',0,''),('dc3d98d8e1fa9ccb4ec692a80448aadd','ef07d2b56a46a1b656689093060ca242','菜单显示','auth-rule/setmenu','POST','admin.auth-rule.setmenu','','',33,0,1,0,'0',0,''),('ddf35a1cd69cdd08e8420a2554299c49','79a1c5e8d0109f00257b84472e13efd1','插件安装','flash/install','POST','admin.flash.install','','',56,0,1,1717663252,'127.0.0.1',0,''),('e0a8efd45364f74c3424d6edd11394cb','ebff38dd2f5cdd54761f855ebdc9074a','编辑管理员','admin/edit','GET','admin.admin.edit','','',46,0,1,0,'0',0,''),('e0bd1dabdf99bd59a4b0d044902a123b','1a831aa1506502eb0c5cd6bdf395b833','更改密码','profile/password','GET','admin.profile.password','','',35,0,1,1717663619,'127.0.0.1',0,''),('e193c4a58ea4213da111fccc8ffba82b','79a1c5e8d0109f00257b84472e13efd1','插件列表数据','flash/index-data','GET','admin.flash.index-data','','',59,0,1,1717663229,'127.0.0.1',0,''),('e2e36cfb1d9ea660985b507c6efd6280','ebff38dd2f5cdd54761f855ebdc9074a','删除管理员','admin/delete','POST','admin.admin.delete','','',44,0,1,0,'0',0,''),('e3b770459528c8d303367c4a5a6b9dc5','68f003a9f280ffe8bea9a2100a363723','账号信息','profile/setting','POST','admin.profile.setting-save','','',5,0,1,1717654214,'127.0.0.1',0,''),('e956545f93a2fdb2ff43198923e9b4c8','e0bd1dabdf99bd59a4b0d044902a123b','更改密码','profile/password','GET','admin.profile.password','','',10,0,1,0,'0',0,''),('ebff38dd2f5cdd54761f855ebdc9074a','a7e2a306ff2204effe9f76620f452f39','管理员','admin/index','GET','admin.admin.index','icon-guanliyuan','',35,1,1,0,'0',0,''),('edc679fef48d9a648d6fcc0fbc3d3d38','13d503862b23386e01bb576800f7245f','访问授权','auth-group/access','GET','admin.auth-group.access','','',30,0,1,0,'0',0,''),('eee10666362ef87a909ac69727d7e127','ebff38dd2f5cdd54761f855ebdc9074a','账号授权保存','admin/access','POST','admin.admin.access-save','','',39,0,1,0,'0',1717662214,'127.0.0.1'),('ef07d2b56a46a1b656689093060ca242','a7e2a306ff2204effe9f76620f452f39','权限菜单','auth-rule/index','GET','admin.auth-rule.index','icon-other','',10,1,1,0,'0',0,''),('f14f17c382957cafaad1860640fa1430','13d503862b23386e01bb576800f7245f','用户组排序','auth-group/listorder','POST','admin.auth-group.listorder','','',34,0,1,0,'0',0,''),('f2f5ac0096654088069d93379275b60b','13d503862b23386e01bb576800f7245f','编辑用户组保存','auth-group/edit','POST','admin.auth-group.edit-save','','',39,0,1,1717662425,'127.0.0.1',0,''),('f5209acbd126ab13cdd5d3d9c670a708','ef07d2b56a46a1b656689093060ca242','编辑权限保存','auth-rule/edit','POST','admin.auth-rule.edit-save','','',39,0,1,1717662576,'127.0.0.1',0,''),('f54a5bd2e4caea1c4c9761c8ef9e35d0','13d503862b23386e01bb576800f7245f','全部用户组数据','auth-group/all-data','GET','admin.auth-group.all-data','','',47,0,1,1717690662,'127.0.0.1',1717690482,'127.0.0.1'),('f57659c04b2dfb3330dd019b3ceebd64','ef07d2b56a46a1b656689093060ca242','添加权限','auth-rule/add','GET','admin.auth-rule.add','','',45,0,1,0,'0',0,''),('f99ce69498e9bb3e12d7f18f2b8d603a','0','系统管理','','OPTIONS','','icon-zidongxiufu','',5,1,1,1616512998,'127.0.0.1',0,''),('fb324b2f7eea95c6e7cfc42244c89eb4','13d503862b23386e01bb576800f7245f','添加用户组保存','auth-group/add','POST','admin.auth-group.add-save','','',44,0,1,1717662442,'127.0.0.1',0,'');
