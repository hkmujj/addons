<?php
/**
 * 
 *
 * @author 奇辰
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Navlange_pincheModuleSite extends WeModuleSite {
	public function is_full($pin_id) {
		$member = pdo_getall('navlange_pinche_member',array('uniacid'=>$_W['uniacid'],'pin_id'=>$pin_id));
		$pin = pdo_get('navlange_pinche_pin',array('id'=>$pin_id));
		$pined_count = 0;
		foreach ($member as $key => $value) {
			$travel = pdo_get('navlange_pinche_travel',array('id'=>$value['travel_id']));
			$pined_count += $travel['amount'];
		}
		if($pin_count>=$pin['passenger_count']) {
			return true;
		} else {
			return false;
		}
	}
	public function pin_count($pin_id) {
		global $_W;
		$member = pdo_getall('navlange_pinche_member',array('uniacid'=>$_W['uniacid'],'pin_id'=>$pin_id));
		$amount = 0;
		foreach ($member as $key => $value) {
			$travel = pdo_get('navlange_pinche_travel',array('id'=>$value['travel_id']));
			$amount += $travel['amount'];
		}
		return $amount;
	}
	public function payResult($params) {
		if ($params['from'] == 'notify') {
			if ($params['result'] == 'success') {
				$member = pdo_get('navlange_pinche_member',array('pay_tid'=>$params['tid']));
				pdo_update('navlange_pinche_travel',array('status'=>'1'),array('id'=>$member['travel_id']));
	            pdo_update('navlange_pinche_member',array('status'=>'1','pay_mode'=>'1'),array('pay_tid'=>$params['tid']));
	            pdo_delete('navlange_pinche_member',array('travel_id'=>$member['travel_id'],'status'=>'0'));
            	$this->pin_success_notify($member['travel_id']);
			} 
		} else if ($params['from'] == 'return') {
			if($params['result'] == 'success') {
				message('支付成功！', $this->createMobileurl('my_travel'), 'success');
			} else {
				message('支付失败！', '', 'error');
			}
		}
	}
	public function trans_status($status) {
		if($status == '0') {
			return '待接单';
		} else if($status == '0.5') {
			return '待确认';
		} else if ($status == '1') {
			return '待出行';
		} else if ($status == '4') {
			return '已到达';
		}
	}
	public function trans_pin_status($status) {
		if($status == '0') {
			return '待出行';
		} else if ($status == '1') {
			return '已出行';
		} else if ($status == '2') {
			return '已结束';
		}
	}
	public function member_info($openid) {
		global $_W;
        $conf = pdo_fetch("SELECT member_on,member_type FROM " . tablename('navlange_pinche_conf') . " WHERE uniacid=" . $_W['uniacid']);
        $uid = mc_openid2uid($openid);
        $member = pdo_fetch("SELECT member.level_id as level_id FROM " . tablename('navlange_member') . " AS member LEFT JOIN " . tablename('navlange_member_level') . " AS level ON member.level_id=level.id WHERE member.uid=:uid AND level.type=:type",array('uid'=>$uid,'type'=>$conf['member_type']));
        if(empty($member) || (!empty($member) && $member['is_permanent'] == '0' && $member['expire']<=time())) {
            $level = pdo_get('navlange_member_level',array('type'=>$conf['member_type'],'is_default'=>'1'));
        } else {
            $level = pdo_get('navlange_member_level',array('id'=>$member['level_id']));
        }
        $info['level'] = $level['name'];
        $info['level_id'] = $level['id'];
        $info['discount'] = $level['discount'];
        return $info;
	}
	function join_result_sms($tel,$name,$owner_tel) {
		global $_W;
		$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
  		$message = pdo_get('navlange_pinche_message',array('uniacid'=>$_W['uniacid']));
		$smsConf = array(
		    'key'   => $message['juhe_key'], //您申请的APPKEY
		    'mobile'    => $tel, //接受短信的用户手机号码
		    'tpl_id'    => $message['join_result_juhe_id'], //您申请的短信模板ID，根据实际情况修改
		    'tpl_value' =>'#name#=' . $name . '&#tel#=' . $owner_tel //您设置的模板变量，根据实际情况修改
		);
		$content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信
		 
		if($content){
		    $result = json_decode($content,true);
		    $error_code = $result['error_code'];
		    if($error_code == 0){
		        return 0;
		    }else{
		        return 1;
		    }
		}else{
		    return 2;
		}
	}
	function join_notice_sms($tel,$owner_name,$passenger_tel) {
		global $_W;
		$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
  		$message = pdo_get('navlange_pinche_message',array('uniacid'=>$_W['uniacid']));
		$smsConf = array(
		    'key'   => $message['juhe_key'], //您申请的APPKEY
		    'mobile'    => $tel, //接受短信的用户手机号码
		    'tpl_id'    => $message['join_notice_juhe_id'], //您申请的短信模板ID，根据实际情况修改
		    'tpl_value' =>'#name#=' . $owner_name . '&#tel#=' . $passenger_tel //您设置的模板变量，根据实际情况修改
		);
		 
		$content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信
		 
		if($content){
		    $result = json_decode($content,true);
		    $error_code = $result['error_code'];
		    if($error_code == 0){
		        return 0;
		    }else{
		        return 1;
		    }
		}else{
		    return 2;
		}
	}
	function release_success_sms($tel,$owner_name) {
		global $_W;
		$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
  		$message = pdo_get('navlange_pinche_message',array('uniacid'=>$_W['uniacid']));
		$smsConf = array(
		    'key'   => $message['juhe_key'], //您申请的APPKEY
		    'mobile'    => $tel, //接受短信的用户手机号码
		    'tpl_id'    => $message['release_success_juhe_id'], //您申请的短信模板ID，根据实际情况修改
		    'tpl_value' =>'#name#=' . $owner_name //您设置的模板变量，根据实际情况修改
		);
		$content = $this->juhecurl($sendUrl,$smsConf,1); //请求发送短信
		 
		if($content){
		    $result = json_decode($content,true);
		    $error_code = $result['error_code'];
		    if($error_code == 0){
		        return 0;
		    }else{
		        return 1;
		    }
		}else{
		    return 2;
		}
	}
	 
	/**
	 * 请求接口返回内容
	 * @param  string $url [请求的URL地址]
	 * @param  string $params [请求的参数]
	 * @param  int $ipost [是否采用POST形式]
	 * @return  string
	 */
	function juhecurl($url,$params=false,$ispost=0){
	    $httpInfo = array();
	    $ch = curl_init();
	    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
	    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
	    curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	    if( $ispost )
	    {
	        curl_setopt( $ch , CURLOPT_POST , true );
	        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
	        curl_setopt( $ch , CURLOPT_URL , $url );
	    }
	    else
	    {
	        if($params){
	            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
	        }else{
	            curl_setopt( $ch , CURLOPT_URL , $url);
	        }
	    }
	    $response = curl_exec( $ch );
	    if ($response === FALSE) {
	        //echo "cURL Error: " . curl_error($ch);
	        return false;
	    }
	    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	    curl_close( $ch );
	    return $response;
	}
	public function get_travel_status($travel_id) {
		$travel = pdo_get('navlange_pinche_travel',array('id'=>$travel_id));
		if($travel['status'] == '0') {
			$member = pdo_get('navlange_pinche_member',array('travel_id'=>$travel_id));
			if(!empty($member)) {
				return '0.5';
			} else {
				return '0';
			}
		} else {
			return $travel['status'];
		}
	}
	public function pin_success_notify($travel_id) {
		global $_W;
		$travel = pdo_get('navlange_pinche_travel',array('id'=>$travel_id));
		$member = pdo_get('navlange_pinche_member',array('travel_id'=>$travel_id,'status'=>'1'));
		$owner = pdo_fetch("SELECT owner.openid as openid,owner.name as name,owner.tel as tel FROM " . tablename('navlange_pinche_pin') . " AS pin LEFT JOIN " . tablename('navlange_pinche_owner') . " AS owner ON pin.owner_id=owner.id WHERE pin.id=" . $member['pin_id']);
		$message = pdo_get('navlange_pinche_message',array('uniacid'=>$_W['uniacid']));
		$pin = pdo_get('navlange_pinche_pin',array('id'=>$member['pin_id']));
		$data['first'] = array('value'=>'拼车成功，谢谢使用！','color'=>'#173177');
		$data['keyword1'] = array('value'=>$pin['departure_station'],'color'=>'#173177');
		$data['keyword2'] = array('value'=>$pin['terminal_station'],'color'=>'#173177');
		$data['keyword3'] = array('value'=>date('Y-m-d H:i:s',$pin['departure_time']),'color'=>'#173177');
		$data['keyword4'] = array('value'=>$owner['name'],'color'=>'#173177');
		$data['keyword5'] = array('value'=>$owner['tel'],'color'=>'#173177');
		$data['remark'] = array('value'=>'请及时联系对方，感谢您的使用！','color'=>'#173177');
		$url = $_W['siteroot'] . ltrim($this->createMobileurl('info',array('id'=>$member['pin_id'])),'./');
		$acidarr = uni_accounts();//获取当前主公众号下的所有子公众号
		reset($acidarr);//重置数组指针
		$account = current($acidarr);//获取第一个字公众号
		$acid = $account['acid'];
		$acc = WeAccount::create($acid);//实例化消息类对象
		$acc->sendTplNotice($_W['openid'],$message['join_result'],$data,$url,'#FF683F');
		$data_owner['first'] = array('value'=>'亲，有新的人加入你的拼车订单哦！','color'=>'#173177');
		$data_owner['keyword1'] = array('value'=>date('Y-m-d H:i:s',$travel['release_time']),'color'=>'#173177');
		$data_owner['keyword2'] = array('value'=>$travel['boarding_place'],'color'=>'#173177');
		$data_owner['keyword3'] = array('value'=>$travel['name'],'color'=>'#173177');
		$data_owner['keyword4'] = array('value'=>$travel['mobile'],'color'=>'#173177');
		$data_owner['remark'] = array('value'=>'请及时联系乘客，感谢您的使用！','color'=>'#173177');
		$res = $acc->sendTplNotice($owner['openid'],$message['join_notice'],$data_owner,$url,'#FF683F');
		if($message['sms_on'] == '1') {
			$this->join_result_sms($travel['mobile'],$travel['name'],$owner['tel']);
			$this->join_notice_sms($owner['tel'],$owner['name'],$travel['mobile']);
		}
	}
}