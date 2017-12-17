<?php
if (!pdo_fieldexists('meepo_online_list', 'zan_style')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `zan_style` tinyint(1) NOT NULL  COMMENT '规则';");
}
if (!pdo_fieldexists('meepo_online_list', 'need_pay')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `need_pay` tinyint(1) NOT NULL  DEFAULT '0';");
}
if (!pdo_fieldexists('meepo_online_list', 'gift_pay_detail')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `gift_pay_detail` tinyint(1) NOT NULL  DEFAULT '1';");
}
if (!pdo_fieldexists('meepo_online_list', 'look_code')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `look_code` varchar(100) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'pay_money')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `pay_money` varchar(100) NOT NULL   DEFAULT '0';");
}
if (!pdo_fieldexists('meepo_online_list', 'top_bj')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `top_bj` varchar(300) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'main_color')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `main_color` varchar(300) NOT NULL DEFAULT '#ff6a00';");
}
if (!pdo_fieldexists('meepo_online_list', 'yuyue_tpl')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `yuyue_tpl` varchar(300) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'yuyue_customer_img')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `yuyue_customer_img` varchar(300) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'activity_uu')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `activity_uu` varchar(100) NOT NULL  COMMENT '规则';");
}
if (!pdo_fieldexists('meepo_online_list', 'activity_vu')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `activity_vu` varchar(100) NOT NULL  COMMENT '规则';");
}
if (!pdo_fieldexists('meepo_online_list', 'content')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `content` text NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list_user', 'oauth_openid')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list_user') . " ADD `oauth_openid` varchar(200) NOT NULL  COMMENT '规则';");
}
if (!pdo_fieldexists('meepo_online_list_user', 'need_info')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list_user') . ' ADD `need_info` text NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'shake_bg_img')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `shake_bg_img` varchar(300) NOT NULL;');
}
if (!pdo_tableexists('meepo_footer_menu')) {
    $sql = "CREATE TABLE IF NOT EXISTS " . tablename("meepo_footer_menu") . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) NOT NULL COMMENT '分类名称',
	`icon` varchar(50) NOT NULL COMMENT '分类名称',
	`isshow` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示',
	`url` varchar(300) NOT NULL,
  `displayorder` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
    pdo_run($sql);
}
if (!pdo_tableexists('meepo_my_live')) {
    $sql = "CREATE TABLE IF NOT EXISTS " . tablename("meepo_my_live") . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `openid` varchar(200) NOT NULL COMMENT '粉丝标识',
	`mobile` varchar(12) NOT NULL COMMENT 'mobile',
	`listid` int(10) NOT NULL COMMENT '直播id',
	`createtime` int(11) NOT NULL DEFAULT '0' COMMENT '是否显示',
  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
    pdo_run($sql);
}
if (!pdo_tableexists('meepo_online_list_lookpay')) {
    $sql = "CREATE TABLE IF NOT EXISTS " . tablename("meepo_online_list_lookpay") . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `openid` varchar(200) NOT NULL COMMENT '粉丝标识',
	`listid` int(10) NOT NULL COMMENT '直播id',
	`money` varchar(10) NOT NULL,
	`status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付',
	`createtime` int(11) NOT NULL DEFAULT '0' COMMENT '支付时间',
  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
    pdo_run($sql);
}
if (!pdo_tableexists('meepo_online_list_lookcode')) {
    $sql = "CREATE TABLE IF NOT EXISTS " . tablename("meepo_online_list_lookcode") . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `openid` varchar(200) NOT NULL COMMENT '粉丝标识',
	`listid` int(10) NOT NULL COMMENT '直播id',
	`code` varchar(15) NOT NULL,
	`createtime` int(11) NOT NULL DEFAULT '0' COMMENT '是否显示',
  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
    pdo_run($sql);
}
if (!pdo_tableexists('meepo_online_dayu_sms')) {
    $sql = "CREATE TABLE IF NOT EXISTS " . tablename("meepo_online_dayu_sms") . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `appkey` varchar(200) NOT NULL COMMENT 'appkey',
	`appsecret` varchar(200) NOT NULL COMMENT 'appsecret',
	`sms_signname` varchar(100) NOT NULL COMMENT 'sms_signname',
	`sms_tpl_id` varchar(100) NOT NULL COMMENT 'sms_tpl_id',
  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
    pdo_run($sql);
}
if (!pdo_fieldexists('meepo_online_list', 'look_type')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `look_type` tinyint(1) NOT NULL  DEFAULT '0';");
}
if (!pdo_fieldexists('meepo_online_dayu_sms', 'sms_success_tpl_id')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_dayu_sms') . ' ADD `sms_success_tpl_id` varchar(100) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'end_type')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `end_type` tinyint(1) NOT NULL  COMMENT '规则';");
}
if (!pdo_fieldexists('meepo_online_list', 'yuyue_show')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `yuyue_show` tinyint(1) NOT NULL  DEFAULT '1';");
}
if (!pdo_fieldexists('meepo_online_list', 'sms_mobile')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `sms_mobile` tinyint(1) NOT NULL  DEFAULT '1';");
}
if (!pdo_fieldexists('meepo_online_list', 'put_mobile')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `put_mobile` tinyint(1) NOT NULL  DEFAULT '1';");
}
if (!pdo_fieldexists('meepo_my_live', 'mobile')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_my_live') . " ADD `mobile` varchar(12) NOT NULL COMMENT 'mobile';");
}
$sql = "CREATE TABLE IF NOT EXISTS " . tablename("meepo_online_dayu_sms_record") . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `openid` varchar(200) NOT NULL COMMENT 'appkey',
	`listid` int(10) NOT NULL COMMENT '直播id',
	`sms_code` varchar(10) NOT NULL COMMENT 'sms_signname',
	`createtime` int(11) NOT NULL COMMENT 'sms_tpl_id',
  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_run($sql);
$sql = "CREATE TABLE IF NOT EXISTS " . tablename("meepo_online_list_need_input") . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) NOT NULL,
	`listid` int(11) NOT NULL,
	`name` varchar(100) NOT NULL,
  `code` varchar(100) NOT NULL,
	`placeholder` varchar(300) NOT NULL,
  `displayorder` int(11) NOT NULL,
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
pdo_run($sql);
if (!pdo_fieldexists('meepo_online_list', 'no_start_activity_id')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `no_start_activity_id` varchar(100) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'no_start_activity_uu')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `no_start_activity_uu` varchar(100) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'no_start_activity_vu')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `no_start_activity_vu` varchar(100) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'no_start_yt_iframe')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `no_start_yt_iframe` varchar(2000) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'no_start_local_media')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `no_start_local_media` varchar(800) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'open_img_url')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `open_img_url` varchar(300) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'djs_txt_color')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `djs_txt_color` varchar(20) NOT NULL  DEFAULT '#ffffff' ;");
}
if (!pdo_fieldexists('meepo_online_list', 'end_activity_id')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `end_activity_id` varchar(100) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'end_activity_uu')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `end_activity_uu` varchar(100) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'end_activity_vu')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `end_activity_vu` varchar(100) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'end_yt_iframe')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `end_yt_iframe` varchar(2000) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'end_local_media')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . ' ADD `end_local_media` varchar(800) NOT NULL;');
}
if (!pdo_fieldexists('meepo_online_list', 'no_start_type')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `no_start_type` tinyint(1) NOT NULL  DEFAULT '3' ;");
}
if (!pdo_fieldexists('meepo_online_list', 'thumb_time')) {
    pdo_query('ALTER TABLE ' . tablename('meepo_online_list') . " ADD `thumb_time` int(10) NOT NULL  DEFAULT '5' ;");
}