<?php
/**
 * 小电影模块处理程序
 *
 * @author 时光互联
 * @url http://www.timecms.com/
 */
 	defined('IN_IA') or exit('Access Denied');
	
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') == false ) {
		message('请在微信内使用');
    }
	
	global $_W, $_GPC;
	
	load()->func('tpl');
	load()->func('cache'); 
	load()->model('mc');
	
	$uniacid = intval($_W['uniacid']);
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid ={$uniacid}");
	$config = unserialize($setting['config']);

	
	if(empty($_W['member'])){
		$userinfo = mc_oauth_userinfo();
		$uid = mc_openid2uid($userinfo['openid']);
		$user = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE uniacid = {$uniacid} AND uid = {$uid}");
		if(empty($user)){
			$user['vip_time'] = $_W['timestamp'];
			$user['uid'] = $uid;
			$user['freenum'] = (int) $config['vip_freenum'];
			$user['uniacid'] = $uniacid;
			pdo_insert($this->modulename . '_user', $user);
		}
	}else{
		$uid = $_W['member']['uid'];
		$user = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE uniacid = {$uniacid} AND uid = {$uid}");
		if(empty($user)){
			$user['vip_time'] = $_W['timestamp'];
			$user['uid'] = $uid;
			$user['freenum'] = (int) $config['vip_freenum'];
			$user['uniacid'] = $uniacid;
			pdo_insert($this->modulename . '_user', $user);
		}
	}
	
	if(empty($config['api_url'])){
		exit('API地址错误');
	}
	
	//防封域名跳转
	if(!empty($config['share_safe'])){
		$domains = explode(',', $config['share_safe']);
		$domain = $_SERVER['SERVER_NAME'];
		if(!in_array($domain, $domains)){
			$rand = mt_rand(0, count($domains) - 1);
			$todomain = $domains[$rand];
		    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		    $nowurl = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$nowurl = str_replace($domain, $todomain, $nowurl);
			header('location:'.$nowurl);
			die;
		}
	}
	
	$type_cache_key = 'addon_time_tvod_type:'.$uniacid;	
	$types = cache_read($type_cache_key);
	
	include_once '../addons/time_tvod/function.php';
	

	
	if(empty($types)){
		
		
		$remote_url = $config['api_url'].'/index.php?m=api&c=vodtype&a=index&token='.md5($config['api_token']);
		$data_type = xHttp($remote_url,array(),'POST');
	
		$array_type = json_decode($data_type,true);	
	
		
		foreach ($array_type['result'] as $key => $val) {
			$types[$val['t_id']] = $val;
		}
		cache_write($type_cache_key, $types);
	}
	
		
	$swiper = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_advert') . " WHERE type = 1 AND uniacid ={$uniacid}");

	$remote_url = $config['api_url'].'/index.php?m=api&c=vodindex&a=index&token='.md5($config['api_token']); 
	
	$data_list = xHttp($remote_url,array(),'POST');
	$array_list = json_decode($data_list,true);
	$list = $array_list['result'];
		
	
	foreach ($list['new'] as $key => $val) {
		if(strpos($val['d_pic'],'http') === false ){
			$list['new'][$key]['d_pic'] = $config['api_web'].'/' . $val['d_pic'];
		}	
	}
	foreach ($list['hot'] as $key => $val) {
		if(strpos($val['d_pic'],'http') === false ){
			$list['hot'][$key]['d_pic'] = $config['api_web'].'/' . $val['d_pic'];
		}	
	}

	include $this->template('index');
?>