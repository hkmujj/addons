<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
    $conf = pdo_get('navlange_pinche_conf',array('uniacid'=>$_W['uniacid']));
    $owner = pdo_get('navlange_pinche_owner',array('openid'=>$_W['openid']));
    include $this->template('owner_info');
} else if ($op == 'info_submit') {
    $owner['name'] = $_GPC['name'];
    $owner['tel'] = $_GPC['tel'];
    $owner['car_number_1'] = $_GPC['part_1'];
    $owner['car_number_2'] = $_GPC['part_2'];
    $owner['car_number_3'] = $_GPC['part_3'];
    $owner['car_series'] = $_GPC['car_series'];
    $owner['car_color'] = $_GPC['car_color'];
    $dir = IA_ROOT . '/attachment/images/pinche/license';
    if(!is_dir($dir)) {
        load()->func('file');
        mkdirs($dir);
    }
    $old_owner = pdo_get('navlange_pinche_owner',array('openid'=>$_W['openid']));
    $tmp_file = IA_ROOT . '/attachment/' . $_GPC['vehicle_travel_license'];
    $file_name_arr = explode('/',$_GPC['vehicle_travel_license']);
    $file_name = $file_name_arr[count($file_name_arr)-1];
    $target_file = $dir . '/' . $file_name;
    if(!file_exists($target_file)) {
        $old_file = $dir . '/' . $old_owner['vehicle_travel_license'];
        if(file_exists($old_file)) {
            unlink($old_file);
        }
        load()->func('file');
        rename($tmp_file,$target_file);
        unlink($tmp_file);
        $owner['vehicle_travel_license'] = $file_name;
    }
    $tmp_file = IA_ROOT . '/attachment/' . $_GPC['driving_license'];
    $file_name_arr = explode('/',$_GPC['driving_license']);
    $file_name = $file_name_arr[count($file_name_arr)-1];
    $target_file = $dir . '/' . $file_name;
    if(!file_exists($target_file)) {
        $old_file = $dir . '/' . $old_owner['driving_license'];
        if(file_exists($old_file)) {
            unlink($old_file);
        }
        load()->func('file');
        rename($tmp_file,$target_file);
        unlink($tmp_file);
        $owner['driving_license'] = $file_name;
    }
    $tmp_file = IA_ROOT . '/attachment/' . $_GPC['car_img'];
    $file_name_arr = explode('/',$_GPC['car_img']);
    $file_name = $file_name_arr[count($file_name_arr)-1];
    $target_file = $dir . '/' . $file_name;
    if(!file_exists($target_file)) {
        $old_file = $dir . '/' . $old_owner['car_img'];
        if(file_exists($old_file)) {
            unlink($old_file);
        }
        load()->func('file');
        rename($tmp_file,$target_file);
        unlink($tmp_file);
        $owner['car_img'] = $file_name;
    }
    $owner['status'] = '0';
    if(empty($old_owner)) {
        $owner['openid'] = $_W['openid'];
        $owner['uniacid'] = $_W['uniacid'];
        pdo_insert('navlange_pinche_owner',$owner);
    } else {
        pdo_update('navlange_pinche_owner',$owner,array('openid'=>$_W['openid']));
    }
    message(error(0,''),'','ajax');
} else if ($op == 'cancel_bind') {
    pdo_update('navlange_pinche_owner',array('status'=>'2'),array('openid'=>$_W['openid']));
    message(error(0,$_GPC),'','ajax');
}
?>