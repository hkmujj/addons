<?php
$sql="CREATE TABLE IF NOT EXISTS `ims_fy_lesson_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id',
  `title` varchar(255) DEFAULT NULL COMMENT '����',
  `author` varchar(100) DEFAULT NULL COMMENT '����',
  `content` text COMMENT '����',
  `isshow` tinyint(1) DEFAULT '1' COMMENT 'ǰ̨չʾ 0.�ر� 1.����',
  `displayorder` varchar(255) DEFAULT '0' COMMENT '���� ��ֵԽ��Խ��ǰ',
  `addtime` int(10) DEFAULT NULL COMMENT '����ʱ��',
  `view` int(11) NOT NULL DEFAULT '0' COMMENT '������',
  `linkurl` varchar(1000) DEFAULT NULL COMMENT 'ԭ������',
  `images` varchar(255) DEFAULT NULL COMMENT '����ͼƬ',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_title` (`title`),
  KEY `idx_author` (`author`),
  KEY `idx_isshow` (`isshow`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_blacklist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `addtime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_cashlog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `cash_type` tinyint(1) NOT NULL COMMENT '���ַ�ʽ 1.����Ա��� 2.�Զ�����',
  `uid` int(11) DEFAULT NULL COMMENT '��Աid',
  `openid` varchar(255) NOT NULL COMMENT '��˿���',
  `cash_num` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '���ֽ��',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '״̬ 0.����� 1.���ֳɹ� -1.���δͨ��',
  `disposetime` int(10) NOT NULL DEFAULT '0' COMMENT '����ʱ��',
  `partner_trade_no` varchar(255) DEFAULT NULL COMMENT '�̻�������',
  `payment_no` varchar(255) DEFAULT NULL COMMENT '΢�Ŷ�����',
  `remark` text COMMENT '����Ա��ע',
  `lesson_type` tinyint(1) NOT NULL COMMENT '�������� 1.����Ӷ������ 2.�γ���������',
  `addtime` int(10) NOT NULL COMMENT '����ʱ��',
  `cash_way` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1.���ֵ����  2.���ֵ�΢��Ǯ��',
  `pay_account` varchar(50) DEFAULT NULL COMMENT '�����ʺ�',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_cash_type` (`cash_type`),
  KEY `idx_cash_way` (`cash_way`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_status` (`status`),
  KEY `idx_lesson_type` (`lesson_type`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Ӷ�����ֱ�';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�����ʺ�',
  `name` varchar(50) NOT NULL COMMENT '��������',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�ϼ�����ID,0Ϊ��һ��',
  `ico` varchar(255) DEFAULT NULL COMMENT '����ͼ��',
  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '����',
  `addtime` int(10) DEFAULT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='�γ̷����';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_collect` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `uid` int(11) NOT NULL COMMENT '��Աid',
  `openid` varchar(255) NOT NULL COMMENT '��˿���',
  `outid` int(11) NOT NULL COMMENT '�ⲿ���(�γ̱�Ż�ʦ���)',
  `ctype` tinyint(1) NOT NULL COMMENT '�ղ����� 1.�γ� 2.��ʦ',
  `addtime` int(10) NOT NULL COMMENT '�ղ�ʱ��',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_ctype` (`ctype`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='�ղر�';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_commission_level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `levelname` varchar(50) DEFAULT NULL COMMENT '�����ȼ�����',
  `commission1` decimal(10,2) DEFAULT '0.00' COMMENT 'һ������Ӷ�����',
  `commission2` decimal(10,2) DEFAULT '0.00' COMMENT '��������Ӷ�����',
  `commission3` decimal(10,2) DEFAULT '0.00' COMMENT '��������Ӷ�����',
  `updatemoney` decimal(10,2) DEFAULT '0.00' COMMENT '��������(����Ӷ��������)',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_levelname` (`levelname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='�����̵ȼ���';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_commission_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id',
  `orderid` varchar(255) DEFAULT NULL COMMENT '����id',
  `uid` int(11) DEFAULT NULL COMMENT '��Աid',
  `openid` varchar(255) DEFAULT NULL COMMENT '��˿���',
  `nickname` varchar(100) DEFAULT NULL COMMENT '��Ա�ǳ�',
  `bookname` varchar(255) DEFAULT NULL COMMENT '�γ�����',
  `change_num` decimal(10,2) DEFAULT '0.00' COMMENT '�䶯��Ŀ',
  `grade` tinyint(1) DEFAULT NULL COMMENT 'Ӷ��ȼ�',
  `remark` varchar(255) DEFAULT NULL COMMENT '�䶯˵��',
  `addtime` int(10) DEFAULT NULL COMMENT '�䶯ʱ��',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_orderid` (`orderid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_nickname` (`nickname`),
  KEY `idx_bookname` (`bookname`),
  KEY `idx_grade` (`grade`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Ӷ����־��';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_coupon` (
  `card_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `password` varchar(18) NOT NULL COMMENT '�Ż�����Կ',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�Ż�����ֵ',
  `validity` int(10) NOT NULL COMMENT '��Ч��',
  `conditions` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'ʹ������(��xԪ����)',
  `is_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT '״̬ 0.δʹ�� 1.��ʹ��',
  `nickname` varchar(50) DEFAULT NULL COMMENT '�ǳ�',
  `uid` int(11) DEFAULT NULL COMMENT '��Ա���',
  `openid` varchar(50) DEFAULT NULL COMMENT '��˿���',
  `ordersn` varchar(50) DEFAULT NULL COMMENT '�������',
  `use_time` int(10) DEFAULT NULL COMMENT 'ʹ��ʱ��',
  `addtime` int(10) NOT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`card_id`),
  UNIQUE KEY `idx_ordersn` (`ordersn`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_password` (`password`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_validity` (`validity`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_evaluate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `orderid` int(11) NOT NULL COMMENT '����id',
  `ordersn` varchar(255) NOT NULL COMMENT '�������',
  `lessonid` int(11) NOT NULL COMMENT '�γ�id',
  `bookname` varchar(255) NOT NULL COMMENT '�γ�����',
  `openid` varchar(255) NOT NULL COMMENT '��˿���',
  `uid` int(11) NOT NULL COMMENT '��Աid',
  `nickname` varchar(50) NOT NULL COMMENT '��Ա�ǳ�',
  `grade` tinyint(1) NOT NULL COMMENT '���� 1.���� 2.���� 3.����',
  `content` text NOT NULL COMMENT '��������',
  `addtime` int(10) NOT NULL COMMENT '����ʱ��',
  `reply` text COMMENT '���ۻظ�',
  `teacherid` int(11) DEFAULT NULL COMMENT '��ʦid(��fy_lesson_teacher���id�ֶζ�Ӧ)',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_orderid` (`orderid`),
  KEY `idx_ordersn` (`ordersn`),
  KEY `idx_lessonid` (`lessonid`),
  KEY `idx_bookname` (`bookname`),
  KEY `idx_teacherid` (`teacherid`),
  KEY `idx_grade` (`grade`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='���ۿγ̱�';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `uid` int(11) NOT NULL COMMENT '��Աid',
  `openid` varchar(255) NOT NULL COMMENT '��˿���',
  `lessonid` int(11) NOT NULL COMMENT '�γ�id',
  `addtime` int(10) NOT NULL COMMENT '������ʱ��',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `uid` int(11) NOT NULL COMMENT '��Աid',
  `openid` varchar(255) NOT NULL COMMENT '��˿��ʶ',
  `studentno` varchar(20) DEFAULT NULL COMMENT 'ѧ��',
  `nickname` varchar(100) DEFAULT NULL COMMENT '��Ա�ǳ�',
  `parentid` int(11) NOT NULL DEFAULT '0' COMMENT '�Ƽ���id',
  `nopay_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'δ����Ӷ��',
  `pay_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�ѽ���Ӷ��',
  `nopay_lesson` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'δ���ֿγ�����',
  `pay_lesson` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�����ֿγ�����',
  `vip` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ�vip 0.�� 1.��',
  `validity` bigint(11) NOT NULL DEFAULT '0' COMMENT 'vip��Ч��',
  `pastnotice` int(10) NOT NULL DEFAULT '0' COMMENT 'vip�������ǰ����֪ͨʱ��',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '����״̬ 0.�ر� 1.����',
  `uptime` int(10) NOT NULL COMMENT '����ʱ��',
  `addtime` int(11) NOT NULL COMMENT '���ʱ��',
  `agent_level` int(11) NOT NULL DEFAULT '0' COMMENT '����������',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_parentid` (`parentid`),
  KEY `idx_vip` (`vip`),
  KEY `idx_validity` (`validity`),
  KEY `idx_pastnotice` (`pastnotice`),
  KEY `idx_status` (`status`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_member_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `acid` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `ordersn` varchar(255) NOT NULL COMMENT '�������',
  `uid` int(11) NOT NULL COMMENT '��Աid',
  `openid` varchar(255) NOT NULL COMMENT '��˿���',
  `viptime` int(4) NOT NULL COMMENT '��Ա����ʱ��',
  `vipmoney` decimal(10,2) NOT NULL COMMENT '��Ա����۸�',
  `paytype` varchar(50) NOT NULL COMMENT '֧����ʽ',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '����״̬ 0.δ֧�� 1.��֧��',
  `paytime` int(10) DEFAULT '0' COMMENT '����֧��ʱ��',
  `addtime` int(10) NOT NULL COMMENT '�������ʱ��',
  `member1` int(11) NOT NULL COMMENT 'һ�������Աid',
  `commission1` decimal(10,2) NOT NULL COMMENT 'һ������Ӷ��',
  `member2` int(11) NOT NULL COMMENT '���������Աid',
  `commission2` decimal(10,2) NOT NULL COMMENT '��������Ӷ��',
  `member3` int(11) NOT NULL COMMENT '���������Աid',
  `commission3` decimal(10,2) NOT NULL COMMENT '��������Ӷ��',
  `update_time` int(10) DEFAULT NULL COMMENT '����ʱ��',
  `refer_id` int(11) DEFAULT NULL COMMENT '��ֵ��id(��vip����id��Ӧ)',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_paytype` (`paytype`),
  KEY `idx_status` (`status`),
  KEY `idx_refer_id` (`refer_id`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `acid` int(11) NOT NULL,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `ordersn` varchar(255) NOT NULL COMMENT '�������',
  `uid` int(11) NOT NULL COMMENT '��Աid',
  `openid` varchar(255) NOT NULL COMMENT '��˿���',
  `lessonid` int(11) NOT NULL COMMENT '�γ�id',
  `bookname` varchar(255) NOT NULL COMMENT '�γ�����',
  `marketprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'ԭ��',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�γ̼۸�',
  `teacher_income` tinyint(3) NOT NULL DEFAULT '0' COMMENT '��ʦ����(�γ̼۸�ֳ�%)',
  `integral` int(4) NOT NULL DEFAULT '0' COMMENT '���ͻ���',
  `paytype` varchar(50) NOT NULL DEFAULT '0' COMMENT '֧����ʽ',
  `paytime` int(10) NOT NULL DEFAULT '0' COMMENT '֧��ʱ��',
  `member1` int(11) NOT NULL DEFAULT '0' COMMENT 'һ�������Աid',
  `commission1` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'һ��Ӷ��',
  `member2` int(11) NOT NULL DEFAULT '0' COMMENT '���������Աid',
  `commission2` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '����Ӷ��',
  `member3` int(11) NOT NULL DEFAULT '0' COMMENT '���������Աid',
  `commission3` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '����Ӷ��',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '����״̬ -1.��ȡ�� 0.δ֧�� 1.��֧�� 2.������',
  `addtime` int(10) DEFAULT NULL COMMENT '�µ�ʱ��',
  `teacherid` int(11) DEFAULT NULL COMMENT '��ʦid(��fy_lesson_teacher���id�ֶζ�Ӧ)',
  `invoice` varchar(100) DEFAULT NULL COMMENT '��Ʊ̧ͷ',
  `coupon` varchar(50) DEFAULT NULL COMMENT '�γ��Ż���',
  `coupon_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�Ż�����ֵ',
  `validity` int(11) NOT NULL DEFAULT '0' COMMENT '��Ч�� ����Ч���ڿɹۿ�ѧϰ�γ�',
  PRIMARY KEY (`id`),
  KEY `idx_acid` (`acid`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_ordersn` (`ordersn`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_lessonid` (`lessonid`),
  KEY `idx_bookname` (`bookname`),
  KEY `idx_teacherid` (`teacherid`),
  KEY `idx_paytype` (`paytype`),
  KEY `idx_status` (`status`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_parent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�ID',
  `cid` int(11) NOT NULL COMMENT '����ID',
  `bookname` varchar(255) NOT NULL COMMENT '�γ�����',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�γ̼۸�',
  `isdiscount` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�Ƿ����ÿγ��ۿ�',
  `vipdiscount` int(3) NOT NULL DEFAULT '0' COMMENT 'vip��Ա�ۿ�',
  `integral` int(11) NOT NULL DEFAULT '0' COMMENT '�������ͻ���',
  `images` varchar(255) DEFAULT NULL COMMENT '�γ̷�ͼ',
  `descript` text COMMENT '�γ̽���',
  `difficulty` varchar(100) DEFAULT NULL COMMENT '�γ��Ѷ�',
  `buynum` int(11) NOT NULL DEFAULT '0' COMMENT '������������',
  `virtual_buynum` int(11) NOT NULL DEFAULT '0' COMMENT '���⹺������',
  `score` decimal(5,2) NOT NULL COMMENT '�γ̺�����',
  `teacherid` int(11) NOT NULL COMMENT '������ʦid',
  `commission` text COMMENT 'Ӷ�����',
  `displayorder` int(4) NOT NULL DEFAULT '0' COMMENT '�γ�����',
  `status` tinyint(1) NOT NULL COMMENT '�Ƿ��ϼ�',
  `recommendid` varchar(255) DEFAULT NULL COMMENT '�Ƽ����id',
  `vipview` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'vipȨ���Ƿ����ѹۿ�',
  `teacher_income` tinyint(3) NOT NULL DEFAULT '0' COMMENT '��ʦ�ֳ�%',
  `addtime` int(10) NOT NULL COMMENT '���ʱ��',
  `stock` int(11) NOT NULL COMMENT '�γ̿��',
  `poster` text COMMENT '��Ƶ���ŷ���ͼ',
  `validity` int(11) NOT NULL DEFAULT '0' COMMENT '��Ч�� ������ʱ�����������Ч',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_cid` (`cid`),
  KEY `idx_bookname` (`bookname`),
  KEY `idx_teacherid` (`teacherid`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_status` (`status`),
  KEY `idx_recommendid` (`recommendid`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='�γ�����';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_playrecord` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id',
  `uid` int(11) DEFAULT NULL COMMENT '��Աid',
  `openid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '��˿���',
  `lessonid` int(11) DEFAULT NULL COMMENT '�γ�id',
  `sectionid` int(11) DEFAULT NULL COMMENT '�½�id',
  `addtime` int(10) DEFAULT NULL COMMENT '����ʱ��',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_qiniu_upload` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `uid` int(11) DEFAULT NULL COMMENT '��Ա���',
  `openid` varchar(255) DEFAULT NULL COMMENT '��˿���',
  `teacher` int(11) DEFAULT NULL COMMENT '��ʦ���',
  `name` varchar(500) DEFAULT NULL COMMENT '�ļ���',
  `com_name` varchar(1000) DEFAULT NULL COMMENT '����ļ���',
  `qiniu_url` varchar(1000) DEFAULT NULL COMMENT '�ļ�����',
  `size` varchar(100) DEFAULT NULL COMMENT '�ļ���С',
  `addtime` int(10) DEFAULT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_teacher` (`teacher`),
  KEY `idx_name` (`name`(333))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_recommend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `rec_name` varchar(255) DEFAULT NULL COMMENT 'ģ������',
  `displayorder` int(4) DEFAULT NULL COMMENT '����',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�Ƿ���ʾ',
  `addtime` int(10) NOT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_is_show` (`is_show`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id',
  `uid` int(11) DEFAULT NULL COMMENT '��Աid',
  `tjgx` text COMMENT '�Ƽ���ϵ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='�Ƽ���ϵ��';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_setting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `logo` varchar(255) NOT NULL COMMENT 'app��logo',
  `istplnotice` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�Ƿ���ģ����Ϣ 0.�ر� 1.����',
  `buysucc` varchar(255) DEFAULT NULL COMMENT '�û�����γ�֪ͨ',
  `pastvip` varchar(255) DEFAULT NULL COMMENT '�û���Ա�������',
  `cnotice` varchar(255) DEFAULT NULL COMMENT 'Ӷ������',
  `newjoin` varchar(255) DEFAULT NULL COMMENT '���������¼���������',
  `newlesson` varchar(255) DEFAULT NULL COMMENT '�¿�������ѧԱ',
  `neworder` varchar(255) NOT NULL COMMENT '����֪ͨ(����Ա)',
  `manageopenid` text NOT NULL COMMENT '�¶�������(����Ա)',
  `sitename` varchar(100) DEFAULT NULL,
  `banner` text COMMENT '����ͼ',
  `copyright` varchar(255) NOT NULL COMMENT '��Ȩ',
  `vipserver` text COMMENT 'vipʱ���ͼ۸�',
  `sharelink` text COMMENT '���ӷ���',
  `sharelesson` text COMMENT '����γ�',
  `shareteacher` text COMMENT '����ʦ',
  `closespace` int(4) NOT NULL DEFAULT '60' COMMENT '�ر�δ�����ʱ����',
  `closelast` int(10) NOT NULL DEFAULT '0' COMMENT '�ϴ�ִ�йر�δ�����ʱ��',
  `is_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�������� 0.�ر� 1.����',
  `self_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�����ڹ� 0.�ر� 1.����',
  `sale_rank` tinyint(1) NOT NULL DEFAULT '1' COMMENT '������� 1.�κ��� 2.VIP���',
  `level` tinyint(1) NOT NULL DEFAULT '3' COMMENT '�����ȼ�',
  `commission` text COMMENT 'Ӷ�����',
  `cash_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '���ַ�ʽ 1.����Ա��� 2.�Զ�����',
  `cash_lower` decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT '����������� Ĭ��Ϊ1Ԫ',
  `mchid` varchar(100) DEFAULT NULL COMMENT '΢��֧���̻���',
  `mchkey` varchar(255) DEFAULT NULL COMMENT '΢��֧���̻�֧����Կ',
  `serverIp` varchar(100) DEFAULT NULL COMMENT '������Ip',
  `savetype` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0.�����洢��ʽ 1.��ţ�ƴ洢 2.��Ѷ�ƴ洢',
  `qiniu` text COMMENT '��ţ�ƴ洢����',
  `qcloud` text COMMENT '��Ѷ�ƴ洢',
  `print_error` tinyint(1) NOT NULL DEFAULT '0' COMMENT '��ӡ��Ƶ������Ϣ 0.�ر� 1.����',
  `vipdiscount` int(3) NOT NULL DEFAULT '0' COMMENT 'vip��Ա����γ��ۿ�',
  `footnav` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�˵���ʾ��ʽ 0.�ײ��˵� 1.�����˵�',
  `lessonshow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '��ҳ�γ���ʾ 1.һ��һ���γ� 2.һ�������γ�',
  `teacher_income` tinyint(3) NOT NULL DEFAULT '0' COMMENT '��ʦ����(�γ̼۸�ֳ�%)',
  `isfollow` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'ǿ�ƹ�ע���ں� 0.��ǿ�� 1.ǿ��',
  `qrcode` varchar(255) DEFAULT NULL COMMENT '���ںŶ�ά��',
  `mustinfo` tinyint(1) NOT NULL DEFAULT '0',
  `autogood` int(4) NOT NULL DEFAULT '0' COMMENT '��ʱ�Զ����� Ĭ��0Ϊ�ر�',
  `posterbg` varchar(255) DEFAULT NULL COMMENT '�ƹ㺣������ͼ',
  `addtime` int(10) NOT NULL COMMENT '���ʱ��',
  `vipdesc` text NOT NULL COMMENT 'vip��������',
  `vip_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'VIP������������',
  `cash_way` text NOT NULL COMMENT '���ַ�ʽ',
  `adv` text NOT NULL COMMENT '�γ�����ҳ���',
  `newcash` varchar(255) NOT NULL COMMENT '��������֪ͨ(����Ա)',
  `mobilechange` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ����޸��ֻ����� 0.�ر� 1.����',
  `main_color` varchar(50) DEFAULT NULL COMMENT 'ǰ̨��ɫ��',
  `minor_color` varchar(50) DEFAULT NULL COMMENT 'ǰ̨��ɫ��',
  `teacherlist` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ���ʾ��ʦ�б� 0.����ʾ  1.��ʾ',
  `category_ico` varchar(255) NOT NULL COMMENT '���з���ͼ��',
  `rec_income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'ֱ���Ƽ��������',
  `apply_teacher` varchar(255) DEFAULT NULL COMMENT '���뽲ʦ��פ���֪ͨ',
  `viporder_commission` text COMMENT 'VIP����Ӷ�����(�����ֵ���趨����ʹ��ȫ�ַ���Ӷ�����)',
  `index_lazyload` text COMMENT '��ҳ�ӳټ���',
  `cash_follow` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�����Ƿ���Ҫ��ע���ں�',
  `front_color` text COMMENT 'ǰ̨������ɫ',
  `self_diy` text COMMENT '���������Զ�����Ŀ',
  `stock_config` tinyint(1) DEFAULT '0' COMMENT '�Ƿ����ÿ�� 0.�� 1.��',
  `is_invoice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '���߷�Ʊ 0.��֧�� 1.֧��',
  `index_upgrade` tinyint(1) NOT NULL DEFAULT '0' COMMENT '��������',
  `poster_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�ƹ㺣����ʽ 1.ֱ�ӽ���΢����  2.ֱ�ӽ��빫�ں�',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='��������';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_son` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `parentid` int(11) NOT NULL COMMENT '�γ̹���id',
  `title` varchar(255) NOT NULL COMMENT '�½�����',
  `sectiontype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�½����� 1.��Ƶ�½� 2.ͼ���½�',
  `savetype` tinyint(1) NOT NULL COMMENT '�洢��ʽ 0.�����洢��ʽ 1.��ţ�洢 2.��Ƕ���Ŵ���ģʽ',
  `videourl` text COMMENT '�½���Ƶurl',
  `videotime` varchar(100) NOT NULL COMMENT '��Ƶʱ��',
  `content` text COMMENT '�½�����',
  `displayorder` int(4) NOT NULL DEFAULT '0',
  `is_free` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ�Ϊ�����½� 0.�� 1.��',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�Ƿ���ʾ 0.���� 1.��ʾ',
  `addtime` int(10) NOT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_parentid` (`parentid`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='�γ��½�����';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_syslog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id',
  `admin_uid` int(11) DEFAULT NULL COMMENT '����Աid',
  `admin_username` varchar(50) DEFAULT NULL COMMENT '����Ա�ǳ�',
  `log_type` tinyint(1) DEFAULT NULL COMMENT '�������� 1.���� 2.ɾ�� 3����',
  `function` varchar(100) DEFAULT NULL COMMENT '�����Ĺ���',
  `content` varchar(1000) DEFAULT NULL COMMENT '��������',
  `ip` varchar(50) DEFAULT NULL COMMENT '����IP��ַ',
  `addtime` int(10) NOT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_admin_uid` (`admin_uid`),
  KEY `idx_log_type` (`log_type`),
  KEY `idx_function` (`function`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='������־��';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_teacher` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '��Աid',
  `openid` varchar(100) NOT NULL DEFAULT '0' COMMENT '��˿���',
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `teacher` varchar(100) NOT NULL COMMENT '��ʦ����',
  `qq` varchar(20) DEFAULT NULL COMMENT '��ʦQQ',
  `qqgroup` varchar(20) DEFAULT NULL COMMENT '��ʦQQȺ',
  `qqgroupLink` varchar(255) DEFAULT NULL COMMENT 'QQȺ��Ⱥ����',
  `weixin_qrcode` varchar(255) NOT NULL COMMENT '��ʦ΢�Ŷ�ά��',
  `first_letter` varchar(10) DEFAULT NULL COMMENT '��ʦ��������ĸƴ��',
  `teacherdes` text COMMENT '��ʦ����',
  `teacherphoto` varchar(255) DEFAULT NULL COMMENT '��ʦ��Ƭ',
  `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '��ʦ״̬ -1.��˲�ͨ�� 1.���� 2.�����',
  `addtime` int(11) NOT NULL COMMENT '���ʱ��',
  `account` varchar(20) DEFAULT NULL COMMENT '��ʦ��¼�ʺ�',
  `password` varchar(32) DEFAULT NULL COMMENT '��ʦ��¼����',
  `upload` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�ϴ�Ȩ�� 0.��ֹ 1.����',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_account` (`account`),
  KEY `idx_status` (`status`),
  KEY `idx_upload` (`upload`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_teacher_income` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id',
  `uid` int(11) DEFAULT NULL COMMENT '��Աid',
  `openid` varchar(100) DEFAULT NULL COMMENT '��˿id',
  `teacher` varchar(255) DEFAULT NULL COMMENT '��ʦ����',
  `ordersn` varchar(100) DEFAULT NULL COMMENT '�������',
  `bookname` varchar(255) DEFAULT NULL COMMENT '�γ�����',
  `orderprice` decimal(10,2) DEFAULT '0.00' COMMENT '�����۸�',
  `teacher_income` tinyint(3) DEFAULT NULL COMMENT '��ʦ�ֳ�',
  `income_amount` decimal(10,2) DEFAULT '0.00' COMMENT '��ʦʵ������',
  `addtime` int(10) DEFAULT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_teacher` (`teacher`),
  KEY `idx_ordersn` (`ordersn`),
  KEY `idx_bookname` (`bookname`),
  KEY `idx_addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='��ʦ�����';
CREATE TABLE IF NOT EXISTS `ims_fy_lesson_vipcard` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL COMMENT '���ں�id',
  `card_id` varchar(50) DEFAULT NULL COMMENT '����',
  `password` varchar(100) DEFAULT NULL COMMENT '��������',
  `viptime` int(11) DEFAULT NULL COMMENT '����ʱ��',
  `is_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT '״̬ 0.δʹ�� 1.��ʹ��',
  `nickname` varchar(100) DEFAULT NULL COMMENT '��Ա�ǳ�',
  `uid` int(11) DEFAULT NULL COMMENT '��Աid',
  `openid` varchar(100) DEFAULT NULL COMMENT '��˿���',
  `ordersn` varchar(50) DEFAULT NULL COMMENT 'ʹ�ö������(��Ӧvip�������ordersn)',
  `use_time` int(10) DEFAULT NULL COMMENT 'ʹ��ʱ��',
  `validity` int(10) DEFAULT NULL COMMENT '��Ч��',
  `addtime` int(10) unsigned DEFAULT NULL COMMENT '���ʱ��',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_card_id` (`card_id`),
  KEY `idx_is_use` (`is_use`),
  KEY `idx_uid` (`uid`),
  KEY `idx_openid` (`openid`),
  KEY `idx_nickname` (`nickname`),
  KEY `idx_ordersn` (`ordersn`),
  KEY `idx_validity` (`validity`),
  KEY `idx_use_time` (`use_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
pdo_run($sql);
if(!pdo_fieldexists('fy_lesson_article',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_article',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_article',  'title')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `title` varchar(255) DEFAULT NULL COMMENT '����';");
}
if(!pdo_fieldexists('fy_lesson_article',  'author')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `author` varchar(100) DEFAULT NULL COMMENT '����';");
}
if(!pdo_fieldexists('fy_lesson_article',  'content')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `content` text COMMENT '����';");
}
if(!pdo_fieldexists('fy_lesson_article',  'isshow')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `isshow` tinyint(1) DEFAULT '1' COMMENT 'ǰ̨չʾ 0.�ر� 1.����';");
}
if(!pdo_fieldexists('fy_lesson_article',  'displayorder')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `displayorder` varchar(255) DEFAULT '0' COMMENT '���� ��ֵԽ��Խ��ǰ';");
}
if(!pdo_fieldexists('fy_lesson_article',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `addtime` int(10) DEFAULT NULL COMMENT '����ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_article',  'view')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `view` int(11) NOT NULL DEFAULT '0' COMMENT '������';");
}
if(!pdo_fieldexists('fy_lesson_article',  'linkurl')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `linkurl` varchar(1000) DEFAULT NULL COMMENT 'ԭ������';");
}
if(!pdo_fieldexists('fy_lesson_article',  'images')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD `images` varchar(255) DEFAULT NULL COMMENT '����ͼƬ';");
}
if(!pdo_indexexists('fy_lesson_article',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_article',  'idx_title')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD KEY `idx_title` (`title`);");
}
if(!pdo_indexexists('fy_lesson_article',  'idx_author')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD KEY `idx_author` (`author`);");
}
if(!pdo_indexexists('fy_lesson_article',  'idx_isshow')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD KEY `idx_isshow` (`isshow`);");
}
if(!pdo_indexexists('fy_lesson_article',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_article')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_blacklist',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_blacklist')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_blacklist',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_blacklist')." ADD `uniacid` int(11) DEFAULT NULL;");
}
if(!pdo_fieldexists('fy_lesson_blacklist',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_blacklist')." ADD `openid` varchar(255) DEFAULT NULL;");
}
if(!pdo_fieldexists('fy_lesson_blacklist',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_blacklist')." ADD `addtime` int(10) DEFAULT NULL;");
}
if(!pdo_indexexists('fy_lesson_blacklist',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_blacklist')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_blacklist',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_blacklist')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_blacklist',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_blacklist')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'cash_type')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `cash_type` tinyint(1) NOT NULL COMMENT '���ַ�ʽ 1.����Ա��� 2.�Զ�����';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `uid` int(11) DEFAULT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `openid` varchar(255) NOT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'cash_num')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `cash_num` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '���ֽ��';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '״̬ 0.����� 1.���ֳɹ� -1.���δͨ��';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'disposetime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `disposetime` int(10) NOT NULL DEFAULT '0' COMMENT '����ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'partner_trade_no')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `partner_trade_no` varchar(255) DEFAULT NULL COMMENT '�̻�������';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'payment_no')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `payment_no` varchar(255) DEFAULT NULL COMMENT '΢�Ŷ�����';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'remark')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `remark` text COMMENT '����Ա��ע';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'lesson_type')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `lesson_type` tinyint(1) NOT NULL COMMENT '�������� 1.����Ӷ������ 2.�γ���������';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `addtime` int(10) NOT NULL COMMENT '����ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'cash_way')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `cash_way` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1.���ֵ����  2.���ֵ�΢��Ǯ��';");
}
if(!pdo_fieldexists('fy_lesson_cashlog',  'pay_account')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD `pay_account` varchar(50) DEFAULT NULL COMMENT '�����ʺ�';");
}
if(!pdo_indexexists('fy_lesson_cashlog',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_cashlog',  'idx_cash_type')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD KEY `idx_cash_type` (`cash_type`);");
}
if(!pdo_indexexists('fy_lesson_cashlog',  'idx_cash_way')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD KEY `idx_cash_way` (`cash_way`);");
}
if(!pdo_indexexists('fy_lesson_cashlog',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_cashlog',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_cashlog',  'idx_status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD KEY `idx_status` (`status`);");
}
if(!pdo_indexexists('fy_lesson_cashlog',  'idx_lesson_type')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD KEY `idx_lesson_type` (`lesson_type`);");
}
if(!pdo_indexexists('fy_lesson_cashlog',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_cashlog')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_category',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_category')." ADD `id` int(10) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_category',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_category')." ADD `uniacid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�����ʺ�';");
}
if(!pdo_fieldexists('fy_lesson_category',  'name')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_category')." ADD `name` varchar(50) NOT NULL COMMENT '��������';");
}
if(!pdo_fieldexists('fy_lesson_category',  'parentid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_category')." ADD `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '�ϼ�����ID,0Ϊ��һ��';");
}
if(!pdo_fieldexists('fy_lesson_category',  'ico')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_category')." ADD `ico` varchar(255) DEFAULT NULL COMMENT '����ͼ��';");
}
if(!pdo_fieldexists('fy_lesson_category',  'displayorder')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_category')." ADD `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '����';");
}
if(!pdo_fieldexists('fy_lesson_category',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_category')." ADD `addtime` int(10) DEFAULT NULL COMMENT '���ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_collect',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_collect',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_collect',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD `uid` int(11) NOT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_collect',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD `openid` varchar(255) NOT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_collect',  'outid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD `outid` int(11) NOT NULL COMMENT '�ⲿ���(�γ̱�Ż�ʦ���)';");
}
if(!pdo_fieldexists('fy_lesson_collect',  'ctype')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD `ctype` tinyint(1) NOT NULL COMMENT '�ղ����� 1.�γ� 2.��ʦ';");
}
if(!pdo_fieldexists('fy_lesson_collect',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD `addtime` int(10) NOT NULL COMMENT '�ղ�ʱ��';");
}
if(!pdo_indexexists('fy_lesson_collect',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_collect',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_collect',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_collect',  'idx_ctype')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD KEY `idx_ctype` (`ctype`);");
}
if(!pdo_indexexists('fy_lesson_collect',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_collect')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_commission_level',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_level')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_commission_level',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_level')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_commission_level',  'levelname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_level')." ADD `levelname` varchar(50) DEFAULT NULL COMMENT '�����ȼ�����';");
}
if(!pdo_fieldexists('fy_lesson_commission_level',  'commission1')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_level')." ADD `commission1` decimal(10,2) DEFAULT '0.00' COMMENT 'һ������Ӷ�����';");
}
if(!pdo_fieldexists('fy_lesson_commission_level',  'commission2')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_level')." ADD `commission2` decimal(10,2) DEFAULT '0.00' COMMENT '��������Ӷ�����';");
}
if(!pdo_fieldexists('fy_lesson_commission_level',  'commission3')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_level')." ADD `commission3` decimal(10,2) DEFAULT '0.00' COMMENT '��������Ӷ�����';");
}
if(!pdo_fieldexists('fy_lesson_commission_level',  'updatemoney')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_level')." ADD `updatemoney` decimal(10,2) DEFAULT '0.00' COMMENT '��������(����Ӷ��������)';");
}
if(!pdo_indexexists('fy_lesson_commission_level',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_level')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_commission_level',  'idx_levelname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_level')." ADD KEY `idx_levelname` (`levelname`);");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'orderid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `orderid` varchar(255) DEFAULT NULL COMMENT '����id';");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `uid` int(11) DEFAULT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `openid` varchar(255) DEFAULT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'nickname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `nickname` varchar(100) DEFAULT NULL COMMENT '��Ա�ǳ�';");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'bookname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `bookname` varchar(255) DEFAULT NULL COMMENT '�γ�����';");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'change_num')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `change_num` decimal(10,2) DEFAULT '0.00' COMMENT '�䶯��Ŀ';");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'grade')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `grade` tinyint(1) DEFAULT NULL COMMENT 'Ӷ��ȼ�';");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'remark')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `remark` varchar(255) DEFAULT NULL COMMENT '�䶯˵��';");
}
if(!pdo_fieldexists('fy_lesson_commission_log',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD `addtime` int(10) DEFAULT NULL COMMENT '�䶯ʱ��';");
}
if(!pdo_indexexists('fy_lesson_commission_log',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_commission_log',  'idx_orderid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD KEY `idx_orderid` (`orderid`);");
}
if(!pdo_indexexists('fy_lesson_commission_log',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_commission_log',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_commission_log',  'idx_nickname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD KEY `idx_nickname` (`nickname`);");
}
if(!pdo_indexexists('fy_lesson_commission_log',  'idx_bookname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD KEY `idx_bookname` (`bookname`);");
}
if(!pdo_indexexists('fy_lesson_commission_log',  'idx_grade')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD KEY `idx_grade` (`grade`);");
}
if(!pdo_indexexists('fy_lesson_commission_log',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_commission_log')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'card_id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `card_id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `uniacid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'password')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `password` varchar(18) NOT NULL COMMENT '�Ż�����Կ';");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'amount')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�Ż�����ֵ';");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'validity')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `validity` int(10) NOT NULL COMMENT '��Ч��';");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'conditions')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `conditions` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'ʹ������(��xԪ����)';");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'is_use')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `is_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT '״̬ 0.δʹ�� 1.��ʹ��';");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'nickname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `nickname` varchar(50) DEFAULT NULL COMMENT '�ǳ�';");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `uid` int(11) DEFAULT NULL COMMENT '��Ա���';");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `openid` varchar(50) DEFAULT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `ordersn` varchar(50) DEFAULT NULL COMMENT '�������';");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'use_time')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `use_time` int(10) DEFAULT NULL COMMENT 'ʹ��ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_coupon',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD `addtime` int(10) NOT NULL COMMENT '���ʱ��';");
}
if(!pdo_indexexists('fy_lesson_coupon',  'idx_ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD UNIQUE KEY `idx_ordersn` (`ordersn`);");
}
if(!pdo_indexexists('fy_lesson_coupon',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_coupon',  'idx_password')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD KEY `idx_password` (`password`);");
}
if(!pdo_indexexists('fy_lesson_coupon',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_coupon',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_coupon',  'idx_validity')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD KEY `idx_validity` (`validity`);");
}
if(!pdo_indexexists('fy_lesson_coupon',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_coupon')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'orderid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `orderid` int(11) NOT NULL COMMENT '����id';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `ordersn` varchar(255) NOT NULL COMMENT '�������';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'lessonid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `lessonid` int(11) NOT NULL COMMENT '�γ�id';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'bookname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `bookname` varchar(255) NOT NULL COMMENT '�γ�����';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `openid` varchar(255) NOT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `uid` int(11) NOT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'nickname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `nickname` varchar(50) NOT NULL COMMENT '��Ա�ǳ�';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'grade')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `grade` tinyint(1) NOT NULL COMMENT '���� 1.���� 2.���� 3.����';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'content')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `content` text NOT NULL COMMENT '��������';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `addtime` int(10) NOT NULL COMMENT '����ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'reply')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `reply` text COMMENT '���ۻظ�';");
}
if(!pdo_fieldexists('fy_lesson_evaluate',  'teacherid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD `teacherid` int(11) DEFAULT NULL COMMENT '��ʦid(��fy_lesson_teacher���id�ֶζ�Ӧ)';");
}
if(!pdo_indexexists('fy_lesson_evaluate',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_evaluate',  'idx_orderid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD KEY `idx_orderid` (`orderid`);");
}
if(!pdo_indexexists('fy_lesson_evaluate',  'idx_ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD KEY `idx_ordersn` (`ordersn`);");
}
if(!pdo_indexexists('fy_lesson_evaluate',  'idx_lessonid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD KEY `idx_lessonid` (`lessonid`);");
}
if(!pdo_indexexists('fy_lesson_evaluate',  'idx_bookname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD KEY `idx_bookname` (`bookname`);");
}
if(!pdo_indexexists('fy_lesson_evaluate',  'idx_teacherid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD KEY `idx_teacherid` (`teacherid`);");
}
if(!pdo_indexexists('fy_lesson_evaluate',  'idx_grade')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_evaluate')." ADD KEY `idx_grade` (`grade`);");
}
if(!pdo_fieldexists('fy_lesson_history',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_history')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_history',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_history')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_history',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_history')." ADD `uid` int(11) NOT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_history',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_history')." ADD `openid` varchar(255) NOT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_history',  'lessonid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_history')." ADD `lessonid` int(11) NOT NULL COMMENT '�γ�id';");
}
if(!pdo_fieldexists('fy_lesson_history',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_history')." ADD `addtime` int(10) NOT NULL COMMENT '������ʱ��';");
}
if(!pdo_indexexists('fy_lesson_history',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_history')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_history',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_history')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_history',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_history')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_history',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_history')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_member',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_member',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_member',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `uid` int(11) NOT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_member',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `openid` varchar(255) NOT NULL COMMENT '��˿��ʶ';");
}
if(!pdo_fieldexists('fy_lesson_member',  'studentno')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `studentno` varchar(20) DEFAULT NULL COMMENT 'ѧ��';");
}
if(!pdo_fieldexists('fy_lesson_member',  'nickname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `nickname` varchar(100) DEFAULT NULL COMMENT '��Ա�ǳ�';");
}
if(!pdo_fieldexists('fy_lesson_member',  'parentid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `parentid` int(11) NOT NULL DEFAULT '0' COMMENT '�Ƽ���id';");
}
if(!pdo_fieldexists('fy_lesson_member',  'nopay_commission')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `nopay_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'δ����Ӷ��';");
}
if(!pdo_fieldexists('fy_lesson_member',  'pay_commission')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `pay_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�ѽ���Ӷ��';");
}
if(!pdo_fieldexists('fy_lesson_member',  'nopay_lesson')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `nopay_lesson` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'δ���ֿγ�����';");
}
if(!pdo_fieldexists('fy_lesson_member',  'pay_lesson')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `pay_lesson` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�����ֿγ�����';");
}
if(!pdo_fieldexists('fy_lesson_member',  'vip')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `vip` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ�vip 0.�� 1.��';");
}
if(!pdo_fieldexists('fy_lesson_member',  'validity')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `validity` bigint(11) NOT NULL DEFAULT '0' COMMENT 'vip��Ч��';");
}
if(!pdo_fieldexists('fy_lesson_member',  'pastnotice')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `pastnotice` int(10) NOT NULL DEFAULT '0' COMMENT 'vip�������ǰ����֪ͨʱ��';");
}
if(!pdo_fieldexists('fy_lesson_member',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '����״̬ 0.�ر� 1.����';");
}
if(!pdo_fieldexists('fy_lesson_member',  'uptime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `uptime` int(10) NOT NULL COMMENT '����ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_member',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `addtime` int(11) NOT NULL COMMENT '���ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_member',  'agent_level')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD `agent_level` int(11) NOT NULL DEFAULT '0' COMMENT '����������';");
}
if(!pdo_indexexists('fy_lesson_member',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_member',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_member',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_member',  'idx_parentid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD KEY `idx_parentid` (`parentid`);");
}
if(!pdo_indexexists('fy_lesson_member',  'idx_vip')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD KEY `idx_vip` (`vip`);");
}
if(!pdo_indexexists('fy_lesson_member',  'idx_validity')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD KEY `idx_validity` (`validity`);");
}
if(!pdo_indexexists('fy_lesson_member',  'idx_pastnotice')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD KEY `idx_pastnotice` (`pastnotice`);");
}
if(!pdo_indexexists('fy_lesson_member',  'idx_status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD KEY `idx_status` (`status`);");
}
if(!pdo_indexexists('fy_lesson_member',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'acid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `acid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `ordersn` varchar(255) NOT NULL COMMENT '�������';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `uid` int(11) NOT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `openid` varchar(255) NOT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'viptime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `viptime` int(4) NOT NULL COMMENT '��Ա����ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'vipmoney')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `vipmoney` decimal(10,2) NOT NULL COMMENT '��Ա����۸�';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'paytype')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `paytype` varchar(50) NOT NULL COMMENT '֧����ʽ';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '����״̬ 0.δ֧�� 1.��֧��';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'paytime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `paytime` int(10) DEFAULT '0' COMMENT '����֧��ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `addtime` int(10) NOT NULL COMMENT '�������ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'member1')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `member1` int(11) NOT NULL COMMENT 'һ�������Աid';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'commission1')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `commission1` decimal(10,2) NOT NULL COMMENT 'һ������Ӷ��';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'member2')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `member2` int(11) NOT NULL COMMENT '���������Աid';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'commission2')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `commission2` decimal(10,2) NOT NULL COMMENT '��������Ӷ��';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'member3')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `member3` int(11) NOT NULL COMMENT '���������Աid';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'commission3')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `commission3` decimal(10,2) NOT NULL COMMENT '��������Ӷ��';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'update_time')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `update_time` int(10) DEFAULT NULL COMMENT '����ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_member_order',  'refer_id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD `refer_id` int(11) DEFAULT NULL COMMENT '��ֵ��id(��vip����id��Ӧ)';");
}
if(!pdo_indexexists('fy_lesson_member_order',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_member_order',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_member_order',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_member_order',  'idx_paytype')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD KEY `idx_paytype` (`paytype`);");
}
if(!pdo_indexexists('fy_lesson_member_order',  'idx_status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD KEY `idx_status` (`status`);");
}
if(!pdo_indexexists('fy_lesson_member_order',  'idx_refer_id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD KEY `idx_refer_id` (`refer_id`);");
}
if(!pdo_indexexists('fy_lesson_member_order',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_member_order')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_order',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_order',  'acid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `acid` int(11) NOT NULL;");
}
if(!pdo_fieldexists('fy_lesson_order',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_order',  'ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `ordersn` varchar(255) NOT NULL COMMENT '�������';");
}
if(!pdo_fieldexists('fy_lesson_order',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `uid` int(11) NOT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_order',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `openid` varchar(255) NOT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_order',  'lessonid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `lessonid` int(11) NOT NULL COMMENT '�γ�id';");
}
if(!pdo_fieldexists('fy_lesson_order',  'bookname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `bookname` varchar(255) NOT NULL COMMENT '�γ�����';");
}
if(!pdo_fieldexists('fy_lesson_order',  'marketprice')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `marketprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'ԭ��';");
}
if(!pdo_fieldexists('fy_lesson_order',  'price')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�γ̼۸�';");
}
if(!pdo_fieldexists('fy_lesson_order',  'teacher_income')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `teacher_income` tinyint(3) NOT NULL DEFAULT '0' COMMENT '��ʦ����(�γ̼۸�ֳ�%)';");
}
if(!pdo_fieldexists('fy_lesson_order',  'integral')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `integral` int(4) NOT NULL DEFAULT '0' COMMENT '���ͻ���';");
}
if(!pdo_fieldexists('fy_lesson_order',  'paytype')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `paytype` varchar(50) NOT NULL DEFAULT '0' COMMENT '֧����ʽ';");
}
if(!pdo_fieldexists('fy_lesson_order',  'paytime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `paytime` int(10) NOT NULL DEFAULT '0' COMMENT '֧��ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_order',  'member1')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `member1` int(11) NOT NULL DEFAULT '0' COMMENT 'һ�������Աid';");
}
if(!pdo_fieldexists('fy_lesson_order',  'commission1')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `commission1` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'һ��Ӷ��';");
}
if(!pdo_fieldexists('fy_lesson_order',  'member2')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `member2` int(11) NOT NULL DEFAULT '0' COMMENT '���������Աid';");
}
if(!pdo_fieldexists('fy_lesson_order',  'commission2')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `commission2` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '����Ӷ��';");
}
if(!pdo_fieldexists('fy_lesson_order',  'member3')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `member3` int(11) NOT NULL DEFAULT '0' COMMENT '���������Աid';");
}
if(!pdo_fieldexists('fy_lesson_order',  'commission3')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `commission3` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '����Ӷ��';");
}
if(!pdo_fieldexists('fy_lesson_order',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '����״̬ -1.��ȡ�� 0.δ֧�� 1.��֧�� 2.������';");
}
if(!pdo_fieldexists('fy_lesson_order',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `addtime` int(10) DEFAULT NULL COMMENT '�µ�ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_order',  'teacherid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `teacherid` int(11) DEFAULT NULL COMMENT '��ʦid(��fy_lesson_teacher���id�ֶζ�Ӧ)';");
}
if(!pdo_fieldexists('fy_lesson_order',  'invoice')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `invoice` varchar(100) DEFAULT NULL COMMENT '��Ʊ̧ͷ';");
}
if(!pdo_fieldexists('fy_lesson_order',  'coupon')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `coupon` varchar(50) DEFAULT NULL COMMENT '�γ��Ż���';");
}
if(!pdo_fieldexists('fy_lesson_order',  'coupon_amount')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `coupon_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�Ż�����ֵ';");
}
if(!pdo_fieldexists('fy_lesson_order',  'validity')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD `validity` int(11) NOT NULL DEFAULT '0' COMMENT '��Ч�� ����Ч���ڿɹۿ�ѧϰ�γ�';");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_acid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_acid` (`acid`);");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_ordersn` (`ordersn`);");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_lessonid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_lessonid` (`lessonid`);");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_bookname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_bookname` (`bookname`);");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_teacherid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_teacherid` (`teacherid`);");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_paytype')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_paytype` (`paytype`);");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_status` (`status`);");
}
if(!pdo_indexexists('fy_lesson_order',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_order')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_parent',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_parent',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�ID';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'cid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `cid` int(11) NOT NULL COMMENT '����ID';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'bookname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `bookname` varchar(255) NOT NULL COMMENT '�γ�����';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'price')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '�γ̼۸�';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'isdiscount')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `isdiscount` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�Ƿ����ÿγ��ۿ�';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'vipdiscount')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `vipdiscount` int(3) NOT NULL DEFAULT '0' COMMENT 'vip��Ա�ۿ�';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'integral')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `integral` int(11) NOT NULL DEFAULT '0' COMMENT '�������ͻ���';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'images')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `images` varchar(255) DEFAULT NULL COMMENT '�γ̷�ͼ';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'descript')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `descript` text COMMENT '�γ̽���';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'difficulty')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `difficulty` varchar(100) DEFAULT NULL COMMENT '�γ��Ѷ�';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'buynum')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `buynum` int(11) NOT NULL DEFAULT '0' COMMENT '������������';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'virtual_buynum')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `virtual_buynum` int(11) NOT NULL DEFAULT '0' COMMENT '���⹺������';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'score')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `score` decimal(5,2) NOT NULL COMMENT '�γ̺�����';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'teacherid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `teacherid` int(11) NOT NULL COMMENT '������ʦid';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'commission')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `commission` text COMMENT 'Ӷ�����';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'displayorder')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `displayorder` int(4) NOT NULL DEFAULT '0' COMMENT '�γ�����';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `status` tinyint(1) NOT NULL COMMENT '�Ƿ��ϼ�';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'recommendid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `recommendid` varchar(255) DEFAULT NULL COMMENT '�Ƽ����id';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'vipview')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `vipview` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'vipȨ���Ƿ����ѹۿ�';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'teacher_income')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `teacher_income` tinyint(3) NOT NULL DEFAULT '0' COMMENT '��ʦ�ֳ�%';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `addtime` int(10) NOT NULL COMMENT '���ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'stock')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `stock` int(11) NOT NULL COMMENT '�γ̿��';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'poster')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `poster` text COMMENT '��Ƶ���ŷ���ͼ';");
}
if(!pdo_fieldexists('fy_lesson_parent',  'validity')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD `validity` int(11) NOT NULL DEFAULT '0' COMMENT '��Ч�� ������ʱ�����������Ч';");
}
if(!pdo_indexexists('fy_lesson_parent',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_parent',  'idx_cid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD KEY `idx_cid` (`cid`);");
}
if(!pdo_indexexists('fy_lesson_parent',  'idx_bookname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD KEY `idx_bookname` (`bookname`);");
}
if(!pdo_indexexists('fy_lesson_parent',  'idx_teacherid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD KEY `idx_teacherid` (`teacherid`);");
}
if(!pdo_indexexists('fy_lesson_parent',  'idx_displayorder')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD KEY `idx_displayorder` (`displayorder`);");
}
if(!pdo_indexexists('fy_lesson_parent',  'idx_status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD KEY `idx_status` (`status`);");
}
if(!pdo_indexexists('fy_lesson_parent',  'idx_recommendid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD KEY `idx_recommendid` (`recommendid`);");
}
if(!pdo_indexexists('fy_lesson_parent',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_parent')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_playrecord',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_playrecord',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_playrecord',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD `uid` int(11) DEFAULT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_playrecord',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD `openid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_playrecord',  'lessonid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD `lessonid` int(11) DEFAULT NULL COMMENT '�γ�id';");
}
if(!pdo_fieldexists('fy_lesson_playrecord',  'sectionid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD `sectionid` int(11) DEFAULT NULL COMMENT '�½�id';");
}
if(!pdo_fieldexists('fy_lesson_playrecord',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD `addtime` int(10) DEFAULT NULL COMMENT '����ʱ��';");
}
if(!pdo_indexexists('fy_lesson_playrecord',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_playrecord',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_playrecord',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_playrecord',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_playrecord')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_qiniu_upload',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_qiniu_upload',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_qiniu_upload',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD `uid` int(11) DEFAULT NULL COMMENT '��Ա���';");
}
if(!pdo_fieldexists('fy_lesson_qiniu_upload',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD `openid` varchar(255) DEFAULT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_qiniu_upload',  'teacher')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD `teacher` int(11) DEFAULT NULL COMMENT '��ʦ���';");
}
if(!pdo_fieldexists('fy_lesson_qiniu_upload',  'name')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD `name` varchar(500) DEFAULT NULL COMMENT '�ļ���';");
}
if(!pdo_fieldexists('fy_lesson_qiniu_upload',  'com_name')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD `com_name` varchar(1000) DEFAULT NULL COMMENT '����ļ���';");
}
if(!pdo_fieldexists('fy_lesson_qiniu_upload',  'qiniu_url')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD `qiniu_url` varchar(1000) DEFAULT NULL COMMENT '�ļ�����';");
}
if(!pdo_fieldexists('fy_lesson_qiniu_upload',  'size')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD `size` varchar(100) DEFAULT NULL COMMENT '�ļ���С';");
}
if(!pdo_fieldexists('fy_lesson_qiniu_upload',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD `addtime` int(10) DEFAULT NULL COMMENT '���ʱ��';");
}
if(!pdo_indexexists('fy_lesson_qiniu_upload',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_qiniu_upload',  'idx_teacher')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD KEY `idx_teacher` (`teacher`);");
}
if(!pdo_indexexists('fy_lesson_qiniu_upload',  'idx_name')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_qiniu_upload')." ADD KEY `idx_name` (`name`(333));");
}
if(!pdo_fieldexists('fy_lesson_recommend',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_recommend')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_recommend',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_recommend')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_recommend',  'rec_name')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_recommend')." ADD `rec_name` varchar(255) DEFAULT NULL COMMENT 'ģ������';");
}
if(!pdo_fieldexists('fy_lesson_recommend',  'displayorder')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_recommend')." ADD `displayorder` int(4) DEFAULT NULL COMMENT '����';");
}
if(!pdo_fieldexists('fy_lesson_recommend',  'is_show')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_recommend')." ADD `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�Ƿ���ʾ';");
}
if(!pdo_fieldexists('fy_lesson_recommend',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_recommend')." ADD `addtime` int(10) NOT NULL COMMENT '���ʱ��';");
}
if(!pdo_indexexists('fy_lesson_recommend',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_recommend')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_recommend',  'idx_is_show')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_recommend')." ADD KEY `idx_is_show` (`is_show`);");
}
if(!pdo_fieldexists('fy_lesson_relation',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_relation')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_relation',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_relation')." ADD `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_relation',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_relation')." ADD `uid` int(11) DEFAULT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_relation',  'tjgx')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_relation')." ADD `tjgx` text COMMENT '�Ƽ���ϵ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_setting',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'logo')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `logo` varchar(255) NOT NULL COMMENT 'app��logo';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'istplnotice')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `istplnotice` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�Ƿ���ģ����Ϣ 0.�ر� 1.����';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'buysucc')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `buysucc` varchar(255) DEFAULT NULL COMMENT '�û�����γ�֪ͨ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'pastvip')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `pastvip` varchar(255) DEFAULT NULL COMMENT '�û���Ա�������';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'cnotice')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `cnotice` varchar(255) DEFAULT NULL COMMENT 'Ӷ������';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'newjoin')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `newjoin` varchar(255) DEFAULT NULL COMMENT '���������¼���������';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'newlesson')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `newlesson` varchar(255) DEFAULT NULL COMMENT '�¿�������ѧԱ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'neworder')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `neworder` varchar(255) NOT NULL COMMENT '����֪ͨ(����Ա)';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'manageopenid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `manageopenid` text NOT NULL COMMENT '�¶�������(����Ա)';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'sitename')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `sitename` varchar(100) DEFAULT NULL;");
}
if(!pdo_fieldexists('fy_lesson_setting',  'banner')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `banner` text COMMENT '����ͼ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'copyright')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `copyright` varchar(255) NOT NULL COMMENT '��Ȩ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'vipserver')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `vipserver` text COMMENT 'vipʱ���ͼ۸�';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'sharelink')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `sharelink` text COMMENT '���ӷ���';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'sharelesson')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `sharelesson` text COMMENT '����γ�';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'shareteacher')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `shareteacher` text COMMENT '����ʦ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'closespace')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `closespace` int(4) NOT NULL DEFAULT '60' COMMENT '�ر�δ�����ʱ����';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'closelast')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `closelast` int(10) NOT NULL DEFAULT '0' COMMENT '�ϴ�ִ�йر�δ�����ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'is_sale')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `is_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�������� 0.�ر� 1.����';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'self_sale')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `self_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�����ڹ� 0.�ر� 1.����';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'sale_rank')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `sale_rank` tinyint(1) NOT NULL DEFAULT '1' COMMENT '������� 1.�κ��� 2.VIP���';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'level')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `level` tinyint(1) NOT NULL DEFAULT '3' COMMENT '�����ȼ�';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'commission')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `commission` text COMMENT 'Ӷ�����';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'cash_type')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `cash_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '���ַ�ʽ 1.����Ա��� 2.�Զ�����';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'cash_lower')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `cash_lower` decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT '����������� Ĭ��Ϊ1Ԫ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'mchid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `mchid` varchar(100) DEFAULT NULL COMMENT '΢��֧���̻���';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'mchkey')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `mchkey` varchar(255) DEFAULT NULL COMMENT '΢��֧���̻�֧����Կ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'serverIp')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `serverIp` varchar(100) DEFAULT NULL COMMENT '������Ip';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'savetype')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `savetype` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0.�����洢��ʽ 1.��ţ�ƴ洢 2.��Ѷ�ƴ洢';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'qiniu')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `qiniu` text COMMENT '��ţ�ƴ洢����';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'qcloud')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `qcloud` text COMMENT '��Ѷ�ƴ洢';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'print_error')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `print_error` tinyint(1) NOT NULL DEFAULT '0' COMMENT '��ӡ��Ƶ������Ϣ 0.�ر� 1.����';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'vipdiscount')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `vipdiscount` int(3) NOT NULL DEFAULT '0' COMMENT 'vip��Ա����γ��ۿ�';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'footnav')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `footnav` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�˵���ʾ��ʽ 0.�ײ��˵� 1.�����˵�';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'lessonshow')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `lessonshow` tinyint(1) NOT NULL DEFAULT '1' COMMENT '��ҳ�γ���ʾ 1.һ��һ���γ� 2.һ�������γ�';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'teacher_income')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `teacher_income` tinyint(3) NOT NULL DEFAULT '0' COMMENT '��ʦ����(�γ̼۸�ֳ�%)';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'isfollow')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `isfollow` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'ǿ�ƹ�ע���ں� 0.��ǿ�� 1.ǿ��';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'qrcode')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `qrcode` varchar(255) DEFAULT NULL COMMENT '���ںŶ�ά��';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'mustinfo')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `mustinfo` tinyint(1) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'autogood')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `autogood` int(4) NOT NULL DEFAULT '0' COMMENT '��ʱ�Զ����� Ĭ��0Ϊ�ر�';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'posterbg')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `posterbg` varchar(255) DEFAULT NULL COMMENT '�ƹ㺣������ͼ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `addtime` int(10) NOT NULL COMMENT '���ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'vipdesc')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `vipdesc` text NOT NULL COMMENT 'vip��������';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'vip_sale')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `vip_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'VIP������������';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'cash_way')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `cash_way` text NOT NULL COMMENT '���ַ�ʽ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'adv')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `adv` text NOT NULL COMMENT '�γ�����ҳ���';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'newcash')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `newcash` varchar(255) NOT NULL COMMENT '��������֪ͨ(����Ա)';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'mobilechange')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `mobilechange` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ����޸��ֻ����� 0.�ر� 1.����';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'main_color')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `main_color` varchar(50) DEFAULT NULL COMMENT 'ǰ̨��ɫ��';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'minor_color')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `minor_color` varchar(50) DEFAULT NULL COMMENT 'ǰ̨��ɫ��';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'teacherlist')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `teacherlist` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ���ʾ��ʦ�б� 0.����ʾ  1.��ʾ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'category_ico')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `category_ico` varchar(255) NOT NULL COMMENT '���з���ͼ��';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'rec_income')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `rec_income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'ֱ���Ƽ��������';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'apply_teacher')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `apply_teacher` varchar(255) DEFAULT NULL COMMENT '���뽲ʦ��פ���֪ͨ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'viporder_commission')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `viporder_commission` text COMMENT 'VIP����Ӷ�����(�����ֵ���趨����ʹ��ȫ�ַ���Ӷ�����)';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'index_lazyload')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `index_lazyload` text COMMENT '��ҳ�ӳټ���';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'cash_follow')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `cash_follow` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�����Ƿ���Ҫ��ע���ں�';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'front_color')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `front_color` text COMMENT 'ǰ̨������ɫ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'self_diy')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `self_diy` text COMMENT '���������Զ�����Ŀ';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'stock_config')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `stock_config` tinyint(1) DEFAULT '0' COMMENT '�Ƿ����ÿ�� 0.�� 1.��';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'is_invoice')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `is_invoice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '���߷�Ʊ 0.��֧�� 1.֧��';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'index_upgrade')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `index_upgrade` tinyint(1) NOT NULL DEFAULT '0' COMMENT '��������';");
}
if(!pdo_fieldexists('fy_lesson_setting',  'poster_type')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD `poster_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�ƹ㺣����ʽ 1.ֱ�ӽ���΢����  2.ֱ�ӽ��빫�ں�';");
}
if(!pdo_indexexists('fy_lesson_setting',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_setting')." ADD UNIQUE KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_fieldexists('fy_lesson_son',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_son',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_son',  'parentid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `parentid` int(11) NOT NULL COMMENT '�γ̹���id';");
}
if(!pdo_fieldexists('fy_lesson_son',  'title')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `title` varchar(255) NOT NULL COMMENT '�½�����';");
}
if(!pdo_fieldexists('fy_lesson_son',  'sectiontype')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `sectiontype` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�½����� 1.��Ƶ�½� 2.ͼ���½�';");
}
if(!pdo_fieldexists('fy_lesson_son',  'savetype')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `savetype` tinyint(1) NOT NULL COMMENT '�洢��ʽ 0.�����洢��ʽ 1.��ţ�洢 2.��Ƕ���Ŵ���ģʽ';");
}
if(!pdo_fieldexists('fy_lesson_son',  'videourl')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `videourl` text COMMENT '�½���Ƶurl';");
}
if(!pdo_fieldexists('fy_lesson_son',  'videotime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `videotime` varchar(100) NOT NULL COMMENT '��Ƶʱ��';");
}
if(!pdo_fieldexists('fy_lesson_son',  'content')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `content` text COMMENT '�½�����';");
}
if(!pdo_fieldexists('fy_lesson_son',  'displayorder')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `displayorder` int(4) NOT NULL DEFAULT '0';");
}
if(!pdo_fieldexists('fy_lesson_son',  'is_free')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `is_free` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ�Ϊ�����½� 0.�� 1.��';");
}
if(!pdo_fieldexists('fy_lesson_son',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�Ƿ���ʾ 0.���� 1.��ʾ';");
}
if(!pdo_fieldexists('fy_lesson_son',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD `addtime` int(10) NOT NULL COMMENT '���ʱ��';");
}
if(!pdo_indexexists('fy_lesson_son',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_son',  'idx_parentid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD KEY `idx_parentid` (`parentid`);");
}
if(!pdo_indexexists('fy_lesson_son',  'idx_status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_son')." ADD KEY `idx_status` (`status`);");
}
if(!pdo_fieldexists('fy_lesson_syslog',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_syslog',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_syslog',  'admin_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD `admin_uid` int(11) DEFAULT NULL COMMENT '����Աid';");
}
if(!pdo_fieldexists('fy_lesson_syslog',  'admin_username')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD `admin_username` varchar(50) DEFAULT NULL COMMENT '����Ա�ǳ�';");
}
if(!pdo_fieldexists('fy_lesson_syslog',  'log_type')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD `log_type` tinyint(1) DEFAULT NULL COMMENT '�������� 1.���� 2.ɾ�� 3����';");
}
if(!pdo_fieldexists('fy_lesson_syslog',  'function')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD `function` varchar(100) DEFAULT NULL COMMENT '�����Ĺ���';");
}
if(!pdo_fieldexists('fy_lesson_syslog',  'content')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD `content` varchar(1000) DEFAULT NULL COMMENT '��������';");
}
if(!pdo_fieldexists('fy_lesson_syslog',  'ip')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD `ip` varchar(50) DEFAULT NULL COMMENT '����IP��ַ';");
}
if(!pdo_fieldexists('fy_lesson_syslog',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD `addtime` int(10) NOT NULL COMMENT '���ʱ��';");
}
if(!pdo_indexexists('fy_lesson_syslog',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_syslog',  'idx_admin_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD KEY `idx_admin_uid` (`admin_uid`);");
}
if(!pdo_indexexists('fy_lesson_syslog',  'idx_log_type')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD KEY `idx_log_type` (`log_type`);");
}
if(!pdo_indexexists('fy_lesson_syslog',  'idx_function')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD KEY `idx_function` (`function`);");
}
if(!pdo_indexexists('fy_lesson_syslog',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_syslog')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `uid` int(11) NOT NULL DEFAULT '0' COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `openid` varchar(100) NOT NULL DEFAULT '0' COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'teacher')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `teacher` varchar(100) NOT NULL COMMENT '��ʦ����';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'qq')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `qq` varchar(20) DEFAULT NULL COMMENT '��ʦQQ';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'qqgroup')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `qqgroup` varchar(20) DEFAULT NULL COMMENT '��ʦQQȺ';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'qqgroupLink')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `qqgroupLink` varchar(255) DEFAULT NULL COMMENT 'QQȺ��Ⱥ����';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'weixin_qrcode')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `weixin_qrcode` varchar(255) NOT NULL COMMENT '��ʦ΢�Ŷ�ά��';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'first_letter')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `first_letter` varchar(10) DEFAULT NULL COMMENT '��ʦ��������ĸƴ��';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'teacherdes')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `teacherdes` text COMMENT '��ʦ����';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'teacherphoto')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `teacherphoto` varchar(255) DEFAULT NULL COMMENT '��ʦ��Ƭ';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '��ʦ״̬ -1.��˲�ͨ�� 1.���� 2.�����';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `addtime` int(11) NOT NULL COMMENT '���ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'account')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `account` varchar(20) DEFAULT NULL COMMENT '��ʦ��¼�ʺ�';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'password')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `password` varchar(32) DEFAULT NULL COMMENT '��ʦ��¼����';");
}
if(!pdo_fieldexists('fy_lesson_teacher',  'upload')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD `upload` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�ϴ�Ȩ�� 0.��ֹ 1.����';");
}
if(!pdo_indexexists('fy_lesson_teacher',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_teacher',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_teacher',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_teacher',  'idx_account')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD KEY `idx_account` (`account`);");
}
if(!pdo_indexexists('fy_lesson_teacher',  'idx_status')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD KEY `idx_status` (`status`);");
}
if(!pdo_indexexists('fy_lesson_teacher',  'idx_upload')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher')." ADD KEY `idx_upload` (`upload`);");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `uniacid` int(11) DEFAULT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `uid` int(11) DEFAULT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `openid` varchar(100) DEFAULT NULL COMMENT '��˿id';");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'teacher')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `teacher` varchar(255) DEFAULT NULL COMMENT '��ʦ����';");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `ordersn` varchar(100) DEFAULT NULL COMMENT '�������';");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'bookname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `bookname` varchar(255) DEFAULT NULL COMMENT '�γ�����';");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'orderprice')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `orderprice` decimal(10,2) DEFAULT '0.00' COMMENT '�����۸�';");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'teacher_income')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `teacher_income` tinyint(3) DEFAULT NULL COMMENT '��ʦ�ֳ�';");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'income_amount')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `income_amount` decimal(10,2) DEFAULT '0.00' COMMENT '��ʦʵ������';");
}
if(!pdo_fieldexists('fy_lesson_teacher_income',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD `addtime` int(10) DEFAULT NULL COMMENT '���ʱ��';");
}
if(!pdo_indexexists('fy_lesson_teacher_income',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_teacher_income',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_teacher_income',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_teacher_income',  'idx_teacher')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD KEY `idx_teacher` (`teacher`);");
}
if(!pdo_indexexists('fy_lesson_teacher_income',  'idx_ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD KEY `idx_ordersn` (`ordersn`);");
}
if(!pdo_indexexists('fy_lesson_teacher_income',  'idx_bookname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD KEY `idx_bookname` (`bookname`);");
}
if(!pdo_indexexists('fy_lesson_teacher_income',  'idx_addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_teacher_income')." ADD KEY `idx_addtime` (`addtime`);");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `uniacid` int(11) NOT NULL COMMENT '���ں�id';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'card_id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `card_id` varchar(50) DEFAULT NULL COMMENT '����';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'password')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `password` varchar(100) DEFAULT NULL COMMENT '��������';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'viptime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `viptime` int(11) DEFAULT NULL COMMENT '����ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'is_use')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `is_use` tinyint(1) NOT NULL DEFAULT '0' COMMENT '״̬ 0.δʹ�� 1.��ʹ��';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'nickname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `nickname` varchar(100) DEFAULT NULL COMMENT '��Ա�ǳ�';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `uid` int(11) DEFAULT NULL COMMENT '��Աid';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `openid` varchar(100) DEFAULT NULL COMMENT '��˿���';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `ordersn` varchar(50) DEFAULT NULL COMMENT 'ʹ�ö������(��Ӧvip�������ordersn)';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'use_time')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `use_time` int(10) DEFAULT NULL COMMENT 'ʹ��ʱ��';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'validity')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `validity` int(10) DEFAULT NULL COMMENT '��Ч��';");
}
if(!pdo_fieldexists('fy_lesson_vipcard',  'addtime')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD `addtime` int(10) unsigned DEFAULT NULL COMMENT '���ʱ��';");
}
if(!pdo_indexexists('fy_lesson_vipcard',  'idx_uniacid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD KEY `idx_uniacid` (`uniacid`);");
}
if(!pdo_indexexists('fy_lesson_vipcard',  'idx_card_id')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD KEY `idx_card_id` (`card_id`);");
}
if(!pdo_indexexists('fy_lesson_vipcard',  'idx_is_use')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD KEY `idx_is_use` (`is_use`);");
}
if(!pdo_indexexists('fy_lesson_vipcard',  'idx_uid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD KEY `idx_uid` (`uid`);");
}
if(!pdo_indexexists('fy_lesson_vipcard',  'idx_openid')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD KEY `idx_openid` (`openid`);");
}
if(!pdo_indexexists('fy_lesson_vipcard',  'idx_nickname')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD KEY `idx_nickname` (`nickname`);");
}
if(!pdo_indexexists('fy_lesson_vipcard',  'idx_ordersn')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD KEY `idx_ordersn` (`ordersn`);");
}
if(!pdo_indexexists('fy_lesson_vipcard',  'idx_validity')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD KEY `idx_validity` (`validity`);");
}
if(!pdo_indexexists('fy_lesson_vipcard',  'idx_use_time')) {
	pdo_query("ALTER TABLE ".tablename('fy_lesson_vipcard')." ADD KEY `idx_use_time` (`use_time`);");
}

?>