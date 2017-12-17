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
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid = {$uniacid} ");
	$config = unserialize($setting['config']);

	if(empty($config['api_url'])){
		message('API地址错误');
	}
	
	//防封域名跳转
	if(!empty($config['share_safe'])){
		$domains = explode(',', $config['share_safe']);
		$domain = $_SERVER['SERVER_NAME'];
		if(!in_array($domain, $domains)){
			$rand = mt_rand(0, count($domains));
			$todomain = $domains[$rand];
		    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		    $nowurl = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$nowurl = str_replace($domain, $todomain, $nowurl);
			header('location:'.$nowurl);
			die;
		}
	}
	include_once '../addons/time_tvod/function.php';
	
	$remote_url = $config['api_url'].'/index.php?m=api&c=vodinfo&a=index&token='.md5($config['api_token']).'&id='. $_GPC['id']; 
	$data_vod = xHttp($remote_url,array(),'POST');
	
	$array_vod = json_decode($data_vod,true);

	$vod = $array_vod['result'];
	if(empty($vod)){
		message('没有找到该视频');
	}

	//观看级别判定
	if($config['vip_open'] == 1 && $vod['d_usergroup'] > 0){
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
		if($user['vip_time'] < $_W['timestamp']){
			if($user['freenum'] > 0){
				pdo_update($this->modulename . '_user', array('freenum' => $user['freenum'] - 1), array('uid' => $uid));
			}else{
				$vipcheck = 1;
			}
		}
	}


	$parse_list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_parse') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id desc");
	foreach ($parse_list as $key => $val) {
		$parses[$val['key']] = $val; 
	}


	$playfrom = explode('$$$', $vod['d_playfrom']);				
	foreach ($playfrom as $key => $val) {		
		$playurl = 'https://open.zmpic.com/parse/index.php?type='.$val.'&url=';
		$arr['type'] = $parses[$val]['title'];
		$arr['url'] =  $parses[$val]['url'];
		$arr['last'] = $parses[$val]['last'];
		$playurls[] = $arr;
	}
	
	$urlss = explode('$$$', $vod['d_playurl']);
	foreach ($urlss as $k => $v) {
		$arr = $arrr= array();
		$urls = explode('#', $v);
		foreach ($urls as $key => $val) {
			$str = explode('$', $val);
			if(count($str) == 1){
				$arr['name'] = $key + 1;
				$arr['url'] = $playurls[$k]['url'].$str[0].$playurls[$k]['last'];
			}else{
				$arr['name'] = $str[0];
				$arr['url'] = $playurls[$k]['url'].$str[1].$playurls[$k]['last'];
			}
			$arrr[] = $arr;
		}	
		$list[$k]['name'] = $playurls[$k]['type'];
		$list[$k]['data'] = $arrr;
	}
	
	//短网址
	$protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$remote_url = 'http://50r.cn/urls/add.json?url='.urlencode($protocal .$_SERVER['HTTP_HOST'].'/app/index.php?i=1&c=entry&id='.$vod['d_id'].'&do=play&m=time_tvod');
	$data_short = xhttp($remote_url,array(),'POST');
	
	$array_short = json_decode($data_short,true);

	$share = array();
	$share['url'] = $protocal .$_SERVER['HTTP_HOST'].'/app/index.php?i=1&c=entry&id='.$vod['d_id'].'&do=play&m=time_tvod';
	$share['title'] = $vod['d_name'];
	$share['pic'] = $config['api_web'].'/'.$vod['d_pic'];
	
	if($vod['d_state'] == 1){
		$share['desc'] = $vod['d_remarks'];
	}else{
		if($vod['d_state'] < 1000 AND  $vod['d_state'] > 0){
			$share['desc'] =  '更新至第'.$vod['d_state'].'集';
		}
		if($vod['d_state'] > 1000){
			$share['desc'] =  '更新至第'.$vod['d_state'].'期';
		}
		if($vod['d_state'] == 0 AND empty($vod['d_remarks'])){
			$share['desc'] =  '全集完结';
		}
	}
	if(empty($share['desc'])){
		$share['desc'] = mb_substr(strip_tags($vod['d_content']),0,50);
	}
	
	include $this->template('play');
?>