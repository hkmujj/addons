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
				$condition .=" AND `ctime` > $starttime  AND `ctime`< $endtime ";
			}

			$sql = "SELECT count(*) FROM " .  tablename('dxf_ycj_activity'). $condition;
			$total = pdo_fetchcolumn($sql, $params);
			$pager = pagination($total, $pindex, $psize);
			$list = pdo_fetchall("SELECT * FROM " . tablename('dxf_ycj_activity'). $condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
		}elseif($operation == 'dogushi'){
				$data = array(
						'weid'=>$_W['uniacid'],
						'openid'=>$_W['openid'],
						'name' =>$_GPC['name'],
						'phone' =>$_GPC['phone'],
						'qq' =>$_GPC['qq'],
						'wchat' =>$_GPC['wchat'],
						'ctime' =>time(),
						'status' =>1
				);
				$ress=pdo_insert('dxf_ycj_activity', $data);
				if($ress){
					message(error(0,''),'','ajax');
					
				}else{
					message(error(1,''),'','ajax');
				}

			
				

		}
		include $this->template('activity/activity');

