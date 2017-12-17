<?php
/**
 * 金梧桐模块模块微站定义
 * <link rel="apple-touch-icon-precomposed" href="{OSSURL}/public/mobile/images/5da37614.apple-touch-icon-precomposed.png">
 * @author dxf
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
define('OSSURL', '../addons/dxf_jwt');
class Dxf_jwtModuleSite extends WeModuleSite {

	public function doWebArea() {
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			if (!empty($_GPC['displayorder'])) {
				foreach ($_GPC['displayorder'] as $id => $displayorder) {
					pdo_update('dxf_jwt_category', array('displayorder' => $displayorder), array('id' => $id, 'weid' => $_W['uniacid']));
				}
				message('分类排序更新成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
			}

			$children = array();
			$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");
			$grandson = array();
			foreach ($category as $indexone => $rowone) {
				if ($rowone['level']==3) {
					$grandson[$rowone['parentid']][] = $rowone;
				
				}
			}
			foreach ($category as $index => $row) {
				if (!empty($row['parentid'])) {
					$children[$row['parentid']][] = $row;
					unset($category[$index]);
				}
			}
			include $this->template('category');
		} elseif ($operation == 'post') {
			$parentid = intval($_GPC['parentid']);
			$id = intval($_GPC['id']);
			$level = intval($_GPC['level']);
			if (!empty($id)) {
				$category = pdo_fetch("SELECT * FROM " . tablename('dxf_jwt_category') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
			} else {
				$category = array(
					'displayorder' => 0,
				);
			}
			if (!empty($parentid)) {
				$parent = pdo_fetch("SELECT id, name,level FROM " . tablename('dxf_jwt_category') . " WHERE id = '$parentid'");
				if (empty($parent)) {
					message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
				}
			}
			if (checksubmit('submit')) {
				
				if (empty($_GPC['catename'])) {
					message('抱歉，请输入分类名称！');
				}
				if($level==2 || $level==3){
					$data = array(
						'weid' => $_W['uniacid'],
						'name' => $_GPC['catename'],
						'enabled' => intval($_GPC['enabled']),
						'displayorder' => intval($_GPC['displayorder']),
						'description' => $_GPC['description'],
						'parentid' => intval($parentid),
				
						'level' => $_GPC['level']
					);
					
				}else{
					$data = array(
						'weid' => $_W['uniacid'],
						'name' => $_GPC['catename'],
						'enabled' => intval($_GPC['enabled']),
						'displayorder' => intval($_GPC['displayorder']),
						'description' => $_GPC['description'],
						'parentid' => intval($parentid),
						'level' => $_GPC['level'],
						'thumb' => $_GPC['thumb'],//缩略图  暂未用
						'isrecommand' => 1,//无用的字段
					);
				}
				if(empty($_GPC['yzmsg'])){
					$data['yzmsg'] ='A'.time();
				}
				if (!empty($id)) {
					unset($data['parentid']);
					pdo_update('dxf_jwt_category', $data, array('id' => $id, 'weid' => $_W['uniacid']));
					load()->func('file');
					file_delete($_GPC['thumb_old']);
				} else {
					pdo_insert('dxf_jwt_category', $data);
					$id = pdo_insertid();

				}
				message('更新分类成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
			}
			include $this->template('category');
		} elseif ($operation == 'delete') {
			
			$id = intval($_GPC['id']);
			$category = pdo_fetch("SELECT * FROM " . tablename('dxf_jwt_category') . " WHERE id = '$id'");
			if (empty($category)) {
				message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('dxf_jwt_category', array('op' => 'display')), 'error');
			}

			if($category['level']==1){
				$list = pdo_fetch("SELECT s.id,s.u_name FROM " . tablename('dxf_jwt_category'). " as c  join ". tablename('dxf_jwt_category'). "as cp on c.id=cp.parentid join ".tablename('dxf_jwt_students')." as s on cp.id=s.cat_id  WHERE  c.parentid=:id AND c.weid=:weid ", array(':id' => $id,':weid' => $_W['uniacid']));
				
			}elseif($category['level']==2){

				$list = pdo_fetch("SELECT s.id,s.u_name FROM " . tablename('dxf_jwt_category'). " as c  join ".tablename('dxf_jwt_students')." as s on c.id=s.cat_id  WHERE  c.parentid=:id AND c.weid=:weid ", array(':id' => $id,':weid' => $_W['uniacid']));
			}elseif($category['level']==3){

				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_jwt_students')." WHERE cat_id=:id AND weid=:weid ", array(':id' => $id,':weid' => $_W['uniacid']));
				
			}

			if($list){
					message('抱歉，分类下有学生信息，不能被删除！');
			}else{
					pdo_delete('dxf_jwt_category', array('id' => $id, 'parentid' => $id), 'OR');
					message('分类删除成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
			}
			
		}
	}
	public function doWebJoin() {
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;
			$condition = ' WHERE s.`weid` = :weid AND s.`status` = 1';
			$params = array(':weid' => $_W['uniacid']);
			if (!empty($_GPC['u_name'])) {
				$condition .= ' AND `u_name` LIKE :u_name';
				$params[':u_name'] = '%' . trim($_GPC['u_name']) . '%';
			}
			if (!empty($_GPC['mobile'])) {
				$condition .= ' AND `mobile` = :mobile';
				$params[':mobile'] =trim($_GPC['mobile']);
			}
			if (!empty($_GPC['cat_id'])) {
				$condition .= ' AND `cat_id` =:cat_id';
				$params[':cat_id'] = $_GPC['cat_id'];
			}
			$sql = "SELECT count(*) FROM " . tablename('dxf_jwt_students'). " as s left join ".tablename('dxf_jwt_category')." as c on s.cat_id=c.id". $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT c.name,s.id,s.u_name,s.mobile,s.qq,s.desc,s.one_status,s.two_status,s.ctime FROM " . tablename('dxf_jwt_students'). " as s left join ".tablename('dxf_jwt_category')." as c on s.cat_id=c.id  ". $condition ."  ORDER BY s.id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
			$children = array();
				$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");
				$grandson = array();
				foreach ($category as $indexone => $rowone) {
					if ($rowone['level']==3) {
						$grandson[$rowone['parentid']][] = $rowone;
					
					}
				}
				foreach ($category as $index => $row) {
					if (!empty($row['parentid'])) {
						$children[$row['parentid']][] = $row;
						unset($category[$index]);
					}
				}
		}elseif($operation == 'post'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_jwt_students') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
				$children = array();
				$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");
				$grandson = array();
				foreach ($category as $indexone => $rowone) {
					if ($rowone['level']==3) {
						$grandson[$rowone['parentid']][] = $rowone;
					
					}
				}
				foreach ($category as $index => $row) {
					if (!empty($row['parentid'])) {
						$children[$row['parentid']][] = $row;
						unset($category[$index]);
					}
				}
			}else{
				message('信息不存在');
			}
			if (checksubmit('submit')) {
				$data = array(
						'cat_id' => $_GPC['cat_id'],
						'one_status' => intval($_GPC['one_status']),
						'two_status' => intval($_GPC['two_status']),
						'desc' => trim($_GPC['desc'])

				);
				$ress=pdo_update('dxf_jwt_students', $data, array('id' => $id, 'weid' => $_W['uniacid']));
				if($ress){
					message('更新成功！', $this->createWebUrl('join', array('op' => 'display')), 'success');
				}else{
					message('更新失败');
				}
			}
		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$ress=pdo_update('dxf_jwt_students', array('status'=>0),array('id' => $id));
			if($ress){
				message('删除成功！', $this->createWebUrl('join', array('op' => 'display')), 'success');
			}else{
				message('删除失败');
			}
		}
		include $this->template('join');
	}
	public function doWebPrivate() {
		global $_GPC, $_W;
		load()->func('tpl');
		/*load()->model('mc');
		$account_api = WeAccount::create();
		$ress = $account_api->fansQueryInfo('oi_j2viLWSPyuv7venJjLj5uc54s');
		var_dump($ress);*/
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 30;
			$condition = ' WHERE d.`weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			
			if (!empty($_GPC['cat_id'])) {
				$condition .= ' AND `cat_id` =:cat_id';
				$params[':cat_id'] = $_GPC['cat_id'];
			}

			$sql = "SELECT count(*) FROM " .  tablename('dxf_jwt_daili'). " as d left join ".tablename('dxf_jwt_category')." as c on d.cat_id=c.id  ". $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$lang['level']=array('1'=>'市级代理','2'=>'区域代理','3'=>'学校代理');
			$list = pdo_fetchall("SELECT d.id,d.headimg,d.nickname,d.realname,d.status,d.ctime,c.name,c.level,c.parentid FROM " . tablename('dxf_jwt_daili'). " as d left join ".tablename('dxf_jwt_category')." as c on d.cat_id=c.id  ". $condition ."  ORDER BY d.id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
			$children = array();
			$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");

			foreach ($category as $indexfour => $rowfour) {
					
					$four[$rowfour['id']] = $rowfour;
					
				}
			$grandson = array();
				foreach ($category as $indexone => $rowone) {
					if ($rowone['level']==3) {
						$grandson[$rowone['parentid']][] = $rowone;
					}
				}
				foreach ($category as $index => $row) {
					if (!empty($row['parentid'])) {
						$children[$row['parentid']][] = $row;
						unset($category[$index]);
					}
				}
			

			//var_dump($four);

		}elseif($operation == 'post'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_jwt_daili') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));

				$children = array();
				$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");
				$grandson = array();
				foreach ($category as $indexone => $rowone) {
					if ($rowone['level']==3) {
						$grandson[$rowone['parentid']][] = $rowone;
					
					}
				}
				foreach ($category as $index => $row) {
					if (!empty($row['parentid'])) {
						$children[$row['parentid']][] = $row;
						unset($category[$index]);
					}
				}
			}else{
				message('信息不存在');
			}
			if (checksubmit('submit')) {
				$data = array(
						'realname' => $_GPC['realname'],
						'cat_id' => $_GPC['cat_id'],
						'status' => intval($_GPC['status'])
				);
				$ress=pdo_update('dxf_jwt_daili', $data, array('id' => $id, 'weid' => $_W['uniacid']));
				if($ress){
					message('更新成功！', $this->createWebUrl('private', array('op' => 'display')), 'success');
				}else{
					message('更新失败');
				}
			}
		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$ress=pdo_delete('dxf_jwt_daili', array('id' => $id));
			if($ress){
				message('删除成功！', $this->createWebUrl('private', array('op' => 'display')), 'success');
			}else{
				message('删除失败');
			}
		}
		include $this->template('private');
	}
	public function doWebFactory(){
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 30;
			$condition = ' WHERE `weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			$sql = "SELECT count(*) FROM " .  tablename('dxf_jwt_factory'). $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_factory'). $condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($operation == 'post'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_jwt_factory') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
			}
			if (checksubmit('submit')) {
				$data = array(
						'weid' => $_W['uniacid'],
						'title' => $_GPC['title'],
						'f_name' => trim($_GPC['f_name']),
						'f_type' => trim($_GPC['f_type']),
						'f_img' => trim($_GPC['f_img']),
						'city' => trim($_GPC['city']),
						'address' => trim($_GPC['address']),
						'hangye' => trim($_GPC['hangye']),
						'fuli' => trim($_GPC['fuli']),
						'yaoqiu' => trim($_GPC['yaoqiu']),
						'money' => trim($_GPC['money']),
						'start_time' => strtotime($_GPC['start_time']),
						'end_time' => strtotime($_GPC['end_time']),
						'status' => trim($_GPC['status']),
						'contents' => trim($_GPC['contents']),
						'desc' => trim($_GPC['desc']),
						'ctime' => time()
				);
				if (!empty($id)) {
					$ress=pdo_update('dxf_jwt_factory', $data, array('id' => $id, 'weid' => $_W['uniacid']));
					load()->func('file');
					file_delete($_GPC['thumb_old']);
				} else {
					$ress=pdo_insert('dxf_jwt_factory', $data);
				}
				if($ress){
					message('保存成功！', $this->createWebUrl('factory', array('op' => 'display')), 'success');
				}else{
					message('保存失败');
				}
			}

		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$ress=pdo_delete('dxf_jwt_factory', array('id' => $id));
			if($ress){
				message('删除成功！', $this->createWebUrl('factory', array('op' => 'display')), 'success');
			}else{
				message('删除失败');
			}
		}
		include $this->template('factory');
	}
	public function doWebNews(){
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 30;
			$condition = ' WHERE `weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			$sql = "SELECT count(*) FROM " .  tablename('dxf_jwt_news'). $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_news'). $condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($operation == 'post'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_jwt_news') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
			}
			if (checksubmit('submit')) {
				$data = array(
						'weid' => $_W['uniacid'],
						'title' => $_GPC['title'],
						'author' => trim($_GPC['author']),
						'status' => trim($_GPC['status']),
						'desc' => trim($_GPC['desc']),
						'ctime' => time()
				);
				if (!empty($id)) {
					$ress=pdo_update('dxf_jwt_news', $data, array('id' => $id, 'weid' => $_W['uniacid']));

				} else {
					$ress=pdo_insert('dxf_jwt_news', $data);
				}
				if($ress){
					message('保存成功！', $this->createWebUrl('news', array('op' => 'display')), 'success');
				}else{
					message('保存失败');
				}
			}

		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$ress=pdo_delete('dxf_jwt_news', array('id' => $id));
			if($ress){
				message('删除成功！', $this->createWebUrl('news', array('op' => 'display')), 'success');
			}else{
				message('删除失败');
			}
		}
		include $this->template('news');
	}
	public function doWebSetStudents() {
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$type = $_GPC['type'];
		$data = intval($_GPC['data']);
		if (in_array($type, array('one_status', 'two_status'))) {
			$data = ($data==1?'0':'1');
			pdo_update("dxf_jwt_students", array($type => $data), array("id" => $id, "weid" => $_W['uniacid']));
			die(json_encode(array("result" => 1, "data" => $data)));
		}
		
		die(json_encode(array("result" => 0)));
	}
	public function doMobileIndex() {
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : '';
		if ($operation == 'display') {
		}elseif($operation == 'post'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_jwt_news') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
			}
			if (checksubmit('submit')) {
				$data = array(
						'weid' => $_W['uniacid'],
						'title' => $_GPC['title'],
						'author' => trim($_GPC['author']),
						'status' => trim($_GPC['status']),
						'desc' => trim($_GPC['desc']),
						'ctime' => time()
				);
				if (!empty($id)) {
					$ress=pdo_update('dxf_jwt_news', $data, array('id' => $id, 'weid' => $_W['uniacid']));

				} else {
					$ress=pdo_insert('dxf_jwt_news', $data);
				}
				if($ress){
					message('保存成功！', $this->createWebUrl('news', array('op' => 'display')), 'success');
				}else{
					message('保存失败');
				}
			}
		}elseif($operation == 'list'){
			$pindex = max(1, intval($_GPC['page']));
			$psize = 30;
			$condition = ' WHERE status=1 and `weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			$city = !empty($_GPC['city']) ? $_GPC['city'] : '0';
			if($city==-1){
			}elseif ($city==1) {
				$condition .= ' AND `city` LIKE :city';
				$params[':city'] = '%' . '苏州' . '%';
			}elseif ($city==2) {
				$condition .= ' AND `city` LIKE :city';
				$params[':city'] = '%' . '上海' . '%';
			}elseif ($city==3) {
				$condition .= ' AND `city` LIKE :city';
				$params[':city'] = '%' . '南京' . '%';
			}elseif ($city==4) {
				$condition .= ' AND `city` LIKE :city';
				$params[':city'] = '%' . '南京' . '%';
			}
			$sql = "SELECT count(*) FROM " . tablename('dxf_jwt_factory'). $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_factory'). $condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
			include $this->template('list');
			die;
		}elseif($operation == 'detail'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_jwt_factory') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
				//var_dump($list);
			}else{
				message('信息不存在');
			}
			include $this->template('detail');
			die;
		}else{
			$condition = ' WHERE status=1 and `weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			$list = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_factory'). $condition ."  ORDER BY id desc LIMIT 0,8", $params);
			//var_dump($list);
			$listnews = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_news'). $condition ."  ORDER BY id desc LIMIT  0,8 ", $params);
		}
		include $this->template('index');
	}
	public function doMobileNews() {
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : '';
		if($operation == 'list'){
			$pindex = max(1, intval($_GPC['page']));
			$psize = 30;
			$condition = ' WHERE status=1 and `weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			$sql = "SELECT count(*) FROM " . tablename('dxf_jwt_news'). $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT title,id FROM " . tablename('dxf_jwt_news'). $condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
			//var_dump($list);
			include $this->template('news');
			die;
		}elseif($operation == 'newsdetail'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_jwt_news') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
				//var_dump($list);
			}else{
				message('信息不存在');
			}
			include $this->template('newsdetail');
			die;
		}

	}
	public function doMobileBaoMing(){
		global $_W,$_GPC;
		load()->func('tpl');
		if (checksubmit('submit')) {
				$data = array(
						'weid' => $_W['uniacid'],
						'cat_id' => $_GPC['cat_id'],
						'openid' => $_W['openid'],
						'f_title' => $_GPC['title'],
						'u_name' => $_GPC['u_name'],
						'mobile' => $_GPC['mobile'],
						'qq' => $_GPC['qq'],
						'ctime' => time(),
						'desc' => trim($_GPC['desc'])

				);
				if(empty($_GPC['u_name'])){
					message('姓名不能为空');
				}
				if(empty($_GPC['mobile'])){
					message('电话不能为空');
				}
				$ress=pdo_insert('dxf_jwt_students', $data);
				if($ress){
					message('报名成功，请等待工作人员联系！', $this->createMobileUrl('index'), 'success');
				}else{
					message('报名失败');
				}
		}
		$title = $_GPC['title'];
		
		$children = array();
			$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_jwt_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");

			foreach ($category as $indexfour => $rowfour) {
					
					$four[$rowfour['id']] = $rowfour;
					
				}
			$grandson = array();
				foreach ($category as $indexone => $rowone) {
					if ($rowone['level']==3) {
						$grandson[$rowone['parentid']][] = $rowone;
					}
				}
				foreach ($category as $index => $row) {
					if (!empty($row['parentid'])) {
						$children[$row['parentid']][] = $row;
						unset($category[$index]);
					}
				}
		include $this->template('baoming');
	}
}