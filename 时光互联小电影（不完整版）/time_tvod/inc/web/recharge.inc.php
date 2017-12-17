<?php
 	defined('IN_IA') or exit('Access Denied');
	
	global $_W, $_GPC;
	checklogin();
	load()->func('tpl');
	$uniacid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['uniacid']);
	$_W['page']['title'] = '充值列表';
	
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid = {$uniacid}");
	$config = unserialize($setting['config']);
	$feelevel = explode("\n", $config['vip_fee']);
	foreach ($feelevel as $key => $val) {
		$arr = explode('|', $val);
		$fees[$arr[0]]['id'] = $arr[0];
		$fees[$arr[0]]['name'] = $arr[1];
		$fees[$arr[0]]['day'] = $arr[2];
		$fees[$arr[0]]['price'] = $arr[3];
		$fees[$arr[0]]['point'] = $arr[4];
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = " WHERE uniacid = {$uniacid}";
	$order_condition = " ORDER BY id DESC ";
	$sql = 'SELECT * FROM ' . tablename($this->modulename . '_order') . $condition . $order_condition . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
	$items = pdo_fetchall($sql, $params);
	
	foreach ($items as $key => $val) {
		$user_ids[$val['uid']] = $val['uid'];
	}
	
	if(!empty($user_ids)){
		$userids = implode(',', $user_ids);
		$userlist =  pdo_fetchall("SELECT * FROM " . tablename('mc_members') . " WHERE uid IN({$userids}) ORDER BY uid DESC");	
		foreach ($userlist as $key => $val) {
			$users[$val['uid']] = $val;
		}
	}

	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_order') . $condition . $order_condition, $params);
	$pager = pagination($total, $pindex, $psize, $url);

	include $this->template('web/recharge');
	
?>