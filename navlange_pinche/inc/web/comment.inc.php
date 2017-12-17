<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
    if(checksubmit('add')) {
        $comment['content'] = $_GPC['content'];
        $comment['uniacid'] = $_W['uniacid'];
        pdo_insert('navlange_pinche_comment_template',$comment);
        message('添加成功！',$this->createWeburl('comment'),'success');
    }
	$template = pdo_getall('navlange_pinche_comment_template',array('uniacid'=>$_W['uniacid']));
	include $this->template('comment');
} else if ($op == 'delete_template') {
	pdo_delete('navlange_pinche_comment_template',array('id'=>$_GPC['id']));
	message(error(0,''),'','ajax');
}
?>