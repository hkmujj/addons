<?php
	
	defined('IN_IA') or exit('Access Denied');
	
	global $_W, $_GPC;
	checklogin();
	load()->func('tpl');
	
	//默认显示那个路由
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	//获取公众号id
	$uniacid = intval($_W['uniacid']);
	//连接数据库查询
	$setting = pdo_fetch(" SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE uniacid='{$uniacid}'");
	if(empty($setting)){
		message('请先配置站点信息');
	}
	
	//判断路由走的那个控制器
	if($operation == 'delete'){
		//获取要删除的TVid
		$id = $_GPC['id'];
		
		//查询数据库是否存在这个id
		$item = pdo_fetch(" SELECT * FROM " . tablename($this->modulename . '_tv') . " WHERE uniacid = {$uniacid} AND id = '{$id}'");
		
		//判断数据库是否存在
		if(empty($item)){
			message('TV不存在或已删除',$this->createWebUrl('vtvadd',array('op' => 'display')),'error');
		}
		
		//删除成功
		pdo_delete($this->modulename . '_tv',array('id' => $id), 'OR');
		message('删除成功',$this->createWebUrl('vtvadd',array('op' => 'display')),'success');
		
	}elseif($operation == 'deleteall'){
		
		if(empty($_GPC['idArr'])){
			message('不存在或是已经被删除', $this->createWebUrl('vtvadd', array('op' => 'display')), 'error');
		}
		
		$rowcount = 0;
		foreach($_GPC['idArr'] as $k => $id){
			pad_delete($this->modulename . '_tv', array('id' => $id));
			$rowcount++;
		}
		echo '{"data":"删除成功"}';
		die;
		
	}elseif($operation == 'post'){
		
		//获取id
		$id = $_GPC['id'];
		
		//判断数据库里有的话就获取出来
		if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_tv') . "WHERE id = '{$id}' and uniacid={$uniacid} ");
		}
		
		if(checksubmit('submit')){
			$data = $_GPC['data'];
			
			if(empty($data['name'])){
				message('请输入TV名称');
			}
			
			$data['cate_id'] = (int)$data['cate_id'];
			if($data['cate_id'] == 0){
				message('请选择TV分类');
			}
			
			if(empty($data['url'])){
				message('请填写TVurl地址');
			}
			
			$data['recommend'] = (int)$data['recommend'];
			if(!empty($id)){
				pdo_update($this->modulename . '_tv',$data,array('id' => $id));
			}else{
				pdo_insert($this->modulename . '_tv',$data);
			}
			
			//更新成功
			message('更新成功', $this->createWebUrl('vtvadd',array('op' => 'display')),'success');
		}
		
		$cates = pdo_fetchall('SELECT * FROM ' . tablename($this->modulename . '_cate'));
		
	}elseif($operation == 'display'){
		$_W['page']['title'] = '商家列表';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;
		$keyword = trim($_GPC['keyword']);
		$condition = " WHERE `uniacid` = '{$uniacid}' ";
		$params = array();
		if (!empty($keyword)) {
			$condition .= " AND name LIKE :keyword";
			$params[':keyword'] = "%{$keyword}%";
		}
		$order_condition = " ORDER BY id DESC ";
		$sql = 'SELECT * FROM ' . tablename($this->modulename . '_tv') . $condition . $order_condition . " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

		$items = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($this->modulename . '_tv') . $condition . $order_condition, $params);
		$xx = $this->createWebUrl('vtvadd', array('op' => 'display'));
		$url = str_replace('./index.php?', '', $xx) . "&page=*&keyword={$keyword}";
		$pager = pagination($total, $pindex, $psize, $url);
		
		$catelist =  pdo_fetchall("SELECT * FROM " . tablename($this->modulename . '_cate') . " ORDER BY cate_id DESC");	
		foreach ($catelist as $key => $val) {
			$cates[$val['cate_id']] = $val;
		}
	}
	
	
	
	
	include $this->template('web/vtvadd');
?>