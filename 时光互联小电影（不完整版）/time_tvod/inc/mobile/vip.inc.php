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
	
	$uniacid = intval($_W['uniacid']);
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid = {$uniacid}");
	$config = unserialize($setting['config']);

	if($config['vip_open'] == 1){
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
	}
		
		
		

	include $this->template('vip');
?>