<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
	$travel = pdo_getall('navlange_pinche_travel',array('uniacid'=>$_W['uniacid']));
	include $this->template('travel');
} else if ($op == 'delete_travel') {
    pdo_delete('navlange_pinche_member',array('travel_id'=>$_GPC['id']));
    pdo_delete('navlange_pinche_travel',array('id'=>$_GPC['id']));
	message(error(0,''),'','ajax');
}
?>