<?php
 	defined('IN_IA') or exit('Access Denied');
	
	global $_W, $_GPC;
	checklogin();
	load()->func('tpl');
	
	
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$uniacid = intval($_W['uniacid']);
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid ={$uniacid}");
	
	$config = unserialize($setting['config']);
	include_once '../addons/time_tvod/function.php';
	
	if ($operation == 'ajaxlist') {
		if(empty($config['api_url'])){
			exit('API地址错误');
		}
		if(!empty($_GPC['keyword'])){
			$keyword = '&keyword='.$_GPC['keyword'];
		}
		
	
		$remote_url = $config['api_url'].'/index.php?m=api&c=vodlist&a=index'.$keyword.'&token='.md5($config['api_token']).'&p='.$_GPC['page'];	
		$html = xHttp($remote_url);
		
		echo $html;
		die;
	}
	if ($operation == 'ajaxdelete') {
		if(empty($config['api_url'])){
			exit(json_encode(array('code' => 0 ,'reason' => 'API地址错误')));
		}
		
		$remote_url = $config['api_url'].'/index.php?m=api&c=vodlist&a=delete&vid='.$_GPC['vid'].'&token='.md5($config['api_token']); 
		$html = xHttp($remote_url);
		echo $html['content'];
		die;
	}
	include $this->template('web/video');
	
	
?>