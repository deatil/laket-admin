# Host: localhost  (Version: 5.5.53)
# Date: 2021-03-19 14:25:55
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "laket_laket_admin"
#

DROP TABLE IF EXISTS `laket_laket_admin`;
CREATE TABLE `laket_laket_admin` (
  `id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户ID',
  `name` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '管理账号',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '管理密码',
  `password_salt` varchar(6) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '加密因子',
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

#
# Data for table "laket_laket_admin"
#

/*!40000 ALTER TABLE `laket_laket_admin` DISABLE KEYS */;
INSERT INTO `laket_laket_admin` VALUES ('dbe97f21a69f67fb361b0be64988ee59','lake','afb7fd3428134349b3b6f390a09d798a','MeVIoT','Lake','lake@qq.com','d0633455bf755b408cbc4a6b4fe2400c',1,1577596275,'2130706433',0,'0',1564415458,'2130706433'),('e92ba0a3f86f4a5693d8487eb8c632b5','admin','82b73cc50afcfdd146cc20d631864390','PaBQfr','管理员','lake-admin@qq.com','531f329e1a237dc26d9e1aabdd39cd1f',1,1616132474,'127.0.0.1',0,'0',1564667925,'2130706433');
/*!40000 ALTER TABLE `laket_laket_admin` ENABLE KEYS */;

#
# Structure for table "laket_laket_attachment"
#

DROP TABLE IF EXISTS `laket_laket_attachment`;
CREATE TABLE `laket_laket_attachment` (
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

#
# Data for table "laket_laket_attachment"
#

/*!40000 ALTER TABLE `laket_laket_attachment` DISABLE KEYS */;
INSERT INTO `laket_laket_attachment` VALUES ('531f329e1a237dc26d9e1aabdd39cd1f','admin','e92ba0a3f86f4a5693d8487eb8c632b5','Penguins.jpg','images/20210319\\24d7f2963941654a425629ef9072d54e.jpg','image/jpeg','jpg','777835','9d377b10ce778c4938b3c7e2c63a229a','df7be9dc4f467187783aca68c7ce98e4df2172d0','public',1,1616129653,1616125759,1616125759,'127.0.0.1'),('d0633455bf755b408cbc4a6b4fe2400c','admin','e92ba0a3f86f4a5693d8487eb8c632b5','Tulips.jpg','images/20210319\\877ec01dc335c683124ba43464391e49.jpg','image/jpeg','jpg','620888','fafa5efeaf3cbe3b23b2748d13e629a1','54c2f1a1eb6f12d681a5c7078421a5500cee02ad','public',1,1616129659,1616129659,1616129659,'127.0.0.1');
/*!40000 ALTER TABLE `laket_laket_attachment` ENABLE KEYS */;

#
# Structure for table "laket_laket_auth_group"
#

DROP TABLE IF EXISTS `laket_laket_auth_group`;
CREATE TABLE `laket_laket_auth_group` (
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

#
# Data for table "laket_laket_auth_group"
#

/*!40000 ALTER TABLE `laket_laket_auth_group` DISABLE KEYS */;
INSERT INTO `laket_laket_auth_group` VALUES ('26d9697f66e341d56af023423d8718b3','538a712299e0ba6011aaf63f2a1317f4','编辑','网站编辑，包括对文章的添加编辑等',105,1,0,'0',0,''),('538a712299e0ba6011aaf63f2a1317f4','0','超级管理员','拥有所有权限',95,1,0,'0',0,'');
/*!40000 ALTER TABLE `laket_laket_auth_group` ENABLE KEYS */;

#
# Structure for table "laket_laket_auth_group_access"
#

DROP TABLE IF EXISTS `laket_laket_auth_group_access`;
CREATE TABLE `laket_laket_auth_group_access` (
  `id` char(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '0',
  `admin_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `group_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  UNIQUE KEY `admin_id` (`admin_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员与用户组关联表';

#
# Data for table "laket_laket_auth_group_access"
#

/*!40000 ALTER TABLE `laket_laket_auth_group_access` DISABLE KEYS */;
INSERT INTO `laket_laket_auth_group_access` VALUES ('d6ce33519dd17872c4d74a382a53cf90','dbe97f21a69f67fb361b0be64988ee59','538a712299e0ba6011aaf63f2a1317f4');
/*!40000 ALTER TABLE `laket_laket_auth_group_access` ENABLE KEYS */;

#
# Structure for table "laket_laket_auth_rule"
#

DROP TABLE IF EXISTS `laket_laket_auth_rule`;
CREATE TABLE `laket_laket_auth_rule` (
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

#
# Data for table "laket_laket_auth_rule"
#

/*!40000 ALTER TABLE `laket_laket_auth_rule` DISABLE KEYS */;
INSERT INTO `laket_laket_auth_rule` VALUES ('011fb80f96970904d07725d7587d0047','ef07d2b56a46a1b656689093060ca242','菜单列表','admin/auth-rule/index','GET','admin.auth-rule.index','','',5,0,1,0,'0',0,''),('0c744d1fbfa4155b89124a789bee65a3','157155dd3aa0a5dd5d218b21cea203ea','控制面板','admin/index/main','GET','','','',15,0,1,0,'0',0,''),('0f95a02350a57a0d7601c17830be880f','e0bd1dabdf99bd59a4b0d044902a123b','更改密码','admin/profile/password','POST','','','',31,0,1,0,'0',0,''),('11db4925d60d0744426847b66071e593','637324c3794352e2ce554aa9869f365e','附件删除','admin/attachment/delete','POST','admin.attachment.delete','','',10,0,1,0,'0',0,''),('1312e1dcbd1860723805e9a332a4b884','ef07d2b56a46a1b656689093060ca242','菜单状态','admin/auth-rule/setstate','POST','admin.auth-rule.setstate','','',45,0,1,0,'0',0,''),('1329fe0776cdfa8cc43b8fdf3c79ce26','ef07d2b56a46a1b656689093060ca242','新增菜单','admin/auth-rule/add','POST','admin.auth-rule.add-post','','',11,0,1,0,'0',0,''),('13d503862b23386e01bb576800f7245f','a7e2a306ff2204effe9f76620f452f39','角色','admin/auth-group/index','GET','admin.auth-group.index','icon-group','',15,1,1,0,'0',0,''),('1435662e0caace3a61967155b890eed5','79a1c5e8d0109f00257b84472e13efd1','模块更新','admin/module/upgrade','GET','','','',20,0,1,0,'0',0,''),('157155dd3aa0a5dd5d218b21cea203ea','0','后台','','','','icon-homepage','',5,0,1,0,'0',0,''),('175029224681e70ffa83d1a2b3afc47f','157155dd3aa0a5dd5d218b21cea203ea','附件上传','admin/attachment/upload','POST','admin.attachment.upload','','',55,0,1,0,'0',0,''),('183844a1ba05e08872679737123c82e3','79a1c5e8d0109f00257b84472e13efd1','模块详情','admin/module/view','GET','','','',35,0,1,0,'0',0,''),('22267f1859bb6d54f104075342166fde','157155dd3aa0a5dd5d218b21cea203ea','缓存更新','admin/index/clear','POST','','','',20,0,1,0,'0',0,''),('24d3c7e1f6b81c2ac19550ff83318ba8','637324c3794352e2ce554aa9869f365e','附件列表','admin/attachment/index','GET','admin.attachment.index','','',5,0,1,0,'0',0,''),('24ee1bcb5c59d23ef80dee1318ca038e','ebff38dd2f5cdd54761f855ebdc9074a','更改密码','admin/admin/password','POST','admin.admin.password-post','','',21,0,1,0,'0',0,''),('2585f778538bfc82540effb40264d2d0','ebff38dd2f5cdd54761f855ebdc9074a','管理员列表','admin/admin/index','POST','admin.admin.index-post','','',6,0,1,0,'0',0,''),('30e18040ab03310a2d5de28ff1fe7969','79a1c5e8d0109f00257b84472e13efd1','模块禁用','admin/module/disable','POST','','','',45,0,1,0,'0',0,''),('318e94311dc98be170fb12c9c85b6ad4','0','设置','','','','icon-setup','',10,1,1,0,'0',0,''),('3761fc2344c50bc9174e40125a0c3976','79a1c5e8d0109f00257b84472e13efd1','全部模块','admin/module/all','GET','','','',7,0,1,0,'0',0,''),('37aa3b209596f6773651441276e2e92d','ebff38dd2f5cdd54761f855ebdc9074a','添加管理员','admin/admin/add','POST','admin.admin.add-post','','',8,0,1,0,'0',0,''),('3e275652992372c676f74a25614c1f22','79a1c5e8d0109f00257b84472e13efd1','模块卸载','admin/module/uninstall','POST','','','',16,0,1,0,'0',0,''),('3f01faa0821768442702b280992a106d','79a1c5e8d0109f00257b84472e13efd1','全部模块','admin/module/all','POST','','','',8,0,1,0,'0',0,''),('407b20d09bc520e8db63309643bf2ac6','637324c3794352e2ce554aa9869f365e','附件列表','admin/attachment/index','POST','admin.attachment.index-post','','',6,0,1,0,'0',0,''),('414f55a603a8374e85fb19cc2b7b2735','637324c3794352e2ce554aa9869f365e','附件详情','admin/attachment/view','GET','admin.attachment.view','','',8,0,1,0,'0',0,''),('47b837912a59370eced195809212cb47','ef07d2b56a46a1b656689093060ca242','菜单排序','admin/auth-rule/listorder','POST','admin.auth-rule.listorder','','',25,0,1,0,'0',0,''),('50ed938a3f32d701e1144c3a5f59ca29','ebff38dd2f5cdd54761f855ebdc9074a','管理员列表','admin/admin/index','GET','admin.admin.index','','',5,0,1,0,'0',0,''),('58b79db312db5ee3e93101b3acaa601c','ef07d2b56a46a1b656689093060ca242','菜单列表','admin/auth-rule/index','POST','admin.auth-rule.index-post','','',6,0,1,0,'0',0,''),('637324c3794352e2ce554aa9869f365e','f99ce69498e9bb3e12d7f18f2b8d603a','附件管理','admin/attachment/index','GET','admin.attachment.index','icon-accessory','',10,1,1,0,'0',0,''),('65855b776c8d1a1f3802624243f58db2','79a1c5e8d0109f00257b84472e13efd1','模块安装','admin/module/install','GET','','','',10,0,1,0,'0',0,''),('68335126b4d2f63e7202c9774b2c15ad','13d503862b23386e01bb576800f7245f','编辑角色','admin/auth-group/edit','GET','admin.auth-group.edit','','',20,0,1,0,'0',0,''),('68f003a9f280ffe8bea9a2100a363723','157155dd3aa0a5dd5d218b21cea203ea','账号信息','admin/profile/index','GET','','','',25,0,1,0,'0',0,''),('6927499efce07e4939ec634cce0fa480','7beb74362d2c7363d01c1f0134115585','解锁屏幕','admin/passport/unlockscreen','POST','','','',15,0,1,0,'0',0,''),('79a1c5e8d0109f00257b84472e13efd1','f99ce69498e9bb3e12d7f18f2b8d603a','模块管理','admin/module/index','GET','','icon-mokuaishezhi','',30,1,1,0,'0',0,''),('7a360a4393162d6ffb44c3ee452acc8c','13d503862b23386e01bb576800f7245f','角色列表','admin/auth-group/index','GET','admin.auth-group.index','','',5,0,1,0,'0',0,''),('7beb74362d2c7363d01c1f0134115585','157155dd3aa0a5dd5d218b21cea203ea','锁定屏幕','admin/passport/lockscreen','POST','','','',35,0,1,0,'0',0,''),('87abee44293a676dc0ad98419c0a54f6','79a1c5e8d0109f00257b84472e13efd1','模块启用','admin/module/enable','POST','','','',40,0,1,0,'0',0,''),('89a6df3c8f24c2e85bfb096bcb416ec4','0','模块','admin/modules/index','GET','','icon-supply','',30,1,1,0,'0',0,''),('89bdfb77700ac902108a2139ee610cd0','79a1c5e8d0109f00257b84472e13efd1','模块更新','admin/module/upgrade','POST','','','',21,0,1,0,'0',0,''),('8b9844f730c66fa81c3b3f85fc28449c','68f003a9f280ffe8bea9a2100a363723','账号信息','admin/profile/index','GET','','','',5,0,1,0,'0',0,''),('9364ab35a553feb99bfdde72fbdc229e','13d503862b23386e01bb576800f7245f','删除角色','admin/auth-group/delete','POST','admin.auth-group.delete','','',30,0,1,0,'0',0,''),('997bfac935e680bba80121dbf1f6a8e0','ebff38dd2f5cdd54761f855ebdc9074a','添加管理员','admin/admin/add','GET','admin.admin.add','','',7,0,1,0,'0',0,''),('9caf84fe6f59d57924c7f4a89cf6fa19','ebff38dd2f5cdd54761f855ebdc9074a','管理员详情','admin/admin/view','GET','admin.admin.view','','',15,0,1,0,'0',0,''),('9fc4a10e76d57cb13ba74efde63e0b29','79a1c5e8d0109f00257b84472e13efd1','模块设置','admin/module/config','GET','','','',25,0,1,0,'0',0,''),('a7e2a306ff2204effe9f76620f452f39','318e94311dc98be170fb12c9c85b6ad4','权限管理','','OPTIONS','','icon-guanliyuan','',20,1,1,0,'0',0,''),('b232de38130130dcaffb9aa8006c18e1','ef07d2b56a46a1b656689093060ca242','编辑菜单','admin/auth-rule/edit','GET','admin.auth-rule.edit','','',15,0,1,0,'0',0,''),('b32ea3e69e09b4069a0a1fac6b3a5a7c','79a1c5e8d0109f00257b84472e13efd1','本地安装','admin/module/local','POST','','','',9,0,1,0,'0',0,''),('c0e834dc0bec7e69f15d1d967c577253','79a1c5e8d0109f00257b84472e13efd1','模块列表','admin/module/index','GET','','','',5,0,1,0,'0',0,''),('c5db8934a7efb788655f360380725cc8','7beb74362d2c7363d01c1f0134115585','锁定屏幕','admin/passport/lockscreen','POST','','','',5,0,1,0,'0',0,''),('c7742f8c6567af98670ba88810e97cc1','ebff38dd2f5cdd54761f855ebdc9074a','编辑管理员','admin/admin/edit','POST','admin.admin.edit-post','','',11,0,1,0,'0',0,''),('c943c6346550e302c72ca8c7332b05c4','13d503862b23386e01bb576800f7245f','访问授权','admin/auth-group/access','POST','admin.auth-group.access-post','','',8,0,1,0,'0',0,''),('ca77deba4bdac3c3146ab38f2e3b5ffb','79a1c5e8d0109f00257b84472e13efd1','模块设置','admin/module/config','POST','','','',26,0,1,0,'0',0,''),('ce37c3ca21be4695556e360083852416','157155dd3aa0a5dd5d218b21cea203ea','管理首页','admin/index/index','GET','','','',10,0,1,0,'0',0,''),('d1dd4f64e1c34ec7f6daabb9a5763232','ebff38dd2f5cdd54761f855ebdc9074a','更改密码','admin/admin/password','GET','admin.admin.password','','',20,0,1,0,'0',0,''),('d1f58d220f2dbb9daebf7a165de71c2c','13d503862b23386e01bb576800f7245f','添加角色','admin/auth-group/create','GET','admin.auth-group.create','','',10,0,1,0,'0',0,''),('d1fc5dda9550ae957c22fc19ce1eaabe','ef07d2b56a46a1b656689093060ca242','删除菜单','admin/auth-rule/delete','POST','admin.auth-rule.delete','','',20,0,1,0,'0',0,''),('d209c1fcb180d277724a72e46a3f9062','79a1c5e8d0109f00257b84472e13efd1','模块卸载','admin/module/uninstall','GET','','','',15,0,1,0,'0',0,''),('d86dfd1f85e0c3c692c32cdbb554b702','13d503862b23386e01bb576800f7245f','角色列表','admin/auth-group/index','POST','admin.auth-group.index-post','','',6,0,1,0,'0',0,''),('dc3d98d8e1fa9ccb4ec692a80448aadd','ef07d2b56a46a1b656689093060ca242','菜单显示','admin/auth-rule/setmenu','POST','admin.auth-rule.setmenu','','',35,0,1,0,'0',0,''),('ddf35a1cd69cdd08e8420a2554299c49','79a1c5e8d0109f00257b84472e13efd1','模块安装','admin/module/install','POST','','','',11,0,1,0,'0',0,''),('e0a8efd45364f74c3424d6edd11394cb','ebff38dd2f5cdd54761f855ebdc9074a','编辑管理员','admin/admin/edit','GET','admin.admin.edit','','',10,0,1,0,'0',0,''),('e0bd1dabdf99bd59a4b0d044902a123b','157155dd3aa0a5dd5d218b21cea203ea','更改密码','admin/profile/password','GET','','','',30,0,1,0,'0',0,''),('e193c4a58ea4213da111fccc8ffba82b','79a1c5e8d0109f00257b84472e13efd1','模块列表','admin/module/index','POST','','','',6,0,1,0,'0',0,''),('e2e36cfb1d9ea660985b507c6efd6280','ebff38dd2f5cdd54761f855ebdc9074a','删除管理员','admin/admin/delete','POST','admin.admin.delete','','',25,0,1,0,'0',0,''),('e3b770459528c8d303367c4a5a6b9dc5','68f003a9f280ffe8bea9a2100a363723','账号信息','admin/profile/index','POST','','','',26,0,1,0,'0',0,''),('e956545f93a2fdb2ff43198923e9b4c8','e0bd1dabdf99bd59a4b0d044902a123b','更改密码','admin/profile/password','GET','','','',5,0,1,0,'0',0,''),('ebff38dd2f5cdd54761f855ebdc9074a','a7e2a306ff2204effe9f76620f452f39','管理员','admin/admin/index','GET','admin.admin.index','icon-guanliyuan','',10,1,1,0,'0',0,''),('edc679fef48d9a648d6fcc0fbc3d3d38','13d503862b23386e01bb576800f7245f','访问授权','admin/auth-group/access','GET','admin.auth-group.access','','',7,0,1,0,'0',0,''),('ef07d2b56a46a1b656689093060ca242','a7e2a306ff2204effe9f76620f452f39','权限菜单','admin/auth-rule/index','GET','admin.auth-rule.index','icon-other','',35,1,1,0,'0',0,''),('f14f17c382957cafaad1860640fa1430','13d503862b23386e01bb576800f7245f','角色排序','admin/auth-group/listorder','POST','admin.auth-group.listorder','','',35,0,1,0,'0',0,''),('f2f5ac0096654088069d93379275b60b','13d503862b23386e01bb576800f7245f','角色更新','admin/auth-group/update','POST','admin.auth-group.update','','',25,0,1,0,'0',0,''),('f5209acbd126ab13cdd5d3d9c670a708','ef07d2b56a46a1b656689093060ca242','编辑菜单','admin/auth-rule/edit','POST','admin.auth-rule.edit-post','','',16,0,1,0,'0',0,''),('f57659c04b2dfb3330dd019b3ceebd64','ef07d2b56a46a1b656689093060ca242','新增菜单','admin/auth-rule/add','GET','admin.auth-rule.add','','',10,0,1,0,'0',0,''),('f99ce69498e9bb3e12d7f18f2b8d603a','318e94311dc98be170fb12c9c85b6ad4','系统管理','','OPTIONS','','icon-zidongxiufu','',30,1,1,0,'0',0,''),('fb324b2f7eea95c6e7cfc42244c89eb4','13d503862b23386e01bb576800f7245f','角色写入','admin/auth-group/write','POST','admin.auth-group.write','','',15,0,1,0,'0',0,'');
/*!40000 ALTER TABLE `laket_laket_auth_rule` ENABLE KEYS */;

#
# Structure for table "laket_laket_auth_rule_access"
#

DROP TABLE IF EXISTS `laket_laket_auth_rule_access`;
CREATE TABLE `laket_laket_auth_rule_access` (
  `id` char(32) CHARACTER SET utf8mb4 NOT NULL DEFAULT '0',
  `group_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `rule_id` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  UNIQUE KEY `rule_id` (`rule_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='用户组与权限关联表';

#
# Data for table "laket_laket_auth_rule_access"
#

/*!40000 ALTER TABLE `laket_laket_auth_rule_access` DISABLE KEYS */;
INSERT INTO `laket_laket_auth_rule_access` VALUES ('2dfd4bdd9391e3c0ab24cee7dd3f4064','26d9697f66e341d56af023423d8718b3','157155dd3aa0a5dd5d218b21cea203ea'),('39d180988a682a22d1ac373b3f41b91d','26d9697f66e341d56af023423d8718b3','7beb74362d2c7363d01c1f0134115585'),('fe3fcd02f147ec532ec9cc5999cbb7ff','26d9697f66e341d56af023423d8718b3','c5db8934a7efb788655f360380725cc8'),('53ea83c60c914090f4f47029bcd26374','26d9697f66e341d56af023423d8718b3','6927499efce07e4939ec634cce0fa480'),('7a3e8f9acde595d55a4f16a0d7041396','26d9697f66e341d56af023423d8718b3','89a6df3c8f24c2e85bfb096bcb416ec4');
/*!40000 ALTER TABLE `laket_laket_auth_rule_access` ENABLE KEYS */;
