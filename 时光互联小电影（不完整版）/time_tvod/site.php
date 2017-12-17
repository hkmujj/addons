<?php
/**
 * 小电影模块微站定义
 *
 * @author 时光
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Time_tvodModuleSite extends WeModuleSite {

	public function payResult($params) {
		
		global $_W, $_GPC;
		$uniacid = intval($_W['uniacid']);
	    //一些业务代码
	    //根据参数params中的result来判断支付是否成功
	    if ($params['result'] == 'success' && $params['from'] == 'notify') {
	        	
	        //此处会处理一些支付成功的业务代码
		    $sn = $params['tid'];
		    $order = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_order') . " WHERE uniacid = '{$uniacid}' AND sn = '{$sn}'");
		   
		   	if($order['status'] == 0){
		   
			    $user =  pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE  uid = {$order['uid']}");
		
				//更新订单状态
				pdo_update($this->modulename . '_order', array('status' => 1), array('id' => $order['id']));
				
				//延长会员时间
				$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid = {$uniacid}");
				$config = unserialize($setting['config']);
				$feelevel = explode("\n", $config['vip_fee']);
				foreach ($feelevel as $key => $val) {
					$arr = explode('|', $val);
					$fees[$arr[0]]['id'] = $arr[0];
					$fees[$arr[0]]['name'] = $arr[1];
					$fees[$arr[0]]['day'] = $arr[2];
					$fees[$arr[0]]['price'] = $arr[3];
					$fees[$arr[0]]['point'] = $arr[4];
				}
				$addtime = $fees[$order['feeid']]['day'] * 24 * 60 * 60;	
				
				if($user['vip_time'] < $_W['timestamp']){
					$newviptime =  $_W['timestamp'] + $addtime;
				}else{
					$newviptime =  $user['vip_time'] + $addtime;
				}
				
				pdo_update($this->modulename . '_user', array('vip_time' => $newviptime), array('uid' => $order['uid']));
				
				
				//三级分销奖励
				$money_leader1 = $money_leader2 = $money_leader3 = 0;
				$fxfees = explode('|', $config['fx_fee']);
				if($config['fx_open'] == 1 && is_array($fxfees)){
					if($user['leader1'] != 0 && !empty($fxfees[0])){
						$money_leader1 = round($order['money'] * $fxfees[0],2);
						if($money_leader1 > 0){
							$leader1 = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE  uid = {$user['leader1']}");
							pdo_update($this->modulename . '_user', array('money' => $leader1['money'] + $money_leader1), array('uid' => $leader1['uid']));
						}
					}
					if($user['leader2'] != 0 && !empty($fxfees[1])){
						$money_leader2 = round($order['money'] * $fxfees[1],2);
						if($money_leader2 > 0){
							$leader2 = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE  uid = {$user['leader2']}");
							pdo_update($this->modulename . '_user', array('money' => $leader2['money'] + $money_leader2), array('uid' => $leader2['uid']));
						}
					}
					if($user['leader3'] != 0 && !empty($fxfees[2])){
						$money_leader3 = round($order['money'] * $fxfees[2],2);
						if($money_leader3 > 0){
							$leader3 = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE  uid = {$user['leader3']}");
							pdo_update($this->modulename . '_user', array('money' => $leader2['money'] + $money_leader3), array('uid' => $leader3['uid']));
						}
					}	
				}
				
				
				$acid = $_W['acid'];
				$acc = WeAccount::create($acid);
				$url = $_W['siteroot'] . $this->createMobileUrl('index');
				//模版通知 -> 充值用户
				$userfan = pdo_fetch("SELECT * FROM " . tablename('mc_mapping_fans') . " WHERE  uid = {$order['uid']}");
		        $postdata = array(
						'first'=>array(
							'value'=>'亲，您充值成功了',
							'color'=>'#173177'
						),
						'keyword1'=>array(
							'value'=> $fees[$order['feeid']]['name'].'('.$fees[$order['feeid']]['day'].'天)',
							'color'=>'#173177'
						),
						'keyword2'=>array(
							'value'=> $order['money'],
							'color'=>'#173177'
						),
						'remark'=>array(
							'value'=> '您的会员时间延长至'.date('Y-m-d',$newviptime).',感谢您的支持！',
							'color'=>'#173177'
						)
					);
				$acc -> sendTplNotice($userfan['openid'], $config['mbxx_user'], $postdata, $url, $topcolor = '#FF683F');
			
			
			
				//模版通知 -> 三级分销
				$fxfees = explode('|', $config['fx_fee']);
				if($config['fx_open'] == 1 && is_array($fxfees)){
					
					//一级领导
					if($money_leader1 > 0){
						$leaderfan1 = pdo_fetch("SELECT * FROM " . tablename('mc_mapping_fans') . " WHERE  uid = {$leader1['uid']}");
			        	$postdata = array(
							'first'=>array(
								'value'=>'亲，您又成功推广了一笔订单',
								'color'=>'#173177'
							),
							'keyword1'=>array(
								'value'=> $order['sn'],
								'color'=>'#173177'
							),
							'keyword2'=>array(
								'value'=> $order['money'],
								'color'=>'#173177'
							),
							'keyword3'=>array(
								'value'=> $money_leader1,
								'color'=>'#173177'
							),
							'remark'=>array(
								'value'=> '感谢有您，如有问题请直接在微信留言，我们将第一时间为您服务！',
								'color'=>'#173177'
							)
						);
						$acc -> sendTplNotice($leaderfan1['openid'], $config['mbxx_leader'], $postdata, $url, $topcolor = '#FF683F');
					}
					
					//二级领导
					if($money_leader2 > 0){
						$leaderfan2 = pdo_fetch("SELECT * FROM " . tablename('mc_mapping_fans') . " WHERE  uid = {$leader2['uid']}");
			        	$postdata = array(
							'first'=>array(
								'value'=>'亲，您的下级又成功推广了一笔订单',
								'color'=>'#173177'
							),
							'keyword1'=>array(
								'value'=> $order['sn'],
								'color'=>'#173177'
							),
							'keyword2'=>array(
								'value'=> $order['money'],
								'color'=>'#173177'
							),
							'keyword3'=>array(
								'value'=> $money_leader2,
								'color'=>'#173177'
							),
							'remark'=>array(
								'value'=> '感谢有您，如有问题请直接在微信留言，我们将第一时间为您服务！',
								'color'=>'#173177'
							)
						);
						$acc -> sendTplNotice($leaderfan2['openid'], $config['mbxx_leader'], $postdata, $url, $topcolor = '#FF683F');
					}
					
					
					//三级领导
					if($money_leader3 > 0){
						$leaderfan2 = pdo_fetch("SELECT * FROM " . tablename('mc_mapping_fans') . " WHERE  uid = {$leader3['uid']}");
			        	$postdata = array(
							'first'=>array(
								'value'=>'亲，您的下下级又成功推广了一笔订单',
								'color'=>'#173177'
							),
							'keyword1'=>array(
								'value'=> $order['sn'],
								'color'=>'#173177'
							),
							'keyword2'=>array(
								'value'=> $order['money'],
								'color'=>'#173177'
							),
							'keyword3'=>array(
								'value'=> $money_leader3,
								'color'=>'#173177'
							),
							'remark'=>array(
								'value'=> '感谢有您，如有问题请直接在微信留言，我们将第一时间为您服务！',
								'color'=>'#173177'
							)
						);
						$acc -> sendTplNotice($leaderfan3['openid'], $config['mbxx_leader'], $postdata, $url, $topcolor = '#FF683F');
					}
				}
			}
		}

	    if ($params['from'] == 'return') {
	        if ($params['result'] == 'success') {
	            message('支付成功！', $this->createMobileUrl('user'), 'success');
	        } else {
	            message('支付失败！', $this->createMobileUrl('user'), 'error');
	        }
	    }
		
		
		
	}



}