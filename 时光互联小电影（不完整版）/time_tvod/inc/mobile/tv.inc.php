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
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid ={$uniacid}");
	$config = unserialize($setting['config']);
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
	
	
	$ops = !empty($_GPC['ops']) ? $_GPC['ops'] : 'tj';
	
	if($ops == 'tj'){
		//查询推荐
		$tvtuijian = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_tv') . " WHERE uniacid = '{$uniacid}' AND recommend = '1' LIMIT 12");
	}elseif($ops == 'zx'){
		//查询最新
		$tvtuijian = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_tv') . " WHERE uniacid = '{$uniacid}' ORDER BY id DESC LIMIT 12");
	}
	
	$tvcate =  pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_cate'). "WHERE uniacid = '{$uniacid}' ORDER BY orderby DESC ");
	
	foreach ($tvcate as $key => $val) {
		$cate_ids[$val['cate_id']] = $val['cate_id'];
	}
	
	if(!empty($cate_ids)){
		$cateids = implode(',', $cate_ids);
		$tvlist = pdo_fetchall("SELECT * FROM" . tablename($this->modulename . '_tv') . " WHERE cate_id IN({$cateids})");	
	}
	
	if($operation == 'play'){
		$id = $_GPC['id'];		
		$tv = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_tv') . " WHERE  id = {$id}");
		include $this->template('tv_play');
	}else{
		include $this->template('tv');
	}
?>