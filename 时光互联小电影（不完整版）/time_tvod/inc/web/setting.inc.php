<?php
 	defined('IN_IA') or exit('Access Denied');
	
	global $_W, $_GPC;
	checklogin();
	load()->func('tpl');
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$uniacid = intval($_W['uniacid']);
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid ={$uniacid}");
	$config = unserialize($setting['config']);
	if (checksubmit('submit')) {
		$data = $_GPC['data'];		
		if (empty($data['title'])) {
			message('请输入平台名称');
		}
		if (strpos($data['share_image'], 'http') === false) {
			$data['share_image'] = $_W['attachurl'] . $data['share_image'];
		}
		if (strpos($data['yd_image'], 'http') === false) {
			$data['yd_image'] = $_W['attachurl'] . $data['yd_image'];
		}		
		$data = array('uniacid' => intval($uniacid), 'config' => serialize($data));
		if (!empty($setting)) {
			pdo_update($this->modulename . '_setting', $data, array('uniacid' => $uniacid));
		} else {
			pdo_insert($this->modulename . '_setting', $data);
		}
		message('资料更新成功', $this->createWebUrl('setting', array('op' => 'display')), 'success');
	}
	
	$cert = array();
	$cert['ca'] = '/addons/time_tvod/cert/' . md5("root{$_W['uniacid']}ca") . ".pem";	//CA
	$cert['cert'] ='/addons/time_tvod/cert/' . md5("apiclient_{$_W['uniacid']}cert") . ".pem";	//CERT
	$cert['key'] = '/addons/time_tvod/cert/' . md5("apiclient_{$_W['uniacid']}key") . ".pem";	//KEY
	
	include $this->template('web/setting');
?>