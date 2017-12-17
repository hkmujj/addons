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
	load()->func('communication');
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
	$uniacid = intval($_W['uniacid']);
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid ={$uniacid}");
	$config = unserialize($setting['config']);

	if($operation == 'play'){
		$url = base64_decode($_GPC['url']);
		$parse_list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_parse') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id desc");
		foreach ($parse_list as $key => $val) {
			$parses[$val['key']] = $val; 
		}
		if(empty($parses['all'])){
			message('没有开通万能解析！');
		}
		$playurl = $parses['all']['url'].$url;
		
		$adlist = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_advert') . " WHERE type = 2 AND uniacid ={$uniacid}");
		
		
		include $this->template('prise_play');
		die;
	}

	if(!empty($config['wx_prise'])){
		$wxprise = explode("\n", $config['wx_prise']);
		foreach ($wxprise as $key => $val) {
			$arr = explode("|", $val);
			$list[$key]['name'] = $arr[0];
			$list[$key]['url'] = $arr[1];
		}
	}

	include $this->template('prise');
?>