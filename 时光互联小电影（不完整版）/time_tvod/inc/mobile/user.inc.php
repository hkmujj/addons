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
	load()->model('mc');
	
	$uniacid = intval($_W['uniacid']);
	$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid ={$uniacid}");
	$config = unserialize($setting['config']);
	$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	
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
	
	$member = pdo_fetch("SELECT * FROM " . tablename('mc_members') . " WHERE uid = {$uid}");	
	$leadnum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename($this->modulename . '_user') ." WHERE leader1 = {$uid}");

	switch ($operation) {
		case 'recharge':
			$feelevel = explode("\n", $config['vip_fee']);
			foreach ($feelevel as $key => $val) {
				$arr = explode('|', $val);
				$fees[$arr[0]]['id'] = $arr[0];
				$fees[$arr[0]]['name'] = $arr[1];
				$fees[$arr[0]]['day'] = $arr[2];
				$fees[$arr[0]]['price'] = $arr[3];
				$fees[$arr[0]]['point'] = $arr[4];
			}

			if (checksubmit('submit')) {
				$data = $_GPC['data'];
				$data['feeid'] = (int) $data['feeid'];
				$data['feetype'] = (int) $data['feetype'];
				
				//现金购买
				if($data['feetype']  == 0){
					if($data['feeid'] == 0){
						exit(json_encode(array('code' => 0 ,'reason' => '请选择开通类型')));
					}	
					$price = $fees[$data['feeid']]['price'];
					if($uid == 0 || empty($uid)){
						exit(json_encode(array('code' => 0 ,'reason' => '请登录后再购买')));
					}	
					$order = array();
					$order['sn'] = date('YmdHis').mt_rand(10000, 99999);
					$order['type'] = 0;
					$order['uniacid'] = $uniacid;
					$order['uid'] = $uid;
					$order['money'] = $price;
					$order['create_time'] = $_W['timestamp'];
					$order['feeid'] = $data['feeid'];
					$result = pdo_insert($this->modulename . '_order', $order);
					if($result){
					    $params = array(
					        'tid' => $order['sn'],
					        'ordersn' => $order['sn'],
					        'title' => '充值'.$fees[$data['feeid']]['name'].'会员', 
					        'fee' => $price, 
					        'user' => $_W['member']['uid'],
					    );			
					    //调用pay方法
					    $this->pay($params);
					}

				}
				//积分兑换
				if($data['feetype']  == 1){
					if($data['feeid'] == 0){
						message('请选择充值类型');
					}	
					$needpoint = $fees[$data['feeid']]['point'];
					if($user['point'] < $needpoint){
						message('您的积分不足');
					}						
					$order = array();
					$order['sn'] = date('YmdHis').mt_rand(10000, 99999);
					$order['type'] = 1;
					$order['uid'] = $uid;
					$order['uniacid'] = $uniacid;
					$order['point'] = $needpoint;
					$order['create_time'] = $_W['timestamp'];
					$order['pay_time'] = $_W['timestamp'];
					$order['status'] = 1;
					$order['feeid'] = $data['feeid'];
					$result = pdo_insert($this->modulename . '_order', $order);
					if($result){
						pdo_update($this->modulename . '_user', array('point' => $user['point'] - $needpoint), array('uid' => $uid));
						message('充值成功', $this->createMobileUrl('user', array('op' => 'index')), 'success');
					}
					message('充值失败', $this->createMobileUrl('user', array('op' => 'recharge')), 'success');
				}
				die;
			}	
			include $this->template('user_recharge');
			break;
		case 'cash':
			if($config['tx_open'] != 1){
				message('不支持提现功能');
			}
			if (checksubmit('submit')) {
				$data = $_GPC['data'];
				$data['money'] = round($data['money'],2);
				if($data['money'] == 0 || $data['money'] >200){
					message('金额填写错误');
				}
				if($data['money'] > $user['money']){
					message('余额不足提现需求');
				}
				$data['agree'] = (int)$data['agree'];
				if($data['agree'] == 0 ){
					message('没有同意协议');
				}
				
				if($config['hbtx_open'] == 1){
					//开启红包提现
					$fans = pdo_fetch("SELECT * FROM " . tablename('mc_mapping_fans') . " WHERE  uid = {$member['uid']}");
					$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
					load()->func('communication');
					$pars = array();
					$pars['nonce_str'] = random(32);
					$pars['mch_billno'] = $_W['cache']["unisetting:$uniacid"]['payment']['wechat']['mchid'] . date('Ymd') . time();
					$pars['mch_id'] = $_W['cache']["unisetting:$uniacid"]['payment']['wechat']['mchid'];
					$pars['wxappid'] = $_W['cache']["uniaccount:$uniacid"]['key'];
					$pars['nick_name'] = $member['nickname'];
					$pars['send_name'] = $config['title'];
					$pars['re_openid'] = $fans['openid'];
					$pars['total_amount'] = $data['money'] * 100;
					$pars['total_num'] = 1;
					$pars['wishing'] = '推广活动';
					$pars['client_ip'] = $_SERVER['SERVER_ADDR'];
					$pars['act_name'] = '推广奖励红包';
					$pars['remark'] = '恭喜您得到了推广奖励';		
					ksort($pars, SORT_STRING);
					$string1 = '';
					foreach ($pars as $k => $v) {
						$string1.= "{$k}={$v}&";
					}
					$string1.= "key={$_W['cache']["unisetting:$uniacid"]['payment']['wechat']['apikey']}";
					$pars['sign'] = strtoupper(md5($string1));
					$xml = array2xml($pars);
					$extras = array();
					$extras['CURLOPT_CAINFO'] = '../addons/time_tvod/cert/' . md5("root{$_W['uniacid']}ca") . ".pem";	//CA
					$extras['CURLOPT_SSLCERT'] ='../addons/time_tvod/cert/' . md5("apiclient_{$_W['uniacid']}cert") . ".pem";	//CERT
					$extras['CURLOPT_SSLKEY'] = '../addons/time_tvod/cert/' . md5("apiclient_{$_W['uniacid']}key") . ".pem";	//KEY
					$procResult = false;
					$resp = ihttp_request($url, $xml, $extras);			
					if (is_error($resp)) {
						$sendstatus = 0;
					} else {
						$xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
						$dom = new DOMDocument();
						if ($dom->loadXML($xml)) {
							$xpath = new DOMXPath($dom);
							$send_listid = $xpath->evaluate('string(//xml/send_listid)');
							$code = $xpath->evaluate('string(//xml/return_code)');
							$ret = $xpath->evaluate('string(//xml/result_code)');
							if (strtolower($code) == 'success' && strtolower($ret) == 'success') {
								$sendstatus = 1;
							} else {
								$sendstatus = 0;
							}
						}else{
							$sendstatus = 0;
						}
					}
	
					if($sendstatus == 1){
						$cash = array();
						$cash['uniacid'] = $uniacid;
						$cash['uid'] = $uid;
						$cash['money'] = $data['money'];		
						$cash['status'] = 1;
						$cash['method'] = 0;
						$cash['create_time'] = $_W['timestamp'];
						pdo_insert($this->modulename . '_cash', $cash);
						pdo_update($this->modulename . '_user', array('money' => $user['money'] - $data['money']), array('uid' => $uid));
						message('提现成功', $this->createMobileUrl('user', array('op' => 'index')), 'success');
					}else{
						message('提现失败', $this->createMobileUrl('user', array('op' => 'index')), 'error');
					}
				}else{
					//普通提现记录申请
					$cash['uniacid'] = $uniacid;
					$cash['uid'] = $uid;
					$cash['money'] = $data['money'];	
					$cash['status'] = 0;
					$cash['method'] = (int) $data['method'];
					$cash['account'] = trim($data['account']);
					$cash['create_time'] = $_W['timestamp'];
					pdo_insert($this->modulename . '_cash', $cash);
					pdo_update($this->modulename . '_user', array('money' => $user['money'] - $data['money']), array('uid' => $uid));
					message('申请成功，请等待审核！', $this->createMobileUrl('user', array('op' => 'index')), 'success');
				}

			}
			include $this->template('user_cash');
			break;
		case 'paper':
			if(empty($config['hb_image'])){
				message('没有上传海报背景图');
			}else{
				$bg = $config['hb_image'];
			}
			include_once '../addons/time_tvod/function.php';
			$qrcode = getQrcode($user,$this->modulename);
			$file = '../addons/time_tvod/temp/'.$uniacid.'_'.$_W['acid'].'_'.$uid.'.png';
			
			
			if(!file_exists($file) || $qrcode['new'] == 1){
				@unlink($file);
				
				set_time_limit(0);
				@ini_set('memory_limit', '256M');
				$size = getimagesize(tomedia($bg));
				$target = imagecreatetruecolor(350,600);
				$bg = imagecreates(tomedia($bg));
				imagecopy($target, $bg, 0, 0, 0, 0, 350, 600);
				imagedestroy($bg);
				$img = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($qrcode['ticket']);
				mergeImage($target, $img, array('left' => 100, 'top' => 260, 'width' => 150, 'height' => 150));	
				imagejpeg($target, $file);
				imagedestroy($target);
			}

			include $this->template('user_paper');
			break;
		case 'order':
			$feelevel = explode("\n", $config['vip_fee']);
			foreach ($feelevel as $key => $val) {
				$arr = explode('|', $val);
				$fees[$arr[0]]['id'] = $arr[0];
				$fees[$arr[0]]['name'] = $arr[1];
				$fees[$arr[0]]['day'] = $arr[2];
				$fees[$arr[0]]['price'] = $arr[3];
				$fees[$arr[0]]['point'] = $arr[4];
			}
			$list = pdo_fetchall('select * from ' . tablename($this->modulename . '_order') . " where uid = '{$uid}' and uniacid = '{$uniacid}' order by id desc limit 20");
			include $this->template('user_order');
			break;
		case 'cashlog':	
			$list = pdo_fetchall('select * from ' . tablename($this->modulename . '_cash') . " where uid = '{$uid}' and uniacid = '{$uniacid}' order by id desc limit 20");	
			include $this->template('user_cashlog');
			break;
		default:
			include $this->template('user_index');
			break;
	}

	
	
	
	
	
	
	
	
?>