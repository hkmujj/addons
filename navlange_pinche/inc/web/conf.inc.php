<?php
global $_W,$_GPC;
$conf = pdo_get('navlange_pinche_conf',array('uniacid'=>$_W['uniacid']));
if(empty($conf)) {
    pdo_insert('navlange_pinche_conf',array('uniacid'=>$_W['uniacid']));
}
if(checksubmit()) {
    $data['color'] = $_GPC['color'];
    $data['owner_color'] = $_GPC['owner_color'];
    $data['bg_color'] = $_GPC['bg_color'];
    $data['member_on'] = ($_GPC['member_on'] == '1') ? '1' : '0';
    $data['member_type'] = $_GPC['member_type'];
    $data['release_need_license'] = ($_GPC['release_need_license'] == '1') ? '1' : '0';
    $data['agreement_on'] = ($_GPC['agreement_on'] == '1') ? '1' : '0';
    $data['agreement_title'] = $_GPC['agreement_title'];
    $data['agreement_content'] = $_GPC['agreement_content'];
	pdo_update('navlange_pinche_conf',$data,array('uniacid'=>$_W['uniacid']));
	message('设置成功！','refresh','success');
}
include $this->template('conf');
?>