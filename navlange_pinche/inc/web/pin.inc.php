<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
	$pin = pdo_fetchall("SELECT pin.*,owner.name as owner_name,owner.tel as owner_tel,owner.car_series as car_series,owner.car_color as car_color,owner.car_number_1 as car_number_1,owner.car_number_2 as car_number_2,owner.car_number_3 as car_number_3 FROM " . tablename('navlange_pinche_pin') . " AS pin LEFT JOIN " . tablename('navlange_pinche_owner') . " AS owner ON pin.owner_id=owner.id WHERE pin.uniacid=" . $_W['uniacid']);
} else if($op == 'detail') {
    $id = $_GPC['id'];
    $pin = pdo_fetch("SELECT pin.*,owner.name as owner_name,owner.tel as owner_tel,owner.car_series as car_series,owner.car_color as car_color,owner.car_number_1 as car_number_1,owner.car_number_2 as car_number_2,owner.car_number_3 as car_number_3 FROM " . tablename('navlange_pinche_pin') . " AS pin LEFT JOIN " . tablename('navlange_pinche_owner') . " AS owner ON pin.owner_id=owner.id WHERE pin.uniacid=" . $_W['uniacid'] . " AND pin.id=" . $_GPC['id']);
    $travel = pdo_fetchall("SELECT member.*,travel.name as name,travel.mobile as mobile,travel.amount as amount,travel.boarding_place as boarding_place FROM " . tablename('navlange_pinche_member') . " AS member LEFT JOIN " . tablename('navlange_pinche_travel') . " AS travel ON member.travel_id=travel.id WHERE member.pin_id=" . $_GPC['id']);
} else if ($op == 'delete_pin') {
    $member = pdo_getall('navlange_pinche_member',array('pin_id'=>$_GPC['id']));
    foreach ($member as $key => $value) {
        pdo_update('navlange_pinche_travel',array('status'=>'0'),array('id'=>$value['travel_id']));
    }
	pdo_delete('navlange_pinche_member',array('pin_id'=>$_GPC['id']));
	pdo_delete('navlange_pinche_comment',array('pin_id'=>$_GPC['id']));
	pdo_delete('navlange_pinche_pin',array('id'=>$_GPC['id']));
	message(error(0,''),'','ajax');
} else if ($op == 'delete_member') {
    $member = pdo_get('navlange_pinche_member',array('id'=>$_GPC['id']));
    pdo_update('navlange_pinche_travel',array('status'=>'0'),array('id'=>$member['travel_id']));
    pdo_delete('navlange_pinche_member',array('id'=>$_GPC['id']));
    message(error(0,''),'','ajax');
}
include $this->template('pin');
?>