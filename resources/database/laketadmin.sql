DROP TABLE IF EXISTS `pre__laket_admin`;
CREATE TABLE `pre__laket_admin` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `name` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '管理账号',
  `password` char(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '管理密码',
  `password_salt` char(6) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '加密因子',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '昵称',
  `email` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '头像',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `last_login_time` int(10) DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `update_ip` varchar(50) CHARACTER SET utf8mb4 DEFAULT '0' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`),
  KEY `username` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

DROP TABLE IF EXISTS `pre__laket_attachment`;
CREATE TABLE `pre__laket_attachment` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '附件关联类型',
  `type_id` char(32) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '关联类型ID',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件名',
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='附件表';

DROP TABLE IF EXISTS `pre__laket_auth_group`;
CREATE TABLE `pre__laket_auth_group` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户组id',
  `parentid` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '父组别',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '描述信息',
  `listorder` int(10) NOT NULL DEFAULT '100' COMMENT '排序ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `update_ip` varchar(50) CHARACTER SET utf8mb4 DEFAULT '0' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`)
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
  `parentid` char(32) CHARACTER SET utf8mb4 DEFAULT '0' COMMENT '上级ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `url` varchar(200) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '访问地址',
  `method` varchar(10) CHARACTER SET utf8mb4 NOT NULL DEFAULT 'GET' COMMENT '请求类型',
  `slug` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `icon` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图标',
  `remark` varchar(255) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '提示',
  `listorder` int(10) NOT NULL DEFAULT '100' COMMENT '排序ID',
  `menu_show` tinyint(1) DEFAULT '1' COMMENT '菜单显示',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `update_ip` varchar(50) CHARACTER SET utf8mb4 DEFAULT '0' COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`),
  KEY `module` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='规则表';

DROP TABLE IF EXISTS `pre__laket_auth_rule_access`;
CREATE TABLE `pre__laket_auth_rule_access` (
  `group_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `rule_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  UNIQUE KEY `rule_id` (`rule_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='用户组与权限关联表';

DROP TABLE IF EXISTS `pre__laket_flash`;
CREATE TABLE `pre__laket_flash` (
  `id` char(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `name` varchar(160) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '扩展包名',
  `title` varchar(250) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '名称',
  `homepage` varchar(200) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '扩展地址',
  `keywords` varchar(200) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '关键字',
  `description` mediumtext CHARACTER SET utf8mb4 COMMENT '描述',
  `authors` text CHARACTER SET utf8mb4 NOT NULL COMMENT '作者',
  `version` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '版本',
  `adaptation` varchar(50) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '适配系统版本',
  `bind_service` text CHARACTER SET utf8mb4 NOT NULL COMMENT '绑定服务',
  `setting` mediumtext CHARACTER SET utf8mb4 COMMENT '配置设置信息',
  `setting_data` text CHARACTER SET utf8mb4 COMMENT '配置结果信息',
  `listorder` int(10) DEFAULT '100' COMMENT '排序',
  `install_time` int(10) DEFAULT '0' COMMENT '安装时间',
  `upgrade_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `update_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '更新IP',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `add_ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '添加IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='已安装闪存列表';

REPLACE INTO `pre__laket_admin` (`id`,`name`,`password`,`password_salt`,`nickname`,`email`,`avatar`,`status`,`last_login_time`,`last_login_ip`,`update_time`,`update_ip`,`add_time`,`add_ip`) VALUES ('dbe97f21a69f67fb361b0be64988ee59','lake','c37866fe5f87adae565cb915d579f216','HwOgkR','Lake','lake@qq.com','d0633455bf755b408cbc4a6b4fe2400c',1,1616257042,'127.0.0.1',1616240760,'127.0.0.1',1564415458,'2130706433'),('e92ba0a3f86f4a5693d8487eb8c632b5','admin','82b73cc50afcfdd146cc20d631864390','PaBQfr','管理员','lake-admin@qq.com','531f329e1a237dc26d9e1aabdd39cd1f',1,1616257118,'127.0.0.1',0,'0',1564667925,'2130706433');
REPLACE INTO `pre__laket_auth_group` (`id`,`parentid`,`title`,`description`,`listorder`,`status`,`update_time`,`update_ip`,`add_time`,`add_ip`) VALUES ('26d9697f66e341d56af023423d8718b3','538a712299e0ba6011aaf63f2a1317f4','编辑','网站编辑，包括对文章的添加编辑等',105,1,1616240778,'127.0.0.1',0,''),('538a712299e0ba6011aaf63f2a1317f4','0','超级管理员','拥有所有权限',95,1,0,'0',0,'');
REPLACE INTO `pre__laket_auth_rule` (`id`,`parentid`,`title`,`url`,`method`,`slug`,`icon`,`remark`,`listorder`,`menu_show`,`status`,`update_time`,`update_ip`,`add_time`,`add_ip`) VALUES ('011fb80f96970904d07725d7587d0047','ef07d2b56a46a1b656689093060ca242','菜单列表','admin/auth-rule/index','GET','admin.auth-rule.index','','',5,0,1,0,'0',0,''),('0c744d1fbfa4155b89124a789bee65a3','157155dd3aa0a5dd5d218b21cea203ea','控制面板','admin/index/main','GET','admin.index.main','','',15,0,1,0,'0',0,''),('0f95a02350a57a0d7601c17830be880f','e0bd1dabdf99bd59a4b0d044902a123b','更改密码','admin/profile/password','POST','admin.profile.password-post','','',31,0,1,0,'0',0,''),('11db4925d60d0744426847b66071e593','637324c3794352e2ce554aa9869f365e','附件删除','admin/attachment/delete','POST','admin.attachment.delete','','',10,0,1,0,'0',0,''),('1312e1dcbd1860723805e9a332a4b884','ef07d2b56a46a1b656689093060ca242','菜单状态','admin/auth-rule/setstate','POST','admin.auth-rule.setstate','','',45,0,1,0,'0',0,''),('1329fe0776cdfa8cc43b8fdf3c79ce26','ef07d2b56a46a1b656689093060ca242','新增菜单','admin/auth-rule/add','POST','admin.auth-rule.add-post','','',11,0,1,0,'0',0,''),('13d503862b23386e01bb576800f7245f','a7e2a306ff2204effe9f76620f452f39','用户组','admin/auth-group/index','GET','admin.auth-group.index','icon-group','',15,1,1,0,'0',0,''),('157155dd3aa0a5dd5d218b21cea203ea','0','控制台','','OPTIONS','','icon-homepage','',10010,0,1,1616513330,'127.0.0.1',0,''),('175029224681e70ffa83d1a2b3afc47f','157155dd3aa0a5dd5d218b21cea203ea','附件上传','admin/attachment/upload','POST','admin.attachment.upload','','',55,0,1,0,'0',0,''),('183844a1ba05e08872679737123c82e3','79a1c5e8d0109f00257b84472e13efd1','闪存详情','admin/flash/view','GET','admin.flash.view','','',35,0,1,0,'0',0,''),('22267f1859bb6d54f104075342166fde','157155dd3aa0a5dd5d218b21cea203ea','缓存更新','admin/index/clear','POST','admin.index.clear','','',20,0,1,0,'0',0,''),('24d3c7e1f6b81c2ac19550ff83318ba8','637324c3794352e2ce554aa9869f365e','附件列表','admin/attachment/index','GET','admin.attachment.index','','',5,0,1,0,'0',0,''),('24ee1bcb5c59d23ef80dee1318ca038e','ebff38dd2f5cdd54761f855ebdc9074a','更改密码','admin/admin/password','POST','admin.admin.password-post','','',21,0,1,0,'0',0,''),('2585f778538bfc82540effb40264d2d0','ebff38dd2f5cdd54761f855ebdc9074a','管理员列表','admin/admin/index','POST','admin.admin.index-post','','',6,0,1,0,'0',0,''),('291c040f603086f5130d2ea5caac2853','79a1c5e8d0109f00257b84472e13efd1','闪存排序','admin/flash/listorder','POST','admin.flash.listorder','','',50,0,1,0,'0',1616173423,'127.0.0.1'),('30e18040ab03310a2d5de28ff1fe7969','79a1c5e8d0109f00257b84472e13efd1','闪存禁用','admin/flash/disable','POST','admin.flash.disable','','',45,0,1,0,'0',0,''),('3761fc2344c50bc9174e40125a0c3976','79a1c5e8d0109f00257b84472e13efd1','本地闪存','admin/flash/local','GET','admin.flash.local','','',7,0,1,0,'0',0,''),('37aa3b209596f6773651441276e2e92d','ebff38dd2f5cdd54761f855ebdc9074a','添加管理员','admin/admin/add','POST','admin.admin.add-post','','',8,0,1,0,'0',0,''),('3e275652992372c676f74a25614c1f22','79a1c5e8d0109f00257b84472e13efd1','闪存卸载','admin/flash/uninstall','POST','admin.flash.uninstall','','',16,0,1,0,'0',0,''),('407b20d09bc520e8db63309643bf2ac6','637324c3794352e2ce554aa9869f365e','附件列表','admin/attachment/index','POST','admin.attachment.index-post','','',6,0,1,0,'0',0,''),('414f55a603a8374e85fb19cc2b7b2735','637324c3794352e2ce554aa9869f365e','附件详情','admin/attachment/view','GET','admin.attachment.view','','',8,0,1,0,'0',0,''),('47b837912a59370eced195809212cb47','ef07d2b56a46a1b656689093060ca242','菜单排序','admin/auth-rule/listorder','POST','admin.auth-rule.listorder','','',25,0,1,0,'0',0,''),('50ed938a3f32d701e1144c3a5f59ca29','ebff38dd2f5cdd54761f855ebdc9074a','管理员列表','admin/admin/index','GET','admin.admin.index','','',5,0,1,0,'0',0,''),('58b79db312db5ee3e93101b3acaa601c','ef07d2b56a46a1b656689093060ca242','菜单列表','admin/auth-rule/index','POST','admin.auth-rule.index-post','','',6,0,1,0,'0',0,''),('637324c3794352e2ce554aa9869f365e','f99ce69498e9bb3e12d7f18f2b8d603a','附件管理','admin/attachment/index','GET','admin.attachment.index','icon-accessory','',10,1,1,0,'0',0,''),('68335126b4d2f63e7202c9774b2c15ad','13d503862b23386e01bb576800f7245f','编辑用户组','admin/auth-group/edit','GET','admin.auth-group.edit','','',20,0,1,0,'0',0,''),('68f003a9f280ffe8bea9a2100a363723','157155dd3aa0a5dd5d218b21cea203ea','账号信息','admin/profile/index','GET','admin.profile.index','','',25,0,1,0,'0',0,''),('6927499efce07e4939ec634cce0fa480','7beb74362d2c7363d01c1f0134115585','解锁屏幕','admin/passport/unlockscreen','POST','admin.passport.unlockscreen','','',15,0,1,0,'0',0,''),('79a1c5e8d0109f00257b84472e13efd1','f99ce69498e9bb3e12d7f18f2b8d603a','闪存插件','admin/flash/index','GET','admin.flash.index','icon-mokuaishezhi','',30,1,1,0,'0',0,''),('7a360a4393162d6ffb44c3ee452acc8c','13d503862b23386e01bb576800f7245f','用户组列表','admin/auth-group/index','GET','admin.auth-group.index','','',5,0,1,0,'0',0,''),('7beb74362d2c7363d01c1f0134115585','157155dd3aa0a5dd5d218b21cea203ea','锁定屏幕','admin/passport/lockscreen','POST','admin.passport.lockscreen','','',35,0,1,0,'0',0,''),('87abee44293a676dc0ad98419c0a54f6','79a1c5e8d0109f00257b84472e13efd1','闪存启用','admin/flash/enable','POST','admin.flash.enable','','',40,0,1,0,'0',0,''),('89bdfb77700ac902108a2139ee610cd0','79a1c5e8d0109f00257b84472e13efd1','闪存更新','admin/flash/upgrade','POST','admin.flash.upgrade','','',21,0,1,0,'0',0,''),('8b9844f730c66fa81c3b3f85fc28449c','68f003a9f280ffe8bea9a2100a363723','账号信息','admin/profile/index','GET','admin.profile.index','','',5,0,1,0,'0',0,''),('9364ab35a553feb99bfdde72fbdc229e','13d503862b23386e01bb576800f7245f','删除用户组','admin/auth-group/delete','POST','admin.auth-group.delete','','',30,0,1,0,'0',0,''),('997bfac935e680bba80121dbf1f6a8e0','ebff38dd2f5cdd54761f855ebdc9074a','添加管理员','admin/admin/add','GET','admin.admin.add','','',7,0,1,0,'0',0,''),('9caf84fe6f59d57924c7f4a89cf6fa19','ebff38dd2f5cdd54761f855ebdc9074a','管理员详情','admin/admin/view','GET','admin.admin.view','','',15,0,1,0,'0',0,''),('a7e2a306ff2204effe9f76620f452f39','0','权限管理','','OPTIONS','','icon-guanliyuan','',10020,1,1,1616512987,'127.0.0.1',0,''),('b232de38130130dcaffb9aa8006c18e1','ef07d2b56a46a1b656689093060ca242','编辑菜单','admin/auth-rule/edit','GET','admin.auth-rule.edit','','',15,0,1,0,'0',0,''),('c0e834dc0bec7e69f15d1d967c577253','79a1c5e8d0109f00257b84472e13efd1','闪存列表','admin/flash/index','GET','admin.flash.index','','',5,0,1,0,'0',0,''),('c5db8934a7efb788655f360380725cc8','7beb74362d2c7363d01c1f0134115585','锁定屏幕','admin/passport/lockscreen','POST','admin.passport.lockscreen','','',5,0,1,0,'0',0,''),('c7742f8c6567af98670ba88810e97cc1','ebff38dd2f5cdd54761f855ebdc9074a','编辑管理员','admin/admin/edit','POST','admin.admin.edit-post','','',11,0,1,0,'0',0,''),('c943c6346550e302c72ca8c7332b05c4','13d503862b23386e01bb576800f7245f','访问授权','admin/auth-group/access','POST','admin.auth-group.access-post','','',8,0,1,0,'0',0,''),('ce37c3ca21be4695556e360083852416','157155dd3aa0a5dd5d218b21cea203ea','管理首页','admin/index/index','GET','admin.index.index','','',10,0,1,0,'0',0,''),('d1dd4f64e1c34ec7f6daabb9a5763232','ebff38dd2f5cdd54761f855ebdc9074a','更改密码','admin/admin/password','GET','admin.admin.password','','',20,0,1,0,'0',0,''),('d1f58d220f2dbb9daebf7a165de71c2c','13d503862b23386e01bb576800f7245f','添加用户组','admin/auth-group/create','GET','admin.auth-group.create','','',10,0,1,0,'0',0,''),('d1fc5dda9550ae957c22fc19ce1eaabe','ef07d2b56a46a1b656689093060ca242','删除菜单','admin/auth-rule/delete','POST','admin.auth-rule.delete','','',20,0,1,0,'0',0,''),('d86dfd1f85e0c3c692c32cdbb554b702','13d503862b23386e01bb576800f7245f','用户组列表','admin/auth-group/index','POST','admin.auth-group.index-post','','',6,0,1,0,'0',0,''),('dc3d98d8e1fa9ccb4ec692a80448aadd','ef07d2b56a46a1b656689093060ca242','菜单显示','admin/auth-rule/setmenu','POST','admin.auth-rule.setmenu','','',35,0,1,0,'0',0,''),('ddf35a1cd69cdd08e8420a2554299c49','79a1c5e8d0109f00257b84472e13efd1','闪存安装','admin/flash/install','POST','admin.flash.install','','',11,0,1,0,'0',0,''),('e0a8efd45364f74c3424d6edd11394cb','ebff38dd2f5cdd54761f855ebdc9074a','编辑管理员','admin/admin/edit','GET','admin.admin.edit','','',10,0,1,0,'0',0,''),('e0bd1dabdf99bd59a4b0d044902a123b','157155dd3aa0a5dd5d218b21cea203ea','更改密码','admin/profile/password','GET','admin.profile.password','','',30,0,1,0,'0',0,''),('e193c4a58ea4213da111fccc8ffba82b','79a1c5e8d0109f00257b84472e13efd1','闪存列表','admin/flash/index','POST','admin.flash.index-post','','',6,0,1,0,'0',0,''),('e2e36cfb1d9ea660985b507c6efd6280','ebff38dd2f5cdd54761f855ebdc9074a','删除管理员','admin/admin/delete','POST','admin.admin.delete','','',25,0,1,0,'0',0,''),('e3b770459528c8d303367c4a5a6b9dc5','68f003a9f280ffe8bea9a2100a363723','账号信息','admin/profile/index','POST','admin.profile.index-post','','',26,0,1,0,'0',0,''),('e956545f93a2fdb2ff43198923e9b4c8','e0bd1dabdf99bd59a4b0d044902a123b','更改密码','admin/profile/password','GET','admin.profile.password','','',5,0,1,0,'0',0,''),('ebff38dd2f5cdd54761f855ebdc9074a','a7e2a306ff2204effe9f76620f452f39','管理员','admin/admin/index','GET','admin.admin.index','icon-guanliyuan','',10,1,1,0,'0',0,''),('edc679fef48d9a648d6fcc0fbc3d3d38','13d503862b23386e01bb576800f7245f','访问授权','admin/auth-group/access','GET','admin.auth-group.access','','',7,0,1,0,'0',0,''),('ef07d2b56a46a1b656689093060ca242','a7e2a306ff2204effe9f76620f452f39','权限菜单','admin/auth-rule/index','GET','admin.auth-rule.index','icon-other','',35,1,1,0,'0',0,''),('f14f17c382957cafaad1860640fa1430','13d503862b23386e01bb576800f7245f','用户组排序','admin/auth-group/listorder','POST','admin.auth-group.listorder','','',35,0,1,0,'0',0,''),('f2f5ac0096654088069d93379275b60b','13d503862b23386e01bb576800f7245f','用户组更新','admin/auth-group/update','POST','admin.auth-group.update','','',25,0,1,0,'0',0,''),('f5209acbd126ab13cdd5d3d9c670a708','ef07d2b56a46a1b656689093060ca242','编辑菜单','admin/auth-rule/edit','POST','admin.auth-rule.edit-post','','',16,0,1,0,'0',0,''),('f57659c04b2dfb3330dd019b3ceebd64','ef07d2b56a46a1b656689093060ca242','新增菜单','admin/auth-rule/add','GET','admin.auth-rule.add','','',10,0,1,0,'0',0,''),('f99ce69498e9bb3e12d7f18f2b8d603a','0','系统管理','','OPTIONS','','icon-zidongxiufu','',10030,1,1,1616512998,'127.0.0.1',0,''),('fb324b2f7eea95c6e7cfc42244c89eb4','13d503862b23386e01bb576800f7245f','用户组写入','admin/auth-group/write','POST','admin.auth-group.write','','',15,0,1,0,'0',0,'');
