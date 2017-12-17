<?php
/**
 * 小电影模块处理程序
 *
 * @author 时光互联
 * @url http://www.timecms.com/
 */
 	defined('IN_IA') or exit('Access Denied');
	
	global $_W, $_GPC;
	load()->func('tpl');
	load()->func('cache'); 
	
	$uniacid = intval($_W['uniacid']);
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid ={$uniacid}");
	$config = unserialize($setting['config']);

	if(empty($config['api_url'])){
		exit('API地址错误');
	}
	
	$type_cache_key = 'addon_time_tvod_type:'.$uniacid;	
	$types = cache_read($type_cache_key);
	include_once '../addons/time_tvod/function.php';
	if(empty($types)){
		
		$remote_url = $config['api_url'].'/index.php?m=api&c=vodtype&a=index&token='.md5($config['api_token']); 
		$data_type = xHttp($remote_url,array(),'POST');
	
		$array_type = json_decode($data_type,true);
		$types = $array_type['result'];
		cache_write($type_cache_key, $types);
	}

	$id = intval($_GPC['id']);	
	$p = intval($_GPC['p']);	
	$keyword = urlencode(trim($_GPC['keyword']));
	
	
	if($id){
		$remote_url = $config['api_url'].'/index.php?m=api&c=vodajax&a=index&p='. $p .'&id=' . $id .'&token='.md5($config['api_token']); 
	}else{
		$remote_url = $config['api_url'].'/index.php?m=api&c=vodajax&a=index&p='. $p .'&keyword=' . $keyword .'&token='.md5($config['api_token']);
	}
	
	
	
	$data_list = xHttp($remote_url,array(),'POST');
	

	$array_list = json_decode($data_list,true);
	
	
	foreach ($array_list['result'] as $key => $val) {
		if(strpos($val['d_pic'],'http') === false ){
			$array_list['result'][$key]['d_pic'] = $config['api_web'].'/' . $val['d_pic'];
		}
		
		$array_list['result'][$key]['url'] = $this->createMobileUrl('play',array('id' => $val['d_id']));
	}	
	echo json_encode($array_list);
	die;
?>