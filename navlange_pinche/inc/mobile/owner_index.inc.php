<?php
global $_W,$_GPC;
$op = !empty($_GPC['op'])? $_GPC['op'] : 'index';
if($op == 'index') {
    $travel = pdo_fetchall("SELECT * FROM " . tablename('navlange_pinche_travel') . " WHERE uniacid=" . $_W['uniacid'] . " AND departure_time>" . time() . " ORDER BY departure_time DESC");
    $travel_info = array();
    foreach ($travel as $key => $value) {
        $owner = pdo_get('navlange_pinche_owner',array('openid'=>$_W['openid']));
        if(!empty($owner)) {
            $member = pdo_fetch("SELECT member.* FROM " . tablename('navlange_pinche_member') . " AS member LEFT JOIN " . tablename('navlange_pinche_pin') . " AS pin ON member.pin_id=pin.id WHERE member.travel_id=" . $value['id'] . " AND pin.owner_id=" . $owner['id']);
        }
        if(empty($member) || empty($owner)) {
            $data = $value;
            load()->model('mc');
            $uid = mc_openid2uid($value['openid']);
            $user = mc_fetch($uid);
            $data['nickname'] = $user['nickname'];
            $data['headimgurl'] = $user['avatar'];
            array_push($travel_info,$data);
        }
    }
} else if ($op == 'get_owner_pin') {
    $pin = pdo_fetchall("SELECT pin.* FROM " . tablename('navlange_pinche_pin') . " AS pin LEFT JOIN " . tablename('navlange_pinche_owner') . " AS owner ON pin.owner_id=owner.id WHERE owner.openid='" . $_W['openid'] . "'");
    $pin_info = array();
    foreach ($pin as $key => $value) {
        $data['departure_station'] = $value['departure_station'];
        $data['terminal_station'] = $value['terminal_station'];
        $data['id'] = $value['id'];
        $data['departure_time'] = date('m-d H:i',$value['departure_time']);
        $data['passenger_count'] = $value['passenger_count'];
        array_push($pin_info,$data);
    }
    message(error(0,$pin_info),'','ajax');
} else if ($op == 'release_submit') {
    $owner = pdo_get('navlange_pinche_owner',array('openid'=>$_W['openid']));
    $pin['owner_id'] = $owner['id'];
    $pin['car_number'] = $owner['car_number_1'] . $owner['car_number_2'] . $owner['car_number_3'];
    $pin['car_series'] = $owner['car_series'];
    $pin['car_color'] = $owner['car_color'];
    $pin['departure_station'] = $_GPC['departure_station'];
    $pin['terminal_station'] = $_GPC['terminal_station'];
    $pin['passenger_count'] = $_GPC['passenger_count'];
    $pin['departure_time'] = strtotime($_GPC['departure_time']);
    $pin['boarding_place'] = $_GPC['boarding_place'];
    $pin['mode'] = $_GPC['mode'];
    $pin['price'] = $_GPC['price'];
    $pin['line'] = $_GPC['line'];
    $pin['note'] = $_GPC['note'];
    $pin['uniacid'] = $_W['uniacid'];
    $pin['release_time'] = time();
    $pin['status'] = '0';
    pdo_insert('navlange_pinche_pin',$pin);
    $id = pdo_insertid('navlange_pinche_pin');
    $message = pdo_get('navlange_pinche_message',array('uniacid'=>$_W['uniacid']));
    $data['first'] = array('value'=>'从'.$_GPC['departure_station'].'到'.$_GPC['terminal_station'].'的拼车发布成功','color'=>'#173177');
    $data['keyword1'] = array('value'=>$_GPC['departure_time'],'color'=>'#173177');
    $data['keyword2'] = array('value'=>$_GPC['departure_station'],'color'=>'#173177');
    $data['keyword3'] = array('value'=>$_GPC['terminal_station'],'color'=>'#173177');
    $data['keyword4'] = array('value'=>$owner['tel'],'color'=>'#173177');
    $data['remark'] = array('value'=>'感谢使用！','color'=>'#173177');
    $url = $_W['siteroot'] . ltrim($this->createMobileurl('info',array('id'=>$id)),'./');
    $acidarr = uni_accounts();//获取当前主公众号下的所有子公众号
    reset($acidarr);//重置数组指针
    $account = current($acidarr);//获取第一个字公众号
    $acid = $account['acid'];
    $acc = WeAccount::create($acid);//实例化消息类对象
    $acc->sendTplNotice($_W['openid'],$message['release_success'],$data,$url,'#FF683F');
    message(error(0,''),'','ajax');
} else if ($op == 'pin_submit') {
    $member['travel_id'] = $_GPC['travel_id'];
    $member['pin_id'] = $_GPC['pin_id'];
    $member['pin_time'] = time();
    $member['status'] = '0';
    $member['uniacid'] = $_W['uniacid'];
    $sn_prefix = date('YmdHis',$member['pin_time']);
    $pin_count = pdo_fetchall("SELECT * FROM " . tablename('navlange_pinche_member') . " WHERE sn LIKE '" . $sn_prefix . "%'");
    $pin_count = count($pin_count)+1;
    if($pin_count < 10) {
        $pin_sn_suffix = '0000' . $pin_count;
    } else if ($pin_count < 100) {
        $pin_sn_suffix = '000' . $pin_count;
    } else if ($pin_count < 1000) {
        $pin_sn_suffix = '00' . $pin_count;
    } else if ($pin_count < 10000) {
        $pin_sn_suffix = '0' . $pin_count;
    }
    $member['sn'] = $sn_prefix . $pin_sn_suffix;
    $pay_count = pdo_fetchall("SELECT * FROM " . tablename('core_paylog') . " WHERE tid LIKE '" . $sn_prefix . "%'");
    $pay_count = count($pay_count)+1;
    if($pay_count < 10) {
        $pay_tid_suffix = '0000' . $pay_count;
    } else if ($pay_count < 100) {
        $pay_tid_suffix = '000' . $pay_count;
    } else if ($pay_count < 1000) {
        $pay_tid_suffix = '00' . $pay_count;
    } else if ($pay_count < 10000) {
        $pay_tid_suffix = '0' . $pay_count;
    }
    $member['pay_tid'] = $sn_prefix . $pay_tid_suffix;
    pdo_insert('navlange_pinche_member',$member);
    message(error(0,''),'','ajax');
}
$conf = pdo_get('navlange_pinche_conf',array('uniacid'=>$_W['uniacid']));
include $this->template('owner_index');
?>