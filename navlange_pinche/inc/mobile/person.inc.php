<?php
global $_W;
$conf = pdo_get('navlange_pinche_conf',array('uniacid'=>$_W['uniacid']));
if($conf['member_on'] == '1') {
    $member = $this->member_info($_W['openid']);
}
include $this->template('person');
?>