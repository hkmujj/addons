<?php
global $_W, $_GPC;
$openid = $_W['fans']['from_user'];
if(empty($openid)){
	message('请在微信浏览器中打开！');
}
$setting = $this->setting;
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	/*if(!pdo_tableexists('bsht_tbk_user')) {
		message('请先购买牛贝淘宝客模块！');
	}*/
	$toopenid = trim($_GPC['toopenid']);
	$qudao = trim($_GPC['qudao']);
	$qudaoarray = array("niubeitaoke","renren","super","kefu");
	if(!in_array($qudao,$qudaoarray)){
		message('渠道来源不正确！');
	}
	if(empty($toopenid)){
		message('参数错误，无法发起聊天！');
	}
	if($toopenid == $openid){
		message('不能和自己聊天！');
	}
	$sanfanskefu2 = pdo_fetch("SELECT * FROM ".tablename(BEST_SANFANSKEFU)." WHERE weid = {$_W['uniacid']} AND fansopenid = '{$openid}' AND kefuopenid = '{$toopenid}' AND qudao = '{$qudao}'");
	$sanfanskefu = pdo_fetch("SELECT * FROM ".tablename(BEST_SANFANSKEFU)." WHERE weid = {$_W['uniacid']} AND kefuopenid = '{$openid}' AND fansopenid = '{$toopenid}' AND qudao = '{$qudao}'");
	if(empty($sanfanskefu)){
		$datasanfk['weid'] = $_W['uniacid'];
		$datasanfk['fansopenid'] = $toopenid;
		$account_api = WeAccount::create();
		$fansuser = $account_api->fansQueryInfo($toopenid);
		if(empty($fansuser)){
			$datasanfk['fansavatar'] = tomedia($setting['defaultavatar']);
			$datasanfk['fansnickname'] = '匿名用户';
		}else{
			$datasanfk['fansavatar'] = empty($fansuser['headimgurl']) ? tomedia($setting['defaultavatar']) : $fansuser['headimgurl'];
			$datasanfk['fansnickname'] = empty($fansuser['nickname']) ? '匿名用户' : $fansuser['nickname'];
		}
		$datasanfk['qudao'] = $qudao;
		$datasanfk['kefuopenid'] = $openid;
		$datasanfk['kefuavatar'] = empty($_W['fans']['tag']['avatar']) ? tomedia($setting['defaultavatar']) : $_W['fans']['tag']['avatar'];
		$datasanfk['kefunickname'] = empty($_W['fans']['tag']['nickname']) ? '匿名用户' : $_W['fans']['tag']['nickname'];
		pdo_insert(BEST_SANFANSKEFU,$datasanfk);
		$sanfanskefu = pdo_fetch("SELECT * FROM ".tablename(BEST_SANFANSKEFU)." WHERE weid = {$_W['uniacid']} AND kefuopenid = '{$openid}' AND fansopenid = '{$toopenid}' AND qudao = '{$qudao}'");
	}
	if(empty($sanfanskefu2)){
		$datasanfk['weid'] = $_W['uniacid'];
		$datasanfk['fansopenid'] = $openid;
		$datasanfk['fansavatar'] = empty($_W['fans']['tag']['avatar']) ? tomedia($setting['defaultavatar']) : $_W['fans']['tag']['avatar'];
		$datasanfk['fansnickname'] = empty($_W['fans']['tag']['nickname']) ? '匿名用户' : $_W['fans']['tag']['nickname'];
		$datasanfk['qudao'] = $qudao;
		$datasanfk['kefuopenid'] = $toopenid;
		$kefuuser = $account_api->fansQueryInfo($toopenid);
		if(empty($kefuuser)){
			$datasanfk['kefuavatar'] = tomedia($setting['defaultavatar']);
			$datasanfk['kefunickname'] = '匿名用户';
		}else{
			$datasanfk['kefuavatar'] = empty($kefuuser['headimgurl']) ? tomedia($setting['defaultavatar']) : $kefuuser['headimgurl'];
			$datasanfk['kefunickname'] = empty($kefuuser['nickname']) ? '匿名用户' : $kefuuser['nickname'];
		}
		pdo_insert(BEST_SANFANSKEFU,$datasanfk);
		$sanfanskefu2 = pdo_fetch("SELECT * FROM ".tablename(BEST_SANFANSKEFU)." WHERE weid = {$_W['uniacid']} AND fansopenid = '{$openid}' AND kefuopenid = '{$toopenid}' AND qudao = '{$qudao}'");
	}
	$sanfkid = $sanfanskefu['id'];
	$sanfkid2 = $sanfanskefu2['id'];
	$chatcon = pdo_fetchall("SELECT * FROM ".tablename(BEST_SANCHAT)." WHERE (sanfkid = {$sanfkid} OR sanfkid = {$sanfkid2}) AND weid = {$_W['uniacid']} ORDER BY time ASC");
	$timestamp = TIMESTAMP;
	foreach($chatcon as $k=>$v){
		$chatcon[$k]['content'] = $this->guolv($chatcon[$k]['content']);
		$chatcon[$k]['content'] = preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '[无法识别字符]', $v['content']);
		$regex = '@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?<<>>“”‘’]))@';
		preg_match_all($regex,$chatcon[$k]['content'],$array2);  
		if(!empty($array2[0]) && $v['type'] == 1){
			foreach($array2[0] as $kk=>$vv){
				if(!empty($vv)){
					$chatcon[$k]['content'] = str_replace($vv,"<a href='".$vv."'>".$vv."</a>",$chatcon[$k]['content']);
				}
			}
		}
		if($v['type'] == 3){
			$donetime = $timestamp - $v['time'];
			if($donetime >= 24*3600*3){
				unset($chatcon[$k]);
			}
		}
	}		
	$imglist = pdo_fetchall("SELECT * FROM ".tablename(BEST_SANCHAT)." WHERE (sanfkid = {$sanfkid} OR sanfkid = {$sanfkid2}) AND weid = {$_W['uniacid']} AND type = 2 ORDER BY time ASC");
	$imglistval = '';
	foreach($imglist as $k=>$v){
		$imglistval .= $v['content'].',';
	}
	$imglistval = substr($imglistval,0,-1);
	$goodsid = intval($_GPC['goodsid']);
	if($qudao == 'renren' && $goodsid != 0){
		if(pdo_tableexists('ewei_shop_goods')) {
			$goodsres = pdo_fetch("SELECT title,thumb,id,productprice FROM ".tablename('ewei_shop_goods')." WHERE id = {$goodsid} AND uniacid = {$_W['uniacid']}");
			$goods['title'] = $goodsres['title'];
			$goods['thumb'] = tomedia($goodsres['thumb']);
			$goods['id'] = $goodsres['id'];
			$goods['price'] = $goodsres['productprice'];
		}
	}
	if($qudao == 'super' && $goodsid != 0){
		if(pdo_tableexists('superman_mall_item')) {
			$goodsres = pdo_fetch("SELECT title,cover,id,price FROM ".tablename('superman_mall_item')." WHERE id = {$goodsid} AND uniacid = {$_W['uniacid']}");
			$goods['title'] = $goodsres['title'];
			$goods['thumb'] = tomedia($goodsres['cover']);
			$goods['id'] = $goodsres['id'];
			$goods['price'] = $goodsres['price'];
		}
	}
	include $this->template('sanchat');
}elseif($operation == 'shuaxin'){
	$toopenid = trim($_GPC['toopenid']);
	$qudao = trim($_GPC['qudao']);
	if(empty($toopenid) || empty($qudao)){
		$resArr['error'] = 1;
		$resArr['msg'] = '数据获取失败！';
		echo json_encode($resArr);
		exit;
	}
	if(empty($openid)){
		$resArr['error'] = 1;
		$resArr['msg'] = '获取微信信息失败！';
		echo json_encode($resArr);
		exit;
	}
	$sanfanskefu = pdo_fetch("SELECT * FROM ".tablename(BEST_SANFANSKEFU)." WHERE weid = {$_W['uniacid']} AND fansopenid = '{$toopenid}' AND kefuopenid = '{$openid}' AND qudao = '{$qudao}'");
	$sanfanskefu2 = pdo_fetch("SELECT * FROM ".tablename(BEST_SANFANSKEFU)." WHERE weid = {$_W['uniacid']} AND kefuopenid = '{$toopenid}' AND fansopenid = '{$openid}' AND qudao = '{$qudao}'");
	$sanfkid2 = $sanfanskefu2['id'];
	$sanfkid = $sanfanskefu['id'];
	pdo_update(BEST_SANFANSKEFU,array('notread'=>0,'seetime'=>TIMESTAMP),array('id'=>$sanfanskefu['id']));
	$timestamp = intval($_GPC['timestamp']);
	$chatcon = pdo_fetchall("SELECT * FROM ".tablename(BEST_SANCHAT)." WHERE (sanfkid = {$sanfkid} OR sanfkid = {$sanfkid2}) AND weid = {$_W['uniacid']} AND time >= {$timestamp} ORDER BY time ASC");
	$html = '';
	if(!empty($chatcon)){
		foreach($chatcon as $k=>$v){
			$v['content'] =  preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '[无法识别字符]', $v['content']);		
			$regex = '@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?<<>>“”‘’]))@';
			preg_match_all($regex,$v['content'],$array2);  
			if(!empty($array2[0]) && $v['type'] == 1){
				foreach($array2[0] as $kk=>$vv){
					if(!empty($vv)){
						$v['content'] = str_replace($vv,"<a href='".$vv."'>".$vv."</a>",$v['content']);
					}
				}
			}
			if($v['openid'] == $openid){
				$html = '<div class="chat-msg"><div class="time">'.date('Y-m-d H:i:s',$v['time']).'</div><div class="msg right clear">';
			}else{
				$html = '<div class="chat-msg"><div class="time">'.date('Y-m-d H:i:s',$v['time']).'</div><div class="msg left clear">';
			}
			if($v['openid'] == $sanfanskefu['fansopenid']){
				$avatarshow = $sanfanskefu['fansavatar'];
			}else{
				$avatarshow = $sanfanskefu['kefuavatar'];
			}
			if($v['type'] == 2){					
				$content = '<img src="'.$v['content'].'" class="sssbbb" style="max-width:100%;" />';
			}elseif($v['type'] == 3){					
				$content = '<span class="audio-msg voiceplay" style="width:'.((($v['yuyintime']*3.5)/60)+0.3).'rem;" onclick="playvoice(\''.$v['content'].'\',$(this).parents(\'.txt-con\').next(\'.weidu\'));"><i class="a-icon iconfont">&#xe601;</i></span>';
			}else{
				$content = $v['content'];
			}
			$content = $this->guolv($content);
			if($v['type'] == 3){
				if($v['hasyuyindu'] == 0 && $openid != $v['openid']){
					$wwwddd = '<span class="weidu"></span>';
				}else{
					$wwwddd = '';
				}
				$wwwddd .= '<span class="miaoshu">'.$v['yuyintime'].'\'\'</span>';
			}else{
				$wwwddd = '';
			}
			$html .= '<div class="nick-img">
						<img src="'.$avatarshow.'" />
					</div>
					<div class="nick-text">
						<div class="txt-con">
							'.$content.'
						</div>
						'.$wwwddd.'
					</div>
					</div></div>';
		}
		$imglist = pdo_fetchall("SELECT content FROM ".tablename(BEST_SANCHAT)." WHERE (sanfkid = {$sanfkid} OR sanfkid = {$sanfkid2}) AND weid = {$_W['uniacid']} AND type = 2 ORDER BY time ASC");
		$imglistval = '';
		foreach($imglist as $k=>$v){
			$imglistval .= $v['content'].',';
		}
		$imglistval = substr($imglistval,0,-1);
		$resArr['imglistjson'] = $imglistval;
		$resArr['error'] = 0;
		$resArr['msg'] = $html;
		$resArr['timestamp'] = TIMESTAMP;
		echo json_encode($resArr);
		exit;
	}else{
		$resArr['error'] = 1;
		$resArr['msg'] = '';
		$resArr['timestamp'] = TIMESTAMP;
		echo json_encode($resArr);
		exit;
	}
}elseif($operation == 'addchat'){
	include_once('../addons/cy163_customerservice/emoji/emoji.php');
	$toopenid = trim($_GPC['toopenid']);
	$qudao = trim($_GPC['qudao']);
	$goodsid = intval($_GPC['goodsid']);
	if(empty($toopenid) || empty($qudao)){
		$resArr['error'] = 1;
		$resArr['msg'] = '数据获取失败！';
		echo json_encode($resArr);
		exit;
	}
	$chatcontent = trim($_GPC['content']);
	if(empty($chatcontent)){
		$resArr['error'] = 1;
		$resArr['msg'] = '请输入对话内容！';
		echo json_encode($resArr);
		exit;
	}
	$sanfanskefu = pdo_fetch("SELECT * FROM ".tablename(BEST_SANFANSKEFU)." WHERE weid = {$_W['uniacid']} AND kefuopenid = '{$toopenid}' AND fansopenid = '{$openid}' AND qudao = '{$qudao}'");
	if(empty($sanfanskefu)){
		$resArr['error'] = 1;
		$resArr['msg'] = '获取数据失败！';
		echo json_encode($resArr);
		exit;
	}
	$sanfkid = $sanfanskefu['id'];
	$chatcontent = emoji_docomo_to_unified($chatcontent);
	$chatcontent = emoji_unified_to_html($chatcontent);
	$data['openid'] = $_W['fans']['from_user'];
	$data['time'] = TIMESTAMP;
	$data['content'] = $chatcontent;
	$data['weid'] = $_W['uniacid'];
	$data['sanfkid'] = $sanfkid;
	$type = intval($_GPC['type']);
	$data['type'] = $type;
	$data['yuyintime'] = intval($_GPC['yuyintime']/1000)+1;
	if($type == 2){
		$tplcon = '聊天内容：对方发送了图片';
	}elseif($type == 3){
		$tplcon = '聊天内容：对方发送了语音';
	}else{
		if(strpos($data['content'],'span class=')){
			$tplcon = '聊天内容：对方发送了表情';
		}else{
			$tplcon = '聊天内容：'.$data['content'];
		}
	}
	$tplcon = $this->guolv($tplcon);
	pdo_insert(BEST_SANCHAT,$data);
	pdo_query("update ".tablename(BEST_SANFANSKEFU)." set notread=notread+1 where id=:id", array(":id" => $data['sanfkid']));
	$guotime = TIMESTAMP-$sanfanskefu['seetime'];
	$guotime2 = TIMESTAMP-$sanfanskefu['lasttime'];
	if($setting['istplon'] == 1 && $guotime > $setting['kefutplminute'] && $guotime2 > $setting['kefutplminute']){
		$tpllist = pdo_fetch("SELECT id,tplbh FROM".tablename(BEST_TPLMESSAGE_TPLLIST)." WHERE (tplbh = 'OPENTM202309749' OR tplbh = 'OPENTM207327169') AND uniacid = {$_W['uniacid']}");
		if(!empty($tpllist)){
			if($tpllist['tplbh'] == 'OPENTM202309749'){
				$arrmsg = array(
					'openid'=>$sanfanskefu['kefuopenid'],
					'topcolor'=>'#980000',
					'first'=>$sanfanskefu['fansnickname'].'对您发起了聊天！',
					'firstcolor'=>'#990000',
					'keyword1'=>$sanfanskefu['fansnickname'],
					'keyword1color'=>'',
					'keyword2'=>date("Y-m-d H:i:s",TIMESTAMP),
					'keyword2color'=>'',
					'remark'=>$tplcon,
					'remarkcolor'=>'',
					'url'=>$_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("sanchat",array('toopenid'=>$sanfanskefu['fansopenid'],'qudao'=>$qudao,'goodsid'=>$goodsid))),
				);
			}else{
				$denghounum = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename(BEST_SANFANSKEFU)." WHERE weid = {$_W['uniacid']} AND kefuopenid = '{$sanfanskefu['kefuopenid']}' AND notread > 0");
				$arrmsg = array(
					'openid'=>$sanfanskefu['kefuopenid'],
					'topcolor'=>'#980000',
					'first'=>$sanfanskefu['fansnickname'].'对您发起了聊天！',
					'firstcolor'=>'#990000',
					'keyword1'=>date("Y-m-d H:i:s",TIMESTAMP),
					'keyword1color'=>'',
					'keyword2'=>$denghounum,
					'keyword2color'=>'',
					'remark'=>$tplcon,
					'remarkcolor'=>'',
					'url'=>$_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("sanchat",array('toopenid'=>$sanfanskefu['fansopenid'],'qudao'=>$qudao,'goodsid'=>$goodsid))),
				);
			}
			$this->sendtemmsg($tpllist['id'],$arrmsg);
		}else{
			$texturl = $_W["siteroot"].'app/'.str_replace("./","",$this->createMobileUrl("sanchat",array('toopenid'=>$sanfanskefu['fansopenid'],'qudao'=>$qudao,'goodsid'=>$goodsid)));
			$texturl = "<a href='".$texturl."'>点击查看</a>";
			$concon = $sanfanskefu['fansnickname'].'对您发起了聊天！'.$tplcon.'。'.$texturl;
			$send['touser'] = $sanfanskefu['kefuopenid'];
			$send['msgtype'] = 'text';
			$send['text'] = array('content' => urlencode($concon));
			$acc = WeAccount::create($_W['uniacid']);
			$res = $acc->sendCustomNotice($send);
		}
	}
	pdo_query("update ".tablename(BEST_SANFANSKEFU)." set lastcon='{$chatcontent}',msgtype={$type},lasttime=:lasttime where id=:id", array(":lasttime" => TIMESTAMP, ":id" => $data['sanfkid']));
	$resArr['error'] = 0;
	$resArr['msg'] = '';
	echo json_encode($resArr);
	exit;
}elseif($operation == 'shuaxinyuyin'){
	$content = trim($_GPC['content']);
	$chat = pdo_fetch("SELECT openid FROM ".tablename(BEST_SANCHAT)." WHERE weid = {$_W['uniacid']} AND content = '{$content}'");
	if($chat['openid'] != $_W['fans']['from_user']){
		pdo_update(BEST_SANCHAT,array('hasyuyindu'=>1),array('weid'=>$_W['uniacid'],'content'=>$content));
		$resArr['error'] = 0;
		$resArr['msg'] = '语音已读成功！';
		echo json_encode($resArr);
		exit;
	}else{
		$resArr['error'] = 1;
		$resArr['msg'] = '语音已读失败！';
		echo json_encode($resArr);
		exit;
	}
}




//易 福 源 码 网 www.efwww.com
?>