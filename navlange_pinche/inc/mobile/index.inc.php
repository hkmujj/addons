<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
	$pin = pdo_fetchall("SELECT * FROM " . tablename('navlange_pinche_pin') . " WHERE uniacid=" . $_W['uniacid'] . " AND departure_time>" . time() . " ORDER BY departure_time DESC");
	$pin_info = array();
	foreach ($pin as $key => $value) {
		$data['id'] = $value['id'];
		$data['departure_station'] = $value['departure_station'];
		$data['terminal_station'] = $value['terminal_station'];
		$data['departure_time'] = $value['departure_time'];
		$data['passenger_count'] = $value['passenger_count'];
		$data['pin_count'] = $this->pin_count($value['id']);
		$data['boarding_place'] = $value['boarding_place'];
		$data['car_series'] = $value['car_series'];
		array_push($pin_info,$data);
	}
	$conf = pdo_get('navlange_pinche_conf',array('uniacid'=>$_W['uniacid']));
	include $this->template('index');
} else if ($op == 'pin_check') {
	$is_member = pdo_fetch("SELECT member.* FROM " . tablename('navlange_pinche_member') . " AS member LEFT JOIN " . tablename('navlange_pinche_travel') . " AS travel ON member.travel_id=travel.id WHERE travel.openid='" . $_W['openid'] . "' AND member.pin_id=" . $_GPC['id']);
	$status = '0';
	if(!empty($is_member)) {
		$status = '1';
	} else {
		$is_full = $this->is_full($_GPC['id']);
		if($is_full) {
			$status = '2';
		}
	}
	message(error($status,''),'','ajax');
} else if ($op == 'pin') {
	$member = pdo_getall('navlange_pinche_member',array('pin_id'=>$_GPC['id']));
	$pined = false;
	foreach ($member as $key => $value) {
		$travel = pdo_get('navlange_pinche_travel',array('id'=>$value['travel_id']));
		if($travel['openid'] == $_W['openid']) {
			$pined = true;
			break;
		}
	}
	if($pined == true) {
		$status = '1';
	} else {
		$is_full = $this->is_full($_GPC['id']);
		if($is_full) {
			$status = '2';
		} else {
			$pin = pdo_get('navlange_pinche_pin',array('id'=>$_GPC['id']));
			$new_travel['departure_station'] = $pin['departure_station'];
			$new_travel['terminal_station'] = $pin['terminal_station'];
			$new_travel['departure_time'] = $pin['departure_time'];
			$new_travel['name'] = $_GPC['name'];
			$new_travel['mobile'] = $_GPC['mobile'];
			$new_travel['amount'] = $_GPC['amount'];
			$new_travel['boarding_place'] = $pin['boarding_place'];
			$new_travel['openid'] = $_W['openid'];
			$new_travel['uniacid'] = $_W['uniacid'];
			$new_travel['release_time'] = time();
			$new_travel['status'] = '0';
			pdo_insert('navlange_pinche_travel',$new_travel);
			$travel_id = pdo_insertid('navlange_pinche_travel');
			$reply['id'] = $travel_id;
			$member['pin_id'] = $_GPC['id'];
			$member['travel_id'] = $travel_id;
			$member['pin_time'] = $new_travel['release_time'];
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
			$reply['id'] = $travel_id;
			$status = '0';
		}
	}	
	message(error($status,$reply),'','ajax');
} else if ($op == 'refresh') {
	$sql = "SELECT * FROM " . tablename('navlange_pinche_pin') . " WHERE uniacid=" . $_W['uniacid'] . " AND departure_station LIKE '%" . $_GPC['departure_station'] . "%' AND terminal_station LIKE '%" . $_GPC['terminal_station'] . "%'";
	if(!empty($_GPC['date'])) {
		$today = strtotime($_GPC['date']);
		$tomorrow = $today + 24*60*60;
		$sql .= " AND departure_time>=" . $today . " AND departure_time<" . $tomorrow;
	} else {
		$sql .= " AND departure_time>=" . time();
	}
	$sql .= " ORDER BY departure_time DESC";
	$pin = pdo_fetchall($sql);
	$pin_info = array();
	foreach ($pin as $key => $value) {
		$data['id'] = $value['id'];
		$data['departure_station'] = $value['departure_station'];
		$data['terminal_station'] = $value['terminal_station'];
		$data['departure_time'] = date('m-d H:i',$value['departure_time']);
		$data['passenger_count'] = $value['passenger_count'];
		$data['pin_count'] = $this->pin_count($value['id']);
		$data['boarding_place'] = $value['boarding_place'];
		$owner = pdo_get('navlange_pinche_owner',array('id'=>$value['owner_id']));
		$data['car_series'] = $owner['car_series'];
		array_push($pin_info,$data);
	}
	message(error(0,$pin_info),'','ajax');
} else if ($op == 'release_travel') {
	$travel['departure_station'] = $_GPC['departure_station'];
	$travel['terminal_station'] = $_GPC['terminal_station'];
	$travel['departure_time'] = strtotime($_GPC['departure_time']);
	$travel['name'] = $_GPC['name'];
	$travel['mobile'] = $_GPC['mobile'];
	$travel['amount'] = $_GPC['amount'];
	$travel['boarding_place'] = $_GPC['boarding_place'];
	$travel['openid'] = $_W['openid'];
	$travel['uniacid'] = $_W['uniacid'];
	$travel['release_time'] = time();
	$travel['status'] = '0';
	pdo_insert('navlange_pinche_travel',$travel);
	message(error(0,''),'','ajax');
}
?>