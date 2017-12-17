<?php
global $_W,$_GPC;
$message = pdo_get('navlange_pinche_message',array('uniacid'=>$_W['uniacid']));
if(empty($message)) {
    pdo_insert('navlange_pinche_message',array('uniacid'=>$_W['uniacid']));
}
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'wx';
if($op == 'wx') {
    if(checksubmit()) {
        $message['release_success'] = $_GPC['release_success'];
        $message['join_notice'] = $_GPC['join_notice'];
        $message['join_result'] = $_GPC['join_result'];
        pdo_update('navlange_pinche_message',$message,array('uniacid'=>$_W['uniacid']));
        message("设置成功！",'refresh','success');
    }
} else if ($op == 'sm') {
    if(checksubmit()) {
        $message['juhe_key'] = $_GPC['juhe_key'];
        $message['release_success_juhe_id'] = $_GPC['release_success_juhe_id'];
        $message['join_notice_juhe_id'] = $_GPC['join_notice_juhe_id'];
        $message['join_result_juhe_id'] = $_GPC['join_result_juhe_id'];
        pdo_update('navlange_pinche_message',$message,array('uniacid'=>$_W['uniacid']));
        message("设置成功！",'refresh','success');
    }
} else if ($op == 'sms_control') {
    pdo_update('navlange_pinche_message',array('sms_on'=>$_GPC['status']),array('uniacid'=>$_W['uniacid']));
    message(error(0,''),'','ajax');
}
include $this->template('message');
?>