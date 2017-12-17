<?php
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'gushi';
		if ($operation == 'gushi') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 30;
			$condition = ' WHERE `weid` = :weid and `type` = 1';
			$params = array(':weid' => $_W['uniacid']);
			if (!empty($_GPC['status'])) {
				$condition .= ' AND  `status` = :status';
				$params[':status'] =$_GPC['status'];
			}
			if (empty($_GPC['time'])) {
				$starttime=time()-(3600*24*7);
				$endtime=time();
			}else{
				$starttime=strtotime($_GPC['time']['start']);
				$endtime=strtotime($_GPC['time']['end']);
				
			}
			$condition .=" AND `ctime` > $starttime  AND `ctime`< $endtime ";
			$sql = "SELECT count(*) FROM " .  tablename('dxf_ycj_activity'). $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_activity'). $condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($operation == 'dogushi'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$children = array();
				$list = pdo_fetch("SELECT * FROM " . tablename('dxf_ycj_activity') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
				
			}
			if (checksubmit('submit')) {
				//var_dump($_GPC);die;
				$data = array(
						
						'status' =>$_GPC['status']
						
				);
				

				$ress=pdo_update('dxf_ycj_activity', $data, array('id' => $id, 'weid' => $_W['uniacid']));
				
				if($ress){
					message('保存成功！', $this->createWebUrl('activity', array('op' => 'gushi')), 'success');
				}else{
					message('保存失败');
				}
			}
			
				

		}
		include $this->template('activity/activity');

