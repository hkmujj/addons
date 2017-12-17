<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
	$owner = pdo_fetchall("SELECT * FROM " . tablename('navlange_pinche_owner') . " WHERE uniacid='" . $_W['uniacid'] . "'");
} else if ($op == 'delete_owner') {
	pdo_delete('navlange_pinche_owner',array('id'=>$_GPC['id']));
	message(error(0, ''), '', 'ajax');
} else if ($op == 'post') {
	$id = intval($_GPC['id']);
	if(checksubmit('edit')) {
		$new_owner['status'] = $_GPC['status'];
		pdo_update('navlange_pinche_owner',$new_owner,array('id'=>$_GPC['id']));
		message('设置成功！','refresh','success');
	}
	if($id > 0) {
		$owner = pdo_get('navlange_pinche_owner',array('id'=>$_GPC['id']));
	}
}
include $this->template('owner');
?>