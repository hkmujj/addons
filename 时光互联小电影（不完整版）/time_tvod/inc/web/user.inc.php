<?php
 	defined('IN_IA') or exit('Access Denied');
	
	global $_W, $_GPC;
	checklogin();
	load()->func('tpl');
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$uniacid = !empty($_W['uniacid']) ? $_W['uniacid'] : intval($_GET['uniacid']);

	if ($operation == 'delete') {
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE uniacid = {$uniacid} AND id = '{$id}'");
		if (empty($item)) {
			message('不存在或是已经被删除', $this->createWebUrl('user', array('op' => 'display')), 'error');
		}
		pdo_delete($this->modulename . '_user', array('id' => $id));
		message('删除成功', $this->createWebUrl('user', array('op' => 'display')), 'success');
	} elseif ($operation == 'deleteall') {
		if (empty($_GPC['idArr'])) {
			message('不存在或是已经被删除', $this->createWebUrl('user', array('op' => 'display')), 'error');
		}
		$rowcount = 0;
		foreach ($_GPC['idArr'] as $k => $id) {
			$id = intval($id);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE uniacid = {$uniacid} AND uid = {$id}");		
				if($item['uniacid'] == $uniacid){
					pdo_delete($this->modulename . '_data', array('id' => $id, 'uniacid' => $uniacid));
				}
				$rowcount++;
			}
		}
		echo '{"data":"删除成功"}';
		die;
	} elseif ($operation == 'post') {
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE id = '{$id}' and uniacid={$uniacid}");
		if (empty($item)) {
			message('不存在或是已经被删除', $this->createWebUrl('user', array('op' => 'display')), 'error');
		}
		if (checksubmit('submit')) {
			$data = $_GPC['data'];
			$data['point']  = (int) $data['point'];
			$data['money']  = $data['money'];
			$data['freenum']  = $data['freenum'];
			$data['vip_time']  = strtotime($data['vip_time']);
			pdo_update($this->modulename . '_user', $data, array('id' => $id));
			message('更新成功', $this->createWebUrl('user', array('op' => 'display')), 'success');
		}
	} elseif ($operation == 'display') {
		$_W['page']['title'] = '会员列表';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$condition = " WHERE uniacid = {$uniacid}";
		$order_condition = " ORDER BY uid DESC ";
		$sql = 'SELECT * FROM ' . tablename($this->modulename . '_user') . $condition . $order_condition . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
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
	
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_user') . $condition . $order_condition, $params);
		$pager = pagination($total, $pindex, $psize, $url);
	}

	include $this->template('web/user');
	
?>