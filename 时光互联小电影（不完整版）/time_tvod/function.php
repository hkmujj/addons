<?php

 function mergeImage($target, $imgurl , $data) {
	$img = imagecreates($imgurl);
	$w = imagesx($img);
	$h = imagesy($img);
	imagecopyresized($target, $img, $data['left'], $data['top'], 0, 0, $data['width'], $data['height'], $w, $h);
	imagedestroy($img);
	return $target;
}


/**创建图片
 * @param $bg 图片路径
 * @return
 */
function imagecreates($bg) {
	$bgImg = @imagecreatefromjpeg($bg);
	if (FALSE == $bgImg) {
		$bgImg = @imagecreatefrompng($bg);
	}
	if (FALSE == $bgImg) {
		$bgImg = @imagecreatefromgif($bg);
	}
	return $bgImg;
}




function getQrcode($user,$modulename){
	global $_W;
	//看看是否已有记录
	if ($user['qrcode'] != 0){
		$qrcode = pdo_fetch('SELECT * FROM '.tablename('qrcode')." WHERE uniacid='{$_W['uniacid']}' AND id = '{$user['qrcode']}'");
		if($qrcode['createtime'] + $qrcode['expire'] < $_W['timestamp']){//过期
			pdo_delete('qrcode',array('id'=>$qrcode['id']));
		}else{
			$qrcode['new'] = 0;
			return $qrcode;
		}
	}
	
	//找出已经有的最大的场景id
	$scene = pdo_fetch('select * from '.tablename("qrcode")." where uniacid='{$_W['uniacid']}' order by qrcid desc");
	$sceneid = $scene['qrcid'];
	if (empty($sceneid)){
		$sceneid = 100000;
	}else{
		$sceneid++;
	}

	load()->model('account');
	$acid = pdo_fetchcolumn('select acid from '.tablename('account')." where uniacid={$_W['uniacid']}");
	$uniacccount = WeAccount::create($acid);

	$barcode['action_info']['scene']['scene_id'] = $sceneid;
	$barcode['action_name'] = 'QR_SCENE';
	$barcode['expire_seconds'] = 30*24*3600;
	
	$res = $uniacccount->barCodeCreateDisposable($barcode);
	
	//将二维码存于微擎官方二维码表
	pdo_insert('qrcode',
			array('uniacid'=>$_W['uniacid'],'acid'=>$acid,'qrcid'=>$sceneid,'name'=>'小电影海报','keyword'=> 'TVOD_'.$user['uid'],'model'=> 1,'ticket'=>$res['ticket'],'expire'=>$barcode['expire_seconds'],'createtime'=> $_W['timestamp'],'status'=>1,'url'=>$res['url']
			,'type' => 'scene')
	);	
	$qrcode = pdo_fetch('SELECT * FROM '.tablename("qrcode")." where uniacid ='{$_W['uniacid']}' AND qrcid = {$sceneid}");
	pdo_update($modulename."_user",array('qrcode'=>$qrcode['id']),array('uid'=>$user['uid']));
	$qrcode['new'] = 1;
	return $qrcode;

	
}

/**
 * 发送HTTP请求方法
 * @param  string $url    请求URL
 * @param  array  $params 请求参数
 * @param  string $method 请求方法GET/POST
 * @return array  $data   响应数据
 */
function xHttp($url, $params = array(), $method = 'GET', $header = array(), $multi = false){
    $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header
    );

    /* 根据请求类型设置特定参数 */
    switch(strtoupper($method)){
        case 'GET':
            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            break;
        case 'POST':
            //判断是否传输文件
            $params = $multi ? $params : http_build_query($params);
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new Exception('不支持的请求方式！');
    }

    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) throw new Exception('请求发生错误：' . $error);
	if($error){
	  	 throw new Exception('请求发生错误：' . $error);
	};
    return  $data;
}

