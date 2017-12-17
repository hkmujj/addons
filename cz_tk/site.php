<?php
/**
 * 疯狂拓客模块微站定义
 *
 * @author 13554686571
 * @url
 */
defined('IN_IA') or exit('Access Denied');
define('SET_ACCESS', 'cz_tk_tj');
define('USER_ACCESS', 'cz_tk_user');
define('SM_ACCESS', 'cz_tk_sm');
define('SMB_ACCESS', 'cz_tk_smb');
define('MSG_ACCESS', 'cz_tk_msg');
define('WA_ACCESS', 'cz_tk_wa');
define('DL_ACCESS', 'cz_tk_dl');
define('PASS_ACCESS', 'cz_tk_pass');
define('TEST_ACCESS', 'cz_tk_tset');
define('SMS_ACCESS', 'cz_tk_sms');
define('BI_ACCESS', 'cz_tk_bill');
define('COO_ACCESS', 'cz_tk_cookie');
define('MODULE_URL', '../addons/cz_tk/');
class Cz_tkModuleSite extends WeModuleSite {
    public function __construct($options) {
		global $_W,$_GPC;
        $this->DEBUG = false;
        $this->uuid = '';
        $this->base_uri = 'https://wx.qq.com/cgi-bin/mmwebwx-bin';
        $this->redirect_uri = 'https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxnewloginpage'; //
        $this->uin = '';
        $this->sid = '';
        $this->skey = '';
        $this->pass_ticket = '';
        $this->deviceId = 'e' . substr(md5(uniqid()), 2, 15);
        $this->BaseRequest = [];
        $this->synckey = '';
        $this->SyncKey = [];
        $this->User = [];
        $this->MemberList = [];
        $this->ContactList = []; # 好友
        $this->GroupList = []; # 群
        $this->GroupMemeberList = []; # 群友
        $this->PublicUsersList = []; # 公众号／服务号
        $this->SpecialUsersList = []; # 特殊账号
        $this->autoReplyMode = false;
        $this->syncHost = '';
        $this->user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.36';
        $this->interactive = false;
        $this->autoOpen = false;
        $this->saveFolder = getcwd() . "/saved/";
        $this->saveSubFolders = ['webwxgeticon' => 'icons', 'webwxgetheadimg' => 'headimgs', 'webwxgetmsgimg' => 'msgimgs', 'webwxgetvideo' => 'videos', 'webwxgetvoice' => 'voices', '_showQRCodeImg' => 'qrcodes'];
        $this->appid = 'wx782c26e4c19acffb';
        $this->lang = 'zh_CN';
        $this->lastCheckTs = time();
        $this->memberCount = 0;
        $this->SpecialUsers = ['newsapp', 'fmessage', 'filehelper', 'weibo', 'qqmail', 'fmessage', 'tmessage', 'qmessage', 'qqsync', 'floatbottle', 'lbsapp', 'shakeapp', 'medianote', 'qqfriend', 'readerapp', 'blogapp', 'facebookapp', 'masssendapp', 'meishiapp', 'feedsapp', 'voip', 'blogappweixin', 'weixin', 'brandsessionholder', 'weixinreminder', 'wxid_novlwrv3lqwv11', 'gh_22b87fa7cb3c', 'officialaccounts', 'notification_messages', 'wxid_novlwrv3lqwv11', 'gh_22b87fa7cb3c', 'wxitil', 'userexperience_alarm', 'notification_messages'];
        $this->TimeOut = 20; # 同步最短时间间隔（单位：秒）
        $this->media_count = - 1;
        $this->cookies = MODULE_URL .'cookies/'.rand(9,999999999999).'.cookie';
        $this->js = MODULE_URL .'cookies/'.time().'.xs';
		$this->set = pdo_fetch("SELECT * FROM" . tablename(SET_ACCESS) . "WHERE uniacid='{$_W['uniacid']}'");
		$this->uutt =  pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND id='{$_SESSION['usid']}'");
		$this->dld =  pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND id='{$_SESSION['usid']}'");
		
    }
    public function doWebSetting() {
        global $_W, $_GPC;
		$set = $this->set;
        $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		$uniacid = $_W['uniacid'];
        if ($op == 'display') {
            //$set = pdo_fetchall("SELECT * FROM" . tablename(SET_ACCESS) . " ORDER BY tid ASC");
        } elseif ($op == 'add') {

            $id = $_GPC['id'];
        }
        include $this->template('web/setting');
    }
	public function doWebXsdl(){
	 global $_W, $_GPC;
	 $id = $_GPC['id'];
	 $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	 if ($op == 'display') {
	 $pindex = max(1, intval($_GPC['page']));
     $psize = 10;
	 $user = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND id='{$id}'");
	 $userlist = pdo_fetchall("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND dl='{$id}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
	foreach($userlist as &$item){
		$qs = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE fid={$item['id']}");
		$qsb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE fid={$item['id']}");
		$item['num'] = $qs;
		$item['numb']= $qsb;
	}
	 $total= pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(USER_ACCESS) . " WHERE uniacid = {$_W['uniacid']}  AND dl='{$id}' " );
	 $pager = pagination($total, $pindex, $psize);
	}elseif ($op == 'msg') {
		$user = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND id='{$_GPC['ids']}'");
		$msg = pdo_fetchall("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND fid='{$id}'");
		$usert = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND id='{$id}'");
		foreach($msg as &$item){
		$tns = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE msgid='{$item['id']}'");
		$tnsq = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE msgid={$item['id']}");
		$item['tns'] = $tns;
		$item['tnsq']= $tnsq;
		if ($item['status'] == 0) { //失效状态
                $item['status'] = '不启用';
            } else if ($item['status'] == 1) {
                $item['status'] = '启用';
            } else if($item['status'] == 2){
				$item['status'] = '审核中';
			}else if($item['status'] == 3){
				$item['status'] = '驳回修改';
		}
        if ($item['type'] == 1) {
                $item['type'] = '文本类型';
            }
		if ($item['type'] == 3) {
                $item['type'] = '图片类型';
        }

	}
	}else if($op == 'del'){
	    $ids = $_GPC['ids'];
        $user = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  id='{$id}'");
		$msgs = pdo_fetch("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE  id='{$ids}'");
        if (empty($msgs)) {
            message('删除出错！id不存在', $this->createWebUrl('xsdl', array('op' => 'msg','id' =>$id)), 'error');
        }
        pdo_delete(MSG_ACCESS, array('id' => $ids));
		pdo_delete(SM_ACCESS, array('msgid' => $ids));
		pdo_delete(SMB_ACCESS, array('msgid' => $ids));
        message('删除成功！', $this->createWebUrl('xsdl', array('op' => 'msg','ids' =>$id)), 'success');
	}else if($op == 'deluser'){
		$userid = $_GPC['userid'];
		$users = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  id='{$userid}'");
		if (empty($users)) {
            message('删除出错！id不存在', $this->createWebUrl('xsdl', array('id' =>$id)), 'error');
        }
		pdo_delete(USER_ACCESS, array('id' => $userid));
		pdo_delete(SM_ACCESS, array('nid' => $userid));
		pdo_delete(SMB_ACCESS, array('nid' => $userid));
        message('删除成功！', $this->createWebUrl('xsdl', array('ids' =>$id)), 'success');
	}
	include $this->template('web/user_xsdl');
	}
	public function doWebXs() {
	 global $_W, $_GPC;
	 $id = $_GPC['id'];
	 $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
	if ($op == 'display') {
	 $pindex = max(1, intval($_GPC['page']));
     $psize = 10;
	 $user = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND id='{$id}'");
	 $userlist = pdo_fetchall("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND tj='{$id}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}");
	foreach($userlist as &$item){
		$qs = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE nid={$item['id']}");
		$qsb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE nid={$item['id']}");
		$item['num'] = $qs;
		$item['numb']= $qsb;
	}
	 $total= pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(USER_ACCESS) . " WHERE uniacid = {$_W['uniacid']}  AND tj='{$id}' " );
	 $pager = pagination($total, $pindex, $psize);
	}elseif ($op == 'msg') {
		$user = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND id='{$id}'");
		$msg = pdo_fetchall("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE uniacid='{$_W['uniacid']}' AND fid='{$id}'");	
	foreach($msg as &$item){
		$tns = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE msgid='{$item['id']}'");
		$tnsq = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE msgid={$item['id']}");
		$item['tns'] = $tns;
		$item['tnsq']= $tnsq;
		if ($item['status'] == 0) { //失效状态
                $item['status'] = '不启用';
            } else if ($item['status'] == 1) {
                $item['status'] = '启用';
            } else if($item['status'] == 2){
				$item['status'] = '审核中';
			}else if($item['status'] == 3){
				$item['status'] = '驳回修改';
		}
        if ($item['type'] == 1) {
                $item['type'] = '文本类型';
            }
		if ($item['type'] == 3) {
                $item['type'] = '图片类型';
        }

	}
	}else if($op == 'del'){
	    $ids = $_GPC['ids'];
        $user = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  id='{$id}'");
		$msgs = pdo_fetch("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE  id='{$ids}'");
        if (empty($msgs)) {
            message('删除出错！id不存在', $this->createWebUrl('xs', array('op' => 'msg','id' =>$id)), 'error');
        }
        pdo_delete(MSG_ACCESS, array('id' => $ids));
		pdo_delete(SM_ACCESS, array('msgid' => $ids));
		pdo_delete(SMB_ACCESS, array('msgid' => $ids));
        message('删除成功！', $this->createWebUrl('xs', array('op' => 'msg','id' =>$id)), 'success');
	}else if($op == 'deluser'){
		$userid = $_GPC['userid'];
		$users = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  id='{$userid}'");
		if (empty($users)) {
            message('删除出错！id不存在', $this->createWebUrl('xs', array('id' =>$id)), 'error');
        }
		pdo_delete(USER_ACCESS, array('id' => $userid));
		pdo_delete(SM_ACCESS, array('nid' => $userid));
		pdo_delete(SMB_ACCESS, array('nid' => $userid));
        message('删除成功！', $this->createWebUrl('xs', array('id' =>$id)), 'success');
	}
	include $this->template('web/user_xs');
	}
	public function doWebXscon() {
		global $_W, $_GPC;
		$id = $_GPC['id'];
		$msgid = $_GPC['msgid'];
		 $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($op == 'display') {
		if($id){
			
		}else if($msgid){
		
		}else{
			message('错误,未知id', $this->createWebUrl('Setting'), 'error');
			exit;
		}}elseif ($op == 'msg') {
		
		
		}
		include $this->template('web/user_con');
	}
	  public function doWebSet() {
        global $_W, $_GPC;
		$uniacid=$_W['uniacid'];
		$set = pdo_fetch("SELECT * FROM" . tablename(SET_ACCESS) . "WHERE uniacid='{$_W['uniacid']}'");
		if(checksubmit()) {
            $data = array(
                'uniacid' => $_W['uniacid'],
                'qcbk' => $_GPC['qcbk'],
				'name' => $_GPC['name'],
				'status' => $_GPC['status'],
				'qcsbk' => $_GPC['qcsbk'],
				'wb' =>$_GPC['wb'],
				'tx'=>$_GPC['tx'],
                'xtb' => $_GPC['xtb'],
				'pclogo' => $_GPC['pclogo'],
				'pcname' =>$_GPC['pcname'],
				'pcdl' => $_GPC['pcdl']
			);
			 if(empty($set['id'])){
                pdo_insert(SET_ACCESS,$data);
            }else{
                pdo_update(SET_ACCESS,$data, array('id' => $set['id'],'uniacid' => $_W['uniacid']));
            }
             message('提交成功！',$this->createWebUrl('set'),'success');
        }
        include $this->template('web/set');
    }
	public function doWebStatus(){
	global $_W, $_GPC;
	$uniacid = $_W['uniacid'];
	$msglist = pdo_fetchall("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE uniacid='{$uniacid}' AND status='2'");
	foreach ($msglist as & $time) {
	$user = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id='{$time['fid']}'");
	$time['fid'] = $user['name'];
	 if ($time['type'] == 1) {
       $time['type'] = '文本类型';
       }
		if ($time['type'] == 3) {
       $time['type'] = '图片类型';
     }
	}
	include $this->template('web/status');
	}

	public function doMobileGet() {
		global $_W, $_GPC;
		$uniacid = $_W['uniacid'];
		if ($_GPC['op'] == 'get') {
		$id = $_GPC['id'];
		$msg = pdo_fetch("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE id='{$id}'");
		$data['title'] = $msg['title'];
		if($msg['type'] == 1){
			$data['type'] = '文本类型';
			$data['content'] = $msg['content'];
		}
		if($msg['type'] == 3){
			$data['type'] = '图片类型';
			$data['content'] = $msg['content'];
		}
        exit(json_encode($data));
	}
		if ($_GPC['op'] == 'fk') {
			$id = $_GPC['id'];
			$msg = array('status' => '3');
			pdo_update(MSG_ACCESS, $msg, array('id' => $id));
			$data['id'] = $id;
			exit(json_encode($data));
		}
		if ($_GPC['op'] == 'sh') {
			$id = $_GPC['id'];
			$msg = array('status' => '0');
			pdo_update(MSG_ACCESS, $msg, array('id' => $id));
			$data['id'] = $id;
			exit(json_encode($data));
		}
	}
	public function doWebDl() {
		global $_W, $_GPC;
		$set = $this->set;
        $uniacid = $_W['uniacid'];
        $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($op == 'display') {
            $user = pdo_fetchall("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE uniacid='{$uniacid}' AND gid='10'");
            foreach ($user as & $time){
                if($time['sh'] == '0'){
                    $time['sh'] = '不开启';
                }else if($time['sh'] == '1'){
                    $time['sh'] ='开启';
                }
            }
        }elseif ($op == 'add') {
            $id = $_GPC['id'];
            if (!empty($id)) {
                $user = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE id='{$id}'");
            }
            if (checksubmit('submit')) {
                $users = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE uniacid={$uniacid} AND name='{$_GPC['name']}'");
                if (empty($_GPC['name'])) {
                    message('请输入帐户');
                }
                if (empty($id)) {
                    if (!empty($users['name'])) {
                        message('重复账号，请重试');
                        exit;
                    }
                    $add = array('uniacid' => $uniacid, 
						'gid' => '10', 
						'name' => $_GPC['name'], 
						'timeend' => strtotime($_GPC['timeend']), 
						'psw' => $_GPC['psw'], 
						'gsname' => $_GPC['gsname'],
						'moblie' => $_GPC['moblie'],
						'tj' => $_GPC['tj'],
						'status' => $_GPC['status'],
						'sm' => $_GPC['sm'],
						'point' => $_GPC['point'],
						'ntime' => $_GPC['ntime'],
						'xs' => $_GPC['xs'],
						'wb' => $_GPC['wb'],
						'sh' => $_GPC['sh'],
					);
                    pdo_insert(DL_ACCESS, $add);
                } else {
                    $add = array('uniacid' => $uniacid, 
						'gid' => '10', 
						'name' => $_GPC['name'], 
						'timeend' => strtotime($_GPC['timeend']), 
						'psw' => $_GPC['psw'], 
						'gsname' => $_GPC['gsname'],
						'moblie' => $_GPC['moblie'],
						'tj' => $_GPC['tj'],
						'status' => $_GPC['status'],
						'sm' => $_GPC['sm'],
						'point' => $_GPC['point'],
						'ntime' => $_GPC['ntime'],
						'xs' => $_GPC['xs'],
						'wb' => $_GPC['wb'],
						'sh' => $_GPC['sh'],
						);
                    pdo_update(DL_ACCESS, $add, array('id' => $id));
                }
                message('操作成功！', $this->createWebUrl('dl', array('op' => 'display')), 'success');
            }
        } elseif ($op == 'del') {
            $id = $_GPC['id'];
            $user = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE  id='{$id}'");
            if (empty($user)) {
                message('删除出错！id不存在', $this->createWebUrl('dl', array('op' => 'display')), 'error');
            }
            pdo_delete(DL_ACCESS, array('id' => $id));
			//pdo_delete(USER_ACCESS, array('tj' => $id));
            message('删除成功！', $this->createWebUrl('dl', array('op' => 'display')), 'success');
        }
	include $this->template('web/dl');
	}
	public function doWebTrial() {
	global $_W, $_GPC;
	$uniacid = $_W['uniacid'];
	$id = $_GPC['id'];
	$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
       if ($op == 'display') {
          $unu = pdo_fetchall("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid='{$uniacid}' AND bg='99' ");
        }else if ($op == 'set'){
		$id = $_GPC['id'];
		$test = pdo_fetch("SELECT * FROM" . tablename(TEST_ACCESS) . "WHERE uniacid='{$uniacid}'");
		if (checksubmit('submit')) {
			if (empty($id)) {
			$data = array(
				'uniacid'=>$_W['uniacid'],
				'status'=>$_GPC['status'],
				'point' => $_GPC['point'],
                'xs' => $_GPC['xs'],
			);
			 pdo_insert(TEST_ACCESS, $data);
			}else{
			$datas = array(
				'uniacid'=>$_W['uniacid'],
				'status'=>$_GPC['status'],
				'point' => $_GPC['point'],
                'xs' => $_GPC['xs'],
			);
			 pdo_update(TEST_ACCESS, $datas, array('id' => $id));
			}
		  message('操作成功！', $this->createWebUrl('trial',array('op' => 'set')), 'success');
		}
		}else if($op == 'del'){
		    $id = $_GPC['id'];
            $user = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  id='{$id}'");
            if (empty($user)) {
                message('删除出错！id不存在', $this->createWebUrl('Trial', array('op' => 'display')), 'error');
            }
            pdo_delete(USER_ACCESS, array('id' => $id));
			pdo_delete(USER_ACCESS, array('tj' => $id));
            message('删除成功！', $this->createWebUrl('Trial', array('op' => 'display')), 'success');
		}

	include $this->template('web/trial');
	}
	public function doWebPass() {
		global $_W, $_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];
		if (checksubmit('submit')) {
			if($_GPC['tnt'] == 1){
				if($_GPC['url'] == ''){
				message('接收地址不能为空', $this->createWebUrl('pass'), 'error');
				}
			}
			if($_GPC['tnt'] == 2){
				if($_GPC['urls'] == ''){
				message('接收地址不能为空', $this->createWebUrl('pass'), 'error');
				}
			}
			if (empty($id)) {
			$data = array(
				'uniacid'=>$_W['uniacid'],
				'url'=>$_GPC['url'],
				'time' => time(),
				'urls'=>$_GPC['urls'],
				'tnt' => $_GPC['tnt'],
			);
			 pdo_insert(PASS_ACCESS, $data);
			}else{
			$datas = array(
				'uniacid'=>$_W['uniacid'],
				'url'=>$_GPC['url'],
				'time' => time(),
				'urls'=>$_GPC['urls'],
				'tnt' => $_GPC['tnt'],
			);
			 pdo_update(PASS_ACCESS, $datas, array('id' => $id));
			}
		  message('操作成功！', $this->createWebUrl('pass'), 'success');
		}
		$clud = pdo_fetch("SELECT * FROM" . tablename(PASS_ACCESS) . "WHERE uniacid='{$uniacid}'");
		include $this->template('web/pass');
	}
	public function sendmsg($cons,$mobile){
	    global  $_W, $_GPC;
	    $uniacid = $_W['uniacid'];
	    $namesms = $sms['bers'];
	    $code = $sms['code'];
        $sms = pdo_fetch("SELECT * FROM" . tablename(SMS_ACCESS) . "WHERE uniacid='{$uniacid}'");
		$statusStr = array(
				"0" => "短信发送成功",
				"-1" => "参数不全",
				"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
				"30" => "密码错误",
				"40" => "账号不存在",
				"41" => "余额不足",
				"42" => "帐户已过期",
				"43" => "IP地址限制",
				"50" => "内容含有敏感词"
		);
		$smsapi = "http://www.smsbao.com/";
		$user = $sms['ad'];
		$psw = $sms['as'];
		$pass = md5($psw);
		$content=  $cons;
		$phone = $mobile;
		$sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
		$result =file_get_contents($sendurl) ;
		echo $statusStr[$result];
    }
	public function doWebSms(){
	    global  $_W, $_GPC;
	    $uniacid = $_W['uniacid'];
            if (checksubmit('submit')) {
                $data = array(
                    'uniacid' => $uniacid,
                    'time' => time(),
                    'ad' => $_GPC['ad'],
                    'as' => $_GPC['as'],
                    'bers' => $_GPC['bers'],
                    'code' => $_GPC['code'],
                    'status' => $_GPC['status']
                );
                if (!$_GPC['id']) {
                pdo_insert(SMS_ACCESS, $data);
                }else{
                pdo_update(SMS_ACCESS,$data, array('id' => $_GPC['id'],'uniacid' => $_W['uniacid']));
                }
            message('提交成功！',$this->createWebSms(),'success');
        }
        $tu =  pdo_fetch("SELECT * FROM" . tablename(SMS_ACCESS) . "WHERE uniacid='{$uniacid}'");
        include  $this->template('web/sms');
    }
    public function doWebUser() {
        global $_W, $_GPC;
		$set = $this->set;
        $uniacid = $_W['uniacid'];
        $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($op == 'display') {
            $user = pdo_fetchall("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid='{$uniacid}' AND gid='8' AND bg!='99'");
            foreach ($user as& $time){
                if($time['dl'] == 0){
                    $time['dl'] = '主管理员';
                }else{
                $dl = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE uniacid='{$uniacid}' AND id='{$time['dl']}'");
                $time['dl'] = $dl['name'];
                }
            }
        } elseif ($op == 'add') {
            $id = $_GPC['id'];
            if (!empty($id)) {
                $user = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id='{$id}'");
            }
            if (checksubmit('submit')) {
                $users = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid={$uniacid} AND name='{$_GPC['name']}'");
                if (empty($_GPC['name'])) {
                    message('请输入帐户');
                }
                if (empty($id)) {
                    if (!empty($users['name'])) {
                        message('重复账号，请重试');
                        exit;
                    }
                    $add = array('uniacid' => $uniacid, 'gid' => '8', 'name' => $_GPC['name'], 'timeend' => strtotime($_GPC['timeend']), 'psw' => $_GPC['psw'],'mobile' => $_GPC['mobile'],'point' => $_GPC['point'],'ntime' => $_GPC['ntime'],'xs' => $_GPC['xs'],'dl'=>'0');
                    pdo_insert(USER_ACCESS, $add);
                } else {
                    $add = array('uniacid' => $uniacid, 'gid' => '8', 'name' => $_GPC['name'], 'timeend' => strtotime($_GPC['timeend']), 'psw' => $_GPC['psw'],'mobile' => $_GPC['mobile'],'point' => $_GPC['point'],'ntime' => $_GPC['ntime'],'xs' => $_GPC['xs'],'dl'=>'0');
                    pdo_update(USER_ACCESS, $add, array('id' => $id));
                }
                message('操作成功-&#25240;&#12288;&#32764;&#12288;&#22825;&#12288;&#20351;&#12288;&#36164;&#12288;&#28304;&#12288;&#31038;&#12288;&#21306;&#12288;&#25552;&#12288;&#20379;！', $this->createWebUrl('user', array('op' => 'display')), 'success');
            }
        } elseif ($op == 'del') {
            $id = $_GPC['id'];
            $user = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  id='{$id}'");
            if (empty($user)) {
                message('删除出错！id不存在', $this->createWebUrl('user', array('op' => 'display')), 'error');
            }
            pdo_delete(USER_ACCESS, array('id' => $id));
			pdo_delete(USER_ACCESS, array('tj' => $id));
            message('删除成功！', $this->createWebUrl('user', array('op' => 'display')), 'success');
        }
        include $this->template('web/user');
    }
    function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return $t2 . ceil(($t1 * 1000));
    }
    private $appid = 'wx782c26e4c19acffb';
	public function doMobileData(){
	     global $_W, $_GPC;
        $op = $_GPC['op'];
        $uniacid = $_GPC['id'];
		  if ($_GPC['op'] == 'ss') {
            $time = time() - 9;
            $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$uniacid} AND time={$time}");
            $data['num'] = $smb;
            exit(json_encode($data));
        }
		   if ($_GPC['op'] == 'pos') {
            $time = date('Ymd', time());
            $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$uniacid}  AND times={$time}");
            $sm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$uniacid}  AND times={$time}");
            $data['num'] = $smb;
            $data['nums'] = $sm;
            exit(json_encode($data));
        }
        if ($_GPC['op'] == 'poss') {
            $time = date('Ymd', strtotime("-1 day"));
            $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$uniacid}  AND times={$time}");
            $sm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$uniacid}  AND times={$time}");
            $data['num'] = $smb;
            $data['nums'] = $sm;
            exit(json_encode($data));
        }
	}
	public function doMobileDatas(){
            $member = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid='{$_POST['uniacid']}' AND id='{$_POST['fid']}'");
            pdo_update(USER_ACCESS, array('point' => $_POST['pio']), array('id' => $_POST['fid']));
            pdo_insert(BI_ACCESS, array('uniacid' => $_POST['uniacid'],'time'=> time(),'name'=>$_POST['name'],'uid'=>$_POST['nid'],'pio'=>'-1','func'=>$_POST['sname'] . '发送'. $_POST['bname'] ,'czname'=>$member['name'],'tid'=>$_POST['fid'],'ye'=>$_POST['pio']));
            pdo_insert(SMB_ACCESS, array('uniacid'=>$_POST['uniacid'],'time'=>$_POST['time'],'times'=>$_POST['times'],'name'=>$_POST['name'],'nid'=>$_POST['nid'],'fid'=>$_POST['fid'],'sname'=>$_POST['sname'],'bname'=>$_POST['bname'],'msgid'=>$_POST['msgid'],'dl'=>$member['dls']));
            exit;
	}
	public function doMobileDs(){
        $name = $_POST['tts'];
        $id = $_POST['id'];
        if($name){
            pdo_insert(COO_ACCESS, array('name' => $id,'txt'=> $name));
        }else{
            session_start();
            $user = $_SESSION['mid'];
            $ts = pdo_fetch("SELECT * FROM" . tablename(COO_ACCESS) . "WHERE name='{$user}'");
            if($ts['id']){
                pdo_delete(COO_ACCESS, array('name' => $user));
                $ouu['code'] = '182';
                exit(json_encode($ouu));
            }
        }
    }
    public function doMobileUser() {
        global $_W, $_GPC;
		$set = $this->set;
		$uniacid = $_W['uniacid'];
        if ($_W['ispost']) {
            $name = $_GPC['user_name'];
            $psw = $_GPC['password'];
            $member = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid={$uniacid} AND name='{$name}' AND psw='{$psw}'");
            if ($member['status'] == 1) {
                $ouu['status'] = '4';
                exit(json_encode($ouu));
            }
			//判断主
			if($member['tj'] == ''){
			if($member['ntime'] == '1'){
			   if($member['timeend'] < time()){
			        $ouu['status'] = '5';
                    exit(json_encode($ouu));
			   }
			}
			}else if($member['tj']){
				$hu = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id={$member['tj']}");
				if($hu['ntime'] == '1'){
				if($hu['timeend'] < time()){
			        $ouu['status'] = '5';
                    exit(json_encode($ouu));
			   }
			}
			}
            if (!empty($member)) {
				session_start();
				$_SESSION['mid'] = $member['id'];
				$_SESSION['gid'] = $member['gid'];
					if($_SESSION['gid'] == 8) {
                    $ouu['status'] = '0';
                    exit(json_encode($ouu));
                } else if ($_SESSION['gid'] == 6) {
                    $ouu['status'] = '1';
                    exit(json_encode($ouu));
                } else {
                    $ouu['status'] = '3';
                    //$ouu['mid'] =$_SESSION['gid'];
                    exit(json_encode($ouu));
                }
            } else {
                $ouu['status'] = '2';
                exit(json_encode($ouu));
            }
        }
        include $this->template('user');
    }
    public function doMobileIndex() {
        global $_W, $_GPC;
        session_start();
        $mid = $_SESSION['mid'];
		$set = $this->set;
        $op = $_GPC['op'];
        //var_dump($_SESSION['gid']);
        if (empty($mid)) {
            echo "<script>window.location.href = '" . $this->createMobileUrl('user') . "';</script>";
            exit;
        } else {
            session_start();
            $id = $_SESSION['mid'];
            $member = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  id='{$id}'");
        }
        if ($_W['ispost']) {
            $tt = array('bg' => $_GPC['sm']);
            pdo_update(USER_ACCESS, $tt, array('id' => $mid));
            $ouu['msg'] = '1';
            exit(json_encode($ouu));
        }
        if ($_GPC['op'] == 'pos') {
            $time = date('Ymd', time());
            $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND nid={$mid} AND times={$time}");
            $sm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND nid={$mid} AND times={$time}");
            $data['num'] = $smb;
            $data['nums'] = $sm;
            exit(json_encode($data));
        }
        include $this->template('index');
    }
    public function doMobileIndexs() {
        global $_W, $_GPC;
        session_start();
        $op = $_GPC['op'];
        $mid = $_SESSION['mid'];
        $gid = $_SESSION['gid'];
		$set = $this->set;
        $uniacid = $_W['uniacid'];
        if (empty($gid) == 8) {
            message('你没有权限访问！', $this->createMobileUrl('user'), 'error');
            exit;
        } else if (empty($mid)) {
            message('未知错误', $this->createMobileUrl('user'), 'error');
            exit;
        } else {
            $users = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id={$mid} AND uniacid={$uniacid}");
			
        }
        if ($_GPC['op'] == 'pos') {
            $time = date('Ymd', time());
            $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$mid} AND times={$time}");
            $sm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$mid} AND times={$time}");
            $data['num'] = $smb;
            $data['nums'] = $sm;
            exit(json_encode($data));
        }
        if ($_GPC['op'] == 'ss') {
            $time = time() - 9;
            $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$mid} AND time={$time}");
            $data['num'] = $smb;
            exit(json_encode($data));
        }
        if ($_GPC['op'] == 'poss') {
            $time = date('Ymd', strtotime("-1 day"));
            $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$mid} AND times={$time}");
            $sm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$mid} AND times={$time}");
            $data['num'] = $smb;
            $data['nums'] = $sm;
            exit(json_encode($data));
        }
		   if ($_GPC['op'] == 'smadd') {
            $time = time();
			pdo_insert(WA_ACCESS, array('uniacid' => $uniacid, 'tid' => $mid, 'time' => $time, 'titel' => $_GPC['title'], 'con' => $_GPC['content'], 'status' => $_GPC['sta']));
            $data['nums'] = 'ok';
            exit(json_encode($data));
        }
		   if ($_GPC['op'] == 'dy') {
            $dy = pdo_fetch("SELECT * FROM" . tablename(WA_ACCESS) . "WHERE uniacid={$uniacid} AND tid={$mid}");
            if ($dy) {
                $data['titel'] = $dy['titel'];
                $data['con'] = $dy['con'];
                $data['status'] = $dy['status'];
                exit(json_encode($data));
            }
        }
		if($_GPC['op'] == 'up'){
            $dy = array('time' => time(), 'titel' => $_GPC['title'], 'con' => $_GPC['content'], 'status' => $_GPC['sta']);
            pdo_update(WA_ACCESS, $dy, array('tid' => $mid));
            $data['ok'] = 'ok';
            exit(json_encode($data));
		}
		$wa = pdo_fetch("SELECT * FROM" . tablename(WA_ACCESS) . "WHERE tid={$mid} AND uniacid={$uniacid}");	
        include $this->template('indexs');
    }
	public function doMobileLogint(){
		session_destroy();
		 echo "<script>window.location.href = '" . $this->createMobileUrl('user') . "';</script>";
         exit;
	}
	public function doMobileLogints(){
		session_destroy();
		 echo "<script>window.location.href = '" . $this->createMobileUrl('pclogin') . "';</script>";
         exit;
	}
    public function doMobileQc() {
        global $_W, $_GPC;
        $op = $_GPC['op'];
        session_start();
        $mid = $_SESSION['mid'];
		$uniacid = $_W['uniacid'];
		$set = $this->set;
        if (empty($mid)) {
            echo "<script>window.location.href = '" . $this->createMobileUrl('user') . "';</script>";
            exit;
        }
        if ($_GPC['op'] == 'poss') {
            $member = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  id='{$id}'");
            $qcs = $this->get_uuid();
            $qc = 'http://login.weixin.qq.com/qrcode/' . $qcs . '';
            $ou['url'] = $qc;
            $ou['tab'] = $qcs;
            exit(json_encode($ou));
        }
        if ($_GPC['op'] == 'pos') {
            $qcss = $_GPC['id'];
            $uh = $this->doMobilePost($qcss);
            exit(json_encode($uh));
        }
        $pass = pdo_fetch("SELECT * FROM" . tablename(PASS_ACCESS) . "WHERE  uniacid='{$uniacid}'");
		$user = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  id='{$mid}'");
		$title = pdo_fetch("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE uniacid={$uniacid} AND fid={$user['tj']} AND status=1");
		$wa = pdo_fetch("SELECT * FROM" . tablename(WA_ACCESS) . "WHERE uniacid={$uniacid} AND tid={$user['tj']}");
        include $this->template('qc');
    }
    public function doMobileUser_dy() {
        global $_W, $_GPC;
        session_start();
        $mid = $_SESSION['mid'];
        $gid = $_SESSION['gid'];
        $op = $_GPC['op'];
		$set = $this->set;
        $uniacid = $_W['uniacid'];
        if (empty($gid) == 8) {
            message('你没有权限访问！', $this->createMobileUrl('user'), 'error');
            exit;
        } else if (empty($mid)) {
            message('未知错误', $this->createMobileUrl('user'), 'error');
            exit;
        } else {
            $userlist = pdo_fetchall("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid={$uniacid} AND tj={$mid}");
			$st = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid={$uniacid} AND id={$mid}");
			$sts = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(USER_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND tj={$mid}");
			$nq = $st['xs'] - $sts;
            foreach ($userlist as & $time) {
                if ($time['status'] == 0) { //失效状态
                    $time['status'] = '正常';
                } else if ($time['status'] == 1) {
                    $time['status'] = '失效';
                }
            }
            if ($_GPC['op'] == 'add') {
                $sm = $_GPC['sm']; //开启邦定实名 0no 1yes
                $users = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE name='{$_GPC['name']}'");
				if($sts == $st['xs']){
					 $ouu['status'] = '5';
                     exit(json_encode($ouu));
				}
                if (empty($id)) {
                    if (!empty($users['name'])) {
                        $ouu['status'] = '3';
                        exit(json_encode($ouu));
                    }
                }
                pdo_insert(USER_ACCESS, array('uniacid' => $uniacid, 'name' => $_GPC['name'], 'psw' => $_GPC['psw'], 'gid' => '6', 'tj' => $mid, 'sm' => $sm));
                $ouu['status'] = '1';
                exit(json_encode($ouu));
            }
			if ($_GPC['op'] == 'ts') {
				$ouu['sl'] = $nq;
                exit(json_encode($ouu));
			}
            if ($_GPC['op'] == 'addpl') {
                $ui = $this->geti();
                $sm = $_GPC['sm'];
                pdo_insert(USER_ACCESS, array('uniacid' => $uniacid, 'name' => $ui, 'psw' => '0000', 'gid' => '6', 'tj' => $mid, 'sm' => $sm));
                $ouu['status'] = '1';
                exit(json_encode($ouu));
            }
            if ($_GPC['op'] == 'del') {
                pdo_delete(USER_ACCESS, array('id' => $_GPC['id']));
                $ouu['status'] = '1';
                exit(json_encode($ouu));
            }
            if ($_GPC['op'] == 'dy') {
                $tt = array('status' => $_GPC['status']);
                pdo_update(USER_ACCESS, $tt, array('id' => $_GPC['id']));
                $ouu['status'] = $_GPC['status'];
                exit(json_encode($ouu));
            }
        }
        include $this->template('user_dy');
    }
    public function geti() {
        $iss = rand(1, 99999999);
        return $iss;
    }
    public function doMobileCons() {
        global $_W, $_GPC;
        session_start();
        $mid = $_SESSION['mid'];
        $gid = $_SESSION['gid'];
        $uniacid = $_W['uniacid'];
        $op = $_GPC['op'];
		$set = $this->set;
        $uniacid = $_W['uniacid'];
		 if (empty($gid) == 8) {
            message('你没有权限访问！', $this->createMobileUrl('user'), 'error');
            exit;
        } else if (empty($mid)) {
            message('未知错误', $this->createMobileUrl('user'), 'error');
            exit;
        }
        $msglist = pdo_fetchall("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE uniacid={$uniacid} AND fid={$mid}");
        foreach ($msglist as & $time) {
            if ($time['status'] == 0) { //失效状态
                $time['status'] = '不启用';
            } else if ($time['status'] == 1) {
                $time['status'] = '启用';
            } else if($time['status'] == 2){
				$time['status'] = '审核中';
			}else if($time['status'] == 3){
				$time['status'] = '驳回修改';
			}
            if ($time['type'] == 1) {
                $time['type'] = '文本类型';
            }
			if ($time['type'] == 3) {
                $time['type'] = '图片类型';
            }
        }
        if ($_GPC['op'] == 'st') {
            $type = $_GPC['types'];
			if($set['status'] == 1){$status = 2;}else{$status = $_GPC['sta'];}
			if($type == 1){$con = $_GPC['content'];}
			if($type == 3){$con = $_GPC['content'];}
                pdo_insert(MSG_ACCESS, array('uniacid' => $uniacid, 'type' => $type, 'time' => time(), 'title' => $_GPC['title'], 'content' => $con, 'status' => $status, 'fid' => $mid));
                $ouu['status'] = '1';
                exit(json_encode($ouu));
			
        }
        if ($_GPC['op'] == 'del') {
            pdo_delete(MSG_ACCESS, array('id' => $_GPC['id']));
            $ouu['status'] = '1';
            exit(json_encode($ouu));
        }
        if ($_GPC['op'] == 'dy') {
            $id = $_GPC['id'];
            $dy = pdo_fetch("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE uniacid={$uniacid} AND id={$id}");
			if($set['status'] == 1){
			if($dy['status'] == 2){
			   $data['sta'] = 2;
               exit(json_encode($data));
			}
			} if($dy){
                $data['title'] = $dy['title'];
                if($dy['type']==1){$data['content'] = $dy['content'];}
				if($dy['type']==3){$data['content'] = $dy['content'];}
                $data['status'] = $dy['status'];
                exit(json_encode($data));
            }
        }
        if ($_GPC['op'] == 'up') {
			$id =$_GPC['id'];
			$title = $_GPC['title'];
			$status = $_GPC['sta'];
			$type = $_GPC['types'];
			$time = time();
			if($type == 1){$con = $_GPC['content'];}
			if($type == 3){$con = $_GPC['content'];}
			$dyn = pdo_fetch("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE id={$id}");
			if($dyn['status'] == 3){
			$dy = array('time' => $time, 'title' => $title, 'content' => $con, 'status' => '2');
            pdo_update(MSG_ACCESS, $dy, array('id' => $id));
            $data['title'] = $dy['title'];
            $data['content'] = $dy['content'];
            $data['status'] = $dy['status'];
            exit(json_encode($data));
			}
			if($set['status'] == 1){
			if($dyn['content'] == $con){
			$dyt = array('time' => $time,'status' => $_GPC['sta']);
			pdo_update(MSG_ACCESS, $dyt, array('id' => $id));
			$data['status'] = $dyt['status'];
			exit(json_encode($data));
			}else{
			$statust = 2;
			$dys = array('time' => $time, 'title' => $title, 'content' => $con, 'status' => $statust);
            pdo_update(MSG_ACCESS, $dys, array('id' => $id));
            $data['title'] = $dys['title'];
            $data['content'] = $dys['content'];
            $data['status'] = $dys['status'];
            exit(json_encode($data));
			}
			}else{
			$dy = array('time' => $time, 'title' => $title, 'content' => $con, 'status' => $_GPC['sta']);
            pdo_update(MSG_ACCESS, $dy, array('id' => $id));
            $data['title'] = $dy['title'];
            $data['content'] = $dy['content'];
            $data['status'] = $dy['status'];
            exit(json_encode($data));
			}
			}
        include $this->template('cons');
    }
    public function get_uuid() {
        $url = 'https://login.weixin.qq.com/jslogin';
        $url.= '?appid=' . $this->appid;
        $url.= '&fun=new';
        $url.= '&lang=zh_CN';
        $url.= '&_=' . time();
        $content = $this->curlPost($url);
        $content = explode(';', $content);
        $content_uuid = explode('"', $content[1]);
        $uuid = $content_uuid[1];
        return $uuid;
    }
    public function doMobilePost() {
        global $_W, $_GPC;
        $qcs = $_GPC['id'];
        $tip = $_GPC['tip'];
        $icon = 'true';
        $url = 'https://login.weixin.qq.com/cgi-bin/mmwebwx-bin/login?loginicon=' . $icon . '&r=' . ~time() . '&uuid=' . $qcs . '&tip=0&_=' . $this->getMillisecond();
        $content = $this->curlPost($url);
        preg_match('/\d+/', $content, $match);
        $code = $match[0];
        preg_match('/([\'"])([^\'"\.]*?)\1/', $content, $icon);
        $user_icon = $icon[2];
        if ($user_icon) {
            $data = array('code' => $code,);
        } else {
            $data['code'] = $code;
        }
        exit(json_encode($data));
    }
	//独立面板开始
    public function doMobilePclogin(){
        global $_W, $_GPC;
        $set = $this->set;
        $uutt = $this->uutt;
        $uniacid = $_GPC['i'];
        $op = $_GPC['op'];
        $tal = pdo_fetch("SELECT * FROM" . tablename(TEST_ACCESS) . "WHERE uniacid='{$_W['uniacid']}'");
        if ($_W['ispost']) {
            $name = $_GPC['username'];
            $psw = $_GPC['password'];
            $member = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  name='{$name}' AND psw='{$psw}' AND uniacid='{$uniacid}'");
            if($member == ""){
                $member = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE  name='{$name}' AND psw='{$psw}'AND uniacid='{$uniacid}'");
            }
            session_start();
            $_SESSION['usid'] = $member['id'];
            $_SESSION['gsid'] = $member['gid'];
            if($_SESSION['gsid'] == 8){
                if($member['ntime'] == '1'){
                    if($member['timeend'] < time()){
                        $data['status'] = '12';
                        exit(json_encode($data));
                    }
                }else if($member['status'] == '1'){
                    $data['status'] = '13';
                    exit(json_encode($data));
                }
                $data['status'] = '8';
                exit(json_encode($data));
            }else if($_SESSION['gsid'] == 6){
                $data['status'] = '6';
                exit(json_encode($data));
            }else if($_SESSION['gsid'] == 10){
                if($member['ntime'] == '1'){
                    if($member['timeend'] < time()){
                        $data['status'] = '12';
                        exit(json_encode($data));
                    }
                }else if($member['status'] == '0'){
                    $data['status'] = '13';
                    exit(json_encode($data));
                }
                $data['status'] = '10';
                exit(json_encode($data));
            }else{
                $data['status'] = '11';
                exit(json_encode($data));
            }
        }
        if ($_GPC['op'] == 'sy') {
            $sj = rand(999,99999);
            $add = array('uniacid' => $uniacid, 'gid' => '8', 'name' => $sj, 'psw' => $sj,'point' => $tal['point'],'xs' => $tal['xs'],'timeend'=>time(),'bg' => '99');
            $ns = pdo_insert(USER_ACCESS, $add);
            $jr = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE  name='{$sj}' AND uniacid='{$uniacid}'");
            if($jr['id']){
                $_SESSION['usid'] = $jr['id'];
                $_SESSION['gsid'] = $jr['gid'];
                echo "<script>window.location.href = '" . $this->createMobileUrl('pcindex') . "';</script>";
            }
        }
        include $this->template('pc/login');
    }
    public function doMobilePcindex(){
        global $_W, $_GPC;
        $set = $this->set;
        $uutt = $this->uutt;
        $dl = $this->dld;
        $pcindex = 0;
        $op = $_GPC['op'];
        $uniacid = $_W['uniacid'];
        session_start();
        $gid = $_SESSION['gsid'];
        $id = $_SESSION['usid'];
        if($gid == 8){
            $ns = pdo_fetchcolumn("SELECT COUNT(*)  FROM " . tablename(SMB_ACCESS) . " WHERE  fid='{$id}'");
            $member = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid='{$uniacid}' AND id='{$id}'");
            $userumn = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(USER_ACCESS) . " WHERE uniacid='{$uniacid}' AND tj='{$id}'");
            $consumn = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(MSG_ACCESS) . " WHERE uniacid='{$uniacid}' AND fid='{$id}'");
            if($_W['ispost']){
                $time = time() - 9;
                $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$id} AND time={$time}");
                $data['num'] = $smb;
                exit(json_encode($data));
            }
            if ($_GPC['op'] == 'pos') {
                $time = date('Ymd', time());
                $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$id} AND times={$time}");
                $sm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$id} AND times={$time}");
                $data['num'] = $smb;
                $data['nums'] = $sm;
                exit(json_encode($data));
            }
            if($_GPC['op'] == 'bg'){
                $name = $_GPC['name'];
                if($name == 'jrbg'){
                    $time = date('Ymd', time());
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$id} AND times={$time}");
                    $data['num'] = $smb;
                    exit(json_encode($data));
                }else if($name == 'zrbg'){
                    $time = date('Ymd', strtotime("-1 day"));
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$id} AND times={$time}");
                    $data['num'] = $smb;
                    exit(json_encode($data));
                }else if($name == 'bgzs'){
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$id}");
                    $data['num'] = $smb;
                    exit(json_encode($data));
                }
            }
            if($_GPC['op'] == 'sm'){
                $name = $_GPC['name'];
                if($name == 'jrsm'){
                    $time = date('Ymd', time());
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$id} AND times={$time}");
                    $data['nums'] = $smb;
                    exit(json_encode($data));
                }else if($name == 'zrsm'){
                    $time = date('Ymd', strtotime("-1 day"));
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$id} AND times={$time}");
                    $data['nums'] = $smb;
                    exit(json_encode($data));
                }else if($name == 'smzs'){
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND fid={$id}");
                    $data['nums'] = $smb;
                    exit(json_encode($data));
                }
            }
            if($_GPC['op'] == 'smg'){
                $mid = $_GPC['id'];
                $userlist = pdo_fetchall("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid={$uniacid} AND tj='{$id}'");
                foreach($userlist as &$item){
                    $msgsm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . "WHERE nid={$item['id']} AND msgid={$mid}");
                    $msgsmb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . "WHERE nid={$item['id']} AND msgid={$mid}");
                    $item['sm'] = $msgsm;
                    $item['smb'] = $msgsmb;
                }
                echo json_encode($userlist);
                exit;
            }
            $msglist = pdo_fetchall("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE uniacid={$uniacid} AND fid={$id}");
        }
        if($gid == 10){
            if($_W['ispost']){
                $time = time() - 9;
                $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND dl={$id} AND time={$time}");
                $data['num'] = $smb;
                exit(json_encode($data));
            }
            $userumn = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(USER_ACCESS) . " WHERE uniacid='{$uniacid}' AND dl='{$id}'");
            $msglist = pdo_fetchall("SELECT * FROM " . tablename(USER_ACCESS) . " WHERE uniacid='{$uniacid}' AND dl='{$id}'");
            $is = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(USER_ACCESS) . " WHERE uniacid='{$uniacid}' AND dls='{$id}'");
            $ns = pdo_fetchcolumn("SELECT COUNT(*)  FROM " . tablename(SMB_ACCESS) . " WHERE  dl='{$id}'");
            $member = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE uniacid='{$uniacid}' AND id='{$id}'");
            if($_GPC['op'] == 'smg'){
                $mid = $_GPC['id'];
                $userlist = pdo_fetchall("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE uniacid={$uniacid} AND fid='{$mid}'");
                foreach($userlist as &$item){
                    $msgsm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . "WHERE msgid={$item['id']} AND fid={$mid}");
                    $msgsmb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . "WHERE msgid={$item['id']} AND fid={$mid}");
                    $item['sm'] = $msgsm;
                    $item['smb'] = $msgsmb;
                }
                echo json_encode($userlist);
                exit;
            }
            if ($_GPC['op'] == 'pos') {
                $time = date('Ymd', time());
                $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND dl={$id} AND times={$time}");
                $sm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND dl={$id} AND times={$time}");
                $data['num'] = $smb;
                $data['nums'] = $sm;
                exit(json_encode($data));
            }
            if($_GPC['op'] == 'bg'){
                $name = $_GPC['name'];
                if($name == 'jrbg'){
                    $time = date('Ymd', time());
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND dl={$id} AND times={$time}");
                    $data['num'] = $smb;
                    exit(json_encode($data));
                }else if($name == 'zrbg'){
                    $time = date('Ymd', strtotime("-1 day"));
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND dl={$id} AND times={$time}");
                    $data['num'] = $smb;
                    exit(json_encode($data));
                }else if($name == 'bgzs'){
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND dl={$id}");
                    $data['num'] = $smb;
                    exit(json_encode($data));
                }
            }
            if($_GPC['op'] == 'sm'){
                $name = $_GPC['name'];
                if($name == 'jrsm'){
                    $time = date('Ymd', time());
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND dl={$id} AND times={$time}");
                    $data['nums'] = $smb;
                    exit(json_encode($data));
                }else if($name == 'zrsm'){
                    $time = date('Ymd', strtotime("-1 day"));
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND dl={$id} AND times={$time}");
                    $data['nums'] = $smb;
                    exit(json_encode($data));
                }else if($name == 'smzs'){
                    $smb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . " WHERE uniacid = {$_W['uniacid']} AND dl={$id}");
                    $data['nums'] = $smb;
                    exit(json_encode($data));
                }
            }
        }

        include $this->template('pc/index');
    }
    public function doMobilePcuser(){
        global $_W, $_GPC;
        $set = $this->set;
        $uutt = $this->uutt;
        $dl = $this->dld;
        $pcuser = 0;
        $uniacid = $_W['uniacid'];
        $op = $_GPC['op'];
        session_start();
        $gid = $_SESSION['gsid'];
        $id = $_SESSION['usid'];
        if($gid == '8'){
            $userlist = pdo_fetchall("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid={$uniacid} AND tj='{$id}'");
            foreach($userlist as &$item){
                $msgsm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . "WHERE nid={$item['id']} ");
                $msgsmb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . "WHERE nid={$item['id']} ");
                $item['sm'] = $msgsm;
                $item['smb'] = $msgsmb;
                if($item['status'] == 0){
                    $item['status'] = '已启用';
                }else if($item['status'] == 1){
                    $item['status'] = '已停用';
                }
            }
            if($_GPC['op'] =='sx'){
                $tt = array('status' => '1');
                pdo_update(USER_ACCESS, $tt, array('id' => $_GPC['id']));
                $ouu['status'] = '1';
                exit(json_encode($ouu));
            }
            if($_GPC['op'] =='qy'){
                $tt = array('status' => '0');
                pdo_update(USER_ACCESS, $tt, array('id' => $_GPC['id']));
                $ouu['status'] = '0';
                exit(json_encode($ouu));
            }
            if($_GPC['op'] =='add'){
                $usert = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id='{$id}'");
                $usu = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(USER_ACCESS) . "WHERE tj={$id} ");
                if ($usu >= $usert['xs']){
                    $data['status'] = '0';
                    exit(json_encode($data));
                }
                $name = $_GPC['name'];
                $users = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE name='{$name}'");
                if(!empty($users['id'])){
                    $ouu['status'] = '3';
                    exit(json_encode($ouu));
                }
                pdo_insert(USER_ACCESS, array('uniacid' => $uniacid, 'name' => $_GPC['name'], 'psw' => $_GPC['paw'], 'gid' => '6', 'tj' => $id,'dls'=> $usert['dl']));
                $ouu['status'] = '1';
                exit(json_encode($ouu));
            }
            if($_GPC['op'] =='dy'){
                $userdy = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id='{$_GPC['id']}'");
                $data['id'] = $userdy['id'];
                $data['name'] = $userdy['name'];
                $data['psw'] = $userdy['psw'];
                $data['status'] = $userdy['status'];
                exit(json_encode($data));
            }
            if($_GPC['op'] =='up'){
                $uid = $_GPC['id'];
                $tt = array('name'=>$_GPC['name'],'psw'=>$_GPC['psw'],'status'=>$_GPC['status']);
                pdo_update(USER_ACCESS, $tt, array('id' => $uid));
                $ouu['status'] = '0';
                exit(json_encode($ouu));
            }
            if($_GPC['op'] =='del'){
                pdo_delete(USER_ACCESS, array('id' => $_GPC['uid']));
                $ouu['status'] = '1';
                exit(json_encode($ouu));

            }}else if($gid == '10'){
            $dls = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE id='{$id}'");
            $tablist = pdo_fetchall("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid={$uniacid} AND dl='{$id}'");
            foreach($tablist as &$item){
                $msgsm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . "WHERE fid={$item['id']} ");
                $msgsmb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . "WHERE fid={$item['id']}");
                $item['sms'] = $msgsm;
                $item['smsb'] = $msgsmb;
                if($item['ntime'] == 0){
                    $item['ntime'] = '不开启';
                }else if($item['ntime'] == 1){
                    $item['ntime'] = '开启';
                }
                if($item['status'] == 0){
                    $item['status'] = '开启';
                }else if($item['status'] == 1){
                    $item['status'] = '不开启';
                }
            }
            if($_GPC['op'] =='add'){
                if($_GPC['id']){
                    $io = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id='{$_GPC['id']}'");
                    if($io['point'] > $_GPC['pio']){
                        $ic = $io['point'] - $_GPC['pio'] ;
                        $in = $dls['point'] + $ic;
                        pdo_insert(BI_ACCESS, array('uniacid' => $uniacid,'time'=> time(),'name'=>$io['name'],'uid'=>$io['id'],'pio'=>'+'.$ic,'func'=>'账号退还','czname'=>$dl['name'],'tid'=>$id,'ye'=>$in));
                    }else if($io['point'] < $_GPC['pio']){
                        $ik = $_GPC['pio'] - $io['point'];
                        if($dls['point'] < $ik){
                            $ouu['status'] = '2';
                            exit(json_encode($ouu));
                        }else if($dls['point'] > $ik){
                            $in = $dls['point'] - $ik;
                            pdo_insert(BI_ACCESS, array('uniacid' => $uniacid,'time'=> time(),'name'=>$io['name'],'uid'=>$io['id'],'pio'=>'-'.$ik,'func'=>'账号增加点数','czname'=>$dl['name'],'tid'=>$id,'ye'=>$in));
                        }
                    }
                    $data = array('name' => $_GPC['name'],'xs' => $_GPC['xs'], 'psw' =>$_GPC['psw'],'mobile' =>$_GPC['mobile'],'status'=> $_GPC['status'],'ntime'=>$_GPC['ntime'],'timeend'=> strtotime($_GPC['time']),'point'=>$_GPC['pio']);
                    pdo_update(USER_ACCESS, $data, array('id' => $_GPC['id']));
                    pdo_update(DL_ACCESS, array('point' => $in), array('id' => $id));
                }else{
                    $usu = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(USER_ACCESS) . "WHERE dl={$id} ");
                    if ($usu > $dls['xs']){
                        $data['status'] = '0';
                        exit(json_encode($data));
                    }
                    $name = $_GPC['name'];
                    $users = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE name='{$name}'");
                    if(!empty($users['id'])){
                        $ouu['status'] = '3';
                        exit(json_encode($ouu));
                    }
                    if($dls['point'] < $_GPC['pio']){
                        $ouu['status'] = '2';
                        exit(json_encode($ouu));
                    }
                    $dlls = $dls['point'] - $_GPC['pio'];
                    pdo_insert(BI_ACCESS, array('uniacid' => $uniacid,'time'=> time(),'name'=>$_GPC['name'],'pio'=>'-'.$_GPC['pio'],'func'=>'新增账号','czname'=>$dl['name'],'tid'=>$id,'ye'=>$dlls));
                    pdo_update(DL_ACCESS, array('point' => $dlls), array('id' => $id));
                    pdo_insert(USER_ACCESS, array('uniacid' => $uniacid, 'name' => $_GPC['name'], 'psw' =>$_GPC['psw'],'xs' =>$_GPC['xs'],'mobile' =>$_GPC['mobile'],'status'=> $_GPC['status'],'ntime'=>$_GPC['ntime'],'timeend'=> strtotime($_GPC['time']),'point'=>$_GPC['pio'], 'gid' => '8', 'dl' => $id));
                }
                $ouu['status'] = '1';
                exit(json_encode($ouu));
            }

            if($_GPC['op'] =='dy'){
                $userdy = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id='{$_GPC['id']}'");
                $data['id'] = $userdy['id'];
                $data['name'] = $userdy['name'];
                $data['psw'] = $userdy['psw'];
                $data['ntime'] = $userdy['ntime'];
                $data['timeend'] = date('Y-m-d H:i:s',$userdy['timeend']);
                $data['point'] = $userdy['point'];
                $data['mobile'] = $userdy['mobile'];
                $data['status'] = $userdy['status'];
                $data['xs'] = $userdy['xs'];
                exit(json_encode($data));
            }
            if($_GPC['op'] =='del'){
                pdo_delete(USER_ACCESS, array('id' => $_GPC['id']));
                $ouu['status'] = '1';
                exit(json_encode($ouu));

            }
        }
        include $this->template('pc/user');
    }
    public function doMobilePcset(){
        global $_W, $_GPC;
        $set = $this->set;
        $uutt = $this->uutt;
        $dl = $this->dld;
        $pcsets = 0;
        $uniacid = $_W['uniacid'];
        session_start();
        $gid = $_SESSION['gsid'];
        $mid = $_SESSION['usid'];
        $pcset = pdo_fetch("SELECT * FROM" . tablename(WA_ACCESS) . "WHERE uniacid='{$uniacid}' AND tid='{$mid}'");
        if(!empty($pcset['bg']) == ''){
            $pcset['bg']='../addons/cz_tk/img/bg.jpg';
        }
        if($_W['ispost']){
            $id = $_GPC['id'];
            $data = array(
                'uniacid' => $_W['uniacid'],
                'tid' => $mid,
                'time' => time(),
                'titel' => $_GPC['name'],
                'con' => $_GPC['con'],
                'status' => $_GPC['status'],
                'bg' => $_GPC['url'],
                'login' => $_GPC['login'],
                'code' => $_GPC['code'],
                'dt' => $_GPC['dt'],
            );
            if (!empty($id)){
                pdo_update(WA_ACCESS,$data, array('id' => $id,'uniacid' => $_W['uniacid']));
            }else{
                pdo_insert(WA_ACCESS,$data);
            }
            $ouu['status'] = '0';
            exit(json_encode($ouu));
        }

        include $this->template('pc/set');
    }
    public function doMobilePccon(){
        global $_W, $_GPC;
        $set = $this->set;
        $uutt = $this->uutt;
        $dl = $this->dld;
        $pccons= 0;
        $uniacid = $_W['uniacid'];
        session_start();
        $gid = $_SESSION['gsid'];
        $mid = $_SESSION['usid'];
        $op = $_GPC['op'];
        if($gid == '8'){
            $msglist = pdo_fetchall("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE uniacid={$uniacid} AND fid='{$mid}'");
            foreach($msglist as &$item){
                $msgsm = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SM_ACCESS) . "WHERE msgid={$item['id']} ");
                $msgsmb = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename(SMB_ACCESS) . "WHERE msgid={$item['id']} ");
                $item['sm'] = $msgsm;
                $item['smb'] = $msgsmb;
                if($item['type'] == '1'){
                    $item['type'] = '文本类型';
                }else if($item['type'] == '3'){
                    $item['type'] = '图片类型';
                }
                if ($item['status'] == 0) { //失效状态
                    $item['status'] = '不启用';
                } else if ($item['status'] == 1) {
                    $item['status'] = '启用';
                } else if($item['status'] == 2){
                    $item['status'] = '审核中';
                }else if($item['status'] == 3){
                    $item['status'] = '驳回修改';
                }
            }
            if($_GPC['op'] =='add'){
                $userdl = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id='{$mid}'");
                $dln = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE id='{$userdl['dl']}'");
                if($userdl['dl'] > '0'){
                if($dln['sh'] == '1'){
                    $status = '2';
                }
                }else if($userdl['dl'] == '0'){
                    if($set['status'] == '1'){
                        $status = '2';
                    }
                }else{
                    $status = $_GPC['status'];
                }
                if($_GPC['type'] == '1'){
                    $data = array(
                        'uniacid' => $uniacid,
                        'type'=>$_GPC['type'],
                        'time'=> time(),
                        'title' => $_GPC['title'],
                        'content' => $_GPC['content'],
                        'status' =>$status,
                        'fid' => $mid,
                        'xz'=> $_GPC['xz'],
                        'sex' => $_GPC['sex'],
                    );
                }else if($_GPC['type']=='3'){
                    $data = array(
                        'uniacid' => $uniacid,
                        'type'=>$_GPC['type'],
                        'time'=> time(),
                        'title' => $_GPC['title'],
                        'content' => $_GPC['content'],
                        'status' =>$status,
                        'fid' => $mid,
                        'xz'=> $_GPC['xz'],
                        'sex' => $_GPC['sex'],
                    );
                }
                pdo_insert(MSG_ACCESS, $data);
                exit(json_encode($ouu));
            }
            if($_GPC['op'] =='up'){
                $id = $_GPC['id'];
                $msgsh = pdo_fetch("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE id='{$id}'");
                $userdl = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id='{$msgsh['fid']}'");
                $dln = pdo_fetch("SELECT * FROM" . tablename(DL_ACCESS) . "WHERE id='{$userdl['dl']}'");
                if($userdl['dl'] > '0'){
                    if($dln['sh'] == '1'){
                        if($_GPC['content'] == $msgsh['content']){
                            $status = $_GPC['status'];
                        }else{
                         $status = '2';
                        }
                    }else{
                        $status = $_GPC['status'];
                    }
                }else if($userdl['dl'] == '0'){
                    if($set['status'] == '1'){
                        if($_GPC['content'] == $msgsh['content']){
                            $status = $_GPC['status'];
                        }else{
                            $status = '2';
                        }
                    }else{
                        $status = $_GPC['status'];
                    }
                }else{
                    $status = $_GPC['status'];
                }
                $data = array(
                    'time'=> time(),
                    'title' => $_GPC['title'],
                    'content' => $_GPC['content'],
                    'status' =>$status,
                    'fid' => $mid,
                    'xz'=> $_GPC['xz'],
                    'sex' => $_GPC['sex'],
                );
                pdo_update(MSG_ACCESS, $data, array('id' => $id));
                $ouu['status'] = '0';
                exit(json_encode($ouu));
            }

            if($_GPC['op'] =='zt'){
                $status = $_GPC['status'];
                if($status == '0'){
                    $tt = array('status' => '0');
                    pdo_update(MSG_ACCESS, $tt, array('id' => $_GPC['id']));
                    $ouu['status'] = '0';
                    exit(json_encode($ouu));
                }else if($status == '1'){
                    $tt = array('status' => '1');
                    pdo_update(MSG_ACCESS, $tt, array('id' => $_GPC['id']));
                    $ouu['status'] = '1';
                    exit(json_encode($ouu));
                }
            }
            if($_GPC['op'] =='dy'){
                $msgdy = pdo_fetch("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE id='{$_GPC['id']}'");
                $data['id'] = $msgdy['id'];
                $data['type'] = $msgdy['type'];
                $data['title'] = $msgdy['title'];
                $data['content'] = $msgdy['content'];
                $data['status'] = $msgdy['status'];
                $data['xz'] = $msgdy['xz'];
                $data['sex'] = $msgdy['sex'];
                exit(json_encode($data));
            }
            if($_GPC['op'] =='del'){
                pdo_delete(MSG_ACCESS, array('id' => $_GPC['id']));
                $ouu['status'] = '2';
                exit(json_encode($ouu));

            }
        }else if($gid == '10'){
            $stn = pdo_fetchall("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE uniacid={$uniacid} AND dl='{$mid}'");
        }

        include $this->template('pc/cons');
    }
    public function doMobilePcdli(){
        global $_W, $_GPC;
        $set = $this->set;
        $uutt = $this->uutt;
        $dl = $this->dld;
        $pcdli= 0;
        $uniacid = $_W['uniacid'];
        session_start();
        $gid = $_SESSION['gsid'];
        $mid = $_SESSION['usid'];
        $dldi = pdo_fetchall("SELECT * FROM" . tablename(BI_ACCESS) . "WHERE uniacid='{$uniacid}' AND tid='{$mid}' ORDER BY time DESC");
        include $this->template('pc/dli');
    }
    public function doMobilePcdemo(){
        global $_W, $_GPC;
        $set = $this->set;
        $uutt = $this->uutt;
        $dl = $this->dld;
        $pcdemo= 0;
        $uniacid = $_W['uniacid'];
        session_start();
        $gid = $_SESSION['gsid'];
        $mid = $_SESSION['usid'];
        $dldi = pdo_fetchall("SELECT * FROM" . tablename(BI_ACCESS) . "WHERE uniacid='{$uniacid}' AND tid='{$mid}' ORDER BY time DESC");
        include $this->template('pc/demo');
    }
	//end pc
    public function doMobileQcs() {
        global $_W, $_GPC;
		$set = $this->set;
        session_start();
        $mid = $_SESSION['mid'];
        $qcs = $_GPC['id'];
        $uniacid = $_GPC['i'];
        $ul = $this->get_uri($qcs);
        $sel = $this->post_self($ul);
        $userto = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id={$mid}");
        $msgfa = pdo_fetch("SELECT * FROM" . tablename(MSG_ACCESS) . "WHERE fid={$userto['tj']} AND status='1'");
		$urlste = pdo_fetch("SELECT * FROM" . tablename(PASS_ACCESS) . "WHERE uniacid='{$uniacid}'");
        $sms = pdo_fetch("SELECT * FROM" . tablename(SMS_ACCESS) . "WHERE uniacid='{$uniacid}'");
        if ($sel == 1203) {
            echo '1203';
            exit;
        }
        $post_url_header = $ul['post_url_header'];
        $post = $this->wxinit($sel, $post_url_header);
        $json = $this->wxstatusnotify($sel, $post, $post_url_header);
        $webcon = $this->webwxgetcontact($sel, $post_url_header);
        $as = json_decode($webcon, true);
        $seq = $as['Seq'];
        if(!$seq ==''){
            $webcons = $this->webwxgetcontact($sel, $post_url_header,$seq);
            $atts = json_decode($webcons,true);
            $stq = $atts['Seq'];
            if(!$stq == ''){
                $webconss = $this->webwxgetcontact($sel, $post_url_header,$stq);
                $attss = json_decode($webconss,true);
                $as= array_merge_recursive($as,$atts,$attss);
            }else{
                $as = array_merge_recursive($as,$atts);
            }
        }
        $wxuin= '';
        $fp = fopen($this->cookies, 'r');
        while ($line = fgets($fp)) {
            if(strpos($line,'wxuin')!==false){
                $arr=explode("\t", trim($line));
                $wxuin = $arr[0];
                break;
            }
        }
        $uts=substr($wxuin,1);
        $headr = $uts;
        $init = json_decode($post, true);
        $User = $init['User'];
        //远程端post点数2.0+
        $userpio = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id={$userto['tj']}");
        //end
        if($sms['status'] == '1') {
            if (!$userpio['point'] == '') {
                if ($userpio['point'] < '100') {
                    $smstime = date('Y-m-d H:i:s', time());
                    $mobilc = $userpio['mobile'];
                    $smscon = '您好"' . $userpio['name'] . '"你在' . $smstime . '您的点数已经剩余' . $userpio['point'] . ',请及时联系管理员充值!';
                    $send = $this->sendmsg($smscon, $mobilc);
                }
            }
            if ($userpio['ntime'] == '1') {
                if ($userpio['timeend'] < time()) {
                    $smstime = date('Y-m-d H:i:s', $userpio['timeend']);
                    $mobilc = $userpio['mobile'];
                    $smscon = '您好"' . $userpio['name'] . '"你的账号于' . $smstime . '过期！请及时联系管理员续费!';
                    $send = $this->sendmsg($smscon, $mobilc);
                }
            }
        }
        pdo_insert(SM_ACCESS, array('uniacid' => $uniacid, 'time' => time(), 'times' => date('Ymd', time()), 'name' => $userto['name'], 'nid' => $mid, 'fid' => $userto['tj'], 'sname' => $User['NickName'], 'msgid' => $msgfa['id'],'dl' => $userto['dls']));
		if($msgfa['type']== 1){
			if($urlste['tnt'] > '0'){
			$pass = $this->webwxsendmsgs($sel, $User, $post_url_header, $as, $userto, $msgfa, $post ,$set ,$userpio,$headr);
			}else{
			$pass = $this->webwxsendmsg($sel, $User, $post_url_header, $as, $userto, $msgfa, $post,$headr);
			}
            $data['code'] = $pass;
            exit(json_encode($data));
		}else if($msgfa['type']== 3){
		$msgimg = $this->msgimg($sel, $User, $post_url_header, $as, $userto, $msgfa, $post,$headr);
		}
        $data['code'] = 'ok';
        exit(json_encode($data));
    }
    public function webwxsendmsg($post, $User, $post_url_header, $arr, $userto, $msgfa, $sss,$headr) {
		$set = $this->set;
        $url =  'https://'.$headr.'/cgi-bin/mmwebwx-bin/webwxsendmsg?pass_ticket=' . $post->pass_ticket;
        $word = $msgfa['content'].$set['wb'];
        $type = $msgfa['type'];
        $v = 0;
        $s = 0;
        $xtb = $set['xtb'];
        //$qq = fopen($this->js, 'w');
		//for(;;){
        foreach ($arr["MemberList"] as $key => $value) {
			$users = pdo_fetch("SELECT * FROM" . tablename(USER_ACCESS) . "WHERE id={$userto['tj']}");
            if ($value['AttrStatus'] > 200) {
				if (time_nanosleep(0,600000000) === true){
                    if($s == 1100){
                        unlink($cookie);
                        exit;
                    }
                    $s++;
					$words = str_replace(['{username}'],$User['NickName'],$word);
					$wordss = str_replace(['{nackname}'],$value['NickName'],$words);
					$clientMsgId = $this->getMillisecond() * 1000 + rand(1000, 9999);
                    $params = array('BaseRequest' => $post->BaseRequest, 'Msg' => array("Type" => $type, "Content" => $wordss, "FromUserName" => $User['UserName'], "ToUserName" => $value['UserName'], "LocalID" => $clientMsgId, "ClientMsgId" => $clientMsgId), 'Scene' => 0,);
					if($users['point'] == '0'){
					unlink($this->cookies);
					return '18';
					exit;
					}
                    if (!$users['point'] == ''){
                        $tt = $users['point'] - '1';
                    }
					pdo_update(USER_ACCESS, array('point' => $tt), array('id' => $userto['tj']));
					pdo_insert(BI_ACCESS, array('uniacid' => $userto['uniacid'],'time'=> time(),'name'=>$userto['name'],'uid'=>$userto['id'],'pio'=>'-1','func'=>$User['NickName'] . '发送'. $value['NickName'] ,'czname'=>$users['name'],'tid'=>$userto['tj'],'ye'=>$tt));
                    pdo_insert(SMB_ACCESS, array('uniacid' => $userto['uniacid'], 'time' => time(), 'times' => date('Ymd', time()), 'name' => $userto['name'], 'nid' => $userto['id'], 'fid' => $userto['tj'], 'sname' => $User['NickName'], 'bname' => $value['NickName'], 'msgid' => $msgfa['id'],'dl' => $userto['dls']));
                    $data = $this->curlPost($url, $params);
                    $v++;
				}
                if($v == $xtb){
					$rets = $this->syncchecks($post,$sss,$headr);
					$v=0;
                    //$ss = str_replace("\\/", "/",json_encode($rets, JSON_UNESCAPED_UNICODE));
                    //fwrite($qq, $ss);
					if($rets == 1101){
					unlink($this->cookies);
					exit;
					}
				} 
            }
		}
   //}
        //fclose($qq);
        return $set['xtb'];
    }
	public function webwxsendmsgs($sel, $User, $post_url_header, $arr, $userto, $msgfa, $sss, $set, $userpio ,$headr)
    {
        global $_W, $_GPC;
        $urlste = pdo_fetch("SELECT * FROM" . tablename(PASS_ACCESS) . "WHERE uniacid='{$_W['uniacid']}'");
        if ($urlste['tnt'] == '1') {
            $url = $urlste['url'] . '/indexs.php';
        } else if ($urlste['tnt'] == '2') {
            $url = $urlste['urls'] . '/indexs.php';
        }
        $urls = $post_url_header . '/webwxsendmsg?pass_ticket=' . $sel->pass_ticket;
        $data = array(
            'sel' => str_replace("\\/", "/", json_encode($sel->BaseRequest, JSON_UNESCAPED_UNICODE)),
            'user' => str_replace("\\/", "/", json_encode($User, JSON_UNESCAPED_UNICODE)),
            'arr' => str_replace("\\/", "/", json_encode($arr, JSON_UNESCAPED_UNICODE)),
            'msgfa' => str_replace("\\/", "/", json_encode($msgfa, JSON_UNESCAPED_UNICODE)),
            'userto' => str_replace("\\/", "/", json_encode($userto, JSON_UNESCAPED_UNICODE)),
            'userpio' => str_replace("\\/", "/", json_encode($userpio, JSON_UNESCAPED_UNICODE)),
            'sss' => $sss,
            'set' => str_replace("\\/", "/", json_encode($set, JSON_UNESCAPED_UNICODE)),
            'urls' => $urls,
            'wx' => $headr,
            'nturl' => $_W['siteroot'],
            'cookie' => $this->cookies,
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
	    }
		public function msgimg($post, $User, $post_url_header, $arr, $userto, $msgfa, $sss,$headr){
		 $url = 'https://'.$headr.'/cgi-bin/mmwebwx-bin/webwxsendmsgimg?fun=async&f=json&pass_ticket=' . $post->pass_ticket;
		 $clientMsgId = $this->getMillisecond() * 1000 + rand(1000, 9999);
		 $acc= parse_url($msgfa['content']);
		 $image = IA_ROOT .$acc['path'];
		 $type = $msgfa['type'];
         $v = 0;
         $userv = '@1111';
         //$qq = fopen($this->js, 'w');
		//for(;;){
		 foreach ($arr["MemberList"] as $key => $value) {
		   if ($value['AttrStatus'] > 200) {
			if (time_nanosleep(3,000000000) === true){
			//if ($v < 140) {
			$response = $this->upimgs($post ,$User, $value, $image, $headr);
			$media_id = "";
			$media_id = $response['MediaId'];
			$datas = array('BaseRequest' => $post->BaseRequest, 'Msg' => array("Type" => $type, 
			 "MediaId"=> $media_id,
			 "FromUserName" => $User['UserName'], 
			 "ToUserName" => $value['UserName'], 
			 "LocalID" => $clientMsgId, 
			 "ClientMsgId" => $clientMsgId), 'Scene' => 0,);
			 pdo_insert(SMB_ACCESS, array('uniacid' => $userto['uniacid'], 'time' => time(), 'times' => date('Ymd', time()), 'name' => $userto['name'], 'nid' => $userto['id'], 'fid' => $userto['tj'], 'sname' => $User['NickName'], 'bname' => $value['NickName'], 'msgid' => $msgfa['id']));
			$data = $this->curlPost($url, $datas);
			//$ss = str_replace("\\/", "/",json_encode($response, JSON_UNESCAPED_UNICODE));
            //fwrite($qq, $ss);
            $rets = $this->syncchecks($post,$sss,$headr);
			$attrs = json_decode($data,true);
			$das = $attrs['BaseResponse']['Ret'];
			if($das == 1205){
				unlink($this->cookies);
				exit;
			}else if( $das == 1101){
				unlink($this->cookies);
				exit;
			}
			// if($v > 10){
			// unlink($this->cookies);
			// exit;
			// }
           if($rets == 1101){
			unlink($this->cookies);
			exit;
			}
			$v++;
				}
			} 
		 }
	//}
            //fclose($qq);
    unlink($this->cookies);
		 return $das;
	}
	public function upimgs($post,$User,$value,$image,$headr){
	$url = 'https://file.'. $headr.'/cgi-bin/mmwebwx-bin/webwxuploadmedia?f=json';
	$image_name = $image;
	//list($mime, $mediaType) = $this->type($image_name);
	$fTime = filemtime($image_name);
	$lastModifieDate = gmdate('D M d Y H:i:s TO',$fTime ).' (CST)';//'Thu Mar 17 2016 00:55:10 GMT+0800 (CST)';
	$file_size = filesize($image_name);
	$pass_ticket = $post->pass_ticket;
	$client_media_id = (time() * 1000).mt_rand(10000,99999);
	$webwx_data_ticket = '';
		$fp = fopen($this->cookies, 'r');
		        while ($line = fgets($fp)) {
            # code...
            if(strpos($line,'webwx_data_ticket')!==false){
                $arr=explode("\t", trim($line));
                //var_dump($arr);
                $webwx_data_ticket = $arr[6];
                break;
            }
        }
		 $uploadmediarequest = json_encode([
            "BaseRequest"=> $post->BaseRequest,
            "ClientMediaId"=> $client_media_id,
            "TotalLen"=> $file_size,
            "StartPos"=> 0,
            "DataLen"=> $file_size,
            "MediaType"=> 4,
            "UploadType"=>2,
            "FromUserName"=>$User["UserName"],
            "ToUserName"=>$value["UserName"],
            "FileMd5"=>md5_file($image_name)
        ]);
        $multipart_encoder = [
            'id'=> 'WU_FILE_0',
            'name'=> $image_name,
            'type'=> 'images/jpeg',
            'lastModifieDate'=> $lastModifieDate,
            'size'=> $file_size,
            'mediatype'=> 'pic',
            'uploadmediarequest'=> $uploadmediarequest,
            'webwx_data_ticket'=> $webwx_data_ticket,
            'pass_ticket'=> $pass_ticket,
            'filename'=> '@'.$image_name
        ];
		$rel= json_decode($this->_post($url,$multipart_encoder,false,true),true);
        return $rel;
	}
    	public function syncchecks($post, $synckey,$headr){
			$init = json_decode($synckey, true);
			$hs = $init['SyncKey']['List'];
				foreach ($hs as $key => $value) {
                if ($key == 1)
				{$SyncKey_value = $value['Key'] . '_' . $value['Val'];
                } else
				{$SyncKey_value .= '|' . $value['Key'] . '_' . $value['Val'];
				}
				}

			$time = $this->getMillisecond() * 1000 + rand(1000, 9999);
			$params = ['r'=> time(),'sid'=> $post->sid,'uin'=> $post->uin,'skey'=> $post->skey,'deviceid'=>$post->BaseRequest['DeviceID'],'synckey'=>$SyncKey_value,'_'=> time(),];
			$url = 'https://webpush.'.$headr.'/cgi-bin/mmwebwx-bin/synccheck?'.http_build_query($params);
			$data = $this->_get($url);
			if(preg_match('/window.synccheck={retcode:"(\d+)",selector:"(\d+)"}/', $data,$pm)){
            $retcode = $pm[1];
            $selector = $pm[2];
        }
		$status = array('ret' => $retcode,'sel' => $selector,);
        return $retcode;
    }
    public function webwxgetcontact($post, $post_url_header,$seq) {
        $url = $post_url_header . '/webwxgetcontact?lang=zh_CN&pass_ticket=' . $post->pass_ticket . '&seq='.$seq.'&skey=' . $post->skey . '&r=' . $this->getMillisecond();
        $params = ['BaseRequest' => $post->BaseRequest];
        $data = $this->curlPost($url, $params);
        return $data;
    }
    public function wxstatusnotify($post, $json, $post_url_header) {
        $init = json_decode($json, true);
        $User = $init['User'];
        $url = $post_url_header . '/webwxstatusnotify?lang=zh_CN&pass_ticket=' . $post->pass_ticket;
        $params = array('BaseRequest' => $post->BaseRequest, "Code" => 3, "FromUserName" => $User['UserName'], "ToUserName" => $User['UserName'], "ClientMsgId" => time());
        $data = $this->curlPost($url, $params);
        $data = json_decode($data, true);
        return $data;
    }
    public function wxinit($post, $post_url_header) {
        $url = $post_url_header . '/webwxinit?pass_ticket=' . $post->pass_ticket . '&skey=' . $post->skey . '&r=' . time();
        $params = ['BaseRequest' => $post->BaseRequest];
        $json = $this->curlPost($url, $post);
        return $json;
    }
    public function get_uri($uuid) {
        $url = 'https://login.weixin.qq.com/cgi-bin/mmwebwx-bin/login?uuid=' . $uuid . '&tip=0&_=e' . time();
        $content = $this->curlPost($url);
        $content = explode(';', $content);
        $content_uri = explode('"', $content[1]);
        $uri = $content_uri[1];
        preg_match("~^https:?(//([^/?#]*))?~", $uri, $match);
        $https_header = $match[0];
        $post_url_header = $https_header . "/cgi-bin/mmwebwx-bin";
        $new_uri = explode('scan', $uri);
        $uri = $new_uri[0] . 'fun=new&scan=' . time();
        $getXML = $this->_get($uri);
        $XML = simplexml_load_string($getXML);
        $callback = array('post_url_header' => $post_url_header, 'Ret' => (array)$XML,);
        return $callback;
    }
    public function post_self($callback) {
        $post = new stdClass;
        $Ret = $callback['Ret'];
        $status = $Ret['ret'];
        if ($status == '1203') {
            $this->error('未知错误,请2小时后重试');
        }
        if ($status == '0') {
            $post->BaseRequest = array('Uin' => $Ret['wxuin'], 'Sid' => $Ret['wxsid'], 'Skey' => $Ret['skey'], 'DeviceID' => 'e' . rand(10000000, 99999999) . rand(1000000, 9999999),);
            $post->skey = $Ret['skey'];
            $post->pass_ticket = $Ret['pass_ticket'];
            $post->sid = $Ret['wxsid'];
            $post->uin = $Ret['wxuin'];
            return $post;
        }
        return $status;
    }
    public function wxloginout($post, $post_url_header) {
        $url = $post_url_header . '/webwxlogout?redirect=1&type=1&skey=' . $post->skey;
        $param = array('sid' => $post->sid, 'uin' => $post->uin);
        $this->curlPost($url, $param);
        return false;
    }
    public function curlPost($url, $data, $is_gbk, $timeout = 30, $CA = false) {
        $cacert = getcwd() . '/cacert.pem'; 
        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        $header = 'ContentType: application/json; charset=UTF-8';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout - 2);
        if ($SSL && $CA) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_CAINFO, $cacert); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
            
        } else if ($SSL && !$CA) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); 
            
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
        if ($data) {
            if ($is_gbk) {
                $data = str_replace("\\/", "/",json_encode($data, JSON_UNESCAPED_UNICODE));
            } else {
                $data = str_replace("\\/", "/",json_encode($data, JSON_UNESCAPED_UNICODE));
            }
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
		private function _get($url,$api = false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		$header = [
			'User-Agent: '.$this->user_agent,
			'Referer: https://wx.qq.com/'
		];
		if($api == 'webwxgetvoice')
			$header[]='Range: bytes=0-';
		if($api == 'webwxgetvideo')
			$header[]='Range: bytes=0-';
		curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_TIMEOUT, 36);
		curl_setopt($oCurl, CURLOPT_COOKIEFILE, $this->cookies);
		curl_setopt($oCurl, CURLOPT_COOKIEJAR, $this->cookies);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		return $sContent;
	}
	private function _post($url,$param,$jsonfmt=true,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
        if (PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')) {
        	$is_curlFile = true;
        } else {
        	$is_curlFile = false;
        	if (defined('CURLOPT_SAFE_UPLOAD')) {
            	curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
        	}
        }
        $header = [
            'User-Agent: '.$this->user_agent
        ];
        if($jsonfmt){
        	$param = self::json_encode($param);
            $header[] = 'Content-Type: application/json; charset=UTF-8';
            //var_dump($param);
        }
		if (is_string($param)) {
        	$strPOST = $param;
        }elseif($post_file) {
        	if($is_curlFile) {
                foreach ($param as $key => $val) {
                	if (substr($val, 0, 1) == '@') {
                    	$param[$key] = new \CURLFile(realpath(substr($val,1)));
                	}
                }
        	}
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  implode("&", $aPOST);
		}
		
		curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		curl_setopt($oCurl, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($oCurl, CURLOPT_COOKIEJAR, $this->cookie);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		return $sContent;
	}
}
