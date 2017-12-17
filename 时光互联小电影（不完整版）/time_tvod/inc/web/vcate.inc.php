<?php
	defined('IN_IA') or exit('Access Denied');
	
	global $_W, $_GPC;
	checklogin();
	load()->func('tpl');
	//默认显示那个路由
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	//获取公众号id
	$uniacid = intval($_W['uniacid']);
	//链接数据库查询
	$setting = pdo_fetch("SELECT * FROM". tablename($this->modulename . '_setting') . " WHERE uniacid = {$uniacid}");
	if(empty($setting)){
		message('请先配置站点信息');
	}
	
	//判断路由走的那个控制器
	if($operation == 'delete'){
		//获取要删除的分类id
		$cate_id = intval($_GPC['cate_id']);
		
		//查询数据库里面是否存在这个id
		$item = pdo_fetch("SELECT * FROM" . tablename($this->modulename . '_cate') . " WHERE cate_id = '{$cate_id}'");
		
		//判断数据库里是否存在
		if(empty($item)){
			message('视频分类不存在或者已删除', $this->createWebUrl('vcate', array('op' => 'display')),'error');
		}
		
		//删除成功
		pdo_delete($this->modulename . "_cate" ,array('cate_id' => $cate_id), 'OR');
		message('删除成功',$this->createWebUrl('vcate', array('op' => 'display')), 'success');		
		
	}elseif ($operation == 'post'){
		//获取分类id
		$cate_id = intval($_GPC['cate_id']);
		
		//判断数据库里有的话就获取出来
		if(!empty($cate_id)){
			$item = pdo_fetch("SELECT * FROM ". tablename($this->modulename . '_cate') . " WHERE cate_id = '{$cate_id}' and uniacid = '{$uniacid}'");
		}
		
		//提交
		if(checksubmit('submit')){
			$data = $_GPC['data'];
			$data['uniacid'] = $uniacid;
			
			//判断视频分类是否为空
			if(empty($data['cate_name'])){
				message('视频分类不能为空');
			}
			
			//判断分类id是否存在
			if(!empty($cate_id)){
				//存在就更新
				pdo_update($this->modulename . '_cate', $data ,array('cate_id' => $cate_id));
			}else{
				//不存在就添加
				pdo_insert($this->modulename . '_cate',$data);
			}
			
			//更新成功
			message('更新成功', $this->createWebUrl('vcate', array('op' => 'display')),'success');
		}
		
		
	}elseif ($operation == 'display'){
		//判断排序是否为空
		if(!empty($_GPC['orderby'])){
			//循环
			foreach($_GPC['orderby'] as $cate_id => $order){
				pdo_update($this->modulename . '_cate', array('orderby' => $order), array('cate_id' => $cate_id));
			}
		}
		
		//获取视频分类表所有的数据
		$list = pdo_fetchall("SELECT * FROM" . tablename($this->modulename . '_cate') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY cate_id DESC,orderby DESC");
	
	}
	
	include $this->template('web/vcate');
?>