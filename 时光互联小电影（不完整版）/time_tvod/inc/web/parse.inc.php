<?php
 	defined('IN_IA') or exit('Access Denied');
	
	global $_W, $_GPC;
	checklogin();
	load()->func('tpl');

	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$uniacid = intval($_W['uniacid']);
	$id = intval($_GPC['id']);
	if ($operation == 'delete') {
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_parse') . " WHERE id = '{$id}'");
		if (empty($item)) {
			message('接口不存在', $this->createWebUrl('parse', array('op' => 'display')), 'error');
		}
		pdo_delete($this->modulename . '_parse', array('id' => $id), 'OR');
		message('删除成功', $this->createWebUrl('parse', array('op' => 'display')), 'success');
		
	} elseif ($operation == 'post') {
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_parse') . " WHERE id = '{$id}' and uniacid={$uniacid}");
		}
		if (checksubmit('submit')) {
			$data = $_GPC['data'];
			if (empty($data['title'])) {
				message('请输入接口名称');
			}
			if (empty($data['key'])) {
				message('请输入接口关键值');
			}
			if (empty($id)){
				$check = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_parse') . " WHERE `key` = '{$data[key]}' and uniacid={$uniacid}");
				if(!empty($check)){
					message('该关键字接口已经存在');
				}
			}
			if (empty($data['url'])) {
				message('请输入调用地址');
			}
			$data['uniacid'] = $uniacid;
			
			if (!empty($id)) {
				pdo_update($this->modulename . '_parse', $data, array('id' => $id));
			} else {
				pdo_insert($this->modulename . '_parse', $data);
			}
			message('更新成功', $this->createWebUrl('parse', array('op' => 'display')), 'success');
		}
	} elseif ($operation == 'display') {
		$list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_parse') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id desc");
	}
	include $this->template('web/parse');

?>