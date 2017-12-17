<?php
/**
 * 英才兼职模块模块微站定义
 * @author dxf
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
define('OSSURL', '../addons/dxf_ycj');
class Dxf_ycjModuleSite extends WeModuleSite {
	//地区分类
	public function doWebArea() {
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			if (!empty($_GPC['displayorder'])) {
				foreach ($_GPC['displayorder'] as $id => $displayorder) {
					pdo_update('dxf_ycj_category', array('displayorder' => $displayorder), array('id' => $id, 'weid' => $_W['uniacid']));
				}
				message('分类排序更新成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
			}

			$children = array();
			$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");
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
				$category = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_category') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
			} else {
				$category = array(
					'displayorder' => 0,
				);
			}
			if (!empty($parentid)) {
				$parent = pdo_fetch("SELECT id, name,level FROM " . tablename('dxf_ycj_category') . " WHERE id = '$parentid'");
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
					pdo_update('dxf_ycj_category', $data, array('id' => $id, 'weid' => $_W['uniacid']));
					load()->func('file');
					file_delete($_GPC['thumb_old']);
				} else {
					pdo_insert('dxf_ycj_category', $data);
					$id = pdo_insertid();

				}
				message('更新分类成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
			}
			include $this->template('category');
		} elseif ($operation == 'delete') {
			
			$id = intval($_GPC['id']);
			$category = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_category') . " WHERE id = '$id'");
			if (empty($category)) {
				message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('dxf_ycj_category', array('op' => 'display')), 'error');
			}

			if($category['level']==1){
				$list = pdo_fetch("SELECT s.id,s.u_name FROM " . tablename('dxf_ycj_category'). " as c  join ". tablename('dxf_ycj_category'). "as cp on c.id=cp.parentid join ".tablename('dxf_ycj_students')." as s on cp.id=s.cat_id  WHERE  c.parentid=:id AND c.weid=:weid ", array(':id' => $id,':weid' => $_W['uniacid']));
				
			}elseif($category['level']==2){

				$list = pdo_fetch("SELECT s.id,s.u_name FROM " . tablename('dxf_ycj_category'). " as c  join ".tablename('dxf_ycj_students')." as s on c.id=s.cat_id  WHERE  c.parentid=:id AND c.weid=:weid ", array(':id' => $id,':weid' => $_W['uniacid']));
			}elseif($category['level']==3){

				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_students')." WHERE cat_id=:id AND weid=:weid ", array(':id' => $id,':weid' => $_W['uniacid']));
				
			}

			if($list){
					message('抱歉，分类下有学生信息，不能被删除！');
			}else{
					pdo_delete('dxf_ycj_category', array('id' => $id, 'parentid' => $id), 'OR');
					message('分类删除成功！', $this->createWebUrl('area', array('op' => 'display')), 'success');
			}
			
		}
	}
	//兼职类别
	public function doWebCate(){
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 30;
			$condition = ' WHERE `weid` = :weid and `is_delete`=1';
			$params = array(':weid' => $_W['uniacid']);
			$sql = "SELECT count(*) FROM " .  tablename('dxf_ycj_cate'). $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_cate'). $condition ."  ORDER BY sort asc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($operation == 'post'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_cate') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
			}
			if (checksubmit('submit')) {
				$data = array(
						'weid' => $_W['uniacid'],
						'title' => $_GPC['title'],
						'sort' => $_GPC['sort'],
						'author' => trim($_GPC['author']),
						'status' => trim($_GPC['status']),
						'ctime' => time()
				);
				if (!empty($id)) {
					$ress=pdo_update('dxf_ycj_cate', $data, array('id' => $id, 'weid' => $_W['uniacid']));

				} else {
					$ress=pdo_insert('dxf_ycj_cate', $data);
				}
				if($ress){
					message('保存成功！', $this->createWebUrl('cate', array('op' => 'display')), 'success');
				}else{
					message('保存失败');
				}
			}

		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$data['is_delete'] =0;
			$ress=pdo_update('dxf_ycj_cate', $data,array('id' => $id));
			if($ress){
				message('删除成功！', $this->createWebUrl('cate', array('op' => 'display')), 'success');
			}else{
				message('删除失败');
			}
		}
		include $this->template('cate');
	}
	//代理管理
	public function doWebAgent() {
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 30;
			$condition = ' WHERE d.`weid` = :weid and d.`status`>0';
			$params = array(':weid' => $_W['uniacid']);
			if (!empty($_GPC['realname'])) {
				$condition .= ' AND d.`realname` =:realname';
				$params[':realname'] = $_GPC['realname'];
			}
			if (!empty($_GPC['phone'])) {
				$condition .= ' AND d.`phone` =:phone';
				$params[':phone'] = $_GPC['phone'];
			}
			if (!empty($_GPC['status'])) {
				$condition .= ' AND d.`status` =:status';
				$params[':status'] = $_GPC['status'];
			}
			if (!empty($_GPC['cat_id'])) {
				$condition .= ' AND d.`cat_id` =:cat_id';
				$params[':cat_id'] = $_GPC['cat_id'];
			}
			$sql = "SELECT count(*) FROM " .  tablename('dxf_ycj_daili'). " as d left join ".tablename('dxf_ycj_category')." as c on d.cat_id=c.id  ". $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$lang['grade']=array('1'=>'金牌代理','2'=>'银牌代理','3'=>'普通代理');
			$lang['status']=array('1'=>'等待审核','2'=>'正常','3'=>'审核不通过','4'=>'禁用','-1'=>'删除');
			$list = pdo_fetchall("SELECT d.id,d.headimg,d.realname,d.phone,d.status,d.grade,d.status,d.address,d.ctime,c.name,c.level,c.parentid FROM " . tablename('dxf_ycj_daili'). " as d left join ".tablename('dxf_ycj_category')." as c on d.cat_id=c.id  ". $condition ."  ORDER BY d.status asc,d.id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
			$children = array();
			$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_category') . " WHERE weid = '{$_W['uniacid']}' and level=2 and enabled=1 ORDER BY parentid ASC, displayorder ASC");
		}elseif($operation == 'post'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_daili') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
				$children = array();
				$category =pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_category') . " WHERE weid = '{$_W['uniacid']}' and level=2 and enabled=1 ORDER BY parentid ASC, displayorder ASC");
			}else{
				message('信息不存在');
			}

			if (checksubmit('submit')) {
				$data = array(
						'realname' => $_GPC['realname'],
						'sex' => $_GPC['sex'],
						'age' => $_GPC['age'],
						'phone' => $_GPC['phone'],
						'qq' => $_GPC['qq'],
						'chat' => $_GPC['chat'],
						'cat_id' => $_GPC['cat_id'],
						'address' => $_GPC['address'],
						'grade' => $_GPC['grade'],
						'type' => $_GPC['type'],
						'status' => $_GPC['status'],
						'desc' => $_GPC['desc']
						
				);
				$ress=pdo_update('dxf_ycj_daili', $data, array('id' => $id, 'weid' => $_W['uniacid']));
				if($ress){
					message('更新成功！', $this->createWebUrl('agent', array('op' => 'display')), 'success');
				}else{
					message('更新失败');
				}
			}
		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$data['status'] ='-1';
			$ress=pdo_update('dxf_ycj_daili', $data,array('id' => $id));
			if($ress){
				message('删除成功！', $this->createWebUrl('agent', array('op' => 'display')), 'success');
			}else{
				message('删除失败');
			}
		}
		include $this->template('agent');
	}
	//招聘管理
	public function doWebJob(){
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			//var_dump($_GPC);
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$condition = ' WHERE j.`weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			if (!empty($_GPC['title'])) {
				$condition .= ' AND j.`title` LIKE :title';
				$params[':title'] = '%' . trim($_GPC['title']) . '%';
			}
			if (!empty($_GPC['realname'])) {
				$condition .= ' AND d.`realname` LIKE :realname';
				$params[':realname'] = '%' . trim($_GPC['realname']) . '%';
			}
			if (empty($_GPC['time'])) {
				$starttime=time()-(3600*24*7);
				$endtime=time();
			}else{
				$starttime=strtotime($_GPC['time']['start']);
				$endtime=strtotime($_GPC['time']['end']);
				$condition .=" AND j.`ctime` > $starttime  AND j.`ctime`< $endtime ";
			}
			
			$sql = "SELECT count(*) FROM " . tablename('dxf_ycj_job'). " as j left join ".tablename('dxf_ycj_category')." as c on j.job_cid=c.id left join ".tablename('dxf_ycj_category')." as ca on c.parentid=ca.id left join ".tablename('dxf_ycj_daili')." as d on j.openid=d.openid ".$condition;

			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);

			$list = pdo_fetchall("SELECT j.*,c.name,ca.name,d.realname  FROM " . tablename('dxf_ycj_job'). " as j left join ".tablename('dxf_ycj_category')." as c on j.job_cid=c.id left join ".tablename('dxf_ycj_category')." as ca on c.parentid=ca.id left join ".tablename('dxf_ycj_daili')." as d on j.openid=d.openid ".$condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($operation == 'post'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$children = array();
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_job') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));

			}
			if (checksubmit('submit')) {
				//var_dump($_GPC);die;
				$data = array(
						'weid' => $_W['uniacid'],
						'title' => $_GPC['title'],
						'cate_id' => $_GPC['cate_id'],
						'type' => trim($_GPC['type']),
						'sex' => trim($_GPC['sex']),
						'age' => trim($_GPC['age']),
						'height' => trim($_GPC['height']),
						'job_stime' => strtotime($_GPC['job_stime']),
						'job_endtime' => strtotime($_GPC['job_endtime']),
						'bm_endtime' => strtotime($_GPC['bm_endtime']),
						'jihe_time' => trim($_GPC['jihe_time']),
						'jihe_area' => trim($_GPC['jihe_area']),
						'job_cid' => trim($_GPC['cat_id']),
						'job_area' => trim($_GPC['job_area']),
						'money' => trim($_GPC['money']),
						'account_time' => trim($_GPC['account_time']),
						'account_type' => trim($_GPC['account_type']),
						'p_num' => trim($_GPC['p_num']),
						'bm_num' => trim($_GPC['bm_num']),
						'job_req' => trim($_GPC['job_req']),
						'job_desc' => trim($_GPC['job_desc']),
						'ad_hot' => trim($_GPC['ad_hot']),
						'ad_status' => trim($_GPC['ad_status']),
						'openid' =>0 ,
						'updat_time' => time()
				);
				if (!empty($id)) {

					$ress=pdo_update('dxf_ycj_job', $data, array('id' => $id, 'weid' => $_W['uniacid']));
				} else {
					$data['ctime']=time();
					$ress=pdo_insert('dxf_ycj_job', $data);
				}
				if($ress){
					message('保存成功！', $this->createWebUrl('job', array('op' => 'display')), 'success');
				}else{
					message('保存失败');
				}
			}
			$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");
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
				$cate = pdo_fetchall(" SELECT * FROM " . tablename('dxf_ycj_cate')." WHERE  weid = :weid AND status= :status AND is_delete= :delete order by sort  ", array(':weid' => $_W['uniacid'],':status' =>1,':delete' => 1));

		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$ress=pdo_delete('dxf_ycj_job', array('id' => $id));
			if($ress){
				message('删除成功！', $this->createWebUrl('job', array('op' => 'display')), 'success');
			}else{
				message('删除失败');
			}
		}if ($operation == 'join') {

			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$condition = ' WHERE j.`weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			if (!empty($_GPC['title'])) {
				$condition .= ' AND j.`title` LIKE :title';
				$params[':title'] = '%' . trim($_GPC['title']) . '%';
			}
		
			if (empty($_GPC['time'])) {
				$starttime=time()-(3600*24*7);
				$endtime=time();
			}else{
				$starttime=strtotime($_GPC['time']['start']);
				$endtime=strtotime($_GPC['time']['end']);
				$condition .=" AND ju.`ctime` > $starttime  AND ju.`ctime`< $endtime ";
		
			}
				//筛选单个信息的人员
			if (!empty($_GPC['id'])) {
				$condition .= ' AND ju.`job_id`=:id';
				$params[':id'] = trim($_GPC['id']);
				$hidden=1;
			}

			
			$sql = "SELECT count(*) FROM " . tablename('dxf_ycj_jobuser'). " as ju left join ".tablename('dxf_ycj_job')." as j on ju.job_id=j.id ".$condition;

			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$lang['status']=array('-1'=>'代理拒绝','-2'=>'用户不去了','1'=>'用户提交','2'=>'代理同意');
			$list = pdo_fetchall("SELECT ju.*,j.title  FROM " . tablename('dxf_ycj_jobuser'). " as ju left join ".tablename('dxf_ycj_job')." as j on ju.job_id=j.id ".$condition ."  ORDER BY ju.id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}
		
		include $this->template('job');
	}
	//发布管理
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
		
			$sql = "SELECT count(*) FROM " . tablename('dxf_ycj_students'). " as s left join ".tablename('dxf_ycj_category')." as c on s.cat_id=c.id". $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT c.name,s.id,s.u_name,s.mobile,s.qq,s.desc,s.one_status,s.two_status,s.ctime FROM " . tablename('dxf_ycj_students'). " as s left join ".tablename('dxf_ycj_category')." as c on s.cat_id=c.id  ". $condition ."  ORDER BY s.id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
			$children = array();
				$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");
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
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_students') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
				$children = array();
				$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");
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
				$ress=pdo_update('dxf_ycj_students', $data, array('id' => $id, 'weid' => $_W['uniacid']));
				if($ress){
					message('更新成功！', $this->createWebUrl('join', array('op' => 'display')), 'success');
				}else{
					message('更新失败');
				}
			}
		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$ress=pdo_update('dxf_ycj_students', array('status'=>0),array('id' => $id));
			if($ress){
				message('删除成功！', $this->createWebUrl('join', array('op' => 'display')), 'success');
			}else{
				message('删除失败');
			}
		}
		include $this->template('join');
	}
	//状态ajax的修改
	public function doWebSetStatus() {
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$type = $_GPC['type'];
		$data = intval($_GPC['data']);
		if (in_array($type, array('ad_hot', 'ad_status'))) {
			$data = ($data==1?'0':'1');
			$ress=pdo_update("dxf_ycj_job", array($type => $data), array("id" => $id, "weid" => $_W['uniacid']));
			if($ress){
				die(json_encode(array("result" => 1, "data" => $data)));
			}else{
				die(json_encode(array("result" => 0)));
			}
		}
	}
	//基本配置
	public function doWebConfig(){
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 30;
			$condition = ' WHERE `weid` = :weid and `type` = 1';
			$params = array(':weid' => $_W['uniacid']);
			$sql = "SELECT count(*) FROM " .  tablename('dxf_ycj_slide'). $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_slide'). $condition ."  ORDER BY status desc,sort asc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($operation == 'post'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_slide') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
			}
			if (checksubmit('submit')) {
				$data = array(
						'weid' => $_W['uniacid'],
						'type' => $_GPC['type'],
						'name' => trim($_GPC['name']),
						'pic_url' => trim($_GPC['pic_url']),
						'url' => trim($_GPC['url']),
						'status' => trim($_GPC['status']),
						'sort' => trim($_GPC['sort']),
						'ctime' => time()
				);
				if (!empty($id)) {
					$ress=pdo_update('dxf_ycj_slide', $data, array('id' => $id, 'weid' => $_W['uniacid']));

				} else {
					$ress=pdo_insert('dxf_ycj_slide', $data);
				}
				if($ress){
					message('保存成功！', $this->createWebUrl('config', array('op' => 'display')), 'success');
				}else{
					message('保存失败');
				}
			}

		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$ress=pdo_delete('dxf_ycj_slide', array('id' => $id));
			if($ress){
				message('删除成功！', $this->createWebUrl('config', array('op' => 'display')), 'success');
			}else{
				message('删除失败');
			}
		}
		include $this->template('config');
	}
	//m端首页
	public function doMobileIndex() {
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if($operation == 'display'){
			$pindex = max(1, intval($_GPC['page']));
			$psize = 100;
			$time=time();
			$condition =  "WHERE j.`ad_status`=1 and j.`status`=1 and j.`weid` = :weid and j.`bm_endtime` > $time";
			$params = array(':weid' => $_W['uniacid']);
			if(!empty($_GPC['job_cid'])){
				$condition .= ' AND  j.`job_cid` LIKE :job_cid';
				$params[':job_cid'] = $_GPC['job_cid'];
			}
			if(!empty($_GPC['cate_id'])){
				$condition .= ' AND  j.`cate_id` LIKE :cate_id';
				$params[':cate_id'] = $_GPC['cate_id'];
			}
			if(!empty($_GPC['uptime'])){
				$condition .= ' AND  j.`updat_time` > :updat_time';
				$updat_time=time()-$_GPC['uptime'];
				$params[':updat_time'] =$updat_time;
			}
			$sql = "SELECT count(*) FROM " . tablename('dxf_ycj_job'). "as j left join ". tablename('dxf_ycj_cate')."as c on j.cate_id=c.id left join ". tablename('dxf_ycj_category')."as ca on j.job_cid=ca.id ". $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT j.id,j.title,j.cate_id,j.sex,j.money,j.p_num,j.updat_time,j.ad_hot,c.title as ctitle,c.id as cid,ca.name as cname FROM " . tablename('dxf_ycj_job'). "as j left join ". tablename('dxf_ycj_cate')."as c on j.cate_id=c.id left join ". tablename('dxf_ycj_category')."as ca on j.job_cid=ca.id ".$condition ."  ORDER BY j.ad_hot desc,j.updat_time desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
			//轮播广告
			$condition_1 = ' WHERE `status`=1 and `weid` = :weid ';
			$params_1 = array(':weid' => $_W['uniacid']);
			$lunbo = pdo_fetchall(" SELECT * FROM " . tablename('dxf_ycj_slide').$condition_1." order by sort asc ",$params_1);
			foreach ($lunbo as $k => $v) {
				$array[$v['type']][]=$v;
			}
			//区域
			$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_category') . " WHERE weid = '{$_W['uniacid']}' and parentid=68 ORDER BY displayorder ASC");
			//兼职类型
			$catelist = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_cate')." WHERE weid = '{$_W['uniacid']}' and is_delete=1  ORDER BY sort asc");
			$lang['sex']=array('1'=>'男','2'=>'女','3'=>'不限');
		}elseif($operation == 'detail'){
			//判断管理员状态
			if($_W['openid']=='oePgSwq8mnmkQgY3rKdSK8i0Kf3g' || $_W['openid']=='oePgSwvWv-rgZ5OyRNBZhz2TI5i0'){
				$hidden=1;
			}
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list=pdo_fetch("SELECT j.id,j.title,j.type,j.sex,j.age,j.height,j.job_stime,j.job_endtime,j.jihe_time,j.bm_endtime,j.jihe_area,j.job_area,j.money,j.account_time,j.account_type,j.p_num,j.bm_num,j.job_req,j.job_desc,j.ctime,j.updat_time,c.title as ctitle,ca.name as cname ,d.realname,d.sex as dsex,d.phone,d.qq,d.chat,d.address,d.type,d.desc  FROM " . tablename('dxf_ycj_job'). "as j left join ". tablename('dxf_ycj_daili'). "as d on j.openid=d.openid left join ".tablename('dxf_ycj_cate')."as c on j.cate_id=c.id left join ". tablename('dxf_ycj_category')."as ca on j.job_cid=ca.id where  j.id = :id",array(':id' => $id));
				$lang['type']=array('1'=>'兼职','2'=>'勤工俭学','3'=>'实习','4'=>'全职');
				$lang['account_time']=array('1'=>'日结','2'=>'周结','3'=>'月结','4'=>'面议');
				$lang['account_type']=array('1'=>'现金','2'=>'支付宝转账','3'=>'微信转账','4'=>'银行卡转账');
				$lang['sex']=array('1'=>'男','2'=>'女','3'=>'不限');
				//var_dump($list);die;
			}else{
				message('信息不存在');
			}
			include $this->template('detail');
			die;
		}
		include $this->template('index');
	}
	//学生报名
	public function doMobileBaoMing(){
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'baoming';
		if ($operation == 'baoming') {
			$id =$_GPC['id'];
			if(empty($_W['openid'])){
				message('在微信客户端打开，请关注‘零零校园’');
			}
			$info = pdo_fetch("SELECT *  FROM " . tablename('dxf_ycj_user'). " where openid=:openid ",array('openid'=>$_W['openid']));
			//var_dump($info);die;
			include $this->template('baoming');
		}elseif ($operation == 'dobaoming') {
			$ress = pdo_fetch("SELECT  *  FROM " . tablename('dxf_ycj_jobuser'). " where openid=:openid and job_id=:job_id ",array('openid'=>$_W['openid'],'job_id'=>$_GPC['job_id']));
			if($ress){
				message(error(1,''),'','ajax');
			}else{
				$data = array(
						'weid' => $_W['uniacid'],
						'job_id' => $_GPC['job_id'],
						'openid' => $_W['openid'],
						'headimg' => $_W['fans']['headimgurl'],
						'user_name' => trim($_GPC['user_name']),
						'mobile' => trim($_GPC['mobile']),
						'qq' => trim($_GPC['qq']),
						'wchat' => trim($_GPC['wchat']),
						'ctime' => time()
				);
				$ress1=pdo_insert('dxf_ycj_jobuser', $data);
				if($ress1){
					//客服通知
					$info = "【兼职报名通知】\n";
					$time=date('Y-m-d H:i:s',time());
					$info .= "尊敬的代理，您好！在{$time},【{$_GPC['user_name']}】报名了你的兼职\n\n";
					$info .= "<a href='http://wx.yingcaijie.cn/app/index.php?i=5&c=entry&op=person_list&id={$_GPC['job_id']}&do=fabu&m=dxf_ycj'>点击查看详情<<</a>";
					$openid = pdo_fetch("SELECT openid FROM " . tablename('dxf_ycj_job') . " WHERE id = {$_GPC['job_id']}");
					$message = array(
					'msgtype' => 'text',
					'text' => array('content' => urlencode($info)),
					'touser' => $openid['openid']
					);
					$account_api = WeAccount::create();
					$status = $account_api->sendCustomNotice($message);
					//***************
					 pdo_query("UPDATE ".tablename('dxf_ycj_job')." SET bm_num=bm_num+1  WHERE id = :id", array( ':id' => $_GPC['job_id']));
					message(error(0,''),'','ajax');
				}else{
					message(error(3,''),'','ajax');
				}
			}

		}
		
	}
	//用户和代理中心
	public function doMobileUser(){
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
		if ($operation == 'index') {
			if(empty($_W['openid'])){
				message('在微信客户端打开，请关注‘零零校园’');
			}
			$_W['page']['title']='个人中心';
			$condition = ' WHERE `weid` = :weid and openid=:openid and is_delete=1';
			$params = array(':weid' => $_W['uniacid'],':openid' => $_W['openid']);
			$sql = "SELECT count(*)  FROM " . tablename('dxf_ycj_jobuser').$condition ;
			$total['jobuser'] = pdo_fetchcolumn($sql, $params);
			$condition_1 = ' WHERE `weid` = :weid and openid=:openid ';
			$params_1 = array(':weid' => $_W['uniacid'],':openid' => $_W['openid']);
			$sql = "SELECT count(*)  FROM " . tablename('dxf_ycj_job').$condition_1 ;
			$total['fabu'] = pdo_fetchcolumn($sql, $params_1);
			//判断管理员状态
			if($_W['openid']=='oePgSwq8mnmkQgY3rKdSK8i0Kf3g' || $_W['openid']=='oePgSwvWv-rgZ5OyRNBZhz2TI5i0'){
				$hidden=1;
			}
			//兼职类别
			$cate = pdo_fetchall(" SELECT * FROM " . tablename('dxf_ycj_cate')." WHERE  weid = :weid AND status= :status AND is_delete= :delete order by sort  ", array(':weid' => $_W['uniacid'],':status' =>1,':delete' => 1));
			$children = array();
				$category = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC");
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

			include $this->template('user');
		}elseif($operation == 'dojob'){
			global $_W,$_GPC;
			load()->model('mc');
				$user = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_daili') . " WHERE openid = :openid ", array(':openid' => $_W['openid']));
				if(empty($user['realname']) || empty($user['phone'])){
					message(error(2,''),'','ajax');
				}elseif($user['status']=='3' || $user['status']=='4' || $user['status']=='1'){
					message(error(3,''),'','ajax');
				}
				$data = array(
						'weid' => $_W['uniacid'],
						'title' => $_GPC['title'],
						'cate_id' => $_GPC['cate_id'],
						'type' => trim($_GPC['type']),
						'sex' => trim($_GPC['sex']),
						'age' => trim($_GPC['age']),
						'height' => trim($_GPC['height']),
						'job_stime' => strtotime($_GPC['job_stime']),
						'job_endtime' => strtotime($_GPC['job_endtime']),
						'bm_endtime' => strtotime($_GPC['bm_endtime']),
						'jihe_time' => trim($_GPC['jihe_time']),
						'jihe_area' => trim($_GPC['jihe_area']),
						'job_cid' => trim($_GPC['cat_id']),
						'job_area' => trim($_GPC['job_area']),
						'money' => trim($_GPC['money']),
						'account_time' => trim($_GPC['account_time']),
						'account_type' => trim($_GPC['account_type']),
						'p_num' => trim($_GPC['p_num']),
						'bm_num' => trim($_GPC['bm_num']),
						'job_req' => trim($_GPC['job_req']),
						'job_desc' => trim($_GPC['job_desc']),
						'ad_hot' => 0,
						'ad_status' => 1,
						'openid' =>$_W['openid'],
						'updat_time' => time()
				);
				if (!empty($id)) {
					$ress=pdo_update('dxf_ycj_job', $data, array('id' => $id, 'weid' => $_W['uniacid']));
				} else {
					$data['ctime']=time();
					//客服通知
					$info = "【新兼职发布通知】\n";
					$time=date('Y-m-d H:i:s',time());

					$info .= "在{$time},又有新的兼职发布，请注意审核！\n\n";
					$info .= "<a href='http://wx.yingcaijie.cn/app/index.php?i=5&c=entry&op=detail&id={$id}&do=index&m=dxf_ycj'>点击查看详情<<</a>";
					$message = array(
					'msgtype' => 'text',
					'text' => array('content' => urlencode($info)),
					'touser' => 'oePgSwq8mnmkQgY3rKdSK8i0Kf3g',
					);
					$account_api = WeAccount::create();
					$status = $account_api->sendCustomNotice($message);
					$message1 = array(
					'msgtype' => 'text',
					'text' => array('content' => urlencode($info)),
					'touser' => 'oePgSwvWv-rgZ5OyRNBZhz2TI5i0',
					);
					$status = $account_api->sendCustomNotice($message1);
					//***************
					$ress=pdo_insert('dxf_ycj_job', $data);
				}
				if($ress){
					message(error(0,''),'','ajax');
				}else{
					message(error(1,''),'','ajax');
				}
			
		}elseif($operation == 'user_info'){
			$_W['page']['title']='个人信息';
			$user = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_user') . " WHERE openid = :openid ", array(':openid' => $_W['openid']));
			include $this->template('user_info');
		}elseif($operation == 'doUserInfo'){
			global $_W,$_GPC;
			load()->model('mc');
			if(empty($_GPC['id'])) {
				$data = array(
						'weid' => $_W['uniacid'],
						'openid' => $_W['openid'],
						'user_name' => trim($_GPC['user_name']),
						'mobile' => trim($_GPC['mobile']),
						'qq' => trim($_GPC['qq']),
						'wchat' => trim($_GPC['wchat']),
						'ctime' => time()
				);
			    $ress=pdo_insert('dxf_ycj_user', $data);
			} else{
				$data = array(
						'user_name' => trim($_GPC['user_name']),
						'mobile' => trim($_GPC['mobile']),
						'qq' => trim($_GPC['qq']),
						'wchat' => trim($_GPC['wchat'])
				);
				$ress=pdo_update('dxf_ycj_user', $data, array('id' => $_GPC['id']));
			}
			message(error(0,''),'','ajax');
			include $this->template('user_info');
		}elseif($operation == 'daili_info'){
			$_W['page']['title']='代理信息';
			$lang['status']=array('1'=>'等待审核','2'=>'审核通过','3'=>'审核不通过','4'=>'禁用','-1'=>'删除');
			$user = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_daili') . " WHERE openid = :openid ", array(':openid' => $_W['openid']));
			include $this->template('daili_info');
		}elseif($operation == 'doDailiInfo'){
			global $_W,$_GPC;
			load()->model('mc');
			if(empty($_GPC['id'])) {
				$data = array(
						'weid' => $_W['uniacid'],
						'openid' => $_W['openid'],
						'headimg' => $_W['fans']['headimgurl'],
						'nickname' => $_W['fans']['nickname'],
						'type' => trim($_GPC['type']),
						'realname' => trim($_GPC['realname']),
						'sex' => trim($_GPC['sex']),
						'phone' => trim($_GPC['phone']),
						'qq' => trim($_GPC['qq']),
						'chat' => trim($_GPC['chat']),
						'cat_id' => trim($_GPC['cat_id']),
						'address' => trim($_GPC['address']),
						'desc' => trim($_GPC['desc']),
						'ctime' => time()
				);

			    $ress=pdo_insert('dxf_ycj_daili', $data);
			   	//客服通知
					$info = "【新代理申请通知】\n";
					$time=date('Y-m-d H:i:s',time());

					$info .= "在{$time},又有新的代理申请了，请注意审核！\n\n";
					$info .= "<a href='http://wx.yingcaijie.cn/app/index.php?i=5&c=entry&do=agent&m=dxf_ycj'>点击查看详情<<</a>";
					$message = array(
					'msgtype' => 'text',
					'text' => array('content' => urlencode($info)),
					'touser' => 'oePgSwq8mnmkQgY3rKdSK8i0Kf3g',
					);
					$account_api = WeAccount::create();
					$status = $account_api->sendCustomNotice($message);
					$message1 = array(
					'msgtype' => 'text',
					'text' => array('content' => urlencode($info)),
					'touser' => 'oePgSwvWv-rgZ5OyRNBZhz2TI5i0',
					);
					$status = $account_api->sendCustomNotice($message1);
					//***************
			} else{
				$data = array(
						'headimg' => $_W['fans']['headimgurl'],
						'nickname' => $_W['fans']['nickname'],
						'type' => trim($_GPC['type']),
						'realname' => trim($_GPC['realname']),
						'sex' => trim($_GPC['sex']),
						'phone' => trim($_GPC['phone']),
						'qq' => trim($_GPC['qq']),
						'chat' => trim($_GPC['chat']),
						'cat_id' => trim($_GPC['cat_id']),
						'address' => trim($_GPC['address']),
						'desc' => trim($_GPC['desc'])
				);
				$ress=pdo_update('dxf_ycj_daili', $data, array('id' => $_GPC['id']));
			}
			 message(error(0,''),'','ajax');
			
		}
	}
	//我的兼职
	public function doMobileMyjob(){
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'myjob';
		$_W['page']['title']='我的兼职';
		if ($operation == 'myjob') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 50;
			$condition = ' WHERE ju.`weid` = :weid and ju.openid=:openid and is_delete=1';
			$params = array(':weid' => $_W['uniacid'],':openid' => $_W['openid']);
			$sql = "SELECT count(*)  FROM " . tablename('dxf_ycj_jobuser'). " as ju left join ".tablename('dxf_ycj_job')." as j on ju.job_id=j.id ".$condition ;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$lang['status']=array('-1'=>'预约失败，代理拒绝','-2'=>'个人原因，放弃兼职','-3'=>'代理点名，未到','1'=>'报名提交待代理同意','2'=>'代理同意兼职预约','3'=>'代理点名，已到');
			$list = pdo_fetchall("SELECT ju.id,ju.headimg,ju.user_name,ju.mobile,ju.wchat,ju.qq,ju.status,ju.ctime,j.title,j.job_stime,j.job_endtime,j.job_area,j.money  FROM " . tablename('dxf_ycj_jobuser'). " as ju left join ".tablename('dxf_ycj_job')." as j on ju.job_id=j.id ".$condition ."  ORDER BY ju.id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif ($operation == 'cancel_myjob') {
			if($_GPC['type']=='is_delete'){
				$ress=pdo_update('dxf_ycj_jobuser', array('is_delete' =>'0'), array('id' => $_GPC['id'], 'openid' => $_W['openid']));

			}elseif($_GPC['type']=='daili_delete'){
				$ress=pdo_update('dxf_ycj_jobuser', array('daili_delete' =>'0'), array('id' => $_GPC['id']));
				
			}
			else{
					//客服通知
					$info = "【兼职通知】\n";
					$time=date('Y-m-d H:i:s',time());
					$openid = pdo_fetch("SELECT j.openid,ju.user_name,ju.job_id FROM " . tablename('dxf_ycj_jobuser') . "as ju left join ".tablename('dxf_ycj_job')."as j on ju.job_id=j.id "." WHERE ju.id = '{$_GPC['id']}'");
					$info .= "尊敬的代理，您好！在{$time},【{$openid['user_name']}】取消了你的兼职报名\n\n";
					$info .= "<a href='http://wx.yingcaijie.cn/app/index.php?i=5&c=entry&op=person_list&id={$openid['job_id']}&do=fabu&m=dxf_ycj'>点击查看详情<<</a>";
					$message = array(
					'msgtype' => 'text',
					'text' => array('content' => urlencode($info)),
					'touser' => $openid['openid']
					);
					$account_api = WeAccount::create();
					$status = $account_api->sendCustomNotice($message);
					//***************
					
				$ress=pdo_update('dxf_ycj_jobuser', array('status' =>'-2'), array('id' => $_GPC['id'], 'openid' => $_W['openid']));
			}
			
			if($ress){

				message(error(0,''),'','ajax');
			}else{
				message(error(1,''),'','ajax');
			}
		}elseif ($operation == 'myjobdetail') {
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list=pdo_fetch("SELECT j.id,j.title,j.type,j.sex,j.age,j.height,j.job_stime,j.job_endtime,j.jihe_time,j.bm_endtime,j.jihe_area,j.job_area,j.money,j.account_time,j.account_type,j.p_num,j.bm_num,j.job_req,j.job_desc,j.ctime,j.updat_time,c.title as ctitle,ca.name as cname ,d.realname,d.sex as dsex,d.phone,d.qq,d.chat,d.address,d.type,d.desc,ju.user_name,ju.mobile,ju.wchat,ju.qq  FROM " . tablename('dxf_ycj_jobuser'). "as ju  left join ". tablename('dxf_ycj_job'). "as j on ju.job_id=j.id left join ". tablename('dxf_ycj_daili'). "as d on j.openid=d.openid left join ".tablename('dxf_ycj_cate')."as c on j.cate_id=c.id left join ". tablename('dxf_ycj_category')."as ca on j.job_cid=ca.id where  ju.id = :id",array(':id' => $id));
				$lang['type']=array('1'=>'兼职','2'=>'勤工俭学','3'=>'实习','4'=>'全职');
				$lang['account_time']=array('1'=>'日结','2'=>'周结','3'=>'月结','4'=>'面议');
				$lang['account_type']=array('1'=>'现金','2'=>'支付宝转账','3'=>'微信转账','4'=>'银行卡转账');
				$lang['sex']=array('1'=>'男','2'=>'女','3'=>'不限');
			}else{
				message('信息不存在');
			}
		}
		include $this->template('my_job');
	}
	//我的发布
	public function doMobileFabu(){
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'fabu';
		if ($operation == 'fabu') {
			$_W['page']['title']='我的发布';
			$pindex = max(1, intval($_GPC['page']));
			$psize = 100;
			$condition = ' WHERE `weid` = :weid and openid=:openid ';
			$params = array(':weid' => $_W['uniacid'],':openid' => $_W['openid']);
			$sql = "SELECT count(*)  FROM " . tablename('dxf_ycj_job').$condition ;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$lang['status']=array('-1'=>'已下线','1'=>'已上线');
			$list = pdo_fetchall("SELECT *  FROM " . tablename('dxf_ycj_job'). $condition ."  ORDER BY updat_time desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif ($operation == 'action_fabu') {
			$ress=pdo_update('dxf_ycj_job', array('status' =>$_GPC['status']), array('id' => $_GPC['id'], 'openid' => $_W['openid']));
			if($ress){
				message(error(0,''),'','ajax');
			}else{
				message(error(1,''),'','ajax');
			}
		}elseif ($operation == 'update_fabu') {
			$ress=pdo_update('dxf_ycj_job', array('updat_time' =>time()), array('id' => $_GPC['id'], 'openid' => $_W['openid']));
			if($ress){
				message(error(0,''),'','ajax');
			}else{
				message(error(1,''),'','ajax');
			}
		}elseif ($operation == 'person_list') {
			$_W['page']['title']='报名列表';
			$job_id=$_GPC['id'];
			$pindex = max(1, intval($_GPC['page']));
			$psize = 100;
			$condition = ' WHERE `weid` = :weid  and `daili_delete`=1	and `job_id` = :job_id';
			$params = array(':job_id' => $job_id,':weid' => $_W['uniacid']);
			$sql = "SELECT count(*)  FROM " . tablename('dxf_ycj_jobuser').$condition ;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$lang['status']=array('-1'=>'已拒绝报名','-2'=>'已放弃该兼职','-3'=>'点名未到','1'=>'已报名，待同意','2'=>'同意兼职报名','3'=>'代理点名已到');
			$list = pdo_fetchall("SELECT *  FROM " . tablename('dxf_ycj_jobuser'). $condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif ($operation == 'update_jobuser') {
			//客服通知开始
			$status=array('-3'=>'代理点名您未到','-1'=>'抱歉！代理拒绝了您的兼职报名，请预约其他兼职。','2'=>'代理同意您的兼职报名，请按时参加！','3'=>'代理点名您已到');
			$info = "【兼职通知】\n";
			$time=date('Y-m-d H:i:s',time());
			$info .= "尊敬的用户，您好！在{$time},【{$status[$_GPC['status']]}】\n\n";
			$info .= "<a href='http://wx.yingcaijie.cn/app/index.php?i=5&c=entry&op=myjob&do=myjob&m=dxf_ycj'>点击查看详情<<</a>";
			//客服通知结束
			$ress=pdo_update('dxf_ycj_jobuser', array('status' =>$_GPC['status']), array('id' => $_GPC['id']));
			if($ress){
				$openid = pdo_fetch("SELECT openid FROM " . tablename('dxf_ycj_jobuser') . " WHERE id = {$_GPC['id']}");
				$message = array(
				'msgtype' => 'text',
				'text' => array('content' => urlencode($info)),
				'touser' => $openid['openid']
				);
				$account_api = WeAccount::create();
				$status = $account_api->sendCustomNotice($message);
				message(error(0,''),'','ajax');
			}else{
				message(error(1,''),'','ajax');
			}
		}
		elseif ($operation == 'jobdetail') {
			$_W['page']['title']='详情';
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$list=pdo_fetch("SELECT j.id,j.title,j.type,j.sex,j.age,j.height,j.job_stime,j.job_endtime,j.jihe_time,j.bm_endtime,j.jihe_area,j.job_area,j.money,j.account_time,j.account_type,j.p_num,j.bm_num,j.job_req,j.job_desc,j.ctime,j.updat_time,c.title as ctitle,ca.name as cname ,d.realname,d.sex as dsex,d.phone,d.qq,d.chat,d.address,d.type,d.desc  FROM " . tablename('dxf_ycj_job'). "as j left join ". tablename('dxf_ycj_daili'). "as d on j.openid=d.openid left join ".tablename('dxf_ycj_cate')."as c on j.cate_id=c.id left join ". tablename('dxf_ycj_category')."as ca on j.job_cid=ca.id where  j.id = :id",array(':id' => $id));
				$lang['type']=array('1'=>'兼职','2'=>'勤工俭学','3'=>'实习','4'=>'全职');
				$lang['account_time']=array('1'=>'日结','2'=>'周结','3'=>'月结','4'=>'面议');
				$lang['account_type']=array('1'=>'现金','2'=>'支付宝转账','3'=>'微信转账','4'=>'银行卡转账');
				$lang['sex']=array('1'=>'男','2'=>'女','3'=>'不限');
				//var_dump($list);die;
			}else{
				message('信息不存在');
			}
			
		}
		include $this->template('fabu');
	}
	//客服中心
	public function doMobileKefu(){
		global $_W,$_GPC;
		load()->func('tpl');
		include $this->template('kefu');
	}
	//操作手册
	public function doMobileShouce(){
		global $_W,$_GPC;
		load()->func('tpl');
		$_W['page']['title']='手册';
		include $this->template('shouce');
	}
	//代理管理
	public function doMobileAgent() {
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$_W['page']['title']='代理管理';
			$pindex = max(1, intval($_GPC['page']));
			$psize = 100;
			$condition = ' WHERE d.`weid` = :weid and d.`status`>0';
			$params = array(':weid' => $_W['uniacid']);
			
			$sql = "SELECT count(*) FROM " .  tablename('dxf_ycj_daili'). " as d left join ".tablename('dxf_ycj_category')." as c on d.cat_id=c.id  ". $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$lang['grade']=array('1'=>'金牌代理','2'=>'银牌代理','3'=>'普通代理');
			$lang['type']=array('1'=>'个人代理','2'=>'公司代理');
			$lang['status']=array('1'=>'等待审核','2'=>'审核通过','3'=>'审核不通过','4'=>'禁用','-1'=>'删除');
			$lang['sex']=array('1'=>'男','2'=>'女','3'=>'不限');
			$list = pdo_fetchall("SELECT d.id,d.headimg,d.realname,d.sex,d.phone,d.chat,d.qq,d.age,d.type,d.status,d.grade,d.address,d.desc,d.ctime,c.name,c.level,c.parentid FROM " . tablename('dxf_ycj_daili'). " as d left join ".tablename('dxf_ycj_category')." as c on d.cat_id=c.id  ". $condition ."  ORDER BY d.status asc,d.id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($operation == 'update_daili'){
			$ress=pdo_update('dxf_ycj_daili', array('status' =>$_GPC['status']), array('id' => $_GPC['id']));
			if($ress){
				//客服通知
					$info = "【通知】\n";
					$status=array('2'=>'通过','4'=>'禁用');
					$ress = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_daili')." WHERE id = '{$_GPC['id']}'");

					$info .= "尊敬的代理，您好！管理员【{$status[$_GPC['status']]}】了你的代理权限\n\n";
					$info .= "<a href='http://wx.yingcaijie.cn/app/index.php?i=5&c=entry&op=daili_info&do=user&m=dxf_ycj'>点击查看详情<<</a>";
					$message = array(
					'msgtype' => 'text',
					'text' => array('content' => urlencode($info)),
					'touser' => $ress['openid']
					);
					$account_api = WeAccount::create();
					$status = $account_api->sendCustomNotice($message);
					//***************
				message(error(0,''),'','ajax');
			}else{
				message(error(1,''),'','ajax');
			}
		}
		include $this->template('agent');
	}
	//用户管理
	public function doMobileYonghu() {
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {

			//*模板消息开始*/
			/*$postdata= array(
			    'first' => array(
			        'value' => '测试模板消息',
			        'color' => '#FF683F'
			    ),
			    'keyword1' => array(
			        'value' => '职位名称',
			        'color' => '#FF683F'
			    ),
			    'keyword2' => array(
			        'value' => '郑州地区',
			        'color' => '#FF683F'
			    ),
			    'keyword3' => array(
			        'value' => '500-800元',
			        'color' => '#FF0000'
			    ),
			    'keyword4' => array(
			        'value' => '2017-09-09',
			        'color' => '#FF683F'
			    ),
			    'remark' => array(
			        'value' => '',
			        'color' => ''
			    )
			);
			$account_api = WeAccount::create();
			$account_api->sendTplNotice('oePgSwq8mnmkQgY3rKdSK8i0Kf3g', '_dEZPRuQWo52sKOOI6CmwAw90lbt94UNQFSpjjcyTYg', $postdata, 'http://wx.yingcaijie.cn/app/index.php?i=5&c=entry&op=daili_info&do=user&m=dxf_ycj', $topcolor = '#FF683F');
			//模板消息结束
			*/
			$_W['page']['title']='用户管理';
			$pindex = max(1, intval($_GPC['page']));
			$psize = 100;
			$condition = ' WHERE `weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			
			$sql = "SELECT count(*) FROM " .  tablename('dxf_ycj_user'). $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			
			$lang['status']=array('1'=>'正常','-1'=>'禁用');
			$lang['sex']=array('1'=>'男','2'=>'女','3'=>'不限');
			$list = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_user'). $condition ."  ORDER BY status asc,id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($operation == 'update_user'){
			$ress=pdo_update('dxf_ycj_user', array('status' =>$_GPC['status']), array('id' => $_GPC['id']));
			if($ress){
				message(error(0,''),'','ajax');
			}else{
				message(error(1,''),'','ajax');
			}
		}
		include $this->template('yonghu');
	}
	//招聘手机端管理
	public function doMobileMgg(){
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$_W['page']['title']='发布管理';
			//var_dump($_GPC);
			$pindex = max(1, intval($_GPC['page']));
			$psize = 50;
			$condition = ' WHERE j.`weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			$sql = "SELECT count(*) FROM " . tablename('dxf_ycj_job'). " as j left join ".tablename('dxf_ycj_category')." as c on j.job_cid=c.id left join ".tablename('dxf_ycj_category')." as ca on c.parentid=ca.id left join ".tablename('dxf_ycj_daili')." as d on j.openid=d.openid ".$condition;

			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$lang['status']=array('1'=>'上线','-1'=>'下线');
			$list = pdo_fetchall("SELECT j.*,c.name,ca.name,d.realname  FROM " . tablename('dxf_ycj_job'). " as j left join ".tablename('dxf_ycj_category')." as c on j.job_cid=c.id left join ".tablename('dxf_ycj_category')." as ca on c.parentid=ca.id left join ".tablename('dxf_ycj_daili')." as d on j.openid=d.openid ".$condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}
		
		include $this->template('jobmanager');
	}
	//状态ajax的修改
	public function doMobileSetStatus() {
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$type = $_GPC['type'];
		$data = intval($_GPC['data']);
		if (in_array($type, array('ad_hot', 'ad_status'))) {
			$data = ($data==1?'0':'1');
			$ress=pdo_update("dxf_ycj_job", array($type => $data), array("id" => $id, "weid" => $_W['uniacid']));
			if($ress){
				die(json_encode(array("result" => 1, "data" => $data)));
			}else{
				die(json_encode(array("result" => 0)));
			}
		}
	}
	
}