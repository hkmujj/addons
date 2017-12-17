<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
    $pin = pdo_fetchall("SELECT pin.* FROM " . tablename('navlange_pinche_pin') . " AS pin LEFT JOIN " . tablename('navlange_pinche_owner') . " AS owner ON pin.owner_id=owner.id WHERE owner.openid='" . $_W['openid'] . "'");
} else if ($op == 'cancel_pin') {
    $pin = pdo_get('navlange_pinche_pin',array('id'=>$_GPC['id']));
    pdo_update('navlange_pinche_travel',array('status'=>'0'),array('id'=>$_GPC['id']));
    pdo_delete('navlange_pinche_member',array('pin_id'=>$_GPC['id']));
    pdo_delete('navlange_pinche_pin',array('id'=>$_GPC['id']));
    message(error(0,''),'','ajax');
}

include $this->template('owner_pin');
?>