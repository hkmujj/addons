<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
    $travel = pdo_getall('navlange_pinche_travel',array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid']));
    $travel_info = array();
    foreach ($travel as $key => $value) {
        $data = $value;
        $data['status'] = $this->get_travel_status($value['id']);
        array_push($travel_info,$data);
    }
} else if($op == 'cancel_travel') {
    pdo_delete('navlange_pinche_member',array('travel_id'=>$_GPC['id']));
    pdo_delete('navlange_pinche_travel',array('id'=>$_GPC['id']));
    message(error(0,''),'','ajax');
}
$conf = pdo_get('navlange_pinche_conf',array('uniacid'=>$_W['uniacid']));
include $this->template('my_travel');
?>