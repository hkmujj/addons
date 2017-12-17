<?php

// 基本设置
// 
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('dxf_ycj_cate') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT '类别标题',
  `author` varchar(255) DEFAULT NULL COMMENT '作者',
  `status` tinyint(1) DEFAULT '1' COMMENT '1.显示  0.不显示',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `is_delete` tinyint(1) DEFAULT '1' COMMENT '0.未删除 1.删除',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='兼职类别表'";

pdo_query($sql);

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('dxf_ycj_category') . " (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `thumb` varchar(255) NOT NULL DEFAULT '' COMMENT '分类图片 暂未用',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID,0为第一级',
  `level` tinyint(1) DEFAULT NULL COMMENT '类别深度',
  `description` varchar(500) NOT NULL COMMENT '分类介绍',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `isrecommand` int(10) DEFAULT '0' COMMENT '首页推荐 --暂未用',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启',
  `yzmsg` varchar(255) DEFAULT NULL COMMENT '验证信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

pdo_query($sql);

/**
 * 项目
 */
$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('dxf_ycj_daili') . " (
 `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL COMMENT '代理的微信openid',
  `headimg` varchar(255) DEFAULT NULL COMMENT '代理的头像',
  `nickname` varchar(255) DEFAULT NULL COMMENT '代理的微信名称',
  `realname` varchar(255) DEFAULT NULL COMMENT '代理的真实名字',
  `sex` tinyint(1) DEFAULT NULL COMMENT '代理性别 1.南 2.女 3.保密',
  `age` int(3) DEFAULT NULL COMMENT '代理年龄',
  `phone` varchar(25) DEFAULT NULL COMMENT '代理电话',
  `qq` varchar(25) DEFAULT NULL COMMENT 'qq',
  `chat` varchar(100) DEFAULT NULL COMMENT '微信号',
  `address` varchar(255) DEFAULT NULL COMMENT '代理的详细地址',
  `cat_id` int(11) DEFAULT NULL COMMENT '代理的城市区域id',
  `status` tinyint(1) DEFAULT '1' COMMENT '1.等待审核 2.审核通过 3.审核不通过 4.禁用 -1彻底删除',
  `grade` tinyint(3) DEFAULT '3' COMMENT '代理的等级 1.金牌代理 2.银代理 3.普通代理',
  `type` tinyint(1) DEFAULT NULL COMMENT '1.个人代理 2.公司代理',
  `desc` text COMMENT '自我简介',
  `ctime` int(11) DEFAULT NULL COMMENT '代理的加入的时间',
  `dl_admin` tinyint(1) DEFAULT '0' COMMENT '1.管理员',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='代理或者公司信息表'";

pdo_query($sql);


$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('dxf_ycj_job') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `cate_id` int(5) DEFAULT NULL COMMENT '工作类型',
  `type` tinyint(1) DEFAULT NULL COMMENT '1.兼职 2.勤工俭学3.实习 4.全职 4.',
  `sex` tinyint(1) DEFAULT NULL COMMENT '性别 1.南 2.女 3.不限',
  `age` char(64) DEFAULT '0' COMMENT '年龄',
  `height` char(64) DEFAULT '' COMMENT '身高',
  `job_stime` int(11) DEFAULT NULL COMMENT '工作开始时间',
  `job_endtime` int(11) DEFAULT NULL COMMENT '工作结束时间',
  `jihe_time` varchar(50) DEFAULT NULL COMMENT '集合时间',
  `bm_endtime` int(11) DEFAULT NULL COMMENT '报名截止时间',
  `jihe_area` varchar(255) DEFAULT NULL COMMENT '集合地点',
  `job_cid` int(5) DEFAULT NULL COMMENT '工作的区域id',
  `job_area` varchar(255) DEFAULT NULL COMMENT '工作详细地址',
  `money` char(64) DEFAULT NULL COMMENT '薪资',
  `account_time` tinyint(1) DEFAULT NULL COMMENT '1.日结 2.周算 3.月结 4.面议',
  `account_type` tinyint(1) DEFAULT NULL COMMENT '结算方式 1.现金 2.支付宝 3.微信 4.银行卡',
  `p_num` int(5) DEFAULT NULL COMMENT '招聘人数',
  `bm_num` int(11) DEFAULT NULL COMMENT '已报名人数',
  `job_req` text COMMENT '兼职要求',
  `job_desc` text COMMENT '兼职描述',
  `x` varchar(255) DEFAULT NULL COMMENT '经度',
  `y` varchar(255) DEFAULT NULL COMMENT '纬度',
  `status` tinyint(1) DEFAULT '1' COMMENT '代理操作  1.上线 -1.下线',
  `openid` varchar(200) DEFAULT NULL COMMENT '代理的openid',
  `ad_status` tinyint(2) DEFAULT '1' COMMENT '管理操作 1.正常显示 0.违规下线',
  `ad_hot` tinyint(1) DEFAULT '0' COMMENT '热门推荐 1.推荐 0.不推荐',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updat_time` int(11) DEFAULT NULL COMMENT '刷新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='厂区信息'";

pdo_query($sql);

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('dxf_ycj_jobuser') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',
  `job_id` int(11) NOT NULL DEFAULT '0' COMMENT '兼职工作的id',
  `openid` varchar(255) NOT NULL DEFAULT '0' COMMENT '会员的微信id',
  `headimg` varchar(255) DEFAULT NULL COMMENT '用户的头像',
  `user_name` varchar(50) DEFAULT NULL COMMENT '用户名',
  `mobile` varchar(50) DEFAULT NULL COMMENT '电话',
  `wchat` varchar(100) DEFAULT NULL COMMENT '维信号',
  `is_delete` tinyint(1) DEFAULT '1' COMMENT '0.删除 1.存在',
  `status` tinyint(1) DEFAULT '1' COMMENT '1.用户提交 2.代理同意  -1代理拒绝 3.用户同意',
  `ctime` int(11) DEFAULT NULL COMMENT '提交的时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";

pdo_query($sql);



$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('dxf_ycj_slide') . " (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(3) DEFAULT NULL,
  `type` tinyint(1) DEFAULT NULL COMMENT '1.首页幻灯片 2.广告',
  `name` varchar(100) DEFAULT NULL COMMENT '名称',
  `pic_url` varchar(255) DEFAULT NULL COMMENT '图片的url',
  `url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `status` tinyint(1) DEFAULT '1' COMMENT '1.显示 0.不显示',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `sort` int(3) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='轮播'";

pdo_query($sql);

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('dxf_ycj_user') . " (
   `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `weid` int(11) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `user_name` varchar(50) DEFAULT NULL COMMENT '用户名字',
  `mobile` varchar(50) DEFAULT NULL COMMENT '用户电话',
  `qq` varchar(50) DEFAULT NULL COMMENT 'qq',
  `wchat` varchar(100) DEFAULT NULL COMMENT '用户维信',
  `status` tinyint(3) DEFAULT '1' COMMENT '1.正常 -1管理员禁用',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户'";

pdo_query($sql);

$sql = "
CREATE TABLE IF NOT EXISTS " . tablename('dxf_ycj_activity') . " (
   `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` tinyint(3) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `type` tinyint(1) DEFAULT '1' COMMENT '1.文学征集报名',
  `name` varchar(255) DEFAULT NULL COMMENT '报名的名字',
  `phone` varchar(50) DEFAULT NULL COMMENT '电话',
  `qq` varchar(15) DEFAULT NULL COMMENT 'qq',
  `wchat` varchar(50) DEFAULT NULL COMMENT '微信',
  `ctime` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1.未联系 2.已联系 3.有效 4.无效',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='活动表'";

pdo_query($sql);













