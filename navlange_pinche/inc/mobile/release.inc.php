<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
$owner = pdo_get('navlange_pinche_owner',array('uniacid'=>$_W['uniacid'],'openid'=>$_W['openid']));
if($op == 'index') {
	
} else if ($op == 'release_submit') {
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
	if($message['sms_on'] == '1') {
		$this->release_success_sms($owner['tel'],$owner['name']);
	}
	message(error(0,''),'','ajax');
}

$conf = pdo_get('navlange_pinche_conf',array('uniacid'=>$_W['uniacid']));
if($conf['release_need_license'] == '1' && (empty($owner) || $owner['status'] != '1')) {
	header("Location: " . $this->createMobileurl('owner_info'));
}
include $this->template('release');
?>