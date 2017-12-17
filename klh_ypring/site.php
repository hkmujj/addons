<?php
/**
 * 预约打印模块微站定义
 *
 * @author dxf
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Klh_ypringModuleSite extends WeModuleSite {
	public function doMobileIndex() {
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if($operation == 'display'){
			
			include $this->template('index');
			die;
		}elseif($operation == 'doindex'){
			//判断验证码是否正确
            if($_SESSION['code']!=trim($_GPC['code'])){
                message(error(4,'验证码不正确'),'','ajax');
            }
            
            $data = array(
                        'weid' => $_W['uniacid'],
                        'openid' => $_W['openid'],
                        'wxname' => $_W['fans']['nickname'],
                        'headimgurl' => $_W['fans']['headimgurl'],
                        'uname' => $_GPC['uname'],
                        'phone' => trim($_GPC['phone']),
                        'company' => trim($_GPC['company']),
                        'location' => $_GPC['location'],
                        'address' => trim($_GPC['address']),
                        'ctime' =>time() 
            );
            if(substr_count($_GPC['location'],',')==1){
            	$data['location']=$_GPC['location'].",";
            }
            if(empty($_GPC['uname']) || empty($_GPC['phone']) || empty($_GPC['company'])){
            	message(error(2,'必填信息不能为空'),'','ajax');
            }

            $ress=pdo_insert('klh_ypring', $data);
           
            if($ress){
            	 /***********/
	            $tt = pdo_fetch("SELECT * FROM " . tablename('klh_setting') . " WHERE weid = :weid order by id desc", array(':weid' => $_W['uniacid']));
		        $title=urlencode($tt['title']);
		        $stitle=urlencode($tt['stitle']);
		        $sn=$tt['sn'].date('YmdHis',time()).rand(1,9);
		        $time=urlencode($tt['time']);
		        $url="http://210.14.159.92:9066/api_Review.ashx?Action=add&title=$title&title_1=$stitle&serial_number=$sn&startDate=$time";
		        $response = ihttp_get($url);
            	message(error(0,''),'','ajax');
            }else{
            	message(error(3,'信息提交失败'),'','ajax');
            }
			die;
		}elseif($operation == 'complete'){
            include $this->template('index');
        }
		
		
	}

	public function doMobileSms() {
		
		$mobile=17521015201;
        $code=random(4,1);
        $_SESSION['code']=$code;
        //$res=$this->sendsms($mobile,$code);
        if($res){
            message(error(0,''),'','ajax');
        }else{
            message(error(1,$code),'','ajax');
        }

	}
	/**发送短信信息*****畅卓验证码*********开始*************/

     public function sendsms($mobile,$sms) {
        //{"taskId":"170619152230429091","overage":9918,"mobileCount":1,"status":0,"desc":"发送成功"}
        $name='13788979719';
        $psw='7802049BA7023AB8DCE849F465929A80';
        $target = "http://api.chanzor.com/send?";
        $post_data = "account={$name}&password={$psw}&mobile=".$mobile."&content=".rawurlencode("您的验证码是：".$sms."请不要把验证码泄露给其他人。【幻装网】");
        ;
        $ress=json_decode(Post($post_data, $target));
        $status=$ress->status;
        if($status==0){
            return true;
        }else{
            return false;
        }
    }
    
    public function xml_to_array($xml){
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches)){
        $count = count($matches[0]);
        for($i = 0; $i < $count; $i++){
        $subxml= $matches[2][$i];
        $key = $matches[1][$i];
            if(preg_match( $reg, $subxml )){
                $arr[$key] = xml_to_array( $subxml );
            }else{
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
    }
    public function Post($curlPost,$url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }
    //随机数
    public function random($length = 6 , $numeric = 0) {
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        if($numeric) {
            $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
            $max = strlen($chars) - 1;
            for($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }

/**发送短信信息**************end*************/
	public function doWebYupring() {
		global $_W,$_GPC;
        load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if($operation == 'display'){
            $pindex = max(1, intval($_GPC['page']));
            $psize = 30;
            $condition = ' WHERE `weid` = :weid';
            $params = array(':weid' => $_W['uniacid']);

            if (!empty($_GPC['uname'])) {
                $condition .= ' AND  `uname` LIKE :uname';
                $params[':uname'] = '%' . trim($_GPC['uname']) . '%';
            }
             if (!empty($_GPC['status'])) {
                $condition .= ' AND  `status` = :status';
                $params[':status'] =$_GPC['status'];
            }
            if (!empty($_GPC['phone'])) {
                $condition .= ' AND  `phone` = :phone';
                $params[':phone'] =$_GPC['phone'];
            }
            if (empty($_GPC['time'])) {
                $starttime=time()-(3600*24*7);
                $endtime=time();
            }else{
                $starttime=strtotime($_GPC['time']['start']);
                $endtime=strtotime($_GPC['time']['end']);
                $condition .=" AND `ctime` > $starttime  AND `ctime`< $endtime ";
            }

            $sql = "SELECT count(*) FROM " .  tablename('klh_ypring'). $condition;
            $total = pdo_fetchcolumn($sql, $params);
            $pager = pagination($total, $pindex, $psize);
            $list = pdo_fetchall("SELECT * FROM " . tablename('klh_ypring'). $condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
            include $this->template('yupring');
        }elseif($operation == 'down'){
        	$condition = ' WHERE `weid` = :weid';
            $params = array(':weid' => $_W['uniacid']);

        	$starttime=$_GPC['start'];
            $endtime=$_GPC['end'];
            $condition .=" AND `ctime` > $starttime  AND `ctime`< $endtime ";
			$list = pdo_fetchall("SELECT * FROM " . tablename('klh_ypring'). $condition, $params);
			$html = "\xEF\xBB\xBF";

			$tableheader[]='姓名';
			$tableheader[]='电话';
			$tableheader[]='公司';
			$tableheader[]='省';
			$tableheader[]='市';
			$tableheader[]='区';
			$tableheader[]='详细地址';
			$tableheader[]='报名时间';
			foreach ($tableheader as $value) {
			    $html .= $value . "\t ,";
		    }
		    $html .= "\n";
			foreach ($list as $k => $v) {
				unset($v['id']);
				unset($v['weid']);
				unset($v['openid']);
				unset($v['wxname']);
				unset($v['headimgurl']);
				unset($v['status']);
				$v['ctime']=date('Y-m-d H:i:s',$v['ctime']);
				foreach ($v as $ko => $vo) {
					$html.=$vo;
					$html .= "\t ,";
				}
				$html .= "\n";
			}
			//var_dump($list);
			header("Content-type:text/csv");
			header("Content-Disposition:attachment; filename=".time().".csv");
			echo $html;
			exit();
        }
	}
    //状态ajax的修改
    public function doWebSetStatus() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $data = intval($_GPC['data']);
            $data = ($data==1?'2':'1');
            $ress=pdo_update("klh_ypring", array('status' => $data), array("id" => $id, "weid" => $_W['uniacid']));
            if($ress){
                die(json_encode(array("result" => 1, "data" => $data)));
            }else{
                die(json_encode(array("result" => 0)));
            }
        
    }
    public function doWebSystem() {

        global $_GPC, $_W;
        load()->func('tpl');
        load()->func('communication');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            
            $pindex = max(1, intval($_GPC['page']));
            $psize = 30;
            $condition = ' WHERE `weid` = :weid ';
            $params = array(':weid' => $_W['uniacid']);
            $sql = "SELECT count(*) FROM " .  tablename('klh_setting'). $condition;
            $total = pdo_fetchcolumn($sql, $params);
            $pager = pagination($total, $pindex, $psize);
            $list = pdo_fetchall("SELECT * FROM " . tablename('klh_setting'). $condition ."  ORDER BY id desc LIMIT ". ($pindex - 1) * $psize . ',' . $psize, $params);
        }elseif($operation == 'post'){
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $list = pdo_fetch("SELECT * FROM " . tablename('klh_setting') . " WHERE id = :id AND weid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
            }
            if (checksubmit('submit')) {
                $data = array(
                        'weid' => $_W['uniacid'],
                        'title' => $_GPC['title'],
                        'stitle' => trim($_GPC['stitle']),
                        'sn' => trim($_GPC['sn']),
                        'time' => trim($_GPC['time']),
                        'ctime' =>time()
                );
                if (!empty($id)) {
                    $ress=pdo_update('klh_setting', $data, array('id' => $id, 'weid' => $_W['uniacid']));
                } else {
                    $ress=pdo_insert('klh_setting', $data);
                }
                if($ress){
                    message('保存成功！', $this->createWebUrl('system', array('op' => 'display')), 'success');
                }else{
                    message('保存失败');
                }
            }

        }elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $ress=pdo_delete('klh_setting', array('id' => $id));
            if($ress){
                message('删除成功！', $this->createWebUrl('system', array('op' => 'display')), 'success');
            }else{
                message('删除失败');
            }
        }
        include $this->template('system');
    }


}