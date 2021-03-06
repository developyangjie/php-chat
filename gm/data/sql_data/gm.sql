/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 50520
Source Host           : localhost:3306
Source Database       : hm_gm

Target Server Type    : MYSQL
Target Server Version : 50520
File Encoding         : 65001

Date: 2015-11-18 18:00:32
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `syx_asset`
-- ----------------------------
DROP TABLE IF EXISTS `syx_asset`;
CREATE TABLE `syx_asset` (
  `aid` bigint(20) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `filename` varchar(50) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `filepath` varchar(200) NOT NULL,
  `uploadtime` int(11) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  `meta` text,
  `suffix` varchar(50) DEFAULT NULL,
  `download_times` int(6) NOT NULL,
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_asset
-- ----------------------------

-- ----------------------------
-- Table structure for `syx_auth_access`
-- ----------------------------
DROP TABLE IF EXISTS `syx_auth_access`;
CREATE TABLE `syx_auth_access` (
  `role_id` mediumint(8) unsigned NOT NULL COMMENT '角色',
  `rule_name` varchar(255) NOT NULL COMMENT '规则唯一英文标识,全小写',
  `type` varchar(30) DEFAULT NULL COMMENT '权限规则分类，请加应用前缀,如admin_',
  KEY `role_id` (`role_id`) USING BTREE,
  KEY `rule_name` (`rule_name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_auth_access
-- ----------------------------
INSERT INTO syx_auth_access VALUES ('2', 'operate/packagetype/export_excel', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/packagetype/distribution_server', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/packagetype/add', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/packagetype/index', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'log/gmlog/index', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'admin/activity/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'admin/log/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/order/index', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/pmd/index', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'admin/order/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/marqueecolor/edit', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/marqueecolor/add', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/pmd/add', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/marqueecolor/index', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'admin/message/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/email/get_props', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/email/send_email', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'admin/operate/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'gm/user/batch_send_props', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'admin/gm/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/packagetype/add', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/packagetype/index', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'admin/activity/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/order/index', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'admin/order/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/pmd/index', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/marqueecolor/edit', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/marqueecolor/add', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/marqueecolor/index', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/pmd/add', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'admin/message/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/email/get_props', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/email/send_email', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'admin/operate/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'gm/user/batch_send_props', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'admin/gm/default', 'admin_url');
INSERT INTO syx_auth_access VALUES ('2', 'operate/packagetype/edit', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/packagetype/distribution_server', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/packagetype/export_excel', 'admin_url');
INSERT INTO syx_auth_access VALUES ('3', 'operate/packagetype/edit', 'admin_url');

-- ----------------------------
-- Table structure for `syx_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `syx_auth_rule`;
CREATE TABLE `syx_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '规则所属module',
  `type` varchar(30) NOT NULL DEFAULT '1' COMMENT '权限规则分类，请加应用前缀,如admin_',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识,全小写',
  `param` varchar(255) DEFAULT NULL COMMENT '额外url参数',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=utf8 COMMENT='权限规则表';

-- ----------------------------
-- Records of syx_auth_rule
-- ----------------------------
INSERT INTO syx_auth_rule VALUES ('1', 'Admin', 'admin_url', 'admin/content/default', null, '内容管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('2', 'Api', 'admin_url', 'api/guestbookadmin/index', null, '所有留言', '1', '');
INSERT INTO syx_auth_rule VALUES ('3', 'Api', 'admin_url', 'api/guestbookadmin/delete', null, '删除网站留言', '1', '');
INSERT INTO syx_auth_rule VALUES ('4', 'Comment', 'admin_url', 'comment/commentadmin/index', null, '评论管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('5', 'Comment', 'admin_url', 'comment/commentadmin/delete', null, '删除评论', '1', '');
INSERT INTO syx_auth_rule VALUES ('6', 'Comment', 'admin_url', 'comment/commentadmin/check', null, '评论审核', '1', '');
INSERT INTO syx_auth_rule VALUES ('7', 'Portal', 'admin_url', 'portal/adminpost/index', null, '文章管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('8', 'Portal', 'admin_url', 'portal/adminpost/listorders', null, '文章排序', '1', '');
INSERT INTO syx_auth_rule VALUES ('9', 'Portal', 'admin_url', 'portal/adminpost/top', null, '文章置顶', '1', '');
INSERT INTO syx_auth_rule VALUES ('10', 'Portal', 'admin_url', 'portal/adminpost/recommend', null, '文章推荐', '1', '');
INSERT INTO syx_auth_rule VALUES ('11', 'Portal', 'admin_url', 'portal/adminpost/move', null, '批量移动', '1', '');
INSERT INTO syx_auth_rule VALUES ('12', 'Portal', 'admin_url', 'portal/adminpost/check', null, '文章审核', '1', '');
INSERT INTO syx_auth_rule VALUES ('13', 'Portal', 'admin_url', 'portal/adminpost/delete', null, '删除文章', '1', '');
INSERT INTO syx_auth_rule VALUES ('14', 'Portal', 'admin_url', 'portal/adminpost/edit', null, '编辑文章', '1', '');
INSERT INTO syx_auth_rule VALUES ('15', 'Portal', 'admin_url', 'portal/adminpost/edit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('16', 'Portal', 'admin_url', 'portal/adminpost/add', null, '添加文章', '1', '');
INSERT INTO syx_auth_rule VALUES ('17', 'Portal', 'admin_url', 'portal/adminpost/add_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('18', 'Portal', 'admin_url', 'portal/adminterm/index', null, '分类管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('19', 'Portal', 'admin_url', 'portal/adminterm/listorders', null, '文章分类排序', '1', '');
INSERT INTO syx_auth_rule VALUES ('20', 'Portal', 'admin_url', 'portal/adminterm/delete', null, '删除分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('21', 'Portal', 'admin_url', 'portal/adminterm/edit', null, '编辑分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('22', 'Portal', 'admin_url', 'portal/adminterm/edit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('23', 'Portal', 'admin_url', 'portal/adminterm/add', null, '添加分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('24', 'Portal', 'admin_url', 'portal/adminterm/add_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('25', 'Portal', 'admin_url', 'portal/adminpage/index', null, '页面管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('26', 'Portal', 'admin_url', 'portal/adminpage/listorders', null, '页面排序', '1', '');
INSERT INTO syx_auth_rule VALUES ('27', 'Portal', 'admin_url', 'portal/adminpage/delete', null, '删除页面', '1', '');
INSERT INTO syx_auth_rule VALUES ('28', 'Portal', 'admin_url', 'portal/adminpage/edit', null, '编辑页面', '1', '');
INSERT INTO syx_auth_rule VALUES ('29', 'Portal', 'admin_url', 'portal/adminpage/edit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('30', 'Portal', 'admin_url', 'portal/adminpage/add', null, '添加页面', '1', '');
INSERT INTO syx_auth_rule VALUES ('31', 'Portal', 'admin_url', 'portal/adminpage/add_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('32', 'Admin', 'admin_url', 'admin/recycle/default', null, '回收站', '1', '');
INSERT INTO syx_auth_rule VALUES ('33', 'Portal', 'admin_url', 'portal/adminpost/recyclebin', null, '文章回收', '1', '');
INSERT INTO syx_auth_rule VALUES ('34', 'Portal', 'admin_url', 'portal/adminpost/restore', null, '文章还原', '1', '');
INSERT INTO syx_auth_rule VALUES ('35', 'Portal', 'admin_url', 'portal/adminpost/clean', null, '彻底删除', '1', '');
INSERT INTO syx_auth_rule VALUES ('36', 'Portal', 'admin_url', 'portal/adminpage/recyclebin', null, '页面回收', '1', '');
INSERT INTO syx_auth_rule VALUES ('37', 'Portal', 'admin_url', 'portal/adminpage/clean', null, '彻底删除', '1', '');
INSERT INTO syx_auth_rule VALUES ('38', 'Portal', 'admin_url', 'portal/adminpage/restore', null, '页面还原', '1', '');
INSERT INTO syx_auth_rule VALUES ('39', 'Admin', 'admin_url', 'admin/extension/default', null, '扩展工具', '1', '');
INSERT INTO syx_auth_rule VALUES ('40', 'Admin', 'admin_url', 'admin/backup/default', null, '备份管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('41', 'Admin', 'admin_url', 'admin/backup/restore', null, '数据还原', '1', '');
INSERT INTO syx_auth_rule VALUES ('42', 'Admin', 'admin_url', 'admin/backup/index', null, '数据备份', '1', '');
INSERT INTO syx_auth_rule VALUES ('43', 'Admin', 'admin_url', 'admin/backup/index_post', null, '提交数据备份', '1', '');
INSERT INTO syx_auth_rule VALUES ('44', 'Admin', 'admin_url', 'admin/backup/download', null, '下载备份', '1', '');
INSERT INTO syx_auth_rule VALUES ('45', 'Admin', 'admin_url', 'admin/backup/del_backup', null, '删除备份', '1', '');
INSERT INTO syx_auth_rule VALUES ('46', 'Admin', 'admin_url', 'admin/backup/import', null, '数据备份导入', '1', '');
INSERT INTO syx_auth_rule VALUES ('47', 'Admin', 'admin_url', 'admin/plugin/index', null, '插件管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('48', 'Admin', 'admin_url', 'admin/plugin/toggle', null, '插件启用切换', '1', '');
INSERT INTO syx_auth_rule VALUES ('49', 'Admin', 'admin_url', 'admin/plugin/setting', null, '插件设置', '1', '');
INSERT INTO syx_auth_rule VALUES ('50', 'Admin', 'admin_url', 'admin/plugin/setting_post', null, '插件设置提交', '1', '');
INSERT INTO syx_auth_rule VALUES ('51', 'Admin', 'admin_url', 'admin/plugin/install', null, '插件安装', '1', '');
INSERT INTO syx_auth_rule VALUES ('52', 'Admin', 'admin_url', 'admin/plugin/uninstall', null, '插件卸载', '1', '');
INSERT INTO syx_auth_rule VALUES ('53', 'Admin', 'admin_url', 'admin/slide/default', null, '幻灯片', '1', '');
INSERT INTO syx_auth_rule VALUES ('54', 'Admin', 'admin_url', 'admin/slide/index', null, '幻灯片管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('55', 'Admin', 'admin_url', 'admin/slide/listorders', null, '幻灯片排序', '1', '');
INSERT INTO syx_auth_rule VALUES ('56', 'Admin', 'admin_url', 'admin/slide/toggle', null, '幻灯片显示切换', '1', '');
INSERT INTO syx_auth_rule VALUES ('57', 'Admin', 'admin_url', 'admin/slide/delete', null, '删除幻灯片', '1', '');
INSERT INTO syx_auth_rule VALUES ('58', 'Admin', 'admin_url', 'admin/slide/edit', null, '编辑幻灯片', '1', '');
INSERT INTO syx_auth_rule VALUES ('59', 'Admin', 'admin_url', 'admin/slide/edit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('60', 'Admin', 'admin_url', 'admin/slide/add', null, '添加幻灯片', '1', '');
INSERT INTO syx_auth_rule VALUES ('61', 'Admin', 'admin_url', 'admin/slide/add_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('62', 'Admin', 'admin_url', 'admin/slidecat/index', null, '幻灯片分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('63', 'Admin', 'admin_url', 'admin/slidecat/delete', null, '删除分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('64', 'Admin', 'admin_url', 'admin/slidecat/edit', null, '编辑分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('65', 'Admin', 'admin_url', 'admin/slidecat/edit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('66', 'Admin', 'admin_url', 'admin/slidecat/add', null, '添加分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('67', 'Admin', 'admin_url', 'admin/slidecat/add_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('68', 'Admin', 'admin_url', 'admin/ad/index', null, '网站广告', '1', '');
INSERT INTO syx_auth_rule VALUES ('69', 'Admin', 'admin_url', 'admin/ad/toggle', null, '广告显示切换', '1', '');
INSERT INTO syx_auth_rule VALUES ('70', 'Admin', 'admin_url', 'admin/ad/delete', null, '删除广告', '1', '');
INSERT INTO syx_auth_rule VALUES ('71', 'Admin', 'admin_url', 'admin/ad/edit', null, '编辑广告', '1', '');
INSERT INTO syx_auth_rule VALUES ('72', 'Admin', 'admin_url', 'admin/ad/edit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('73', 'Admin', 'admin_url', 'admin/ad/add', null, '添加广告', '1', '');
INSERT INTO syx_auth_rule VALUES ('74', 'Admin', 'admin_url', 'admin/ad/add_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('75', 'Admin', 'admin_url', 'admin/link/index', null, '友情链接', '1', '');
INSERT INTO syx_auth_rule VALUES ('76', 'Admin', 'admin_url', 'admin/link/listorders', null, '友情链接排序', '1', '');
INSERT INTO syx_auth_rule VALUES ('77', 'Admin', 'admin_url', 'admin/link/toggle', null, '友链显示切换', '1', '');
INSERT INTO syx_auth_rule VALUES ('78', 'Admin', 'admin_url', 'admin/link/delete', null, '删除友情链接', '1', '');
INSERT INTO syx_auth_rule VALUES ('79', 'Admin', 'admin_url', 'admin/link/edit', null, '编辑友情链接', '1', '');
INSERT INTO syx_auth_rule VALUES ('80', 'Admin', 'admin_url', 'admin/link/edit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('81', 'Admin', 'admin_url', 'admin/link/add', null, '添加友情链接', '1', '');
INSERT INTO syx_auth_rule VALUES ('82', 'Admin', 'admin_url', 'admin/link/add_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('83', 'Api', 'admin_url', 'api/oauthadmin/setting', null, '第三方登陆', '1', '');
INSERT INTO syx_auth_rule VALUES ('84', 'Api', 'admin_url', 'api/oauthadmin/setting_post', null, '提交设置', '1', '');
INSERT INTO syx_auth_rule VALUES ('85', 'Admin', 'admin_url', 'admin/menu/default', null, '菜单管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('86', 'Admin', 'admin_url', 'admin/navcat/default1', null, '前台菜单', '1', '');
INSERT INTO syx_auth_rule VALUES ('87', 'Admin', 'admin_url', 'admin/nav/index', null, '菜单管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('88', 'Admin', 'admin_url', 'admin/nav/listorders', null, '前台导航排序', '1', '');
INSERT INTO syx_auth_rule VALUES ('89', 'Admin', 'admin_url', 'admin/nav/delete', null, '删除菜单', '1', '');
INSERT INTO syx_auth_rule VALUES ('90', 'Admin', 'admin_url', 'admin/nav/edit', null, '编辑菜单', '1', '');
INSERT INTO syx_auth_rule VALUES ('91', 'Admin', 'admin_url', 'admin/nav/edit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('92', 'Admin', 'admin_url', 'admin/nav/add', null, '添加菜单', '1', '');
INSERT INTO syx_auth_rule VALUES ('93', 'Admin', 'admin_url', 'admin/nav/add_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('94', 'Admin', 'admin_url', 'admin/navcat/index', null, '菜单分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('95', 'Admin', 'admin_url', 'admin/navcat/delete', null, '删除分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('96', 'Admin', 'admin_url', 'admin/navcat/edit', null, '编辑分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('97', 'Admin', 'admin_url', 'admin/navcat/edit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('98', 'Admin', 'admin_url', 'admin/navcat/add', null, '添加分类', '1', '');
INSERT INTO syx_auth_rule VALUES ('99', 'Admin', 'admin_url', 'admin/navcat/add_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('100', 'Admin', 'admin_url', 'admin/menu/index', null, '后台菜单', '1', '');
INSERT INTO syx_auth_rule VALUES ('101', 'Admin', 'admin_url', 'admin/menu/add', null, '添加菜单', '1', '');
INSERT INTO syx_auth_rule VALUES ('102', 'Admin', 'admin_url', 'admin/menu/add_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('103', 'Admin', 'admin_url', 'admin/menu/listorders', null, '后台菜单排序', '1', '');
INSERT INTO syx_auth_rule VALUES ('104', 'Admin', 'admin_url', 'admin/menu/export_menu', null, '菜单备份', '1', '');
INSERT INTO syx_auth_rule VALUES ('105', 'Admin', 'admin_url', 'admin/menu/edit', null, '编辑菜单', '1', '');
INSERT INTO syx_auth_rule VALUES ('106', 'Admin', 'admin_url', 'admin/menu/edit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('107', 'Admin', 'admin_url', 'admin/menu/delete', null, '删除菜单', '1', '');
INSERT INTO syx_auth_rule VALUES ('108', 'Admin', 'admin_url', 'admin/menu/lists', null, '所有菜单', '1', '');
INSERT INTO syx_auth_rule VALUES ('109', 'Admin', 'admin_url', 'admin/setting/default', null, '设置', '1', '');
INSERT INTO syx_auth_rule VALUES ('110', 'Admin', 'admin_url', 'admin/setting/userdefault', null, '个人信息', '1', '');
INSERT INTO syx_auth_rule VALUES ('111', 'Admin', 'admin_url', 'admin/user/userinfo', null, '修改信息', '1', '');
INSERT INTO syx_auth_rule VALUES ('112', 'Admin', 'admin_url', 'admin/user/userinfo_post', null, '修改信息提交', '1', '');
INSERT INTO syx_auth_rule VALUES ('113', 'Admin', 'admin_url', 'admin/setting/password', null, '修改密码', '1', '');
INSERT INTO syx_auth_rule VALUES ('114', 'Admin', 'admin_url', 'admin/setting/password_post', null, '提交修改', '1', '');
INSERT INTO syx_auth_rule VALUES ('115', 'Admin', 'admin_url', 'admin/setting/site', null, '网站信息', '1', '');
INSERT INTO syx_auth_rule VALUES ('116', 'Admin', 'admin_url', 'admin/setting/site_post', null, '提交修改', '1', '');
INSERT INTO syx_auth_rule VALUES ('117', 'Admin', 'admin_url', 'admin/route/index', null, '路由列表', '1', '');
INSERT INTO syx_auth_rule VALUES ('118', 'Admin', 'admin_url', 'admin/route/add', null, '路由添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('119', 'Admin', 'admin_url', 'admin/route/add_post', null, '路由添加提交', '1', '');
INSERT INTO syx_auth_rule VALUES ('120', 'Admin', 'admin_url', 'admin/route/edit', null, '路由编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('121', 'Admin', 'admin_url', 'admin/route/edit_post', null, '路由编辑提交', '1', '');
INSERT INTO syx_auth_rule VALUES ('122', 'Admin', 'admin_url', 'admin/route/delete', null, '路由删除', '1', '');
INSERT INTO syx_auth_rule VALUES ('123', 'Admin', 'admin_url', 'admin/route/ban', null, '路由禁止', '1', '');
INSERT INTO syx_auth_rule VALUES ('124', 'Admin', 'admin_url', 'admin/route/open', null, '路由启用', '1', '');
INSERT INTO syx_auth_rule VALUES ('125', 'Admin', 'admin_url', 'admin/route/listorders', null, '路由排序', '1', '');
INSERT INTO syx_auth_rule VALUES ('126', 'Admin', 'admin_url', 'admin/mailer/default', null, '邮箱配置', '1', '');
INSERT INTO syx_auth_rule VALUES ('127', 'Admin', 'admin_url', 'admin/mailer/index', null, 'SMTP配置', '1', '');
INSERT INTO syx_auth_rule VALUES ('128', 'Admin', 'admin_url', 'admin/mailer/index_post', null, '提交配置', '1', '');
INSERT INTO syx_auth_rule VALUES ('129', 'Admin', 'admin_url', 'admin/mailer/active', null, '邮件模板', '1', '');
INSERT INTO syx_auth_rule VALUES ('130', 'Admin', 'admin_url', 'admin/mailer/active_post', null, '提交模板', '1', '');
INSERT INTO syx_auth_rule VALUES ('131', 'Admin', 'admin_url', 'admin/setting/clearcache', null, '清除缓存', '1', '');
INSERT INTO syx_auth_rule VALUES ('132', 'User', 'admin_url', 'user/indexadmin/default', null, '用户管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('133', 'User', 'admin_url', 'user/indexadmin/default1', null, '用户组', '1', '');
INSERT INTO syx_auth_rule VALUES ('134', 'User', 'admin_url', 'user/indexadmin/index', null, '本站用户', '1', '');
INSERT INTO syx_auth_rule VALUES ('135', 'User', 'admin_url', 'user/indexadmin/ban', null, '拉黑会员', '1', '');
INSERT INTO syx_auth_rule VALUES ('136', 'User', 'admin_url', 'user/indexadmin/cancelban', null, '启用会员', '1', '');
INSERT INTO syx_auth_rule VALUES ('137', 'User', 'admin_url', 'user/oauthadmin/index', null, '第三方用户', '1', '');
INSERT INTO syx_auth_rule VALUES ('138', 'User', 'admin_url', 'user/oauthadmin/delete', null, '第三方用户解绑', '1', '');
INSERT INTO syx_auth_rule VALUES ('139', 'User', 'admin_url', 'user/indexadmin/default3', null, '管理组', '1', '');
INSERT INTO syx_auth_rule VALUES ('140', 'Admin', 'admin_url', 'admin/rbac/index', null, '角色管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('141', 'Admin', 'admin_url', 'admin/rbac/member', null, '成员管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('142', 'Admin', 'admin_url', 'admin/rbac/authorize', null, '权限设置', '1', '');
INSERT INTO syx_auth_rule VALUES ('143', 'Admin', 'admin_url', 'admin/rbac/authorize_post', null, '提交设置', '1', '');
INSERT INTO syx_auth_rule VALUES ('144', 'Admin', 'admin_url', 'admin/rbac/roleedit', null, '编辑角色', '1', '');
INSERT INTO syx_auth_rule VALUES ('145', 'Admin', 'admin_url', 'admin/rbac/roleedit_post', null, '提交编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('146', 'Admin', 'admin_url', 'admin/rbac/roledelete', null, '删除角色', '1', '');
INSERT INTO syx_auth_rule VALUES ('147', 'Admin', 'admin_url', 'admin/rbac/roleadd', null, '添加角色', '1', '');
INSERT INTO syx_auth_rule VALUES ('148', 'Admin', 'admin_url', 'admin/rbac/roleadd_post', null, '提交添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('149', 'Admin', 'admin_url', 'admin/user/index', null, '管理员', '1', '');
INSERT INTO syx_auth_rule VALUES ('150', 'Admin', 'admin_url', 'admin/user/delete', null, '删除管理员', '1', '');
INSERT INTO syx_auth_rule VALUES ('151', 'Admin', 'admin_url', 'admin/user/edit', null, '管理员编辑', '1', '');
INSERT INTO syx_auth_rule VALUES ('152', 'Admin', 'admin_url', 'admin/user/edit_post', null, '编辑提交', '1', '');
INSERT INTO syx_auth_rule VALUES ('153', 'Admin', 'admin_url', 'admin/user/add', null, '管理员添加', '1', '');
INSERT INTO syx_auth_rule VALUES ('154', 'Admin', 'admin_url', 'admin/user/add_post', null, '添加提交', '1', '');
INSERT INTO syx_auth_rule VALUES ('155', 'Admin', 'admin_url', 'admin/plugin/update', null, '插件更新', '1', '');
INSERT INTO syx_auth_rule VALUES ('156', 'Admin', 'admin_url', 'admin/storage/index', null, '文件存储', '1', '');
INSERT INTO syx_auth_rule VALUES ('157', 'Admin', 'admin_url', 'admin/storage/setting_post', null, '文件存储设置提交', '1', '');
INSERT INTO syx_auth_rule VALUES ('158', 'Admin', 'admin_url', 'admin/slide/ban', null, '禁用幻灯片', '1', '');
INSERT INTO syx_auth_rule VALUES ('159', 'Admin', 'admin_url', 'admin/slide/cancelban', null, '启用幻灯片', '1', '');
INSERT INTO syx_auth_rule VALUES ('160', 'Admin', 'admin_url', 'admin/user/ban', null, '禁用管理员', '1', '');
INSERT INTO syx_auth_rule VALUES ('161', 'Admin', 'admin_url', 'admin/user/cancelban', null, '启用管理员', '1', '');
INSERT INTO syx_auth_rule VALUES ('162', 'Admin', 'admin_url', 'admin/gm/default', null, '玩家管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('163', 'Gm', 'admin_url', 'gm/user/batch_send_props', null, '批量发道具', '1', '');
INSERT INTO syx_auth_rule VALUES ('164', 'Admin', 'admin_url', 'admin/game/default', null, 'GM用户管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('165', 'Game', 'admin_url', 'game/user/batch_send_props', null, '批量发道具', '1', '');
INSERT INTO syx_auth_rule VALUES ('166', 'Admin', 'admin_url', 'admin/email/default', null, '邮件管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('167', 'Email', 'admin_url', 'email/email/send_email', null, '发送邮件', '1', '');
INSERT INTO syx_auth_rule VALUES ('168', 'Admin', 'admin_url', 'admin/operate/default', null, '邮件管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('169', 'Operate', 'admin_url', 'operate/email/send_email', null, '发送邮件', '1', '');
INSERT INTO syx_auth_rule VALUES ('170', 'Admin', 'admin_url', 'admin/message/default', null, '信息管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('171', 'Operate', 'admin_url', 'operate/pmd/add', null, '跑马灯', '1', '');
INSERT INTO syx_auth_rule VALUES ('172', 'Operate', 'admin_url', 'operate/marqueecolor/index', null, 'Rgb颜色值', '1', '');
INSERT INTO syx_auth_rule VALUES ('173', 'Operate', 'admin_url', 'operate/marqueecolor/add', null, '添加rgb颜色', '1', '');
INSERT INTO syx_auth_rule VALUES ('174', 'Operate', 'admin_url', 'operate/marqueecolor/edit', null, '修改rgb颜色值', '1', '');
INSERT INTO syx_auth_rule VALUES ('175', 'Operate', 'admin_url', 'operate/pmd/index', null, '跑马灯列表', '1', '');
INSERT INTO syx_auth_rule VALUES ('176', 'Admin', 'admin_url', 'admin/order/default', null, '订单管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('177', 'Operate', 'admin_url', 'operate/order/index', null, '订单管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('178', 'Admin', 'admin_url', 'admin/log/default', null, '行为日志', '1', '');
INSERT INTO syx_auth_rule VALUES ('179', 'Log', 'admin_url', 'log/gmlog/index', null, '操作日志', '1', '');
INSERT INTO syx_auth_rule VALUES ('180', 'Admin', 'admin_url', 'admin/activity/default', null, '活动管理', '1', '');
INSERT INTO syx_auth_rule VALUES ('181', 'Operate', 'admin_url', 'operate/activity/add', null, '新增礼包', '1', '');
INSERT INTO syx_auth_rule VALUES ('182', 'Operate', 'admin_url', 'operate/activity/index', null, '礼包码列表', '1', '');
INSERT INTO syx_auth_rule VALUES ('183', 'Operate', 'admin_url', 'operate/packagetype/index', null, '礼包码列表', '1', '');
INSERT INTO syx_auth_rule VALUES ('184', 'Operate', 'admin_url', 'operate/packagetype/add', null, '新增礼包类型', '1', '');
INSERT INTO syx_auth_rule VALUES ('185', 'Operate', 'admin_url', 'operate/packagetype/distribution_server', null, '批量发送礼包码', '1', '');
INSERT INTO syx_auth_rule VALUES ('186', 'Operate', 'admin_url', 'operate/email/get_props', null, '邮件ajax加载道具', '1', '');
INSERT INTO syx_auth_rule VALUES ('187', 'Operate', 'admin_url', 'operate/packagetype/export_excel', null, '导出礼包码', '1', '');
INSERT INTO syx_auth_rule VALUES ('188', 'Operate', 'admin_url', 'operate/packagetype/edit', null, '修改礼包码', '1', '');

-- ----------------------------
-- Table structure for `syx_chat_log`
-- ----------------------------
DROP TABLE IF EXISTS `syx_chat_log`;
CREATE TABLE `syx_chat_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `msg` text NOT NULL,
  `ctime` int(10) NOT NULL,
  `start_time` int(10) NOT NULL,
  `end_time` int(10) NOT NULL,
  `interval_time` int(10) NOT NULL DEFAULT '5' COMMENT '间隔时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_chat_log
-- ----------------------------

-- ----------------------------
-- Table structure for `syx_comments`
-- ----------------------------
DROP TABLE IF EXISTS `syx_comments`;
CREATE TABLE `syx_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_table` varchar(100) NOT NULL COMMENT '评论内容所在表，不带表前缀',
  `post_id` int(11) unsigned NOT NULL DEFAULT '0',
  `url` varchar(255) DEFAULT NULL COMMENT '原文地址',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '发表评论的用户id',
  `to_uid` int(11) NOT NULL DEFAULT '0' COMMENT '被评论的用户id',
  `full_name` varchar(50) DEFAULT NULL COMMENT '评论者昵称',
  `email` varchar(255) DEFAULT NULL COMMENT '评论者邮箱',
  `createtime` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `content` text NOT NULL COMMENT '评论内容',
  `type` smallint(1) NOT NULL DEFAULT '1' COMMENT '评论类型；1实名评论',
  `parentid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '被回复的评论id',
  `path` varchar(500) DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1' COMMENT '状态，1已审核，0未审核',
  PRIMARY KEY (`id`),
  KEY `comment_post_ID` (`post_id`) USING BTREE,
  KEY `comment_approved_date_gmt` (`status`) USING BTREE,
  KEY `comment_parent` (`parentid`) USING BTREE,
  KEY `table_id_status` (`post_table`,`post_id`,`status`) USING BTREE,
  KEY `createtime` (`createtime`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_comments
-- ----------------------------

-- ----------------------------
-- Table structure for `syx_common_action_log`
-- ----------------------------
DROP TABLE IF EXISTS `syx_common_action_log`;
CREATE TABLE `syx_common_action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) DEFAULT '0' COMMENT '用户id',
  `object` varchar(100) DEFAULT NULL COMMENT '访问对象的id,格式：不带前缀的表名+id;如posts1表示xx_posts表里id为1的记录',
  `action` varchar(50) DEFAULT NULL COMMENT '操作名称；格式规定为：应用名+控制器+操作名；也可自己定义格式只要不发生冲突且惟一；',
  `count` int(11) DEFAULT '0' COMMENT '访问次数',
  `last_time` int(11) DEFAULT '0' COMMENT '最后访问的时间戳',
  `ip` varchar(15) DEFAULT NULL COMMENT '访问者最后访问ip',
  PRIMARY KEY (`id`),
  KEY `user_object_action` (`user`,`object`,`action`) USING BTREE,
  KEY `user_object_action_ip` (`user`,`object`,`action`,`ip`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_common_action_log
-- ----------------------------

-- ----------------------------
-- Table structure for `syx_gm_log`
-- ----------------------------
DROP TABLE IF EXISTS `syx_gm_log`;
CREATE TABLE `syx_gm_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `action` varchar(255) NOT NULL COMMENT '动作',
  `request` varchar(512) DEFAULT NULL,
  `params` varchar(512) DEFAULT NULL,
  `actionip` varchar(20) NOT NULL,
  `actiontime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_gm_log
-- ----------------------------

-- ----------------------------
-- Table structure for `syx_links`
-- ----------------------------
DROP TABLE IF EXISTS `syx_links`;
CREATE TABLE `syx_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) NOT NULL COMMENT '友情链接地址',
  `link_name` varchar(255) NOT NULL COMMENT '友情链接名称',
  `link_image` varchar(255) DEFAULT NULL COMMENT '友情链接图标',
  `link_target` varchar(25) NOT NULL DEFAULT '_blank' COMMENT '友情链接打开方式',
  `link_description` text NOT NULL COMMENT '友情链接描述',
  `link_status` int(2) NOT NULL DEFAULT '1',
  `link_rating` int(11) NOT NULL DEFAULT '0' COMMENT '友情链接评级',
  `link_rel` varchar(255) DEFAULT '',
  `listorder` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_links
-- ----------------------------
INSERT INTO syx_links VALUES ('1', 'http://www.thinkcmf.com', 'ThinkCMF', '', '_blank', '', '1', '0', '', '0');

-- ----------------------------
-- Table structure for `syx_mail_log`
-- ----------------------------
DROP TABLE IF EXISTS `syx_mail_log`;
CREATE TABLE `syx_mail_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL COMMENT '管理员id',
  `sid` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '服务器编号',
  `reward` varchar(512) CHARACTER SET utf8 DEFAULT NULL COMMENT '发送物品',
  `player_name` varchar(30) NOT NULL COMMENT '玩家名称',
  `send_time` int(10) unsigned DEFAULT '0' COMMENT '发送时间',
  `login_IP` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '登录IP',
  `status` tinyint(1) DEFAULT NULL COMMENT '发送状态:0:失败;1:成功',
  `type` tinyint(1) DEFAULT NULL COMMENT '1：名称，2：全服，3：UID',
  `title` varchar(500) DEFAULT NULL,
  `content` varchar(500) DEFAULT NULL,
  `uid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of syx_mail_log
-- ----------------------------

-- ----------------------------
-- Table structure for `syx_marquee_color`
-- ----------------------------
DROP TABLE IF EXISTS `syx_marquee_color`;
CREATE TABLE `syx_marquee_color` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_marquee_color
-- ----------------------------
INSERT INTO syx_marquee_color VALUES ('1', '255,192,203', '粉红');
INSERT INTO syx_marquee_color VALUES ('2', '255,180,0', '橙色');
INSERT INTO syx_marquee_color VALUES ('3', '255,255,255', '白色');
INSERT INTO syx_marquee_color VALUES ('4', '219,112,147', '脸红的淡紫红');
INSERT INTO syx_marquee_color VALUES ('5', '255,105,180', '热情的粉红');

-- ----------------------------
-- Table structure for `syx_menu`
-- ----------------------------
DROP TABLE IF EXISTS `syx_menu`;
CREATE TABLE `syx_menu` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `app` char(20) NOT NULL COMMENT '应用名称app',
  `model` char(20) NOT NULL COMMENT '控制器',
  `action` char(20) NOT NULL COMMENT '操作名称',
  `data` char(50) NOT NULL COMMENT '额外参数',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '菜单类型  1：权限认证+菜单；0：只作为菜单',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态，1显示，0不显示',
  `name` varchar(50) NOT NULL COMMENT '菜单名称',
  `icon` varchar(50) DEFAULT NULL COMMENT '菜单图标',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `listorder` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '排序ID',
  PRIMARY KEY (`id`),
  KEY `status` (`status`) USING BTREE,
  KEY `parentid` (`parentid`) USING BTREE,
  KEY `model` (`model`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=185 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_menu
-- ----------------------------
INSERT INTO syx_menu VALUES ('1', '0', 'Admin', 'Content', 'default', '', '0', '0', '内容管理', 'th', '', '30');
INSERT INTO syx_menu VALUES ('2', '1', 'Api', 'Guestbookadmin', 'index', '', '1', '1', '所有留言', '', '', '0');
INSERT INTO syx_menu VALUES ('3', '2', 'Api', 'Guestbookadmin', 'delete', '', '1', '0', '删除网站留言', '', '', '0');
INSERT INTO syx_menu VALUES ('4', '1', 'Comment', 'Commentadmin', 'index', '', '1', '1', '评论管理', '', '', '0');
INSERT INTO syx_menu VALUES ('5', '4', 'Comment', 'Commentadmin', 'delete', '', '1', '0', '删除评论', '', '', '0');
INSERT INTO syx_menu VALUES ('6', '4', 'Comment', 'Commentadmin', 'check', '', '1', '0', '评论审核', '', '', '0');
INSERT INTO syx_menu VALUES ('7', '1', 'Portal', 'AdminPost', 'index', '', '1', '1', '文章管理', '', '', '1');
INSERT INTO syx_menu VALUES ('8', '7', 'Portal', 'AdminPost', 'listorders', '', '1', '0', '文章排序', '', '', '0');
INSERT INTO syx_menu VALUES ('9', '7', 'Portal', 'AdminPost', 'top', '', '1', '0', '文章置顶', '', '', '0');
INSERT INTO syx_menu VALUES ('10', '7', 'Portal', 'AdminPost', 'recommend', '', '1', '0', '文章推荐', '', '', '0');
INSERT INTO syx_menu VALUES ('11', '7', 'Portal', 'AdminPost', 'move', '', '1', '0', '批量移动', '', '', '1000');
INSERT INTO syx_menu VALUES ('12', '7', 'Portal', 'AdminPost', 'check', '', '1', '0', '文章审核', '', '', '1000');
INSERT INTO syx_menu VALUES ('13', '7', 'Portal', 'AdminPost', 'delete', '', '1', '0', '删除文章', '', '', '1000');
INSERT INTO syx_menu VALUES ('14', '7', 'Portal', 'AdminPost', 'edit', '', '1', '0', '编辑文章', '', '', '1000');
INSERT INTO syx_menu VALUES ('15', '14', 'Portal', 'AdminPost', 'edit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('16', '7', 'Portal', 'AdminPost', 'add', '', '1', '0', '添加文章', '', '', '1000');
INSERT INTO syx_menu VALUES ('17', '16', 'Portal', 'AdminPost', 'add_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('18', '1', 'Portal', 'AdminTerm', 'index', '', '0', '1', '分类管理', '', '', '2');
INSERT INTO syx_menu VALUES ('19', '18', 'Portal', 'AdminTerm', 'listorders', '', '1', '0', '文章分类排序', '', '', '0');
INSERT INTO syx_menu VALUES ('20', '18', 'Portal', 'AdminTerm', 'delete', '', '1', '0', '删除分类', '', '', '1000');
INSERT INTO syx_menu VALUES ('21', '18', 'Portal', 'AdminTerm', 'edit', '', '1', '0', '编辑分类', '', '', '1000');
INSERT INTO syx_menu VALUES ('22', '21', 'Portal', 'AdminTerm', 'edit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('23', '18', 'Portal', 'AdminTerm', 'add', '', '1', '0', '添加分类', '', '', '1000');
INSERT INTO syx_menu VALUES ('24', '23', 'Portal', 'AdminTerm', 'add_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('25', '1', 'Portal', 'AdminPage', 'index', '', '1', '1', '页面管理', '', '', '3');
INSERT INTO syx_menu VALUES ('26', '25', 'Portal', 'AdminPage', 'listorders', '', '1', '0', '页面排序', '', '', '0');
INSERT INTO syx_menu VALUES ('27', '25', 'Portal', 'AdminPage', 'delete', '', '1', '0', '删除页面', '', '', '1000');
INSERT INTO syx_menu VALUES ('28', '25', 'Portal', 'AdminPage', 'edit', '', '1', '0', '编辑页面', '', '', '1000');
INSERT INTO syx_menu VALUES ('29', '28', 'Portal', 'AdminPage', 'edit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('30', '25', 'Portal', 'AdminPage', 'add', '', '1', '0', '添加页面', '', '', '1000');
INSERT INTO syx_menu VALUES ('31', '30', 'Portal', 'AdminPage', 'add_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('32', '1', 'Admin', 'Recycle', 'default', '', '1', '1', '回收站', '', '', '4');
INSERT INTO syx_menu VALUES ('33', '32', 'Portal', 'AdminPost', 'recyclebin', '', '1', '1', '文章回收', '', '', '0');
INSERT INTO syx_menu VALUES ('34', '33', 'Portal', 'AdminPost', 'restore', '', '1', '0', '文章还原', '', '', '1000');
INSERT INTO syx_menu VALUES ('35', '33', 'Portal', 'AdminPost', 'clean', '', '1', '0', '彻底删除', '', '', '1000');
INSERT INTO syx_menu VALUES ('36', '32', 'Portal', 'AdminPage', 'recyclebin', '', '1', '1', '页面回收', '', '', '1');
INSERT INTO syx_menu VALUES ('37', '36', 'Portal', 'AdminPage', 'clean', '', '1', '0', '彻底删除', '', '', '1000');
INSERT INTO syx_menu VALUES ('38', '36', 'Portal', 'AdminPage', 'restore', '', '1', '0', '页面还原', '', '', '1000');
INSERT INTO syx_menu VALUES ('39', '0', 'Admin', 'Extension', 'default', '', '0', '0', '扩展工具', 'cloud', '', '40');
INSERT INTO syx_menu VALUES ('40', '39', 'Admin', 'Backup', 'default', '', '1', '1', '备份管理', '', '', '0');
INSERT INTO syx_menu VALUES ('41', '40', 'Admin', 'Backup', 'restore', '', '1', '1', '数据还原', '', '', '0');
INSERT INTO syx_menu VALUES ('42', '40', 'Admin', 'Backup', 'index', '', '1', '1', '数据备份', '', '', '0');
INSERT INTO syx_menu VALUES ('43', '42', 'Admin', 'Backup', 'index_post', '', '1', '0', '提交数据备份', '', '', '0');
INSERT INTO syx_menu VALUES ('44', '40', 'Admin', 'Backup', 'download', '', '1', '0', '下载备份', '', '', '1000');
INSERT INTO syx_menu VALUES ('45', '40', 'Admin', 'Backup', 'del_backup', '', '1', '0', '删除备份', '', '', '1000');
INSERT INTO syx_menu VALUES ('46', '40', 'Admin', 'Backup', 'import', '', '1', '0', '数据备份导入', '', '', '1000');
INSERT INTO syx_menu VALUES ('47', '39', 'Admin', 'Plugin', 'index', '', '1', '1', '插件管理', '', '', '0');
INSERT INTO syx_menu VALUES ('48', '47', 'Admin', 'Plugin', 'toggle', '', '1', '0', '插件启用切换', '', '', '0');
INSERT INTO syx_menu VALUES ('49', '47', 'Admin', 'Plugin', 'setting', '', '1', '0', '插件设置', '', '', '0');
INSERT INTO syx_menu VALUES ('50', '49', 'Admin', 'Plugin', 'setting_post', '', '1', '0', '插件设置提交', '', '', '0');
INSERT INTO syx_menu VALUES ('51', '47', 'Admin', 'Plugin', 'install', '', '1', '0', '插件安装', '', '', '0');
INSERT INTO syx_menu VALUES ('52', '47', 'Admin', 'Plugin', 'uninstall', '', '1', '0', '插件卸载', '', '', '0');
INSERT INTO syx_menu VALUES ('53', '39', 'Admin', 'Slide', 'default', '', '1', '1', '幻灯片', '', '', '1');
INSERT INTO syx_menu VALUES ('54', '53', 'Admin', 'Slide', 'index', '', '1', '1', '幻灯片管理', '', '', '0');
INSERT INTO syx_menu VALUES ('55', '54', 'Admin', 'Slide', 'listorders', '', '1', '0', '幻灯片排序', '', '', '0');
INSERT INTO syx_menu VALUES ('56', '54', 'Admin', 'Slide', 'toggle', '', '1', '0', '幻灯片显示切换', '', '', '0');
INSERT INTO syx_menu VALUES ('57', '54', 'Admin', 'Slide', 'delete', '', '1', '0', '删除幻灯片', '', '', '1000');
INSERT INTO syx_menu VALUES ('58', '54', 'Admin', 'Slide', 'edit', '', '1', '0', '编辑幻灯片', '', '', '1000');
INSERT INTO syx_menu VALUES ('59', '58', 'Admin', 'Slide', 'edit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('60', '54', 'Admin', 'Slide', 'add', '', '1', '0', '添加幻灯片', '', '', '1000');
INSERT INTO syx_menu VALUES ('61', '60', 'Admin', 'Slide', 'add_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('62', '53', 'Admin', 'Slidecat', 'index', '', '1', '1', '幻灯片分类', '', '', '0');
INSERT INTO syx_menu VALUES ('63', '62', 'Admin', 'Slidecat', 'delete', '', '1', '0', '删除分类', '', '', '1000');
INSERT INTO syx_menu VALUES ('64', '62', 'Admin', 'Slidecat', 'edit', '', '1', '0', '编辑分类', '', '', '1000');
INSERT INTO syx_menu VALUES ('65', '64', 'Admin', 'Slidecat', 'edit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('66', '62', 'Admin', 'Slidecat', 'add', '', '1', '0', '添加分类', '', '', '1000');
INSERT INTO syx_menu VALUES ('67', '66', 'Admin', 'Slidecat', 'add_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('68', '39', 'Admin', 'Ad', 'index', '', '1', '1', '网站广告', '', '', '2');
INSERT INTO syx_menu VALUES ('69', '68', 'Admin', 'Ad', 'toggle', '', '1', '0', '广告显示切换', '', '', '0');
INSERT INTO syx_menu VALUES ('70', '68', 'Admin', 'Ad', 'delete', '', '1', '0', '删除广告', '', '', '1000');
INSERT INTO syx_menu VALUES ('71', '68', 'Admin', 'Ad', 'edit', '', '1', '0', '编辑广告', '', '', '1000');
INSERT INTO syx_menu VALUES ('72', '71', 'Admin', 'Ad', 'edit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('73', '68', 'Admin', 'Ad', 'add', '', '1', '0', '添加广告', '', '', '1000');
INSERT INTO syx_menu VALUES ('74', '73', 'Admin', 'Ad', 'add_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('75', '39', 'Admin', 'Link', 'index', '', '0', '1', '友情链接', '', '', '3');
INSERT INTO syx_menu VALUES ('76', '75', 'Admin', 'Link', 'listorders', '', '1', '0', '友情链接排序', '', '', '0');
INSERT INTO syx_menu VALUES ('77', '75', 'Admin', 'Link', 'toggle', '', '1', '0', '友链显示切换', '', '', '0');
INSERT INTO syx_menu VALUES ('78', '75', 'Admin', 'Link', 'delete', '', '1', '0', '删除友情链接', '', '', '1000');
INSERT INTO syx_menu VALUES ('79', '75', 'Admin', 'Link', 'edit', '', '1', '0', '编辑友情链接', '', '', '1000');
INSERT INTO syx_menu VALUES ('80', '79', 'Admin', 'Link', 'edit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('81', '75', 'Admin', 'Link', 'add', '', '1', '0', '添加友情链接', '', '', '1000');
INSERT INTO syx_menu VALUES ('82', '81', 'Admin', 'Link', 'add_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('83', '39', 'Api', 'Oauthadmin', 'setting', '', '1', '1', '第三方登陆', 'leaf', '', '4');
INSERT INTO syx_menu VALUES ('84', '83', 'Api', 'Oauthadmin', 'setting_post', '', '1', '0', '提交设置', '', '', '0');
INSERT INTO syx_menu VALUES ('85', '0', 'Admin', 'Menu', 'default', '', '1', '1', '菜单管理', 'list', '', '20');
INSERT INTO syx_menu VALUES ('86', '85', 'Admin', 'Navcat', 'default1', '', '1', '0', '前台菜单', '', '', '0');
INSERT INTO syx_menu VALUES ('87', '86', 'Admin', 'Nav', 'index', '', '1', '1', '菜单管理', '', '', '0');
INSERT INTO syx_menu VALUES ('88', '87', 'Admin', 'Nav', 'listorders', '', '1', '0', '前台导航排序', '', '', '0');
INSERT INTO syx_menu VALUES ('89', '87', 'Admin', 'Nav', 'delete', '', '1', '0', '删除菜单', '', '', '1000');
INSERT INTO syx_menu VALUES ('90', '87', 'Admin', 'Nav', 'edit', '', '1', '0', '编辑菜单', '', '', '1000');
INSERT INTO syx_menu VALUES ('91', '90', 'Admin', 'Nav', 'edit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('92', '87', 'Admin', 'Nav', 'add', '', '1', '0', '添加菜单', '', '', '1000');
INSERT INTO syx_menu VALUES ('93', '92', 'Admin', 'Nav', 'add_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('94', '86', 'Admin', 'Navcat', 'index', '', '1', '1', '菜单分类', '', '', '0');
INSERT INTO syx_menu VALUES ('95', '94', 'Admin', 'Navcat', 'delete', '', '1', '0', '删除分类', '', '', '1000');
INSERT INTO syx_menu VALUES ('96', '94', 'Admin', 'Navcat', 'edit', '', '1', '0', '编辑分类', '', '', '1000');
INSERT INTO syx_menu VALUES ('97', '96', 'Admin', 'Navcat', 'edit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('98', '94', 'Admin', 'Navcat', 'add', '', '1', '0', '添加分类', '', '', '1000');
INSERT INTO syx_menu VALUES ('99', '98', 'Admin', 'Navcat', 'add_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('100', '85', 'Admin', 'Menu', 'index', '', '1', '1', '后台菜单', '', '', '0');
INSERT INTO syx_menu VALUES ('101', '100', 'Admin', 'Menu', 'add', '', '1', '0', '添加菜单', '', '', '0');
INSERT INTO syx_menu VALUES ('102', '101', 'Admin', 'Menu', 'add_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('103', '100', 'Admin', 'Menu', 'listorders', '', '1', '0', '后台菜单排序', '', '', '0');
INSERT INTO syx_menu VALUES ('104', '100', 'Admin', 'Menu', 'export_menu', '', '1', '0', '菜单备份', '', '', '1000');
INSERT INTO syx_menu VALUES ('105', '100', 'Admin', 'Menu', 'edit', '', '1', '0', '编辑菜单', '', '', '1000');
INSERT INTO syx_menu VALUES ('106', '105', 'Admin', 'Menu', 'edit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('107', '100', 'Admin', 'Menu', 'delete', '', '1', '0', '删除菜单', '', '', '1000');
INSERT INTO syx_menu VALUES ('108', '100', 'Admin', 'Menu', 'lists', '', '1', '0', '所有菜单', '', '', '1000');
INSERT INTO syx_menu VALUES ('109', '0', 'Admin', 'Setting', 'default', '', '0', '0', '设置', 'cogs', '', '0');
INSERT INTO syx_menu VALUES ('110', '109', 'Admin', 'Setting', 'userdefault', '', '0', '1', '个人信息', '', '', '0');
INSERT INTO syx_menu VALUES ('111', '110', 'Admin', 'User', 'userinfo', '', '1', '1', '修改信息', '', '', '0');
INSERT INTO syx_menu VALUES ('112', '111', 'Admin', 'User', 'userinfo_post', '', '1', '0', '修改信息提交', '', '', '0');
INSERT INTO syx_menu VALUES ('113', '110', 'Admin', 'Setting', 'password', '', '1', '1', '修改密码', '', '', '0');
INSERT INTO syx_menu VALUES ('114', '113', 'Admin', 'Setting', 'password_post', '', '1', '0', '提交修改', '', '', '0');
INSERT INTO syx_menu VALUES ('115', '109', 'Admin', 'Setting', 'site', '', '1', '1', '网站信息', '', '', '0');
INSERT INTO syx_menu VALUES ('116', '115', 'Admin', 'Setting', 'site_post', '', '1', '0', '提交修改', '', '', '0');
INSERT INTO syx_menu VALUES ('117', '115', 'Admin', 'Route', 'index', '', '1', '0', '路由列表', '', '', '0');
INSERT INTO syx_menu VALUES ('118', '115', 'Admin', 'Route', 'add', '', '1', '0', '路由添加', '', '', '0');
INSERT INTO syx_menu VALUES ('119', '118', 'Admin', 'Route', 'add_post', '', '1', '0', '路由添加提交', '', '', '0');
INSERT INTO syx_menu VALUES ('120', '115', 'Admin', 'Route', 'edit', '', '1', '0', '路由编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('121', '120', 'Admin', 'Route', 'edit_post', '', '1', '0', '路由编辑提交', '', '', '0');
INSERT INTO syx_menu VALUES ('122', '115', 'Admin', 'Route', 'delete', '', '1', '0', '路由删除', '', '', '0');
INSERT INTO syx_menu VALUES ('123', '115', 'Admin', 'Route', 'ban', '', '1', '0', '路由禁止', '', '', '0');
INSERT INTO syx_menu VALUES ('124', '115', 'Admin', 'Route', 'open', '', '1', '0', '路由启用', '', '', '0');
INSERT INTO syx_menu VALUES ('125', '115', 'Admin', 'Route', 'listorders', '', '1', '0', '路由排序', '', '', '0');
INSERT INTO syx_menu VALUES ('126', '109', 'Admin', 'Mailer', 'default', '', '1', '1', '邮箱配置', '', '', '0');
INSERT INTO syx_menu VALUES ('127', '126', 'Admin', 'Mailer', 'index', '', '1', '1', 'SMTP配置', '', '', '0');
INSERT INTO syx_menu VALUES ('128', '127', 'Admin', 'Mailer', 'index_post', '', '1', '0', '提交配置', '', '', '0');
INSERT INTO syx_menu VALUES ('129', '126', 'Admin', 'Mailer', 'active', '', '1', '1', '邮件模板', '', '', '0');
INSERT INTO syx_menu VALUES ('130', '129', 'Admin', 'Mailer', 'active_post', '', '1', '0', '提交模板', '', '', '0');
INSERT INTO syx_menu VALUES ('131', '109', 'Admin', 'Setting', 'clearcache', '', '1', '1', '清除缓存', '', '', '1');
INSERT INTO syx_menu VALUES ('132', '0', 'User', 'Indexadmin', 'default', '', '1', '1', '用户管理', 'group', '', '10');
INSERT INTO syx_menu VALUES ('133', '132', 'User', 'Indexadmin', 'default1', '', '1', '1', '用户组', '', '', '0');
INSERT INTO syx_menu VALUES ('134', '133', 'User', 'Indexadmin', 'index', '', '1', '1', '本站用户', 'leaf', '', '0');
INSERT INTO syx_menu VALUES ('135', '134', 'User', 'Indexadmin', 'ban', '', '1', '0', '拉黑会员', '', '', '0');
INSERT INTO syx_menu VALUES ('136', '134', 'User', 'Indexadmin', 'cancelban', '', '1', '0', '启用会员', '', '', '0');
INSERT INTO syx_menu VALUES ('137', '133', 'User', 'Oauthadmin', 'index', '', '1', '1', '第三方用户', 'leaf', '', '0');
INSERT INTO syx_menu VALUES ('138', '137', 'User', 'Oauthadmin', 'delete', '', '1', '0', '第三方用户解绑', '', '', '0');
INSERT INTO syx_menu VALUES ('139', '132', 'User', 'Indexadmin', 'default3', '', '1', '1', '管理组', '', '', '0');
INSERT INTO syx_menu VALUES ('140', '139', 'Admin', 'Rbac', 'index', '', '1', '1', '角色管理', '', '', '0');
INSERT INTO syx_menu VALUES ('141', '140', 'Admin', 'Rbac', 'member', '', '1', '0', '成员管理', '', '', '1000');
INSERT INTO syx_menu VALUES ('142', '140', 'Admin', 'Rbac', 'authorize', '', '1', '0', '权限设置', '', '', '1000');
INSERT INTO syx_menu VALUES ('143', '142', 'Admin', 'Rbac', 'authorize_post', '', '1', '0', '提交设置', '', '', '0');
INSERT INTO syx_menu VALUES ('144', '140', 'Admin', 'Rbac', 'roleedit', '', '1', '0', '编辑角色', '', '', '1000');
INSERT INTO syx_menu VALUES ('145', '144', 'Admin', 'Rbac', 'roleedit_post', '', '1', '0', '提交编辑', '', '', '0');
INSERT INTO syx_menu VALUES ('146', '140', 'Admin', 'Rbac', 'roledelete', '', '1', '1', '删除角色', '', '', '1000');
INSERT INTO syx_menu VALUES ('147', '140', 'Admin', 'Rbac', 'roleadd', '', '1', '1', '添加角色', '', '', '1000');
INSERT INTO syx_menu VALUES ('148', '147', 'Admin', 'Rbac', 'roleadd_post', '', '1', '0', '提交添加', '', '', '0');
INSERT INTO syx_menu VALUES ('149', '139', 'Admin', 'User', 'index', '', '1', '1', '管理员', '', '', '0');
INSERT INTO syx_menu VALUES ('150', '149', 'Admin', 'User', 'delete', '', '1', '0', '删除管理员', '', '', '1000');
INSERT INTO syx_menu VALUES ('151', '149', 'Admin', 'User', 'edit', '', '1', '0', '管理员编辑', '', '', '1000');
INSERT INTO syx_menu VALUES ('152', '151', 'Admin', 'User', 'edit_post', '', '1', '0', '编辑提交', '', '', '0');
INSERT INTO syx_menu VALUES ('153', '149', 'Admin', 'User', 'add', '', '1', '0', '管理员添加', '', '', '1000');
INSERT INTO syx_menu VALUES ('154', '153', 'Admin', 'User', 'add_post', '', '1', '0', '添加提交', '', '', '0');
INSERT INTO syx_menu VALUES ('155', '47', 'Admin', 'Plugin', 'update', '', '1', '0', '插件更新', '', '', '0');
INSERT INTO syx_menu VALUES ('156', '39', 'Admin', 'Storage', 'index', '', '1', '1', '文件存储', '', '', '0');
INSERT INTO syx_menu VALUES ('157', '156', 'Admin', 'Storage', 'setting_post', '', '1', '0', '文件存储设置提交', '', '', '0');
INSERT INTO syx_menu VALUES ('158', '54', 'Admin', 'Slide', 'ban', '', '1', '0', '禁用幻灯片', '', '', '0');
INSERT INTO syx_menu VALUES ('159', '54', 'Admin', 'Slide', 'cancelban', '', '1', '0', '启用幻灯片', '', '', '0');
INSERT INTO syx_menu VALUES ('160', '149', 'Admin', 'User', 'ban', '', '1', '0', '禁用管理员', '', '', '0');
INSERT INTO syx_menu VALUES ('161', '149', 'Admin', 'User', 'cancelban', '', '1', '0', '启用管理员', '', '', '0');
INSERT INTO syx_menu VALUES ('162', '0', 'Admin', 'Gm', 'default', '', '0', '1', '玩家管理', '', 'Gm用户管理', '50');
INSERT INTO syx_menu VALUES ('163', '162', 'Gm', 'User', 'batch_send_props', '', '1', '1', '批量发道具', '', '', '0');
INSERT INTO syx_menu VALUES ('164', '0', 'Admin', 'Operate', 'default', '', '1', '1', '邮件管理', 'envelope-o', '邮件管理', '60');
INSERT INTO syx_menu VALUES ('165', '164', 'Operate', 'Email', 'send_email', '', '1', '1', '发送邮件', '', '发送邮件', '0');
INSERT INTO syx_menu VALUES ('166', '0', 'Admin', 'Message', 'default', '', '1', '1', '信息管理', '', '', '70');
INSERT INTO syx_menu VALUES ('167', '166', 'Operate', 'Pmd', 'add', '', '1', '0', '跑马灯', '', '新增跑马灯', '3');
INSERT INTO syx_menu VALUES ('168', '166', 'Operate', 'MarqueeColor', 'index', '', '1', '1', 'Rgb颜色值', '', '颜色值', '1');
INSERT INTO syx_menu VALUES ('169', '168', 'Operate', 'MarqueeColor', 'add', '', '1', '0', '添加rgb颜色', '', '', '0');
INSERT INTO syx_menu VALUES ('170', '168', 'Operate', 'MarqueeColor', 'edit', '', '1', '0', '修改rgb颜色值', '', '', '0');
INSERT INTO syx_menu VALUES ('171', '166', 'Operate', 'Pmd', 'index', '', '1', '1', '跑马灯列表', '', '', '2');
INSERT INTO syx_menu VALUES ('172', '0', 'Admin', 'Order', 'default', '', '1', '1', '订单管理', 'reorder', '', '80');
INSERT INTO syx_menu VALUES ('173', '172', 'Operate', 'Order', 'index', '', '1', '1', '订单管理', '', '订单号码查询', '0');
INSERT INTO syx_menu VALUES ('174', '0', 'Admin', 'Log', 'default', '', '1', '1', '行为日志', 'database', '', '100');
INSERT INTO syx_menu VALUES ('175', '174', 'Log', 'GmLog', 'index', '', '1', '1', '操作日志', '', '行为日志', '0');
INSERT INTO syx_menu VALUES ('176', '0', 'Admin', 'Activity', 'default', '', '1', '1', '活动管理', '', '活动管理', '110');
INSERT INTO syx_menu VALUES ('179', '176', 'Operate', 'PackageType', 'index', '', '1', '1', '礼包码列表', '', '礼包类型列表', '0');
INSERT INTO syx_menu VALUES ('180', '179', 'Operate', 'PackageType', 'add', '', '1', '0', '新增礼包类型', '', '新增礼包类型', '0');
INSERT INTO syx_menu VALUES ('181', '179', 'Operate', 'PackageType', 'distribution_server', '', '1', '0', '批量发送礼包码', '', '', '0');
INSERT INTO syx_menu VALUES ('182', '165', 'Operate', 'Email', 'get_props', '', '1', '0', '邮件ajax加载道具', '', '', '0');
INSERT INTO syx_menu VALUES ('183', '179', 'Operate', 'PackageType', 'export_excel', '', '1', '0', '导出礼包码', '', '', '0');
INSERT INTO syx_menu VALUES ('184', '179', 'Operate', 'PackageType', 'Edit', '', '1', '0', '修改礼包码', '', '', '0');
INSERT INTO syx_menu VALUES ('181', '0', 'Admin', 'GameUser', 'default', '', '1', '1', '玩家信息', 'gamepad', '玩家信息，以后所有玩家的操作都会放在这里面', '120');
INSERT INTO syx_menu VALUES ('182', '181', 'Operate', 'PlayerItems', 'index', '', '1', '1', '玩家物品', '', '玩家的道具，元宝，铜钱，粮草等...', '0');
INSERT INTO syx_menu VALUES ('183', '182', 'Operate', 'PlayerItems', 'delete_items', '', '1', '0', '修改物品', '', '', '0');

-- ----------------------------
-- Table structure for `syx_oauth_user`
-- ----------------------------
DROP TABLE IF EXISTS `syx_oauth_user`;
CREATE TABLE `syx_oauth_user` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `from` varchar(20) NOT NULL COMMENT '用户来源key',
  `name` varchar(30) NOT NULL COMMENT '第三方昵称',
  `head_img` varchar(200) NOT NULL COMMENT '头像',
  `uid` int(20) NOT NULL COMMENT '关联的本站用户id',
  `create_time` datetime NOT NULL COMMENT '绑定时间',
  `last_login_time` datetime NOT NULL COMMENT '最后登录时间',
  `last_login_ip` varchar(16) NOT NULL COMMENT '最后登录ip',
  `login_times` int(6) NOT NULL COMMENT '登录次数',
  `status` tinyint(2) NOT NULL,
  `access_token` varchar(60) NOT NULL,
  `expires_date` int(12) NOT NULL COMMENT 'access_token过期时间',
  `openid` varchar(40) NOT NULL COMMENT '第三方用户id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_oauth_user
-- ----------------------------

-- ----------------------------
-- Table structure for `syx_options`
-- ----------------------------
DROP TABLE IF EXISTS `syx_options`;
CREATE TABLE `syx_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(64) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_options
-- ----------------------------
INSERT INTO syx_options VALUES ('1', 'member_email_active', '{\"title\":\"ThinkCMF\\u90ae\\u4ef6\\u6fc0\\u6d3b\\u901a\\u77e5.\",\"template\":\"<p>\\u672c\\u90ae\\u4ef6\\u6765\\u81ea<a href=\\\"http:\\/\\/www.thinkcmf.com\\\">ThinkCMF<\\/a><br\\/><br\\/>&nbsp; &nbsp;<strong>---------------<\\/strong><br\\/>&nbsp; &nbsp;<strong>\\u5e10\\u53f7\\u6fc0\\u6d3b\\u8bf4\\u660e<\\/strong><br\\/>&nbsp; &nbsp;<strong>---------------<\\/strong><br\\/><br\\/>&nbsp; &nbsp; \\u5c0a\\u656c\\u7684<span style=\\\"FONT-SIZE: 16px; FONT-FAMILY: Arial; COLOR: rgb(51,51,51); LINE-HEIGHT: 18px; BACKGROUND-COLOR: rgb(255,255,255)\\\">#username#\\uff0c\\u60a8\\u597d\\u3002<\\/span>\\u5982\\u679c\\u60a8\\u662fThinkCMF\\u7684\\u65b0\\u7528\\u6237\\uff0c\\u6216\\u5728\\u4fee\\u6539\\u60a8\\u7684\\u6ce8\\u518cEmail\\u65f6\\u4f7f\\u7528\\u4e86\\u672c\\u5730\\u5740\\uff0c\\u6211\\u4eec\\u9700\\u8981\\u5bf9\\u60a8\\u7684\\u5730\\u5740\\u6709\\u6548\\u6027\\u8fdb\\u884c\\u9a8c\\u8bc1\\u4ee5\\u907f\\u514d\\u5783\\u573e\\u90ae\\u4ef6\\u6216\\u5730\\u5740\\u88ab\\u6ee5\\u7528\\u3002<br\\/>&nbsp; &nbsp; \\u60a8\\u53ea\\u9700\\u70b9\\u51fb\\u4e0b\\u9762\\u7684\\u94fe\\u63a5\\u5373\\u53ef\\u6fc0\\u6d3b\\u60a8\\u7684\\u5e10\\u53f7\\uff1a<br\\/>&nbsp; &nbsp; <a title=\\\"\\\" href=\\\"http:\\/\\/#link#\\\" target=\\\"_self\\\">http:\\/\\/#link#<\\/a><br\\/>&nbsp; &nbsp; (\\u5982\\u679c\\u4e0a\\u9762\\u4e0d\\u662f\\u94fe\\u63a5\\u5f62\\u5f0f\\uff0c\\u8bf7\\u5c06\\u8be5\\u5730\\u5740\\u624b\\u5de5\\u7c98\\u8d34\\u5230\\u6d4f\\u89c8\\u5668\\u5730\\u5740\\u680f\\u518d\\u8bbf\\u95ee)<br\\/>&nbsp; &nbsp; \\u611f\\u8c22\\u60a8\\u7684\\u8bbf\\u95ee\\uff0c\\u795d\\u60a8\\u4f7f\\u7528\\u6109\\u5feb\\uff01<br\\/><br\\/>&nbsp; &nbsp; \\u6b64\\u81f4<br\\/>&nbsp; &nbsp; ThinkCMF \\u7ba1\\u7406\\u56e2\\u961f.<\\/p>\"}', '1');
INSERT INTO syx_options VALUES ('2', 'site_options', '{\"site_name\":\"ThinkCMF\\u5185\\u5bb9\\u7ba1\\u7406\\u6846\\u67b6\",\"site_host\":\"http:\\/\\/localhost\\/ThinkCMFX%201.6.1\\/\",\"site_tpl\":\"simplebootx\",\"site_adminstyle\":\"bluesky\",\"site_icp\":\"\",\"site_admin_email\":\"724708346@qq.com\",\"site_tongji\":\"\",\"site_copyright\":\"\",\"site_seo_title\":\"ThinkCMF\\u5185\\u5bb9\\u7ba1\\u7406\\u6846\\u67b6\",\"site_seo_keywords\":\"\",\"site_seo_description\":\"ThinkCMF\\u662f\\u7b80\\u7ea6\\u98ce\\u7f51\\u7edc\\u79d1\\u6280\\u53d1\\u5e03\\u7684\\u4e00\\u6b3e\\u7528\\u4e8e\\u5feb\\u901f\\u5f00\\u53d1\\u7684\\u5185\\u5bb9\\u7ba1\\u7406\\u6846\\u67b6\",\"urlmode\":\"2\",\"html_suffix\":\"\",\"comment_time_interval\":60}', '1');
INSERT INTO syx_options VALUES ('3', 'cmf_settings', '{\"banned_usernames\":\"\"}', '1');

-- ----------------------------
-- Table structure for `syx_plugins`
-- ----------------------------
DROP TABLE IF EXISTS `syx_plugins`;
CREATE TABLE `syx_plugins` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(50) NOT NULL COMMENT '插件名，英文',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '插件名称',
  `description` text COMMENT '插件描述',
  `type` tinyint(2) DEFAULT '0' COMMENT '插件类型, 1:网站；8;微信',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态；1开启；',
  `config` text COMMENT '插件配置',
  `hooks` varchar(255) DEFAULT NULL COMMENT '实现的钩子;以“，”分隔',
  `has_admin` tinyint(2) DEFAULT '0' COMMENT '插件是否有后台管理界面',
  `author` varchar(50) DEFAULT '' COMMENT '插件作者',
  `version` varchar(20) DEFAULT '' COMMENT '插件版本号',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '插件安装时间',
  `listorder` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='插件表';

-- ----------------------------
-- Records of syx_plugins
-- ----------------------------
INSERT INTO syx_plugins VALUES ('1', 'Demo', '插件演示', '插件演示', '0', '1', '{\"text\":\"hello,ThinkCMF!\",\"password\":\"\",\"select\":\"1\",\"checkbox\":1,\"radio\":\"1\",\"textarea\":\"\\u8fd9\\u91cc\\u662f\\u4f60\\u8981\\u586b\\u5199\\u7684\\u5185\\u5bb9\"}', 'footer', '1', 'ThinkCMF', '1.0', '0', '0');

-- ----------------------------
-- Table structure for `syx_role`
-- ----------------------------
DROP TABLE IF EXISTS `syx_role`;
CREATE TABLE `syx_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '角色名称',
  `pid` smallint(6) DEFAULT NULL COMMENT '父角色ID',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '状态',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `listorder` int(3) NOT NULL DEFAULT '0' COMMENT '排序字段',
  PRIMARY KEY (`id`),
  KEY `parentId` (`pid`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_role
-- ----------------------------
INSERT INTO syx_role VALUES ('1', '超级管理员', '0', '1', '拥有网站最高管理员权限！', '1329633709', '1329633709', '0');
INSERT INTO syx_role VALUES ('2', '运营', null, '1', '以俊哥为首的运营兄弟们。', '1447061701', '0', '0');
INSERT INTO syx_role VALUES ('3', '运营助理', null, '1', '俊哥小弟', '1447075137', '0', '0');

-- ----------------------------
-- Table structure for `syx_role_user`
-- ----------------------------
DROP TABLE IF EXISTS `syx_role_user`;
CREATE TABLE `syx_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT NULL,
  `user_id` char(32) DEFAULT NULL,
  KEY `group_id` (`role_id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_role_user
-- ----------------------------
INSERT INTO syx_role_user VALUES ('2', '2');
INSERT INTO syx_role_user VALUES ('3', '3');

-- ----------------------------
-- Table structure for `syx_route`
-- ----------------------------
DROP TABLE IF EXISTS `syx_route`;
CREATE TABLE `syx_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '路由id',
  `full_url` varchar(255) DEFAULT NULL COMMENT '完整url， 如：portal/list/index?id=1',
  `url` varchar(255) DEFAULT NULL COMMENT '实际显示的url',
  `listorder` int(5) DEFAULT '0' COMMENT '排序，优先级，越小优先级越高',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1：启用 ;0：不启用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_route
-- ----------------------------

-- ----------------------------
-- Table structure for `syx_users`
-- ----------------------------
DROP TABLE IF EXISTS `syx_users`;
CREATE TABLE `syx_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `user_pass` varchar(64) NOT NULL DEFAULT '' COMMENT '登录密码；sp_password加密',
  `user_nicename` varchar(50) NOT NULL DEFAULT '' COMMENT '用户美名',
  `user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '登录邮箱',
  `user_url` varchar(100) NOT NULL DEFAULT '' COMMENT '用户个人网站',
  `avatar` varchar(255) DEFAULT NULL COMMENT '用户头像，相对于upload/avatar目录',
  `sex` smallint(1) DEFAULT '0' COMMENT '性别；0：保密，1：男；2：女',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `signature` varchar(255) DEFAULT NULL COMMENT '个性签名',
  `last_login_ip` varchar(16) NOT NULL COMMENT '最后登录ip',
  `last_login_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '最后登录时间',
  `create_time` datetime NOT NULL DEFAULT '2000-01-01 00:00:00' COMMENT '注册时间',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '' COMMENT '激活码',
  `user_status` int(11) NOT NULL DEFAULT '1' COMMENT '用户状态 0：禁用； 1：正常 ；2：未验证',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `user_type` smallint(1) DEFAULT '1' COMMENT '用户类型，1:admin ;2:会员',
  PRIMARY KEY (`id`),
  KEY `user_login_key` (`user_login`) USING BTREE,
  KEY `user_nicename` (`user_nicename`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of syx_users
-- ----------------------------
INSERT INTO syx_users VALUES ('1', 'admin', '2ccb45a5bba4636009bd7a0a7fa59a4ad3a5a34b0fbac23a', 'admin', 'vikichan@shengyouxi.com', '', null, '0', null, null, '180.175.186.240', '2015-11-18 17:33:36', '2015-09-25 06:22:00', '', '1', '0', '1');
INSERT INTO syx_users VALUES ('2', 'zhongjun', '2ccb45a5bba450946294b524581465798a1a0e1f3563c23a', '', 'jun.zhong@shengyouxi.com', '', null, '0', null, null, '180.175.186.240', '2015-11-18 17:38:56', '2000-01-01 00:00:00', '', '1', '0', '1');
INSERT INTO syx_users VALUES ('3', 'gongjiaan', '2ccb45a5bba4fd3e2af5b4a9a7fc8ed8615ade5b7523c23a', '', 'jiaan.gong@shengyouxi.com', '', null, '0', null, null, '180.175.164.180', '2015-11-17 20:51:04', '2000-01-01 00:00:00', '', '1', '0', '1');
