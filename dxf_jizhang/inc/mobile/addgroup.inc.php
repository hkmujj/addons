<?php
	global $_W,$_GPC;
	load()->func('tpl');
	if(checksubmit()){
		/*if(empty($_W['openid'])){
			message('请在微信客户端打开','','error');
		}*/
		$account_api = WeAccount::create();
		$info = $account_api->fansQueryInfo($_W['openid']);
		if(empty($_GPC['group_name'])){
			message('组名不能为空','','error');
		}
		if($_GPC['group_id']){

			$result = pdo_update('dxf_jizhang_group', array('group_name'=>trim($_GPC['group_name']),'group_desc'=>$_GPC['group_desc']), array('group_id' => $_GPC['group_id']));
			if($result){
				message('修改成功',$this->createMobileUrl("grouplist", array('op' => 'ownc')),'success');
			}else{
				message('修改失败','','error');
			}
		}
		$data=array(
			'uniacid'=>$_W['uniacid'],
			'from_user'=>$_W['openid'],
			'group_name'=>trim($_GPC['group_name']),
			'group_status'=>1,
			'group_desc'=>$_GPC['group_desc'],
			'group_ctime'=>time()
		);

		$ress=pdo_insert('dxf_jizhang_group',$data);
		if($ress){
			$group_id=pdo_insertid();
			$data_u=array(
				'uniacid'=>$_W['uniacid'],
				'group_id'=>$group_id,
				'gu_from_user'=>$_W['openid'],
				'gu_manager'=>1,
				'gu_ctime'=>time()
			);
			$ress=pdo_insert('dxf_jizhang_group_user',$data_u);
			if($ress){
				message('保存成功',$this->createMobileUrl("grouplist", array('op' => 'ownc')),'success');
			}else{
				message('保存失败','','error');
			}
			
		}else{
			message('保存失败','','error');
		}
	}
	include $this->template('addgroup');

