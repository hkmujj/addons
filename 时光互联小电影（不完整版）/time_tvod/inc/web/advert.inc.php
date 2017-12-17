<?php
 	defined('IN_IA') or exit('Access Denied');

	global $_W, $_GPC;
	checklogin();
	load()->func('tpl');
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	$uniacid = intval($_W['uniacid']);
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid ={$uniacid}");
	if(empty($setting)){
		message('请先配置站点信息');
	}
	$config = unserialize($setting['config']);
	
	if ($operation == 'delete') {
		$id = intval($_GPC['id']);
		$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_advert') . " WHERE id = '{$id}'");
		if (empty($item)) {
			message('广告不存在或是已经被删除', $this->createWebUrl('advert', array('op' => 'display')), 'error');
		}
		pdo_delete($this->modulename . '_advert', array('id' => $id), 'OR');
		message('删除成功', $this->createWebUrl('advert', array('op' => 'display')), 'success');
		
	} elseif ($operation == 'post') {
		$id = intval($_GPC['id']);
		if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_advert') . " WHERE id = '{$id}' and uniacid={$uniacid}");
		} else {
			$item["pic"] = "../addons/time_tvod/static/images/nopic.jpg";
		}

		if (checksubmit('submit')) {
			$data = $_GPC['data'];
			
			$data['type']  = (int) $data['type'];
			$data['sort']  = (int) $data['sort'];
			
			if ($data['type'] == 0) {
				message('请选择广告类型');
			}
			if (empty($data['title'])) {
				message('请输入广告名称');
			}
			if (empty($data['url'])) {
				message('请输入广告连接');
			}
			if (!empty($data['pic'])) {
				if (strstr($data['pic'], 'http://')) {
					$data['pic'] = $data['pic'];
				} else {
					$data['pic'] = $_W['attachurl'] . $data['pic'];
				}
			} else {
				message('请上传广告图片');
			}
			$data['uniacid'] = $uniacid;
			if (!empty($id)) {
				pdo_update($this->modulename . '_advert', $data, array('id' => $id));
			} else {
				pdo_insert($this->modulename . '_advert', $data);
			}
			message('更新成功', $this->createWebUrl('advert', array('op' => 'display')), 'success');
		}
	} elseif ($operation == 'display') {
		if (!empty($_GPC['sort'])) {
			foreach ($_GPC['sort'] as $id => $order) {
				pdo_update($this->modulename . '_advert', array('sort' => $order), array('id' => $id));
			}
		}
		$list = pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_advert') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY id DESC,sort desc");
	}

	include $this->template('web/advert');
?>