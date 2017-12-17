<?php
 	defined('IN_IA') or exit('Access Denied');
	
	global $_W, $_GPC;
	checklogin();
	load()->func('tpl');
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$uniacid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['uniacid']);

	if ($operation == 'audit') {
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_cash') . " WHERE id = '{$id}' and uniacid = {$uniacid}");
		}
		pdo_update($this->modulename . '_cash', array('status' => 1), array('id' => $id));
		message('操作成功', $this->createWebUrl('cash', array('op' => 'display')), 'success');
	} elseif ($operation == 'close'){
		
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_cash') . " WHERE id = '{$id}' and uniacid = {$uniacid}");
		}
		
		$user = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE uid = '{$item['uid']}'");
		
		pdo_update($this->modulename . '_user', array('money' => $user['money'] + $item['money']), array('uid' => $user['uid']));
		pdo_update($this->modulename . '_cash', array('status' => -1), array('id' => $item['id']));
		message('操作成功', $this->createWebUrl('cash', array('op' => 'display')), 'success');
		
	} elseif ($operation == 'display') {
		$_W['page']['title'] = '提现列表';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$condition = " WHERE uniacid = {$uniacid}";
		$order_condition = " ORDER BY id DESC ";
		$sql = 'SELECT * FROM ' . tablename($this->modulename . '_cash') . $condition . $order_condition . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
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
	
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_cash') . $condition . $order_condition, $params);
		$pager = pagination($total, $pindex, $psize, $url);
	}

	include $this->template('web/cash');
	
?>