<?php
global $_W,$_GPC;
load()->model('mc');
$uid = mc_openid2uid($_W['openid']);
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
    $user = mc_fetch($uid);
} else if($op == 'update') {
    mc_update($uid,array('realname'=>$_GPC['realname'],'mobile'=>$_GPC['mobile']));
    message(error(0,''),'','ajax');
}
$conf = pdo_get('navlange_pinche_conf',array('uniacid'=>$_W['uniacid']));
include $this->template('person_info');
?>