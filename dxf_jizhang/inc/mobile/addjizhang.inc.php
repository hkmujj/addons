<?php
	global $_W,$_GPC;
	load()->func('tpl');
	if(checksubmit()){
		if(empty($_W['openid'])){
			message('请在微信客户端打开','','error');
		}
		if(empty($_GPC['group_id'])){
			message('请新建组之后再进行操作','','error');
		}
		$account_api = WeAccount::create();
		$info = $account_api->fansQueryInfo($_W['openid']);
		$data=array(
			'uniacid'=>$_W['uniacid'],
			'from_user'=>$_W['openid'],
			'nickname'=>$info['nickname'],
			'headimgurl'=>$info['headimgurl'],
			'share_type'=>$_GPC['share_type'],
			'share_name'=>trim($_GPC['share_name']),
			'share_money'=>trim($_GPC['share_money']),
			'share_img'=>implode(',',$_GPC['share_img']),
			'share_time'=>strtotime($_GPC['share_time']),
			'share_desc'=>trim($_GPC['share_desc']),
			'add_time'=>time(),
			'group_id'=>trim($_GPC['group_id'])
		);
		$ress=pdo_insert('dxf_jizhang_share',$data);
		if($ress){
			message('添加成功',$this->createMobileUrl("tradelist"),'success');
		}else{
			message('保存失败','','error');
		}
	}
	$list_group = pdo_fetchall("select * from " . tablename($this->modulename.'_group_user'). " as gu join ". tablename($this->modulename.'_group'). " as g on gu.group_id=g.group_id WHERE g.group_status=1 and  gu.gu_from_user=:from_user AND gu.uniacid=:uniacid ORDER BY  gu.gu_type desc", array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
	$time=explode('-',date('Y-m-d',$_W['timestamp']));
	include $this->template('addjizhang');

