<?php
/**
 * 生活记账小助手模块微站定义
 *
 * @author dxf
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Dxf_jizhangModuleSite extends WeModuleSite {

	public $modulename = 'dxf_jizhang';
	//首页
	public function doMobileIndex() {
		global $_W,$_GPC;
		load()->func('tpl');
		$account_api = WeAccount::create();
		$info = $account_api->fansQueryInfo($_W['openid']);
		if(empty($_W['openid'])){
			message('请在微信客户端打开','','error');
		}
		$beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));

		$total['zhang']=pdo_fetch("select sum(zhang_money) as sum from " . tablename($this->modulename.'_zhang') . " WHERE  from_user=:from_user AND uniacid=:uniacid and zhang_addtime>$beginThismonth and zhang_addtime<$endThismonth and zhang_type=1 ",array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
		$total['share']=pdo_fetch("select sum(share_money) as sum from " . tablename($this->modulename.'_share') . " WHERE  from_user=:from_user AND uniacid=:uniacid and add_time>$beginThismonth and add_time<$endThismonth",array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
		$total['shouru']=pdo_fetch("select sum(zhang_money) as sum from " . tablename($this->modulename.'_zhang') . " WHERE  from_user=:from_user AND uniacid=:uniacid and zhang_addtime>$beginThismonth and zhang_addtime<$endThismonth and zhang_type=2 ",array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
		$sex=array('0'=>'','1'=>'男','2'=>'女');
		include $this->template('index');
	}
	//组别管理
	public function doMobileGroupList() {
		global $_W,$_GPC;
		$gu_type=array('1'=>'已默认','0'=>'设为默认');
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'on';
		if($operation == 'ownc'){
			$list = pdo_fetchall("select * from " . tablename($this->modulename.'_group') . " WHERE  from_user=:from_user AND uniacid=:uniacid ORDER BY  group_status desc,group_ctime desc ", array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
		}elseif($operation == 'on'){
			$list = pdo_fetchall("select * from " . tablename($this->modulename.'_group_user'). " as gu join ". tablename($this->modulename.'_group'). "as g on gu.group_id=g.group_id WHERE  gu.gu_from_user=:from_user AND gu.uniacid=:uniacid and g.group_status=1 ORDER BY g.group_ctime desc ", array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
			
		}elseif($operation == 'close'){
			$list = pdo_fetchall("select * from " . tablename($this->modulename.'_group_user'). " as gu join ". tablename($this->modulename.'_group'). "as g on gu.group_id=g.group_id WHERE  gu.gu_from_user=:from_user AND gu.uniacid=:uniacid and g.group_status=0 ORDER BY g.group_ctime desc ", array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
		}elseif($operation == 'groupstatus'){
			$group_status=$_GPC['group_status']==0?1:0;
			$ress=pdo_update($this->modulename.'_group', array('group_status'=>$group_status), array('group_id' =>$_GPC['group_id'],'from_user'=>$_W['openid']));
			if($ress){
				message('修改成功',$this->createMobileUrl("grouplist", array('op' => 'ownc')),'success');
			}else{
				message('修改失败','','error');
			}
		}elseif($operation == 'moren'){

			if($_GPC['gu_type']=='1'){
				$ress=pdo_update($this->modulename.'_group_user', array('gu_type'=>0), array('gu_id' =>$_GPC['gu_id'],'gu_from_user'=>$_W['openid']));
			}else{
				$ress=pdo_update($this->modulename.'_group_user', array('gu_type'=>0), array('gu_from_user'=>$_W['openid']));

				$ress=pdo_update($this->modulename.'_group_user', array('gu_type'=>1), array('gu_id' =>$_GPC['gu_id'],'gu_from_user'=>$_W['openid']));

			}
			if($ress){
				message('修改成功',$this->createMobileUrl("grouplist", array('op' => 'on')),'success');
			}else{
				message('修改失败','','error');
			}
		}


		include $this->template('grouplist');
	}
	//共享组详情
	public function doMobileGroupDetail() {
		global $_W,$_GPC;
		load()->func('tpl');
		$group_id=$_GPC['group_id'];
		$hidden=$_GPC['hidden'];
		if(empty($group_id)){
			message('操作失败','','error');
		}
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'detail';
		if($operation == 'edit'){
			$ress=pdo_fetch("SELECT * FROM ".tablename($this->modulename.'_group')." WHERE group_id = :group_id ", array(':group_id' => $group_id));
			include $this->template('addgroup');
		}elseif($operation == 'detail'){
			$account_api = WeAccount::create();
			$list = pdo_fetchall("select * from " . tablename($this->modulename.'_group_user'). " as gu join ". tablename($this->modulename.'_group'). "as g on gu.group_id=g.group_id WHERE  gu.group_id=:group_id AND gu.uniacid=:uniacid  ORDER BY gu.gu_manager desc ", array(':group_id' =>$group_id,':uniacid' => $_W['uniacid']));
			foreach ($list as $key => $value) {
				$list[$key]['fans'] = $account_api->fansQueryInfo($value['gu_from_user']);
			}
			//var_dump($list);
			include $this->template('groupdetail');
		}
	}
	//交易列表
	public function doMobileTradeList(){
		global $_W,$_GPC;
		load()->func('tpl');
		if(empty($_GPC['add_time'])){
			$time=explode('-',date('Y-m-d',$_W['timestamp']-(60*60*24)*7));
			$time1=explode('-',date('Y-m-d',$_W['timestamp']));
		}else{
			$begintime=strtotime(implode('-', $_GPC['add_time']));
			$endtime=strtotime(implode('-', $_GPC['add_time_1']))+(60*60*24);
			$where=" and s.add_time> $begintime and s.add_time<$endtime ";
			$time=explode('-',implode('-', $_GPC['add_time']));
			$time1=explode('-',implode('-', $_GPC['add_time_1']));
		}
		

		$list = pdo_fetchall("select * from " . tablename($this->modulename.'_group_user'). " as gu join ". tablename($this->modulename.'_group'). "as g on gu.group_id=g.group_id join".tablename($this->modulename.'_share')." as s on gu.group_id=s.group_id WHERE  gu.gu_from_user=:gu_from_user AND gu.uniacid=:uniacid ".$where." ORDER BY s.add_time desc ", array(':gu_from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
		//var_dump($list);
		
		$share_type=array('1'=>'三餐所需','2'=>'家居用品','3'=>'水电气修','4'=>'其他花费');
		include $this->template('tradelist');
	}
	//交易详情
	public function doMobileTradeDetail(){
		global $_W,$_GPC;
		load()->func('tpl');
		$share_id=$_GPC['share_id'];
		if(empty($share_id)){
			message('操作失败','','error');
		}
		$ress=pdo_fetch("SELECT * FROM ".tablename($this->modulename.'_share')."as s join ".tablename($this->modulename.'_group')."as g on s.group_id=g.group_id WHERE s.share_id = :share_id ", array(':share_id' => $share_id));
		$share_img=explode(',',$ress['share_img']);
		//var_dump($share_img);
		$share_type=array('1'=>'三餐所需','2'=>'家居用品','3'=>'水电气修','2'=>'其他花费');
		include $this->template('tradedetail');
	}
	//删除交易
	public function doMobileTradeDelete(){
		global $_W,$_GPC;
		load()->func('tpl');
		$share_id=$_GPC['share_id'];
		$ress=pdo_fetch("SELECT * FROM ".tablename($this->modulename.'_share')."where share_id = :share_id",array(':share_id' => $share_id));
		if($ress['from_user']==$_W['openid']){
			$ress=pdo_delete('dxf_jizhang_share',array('share_id'=>$share_id));
			if($ress){
				message('删除成功',$this->createMobileUrl("tradelist"),'success');
			}else{
				message('删除失败','','error');
			}
		}else{
			message('无权限删除','','error');
		}
	}
	//账单管理
	public function doMobileAccount(){
		global $_W,$_GPC;
		load()->func('tpl');
		$op=$_GPC['op'];//隐藏搜索
		if(empty($_GPC['year'])){
			$year=date('Y',time());
			$month=date('m',time());
		}else{
			$begintime=strtotime($_GPC['year']."-".$_GPC['month']);
			if($_GPC['month']==12){
				$year1=$_GPC['year']+1;
				$month1=1;
				$endtime=strtotime($year1."-".$month1);
			}else{
				$endtime=strtotime($_GPC['year']."-".($_GPC['month']+1));
			}
			$year=$_GPC['year'];
			$month=$_GPC['month'];
			$where=" and s.add_time>$begintime and s.add_time<$endtime ";
		}

		$grouplist=pdo_fetchall("select group_id from " .tablename($this->modulename.'_group_user')." WHERE  gu_from_user=:gu_from_user AND uniacid=:uniacid ",array(':gu_from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
		foreach ($grouplist as $key => $value) {
			$group.="'".$value['group_id']."',";
		}
		$groupress=rtrim($group,",");
		if(empty($groupress)){
			message('暂无账单','','error');
		}
		$list = pdo_fetchall("select g.group_id,g.group_name,g.group_num,gu.gu_from_user,gu.gu_manager,s.share_id,s.share_name,s.share_money,s.from_user from " .tablename($this->modulename.'_group'). "as g  join" .tablename($this->modulename.'_group_user'). " as gu on gu.group_id=g.group_id join " .tablename($this->modulename.'_share')." as s on gu.group_id=s.group_id WHERE  gu.group_id in ($groupress) AND gu.uniacid=:uniacid ".$where,array(':uniacid' => $_W['uniacid']));
		foreach ($list as $key => $value) {
			$ress[$value['group_id']]['share_id'][$key]['money']=$value['share_money'];
			$ress[$value['group_id']]['share_id'][$key]['from_user']=$value['from_user'];
			$ress[$value['group_id']]['gu_from_user'][]=$value['gu_from_user'];
			$ress[$value['group_id']]['group_name']=$value['group_name'];
			$ress[$value['group_id']]['group_num']=$value['group_num'];
		}
		foreach ($ress as $k => $v) {
			$listone[$k]['gu_from_user']=$v['gu_from_user'];
			foreach ($v['share_id'] as $ko => $vo) {
				$listone[$k][$vo['from_user']]+=$vo['money'];
				$listone[$k]['total']+=$vo['money'];
			}
			$listone[$k]['group_name']=$ress[$k]['group_name'];
			$listone[$k]['group_num']=$ress[$k]['group_num'];
		}
		$account_api = WeAccount::create();
		foreach ($listone as $keo => $veo) {
			foreach (array_unique($veo['gu_from_user']) as $ke => $ve) {
				$info[$keo][]= $account_api->fansQueryInfo($ve);
			}
		}
		include $this->template('account');
	}
	//个人账单
	public function doMobileZhangDan(){
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
		if($operation == 'list'){
			if(empty($_GPC['add_time'])){
				$time1=date('Y-m-d',$_W['timestamp']);
				$time=date('Y-m-d',$_W['timestamp']-(60*60*24*7));
				//echo $time;die;
			}else{
				$begintime=strtotime($_GPC['add_time']);
				$endtime=strtotime($_GPC['add_time_1'])+60*60*24;
				$where=" and zhang_addtime> $begintime and zhang_addtime<$endtime ";
				$time=$_GPC['add_time'];
				$time1=$_GPC['add_time_1'];
			}
			if(empty($_GPC['zhang_type'])){
				$where=" and zhang_type=1";
				$zhang_type=1;
			}else{
				$zhang_type=$_GPC['zhang_type'];
				$where.=" and zhang_type= $zhang_type ";

			}
			$list = pdo_fetchall("select * from " . tablename($this->modulename.'_zhang') . " WHERE  from_user=:from_user AND uniacid=:uniacid ".$where." ORDER BY  zhang_addtime desc", array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
			$sum=pdo_fetch("select sum(zhang_money) as sum from " . tablename($this->modulename.'_zhang') . " WHERE  from_user=:from_user AND uniacid=:uniacid ".$where,array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
		}
		if(checksubmit()){
			$data=array(
				'uniacid'=>$_W['uniacid'],
				'from_user'=>$_W['openid'],
				'zhang_type'=>trim($_GPC['zhang_type']),
				'zhang_name'=>trim($_GPC['zhang_name']),
				'zhang_money'=>trim($_GPC['zhang_money']),
				'zhang_desc'=>'',
				'zhang_addtime'=>time()
			);
			$ress=pdo_insert('dxf_jizhang_zhang',$data);
			if($ress){
				message('保存成功',$this->createMobileUrl("zhangdan", array('op' => 'list','zhang_type' => $_GPC['zhang_type'])),'success');
			}else{
				message('保存失败','','error');
			}
		}
		$zhang_type_1=array('1'=>'日常支出','2'=>'日常收入','3'=>'礼金支出','4'=>'朋友借款');
		include $this->template('zhangdan');
	}
	//删除账单
	public function doMobileZhangDanDelete(){
		global $_W,$_GPC;
		load()->func('tpl');
		$z_id=$_GPC['z_id'];
		$ress=pdo_fetch("SELECT * FROM ".tablename($this->modulename.'_zhang')."where z_id = :z_id",array(':z_id' => $z_id));
		if($ress['from_user']==$_W['openid']){
			$ress=pdo_delete('dxf_jizhang_zhang',array('z_id'=>$z_id));
			if($ress){
				message('删除成功',$this->createMobileUrl("ZhangDan",array('zhang_type'=>$_GPC['zhang_type'])),'success');
			}else{
				message('删除失败','','error');
			}
		}else{
			message('无权限删除','','error');
		}
	}
	//朋友管理
	public function doMobileFriends(){
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
		if($operation == 'list'){
			$list = pdo_fetchall("select * from " . tablename($this->modulename.'_friends') . " WHERE  from_user=:from_user AND uniacid=:uniacid ".$where." ORDER BY  yearbrithday ", array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
		}
		if(checksubmit()){
			if($_GPC['f_id']){
				$result = pdo_update('dxf_jizhang_friends', array('f_name'=>trim($_GPC['f_name']),'address'=>$_GPC['address'],'brithday'=>$_GPC['brithday'],'yearbrithday'=>strtotime(implode('-',$_GPC['yearbrithday'])),'desc'=>$_GPC['desc']), array('f_id' => $_GPC['f_id']));
				if($result){
					message('修改成功',$this->createMobileUrl("friends"),'success');
				}else{
					message('修改失败','','error');
				}
			}
			$data=array(
				'uniacid'=>$_W['uniacid'],
				'from_user'=>$_W['openid'],
				'f_name'=>trim($_GPC['f_name']),
				'address'=>trim($_GPC['address']),
				'brithday'=>trim($_GPC['brithday']),
				'yearbrithday'=>strtotime(implode('-',$_GPC['yearbrithday'])),
				'desc'=>trim($_GPC['desc']),
				'add_time'=>time()
			);
			$ress=pdo_insert('dxf_jizhang_friends',$data);
			if($ress){
				message('保存成功',$this->createMobileUrl("friends", array('op' => 'list')),'success');
			}else{
				message('保存失败','','error');
			}
		}
		include $this->template('friends');
	}
	//删除朋友
	public function doMobileFriendsDelete(){
		global $_W,$_GPC;
		load()->func('tpl');
		$f_id=$_GPC['f_id'];
		$ress=pdo_fetch("SELECT * FROM ".tablename($this->modulename.'_friends')."where f_id = :f_id",array(':f_id' => $f_id));
		if($ress['from_user']==$_W['openid']){
			$ress=pdo_delete('dxf_jizhang_friends',array('f_id'=>$f_id));
			if($ress){
				message('删除成功',$this->createMobileUrl("Friends"),'success');
			}else{
				message('删除失败','','error');
			}
		}else{
			message('无权限删除','','error');
		}
	}
	//编辑朋友
	public function doMobileFriendsEdit(){
		global $_W,$_GPC;
		load()->func('tpl');
		$f_id=$_GPC['f_id'];
		$operation = 'add';
		$ress=pdo_fetch("SELECT * FROM ".tablename($this->modulename.'_friends')." WHERE f_id = :f_id limit 1", array(':f_id' => $f_id));
		$time1=explode('-',date('Y-m-d',$ress['yearbrithday']));
		//var_dump($time1);
		include $this->template('friends');
	}
	//待办事项管理
	public function doMobileDaiBan(){
		global $_W,$_GPC;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
		if($operation == 'list'){
			$list = pdo_fetchall("select * from " . tablename($this->modulename.'_daiban') . " WHERE  from_user=:from_user AND uniacid=:uniacid ".$where." ORDER BY  d_time ", array(':from_user' =>$_W['openid'],':uniacid' => $_W['uniacid']));
		}
		$value=date('Y-m-d',$_W['timestamp']);
		if(checksubmit()){
			
			$data=array(
				'uniacid'=>$_W['uniacid'],
				'from_user'=>$_W['openid'],
				'd_name'=>trim($_GPC['d_name']),
				'd_time'=>strtotime($_GPC['d_time']),
				'add_time'=>time()
			);
			$ress=pdo_insert('dxf_jizhang_daiban',$data);
			if($ress){
				message('保存成功',$this->createMobileUrl("daiban", array('op' => 'list')),'success');
			}else{
				message('保存失败','','error');
			}
		}
		include $this->template('daiban');
	}
	//删除待办事项
	public function doMobileDaiBanDelete(){
		global $_W,$_GPC;
		load()->func('tpl');
		$d_id=$_GPC['d_id'];
		$ress=pdo_fetch("SELECT * FROM ".tablename($this->modulename.'_daiban')."where d_id = :d_id",array(':d_id' => $d_id));
		if($ress['from_user']==$_W['openid']){
			$ress=pdo_delete('dxf_jizhang_daiban',array('d_id'=>$d_id));
			if($ress){
				message('删除成功',$this->createMobileUrl("daiban"),'success');
			}else{
				message('删除失败','','error');
			}
		}else{
			message('无权限删除','','error');
		}
	}
	//手册
	public function doMobileShouCe(){
		global $_W,$_GPC;
		load()->func('tpl');
		include $this->template('shouce');
	}
	//二维码
	public function doMobileQcode(){
		global $_W,$_GPC;
		load()->func('tpl');
		$group_id='jizhang'.$_GPC['group_id'];
		//echo $group_id;
		$barcode='{"action_name":"QR_LIMIT_STR_SCENE", "action_info":{"scene":{"scene_str":"'.$group_id.'""}}}';

		$account_api = WeAccount::create();
		$token = $account_api->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
		$ress=$this->https_request($url,$barcode);
		//var_dump($ress);die;
		$re=json_decode($ress);
		$img=$re->ticket;
		$showimg='<img src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$img.'";>';
		$hidden=2;
		include $this->template('groupdetail');
	}
	//通过https中的get或者post请求 
	function https_request($url,$data=null){    
	    $ch = curl_init();  
	    curl_setopt($ch, CURLOPT_URL,$url);  
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    if(!empty($data)){
	        curl_setopt($ch, CURLOPT_POST,1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);  
	    }
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
	    $result = curl_exec($ch);  
	    curl_close($ch);  
	    return $result;    
	}


}