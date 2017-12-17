<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
	$pin = pdo_get('navlange_pinche_pin',array('id'=>$_GPC['id']));
	$car_series = $pin['car_series'];
	$pin_count = $this->pin_count($_GPC['id']);
	$comment = pdo_getall('navlange_pinche_comment',array('pin_id'=>$_GPC['id']));
	$comment_info = array();
	foreach ($comment as $key => $value) {
		$wx_member = pdo_get('navlange_wx_member',array('openid'=>$value['openid']));
		$data['nickname'] = $wx_member['nickname'];
		$data['headimgurl'] = $wx_member['headimgurl'];
		$data['time'] = date('m-d H:i',$value['time']);
		$item = json_decode($value['content']);
		foreach ($item as $key => $value) {
			$data['content'] .= $value . ';';
		}
		array_push($comment_info,$data);
	}
	$my_comment = pdo_get('navlange_pinche_comment',array('pin_id'=>$_GPC['id'],'openid'=>$_W['openid']));
	if(!empty($my_comment)) {
		$commentable = '0';
	} else {
		$commentable = '1';
	}
	$template = pdo_getall('navlange_pinche_comment_template',array('uniacid'=>$_W['uniacid']));
	include $this->template('info');
} else if ($op == 'comment') {
	$comment['pin_id'] = $_GPC['pin_id'];
	$comment['uniacid'] = $_W['uniacid'];
	$comment['openid'] = $_W['openid'];
	$content = array();
	foreach ($_GPC['template'] as $key => $value) {
		$template = pdo_get('navlange_pinche_comment_template',array('id'=>$value));
		array_push($content,$template['content']);
	}
	if($_GPC['other'] != '') {
		array_push($content,$_GPC['other']);
	}
	$comment['content'] = json_encode($content);
	$comment['time'] = time();
	pdo_insert('navlange_pinche_comment',$comment);
	message(error(0,''),'','ajax');
}

?>