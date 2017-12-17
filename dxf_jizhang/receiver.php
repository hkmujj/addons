<?php
/**
 * 生活记账小助手模块订阅器
 *
 * @author dxf
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Dxf_jizhangModuleReceiver extends WeModuleReceiver {
	public function receive() {
		global $_W,$_GPC;
		
		$scene = $this->message['scene'];
		$gu_from_user = $this->message['fromusername'];
		/*WeUtility::logging('debug', $scene);
        WeUtility::logging('debug', $gu_from_user);*/
        $ress=strstr($scene,'jizhang');
		if($ress){
			$group_id=ltrim($scene,'jizhang');
			$ress=pdo_fetch("SELECT * FROM ".tablename('dxf_jizhang_group_user')."where uniacid=:uniacid and group_id=:group_id and gu_from_user=:gu_from_user",array(':uniacid' => $_W['uniacid'],':group_id' => $group_id,':gu_from_user' => $gu_from_user));
			if(!$ress){
				$data=array(
					'uniacid'=>$_W['uniacid'],
					'group_id'=>$group_id,
					'gu_from_user'=>$gu_from_user,
					'gu_manager'=>0,
					'gu_ctime'=>time(),
					'gu_type'=>0
				);
				$ress=pdo_insert('dxf_jizhang_group_user',$data);
				//$ress=pdo_query(" update dxf_jizhang_group set group_num=group_num+1 where group_id = ".$group_id);
				$result = pdo_query(" UPDATE ".tablename('dxf_jizhang_group')." SET group_num = group_num+1  WHERE group_id = :group_id", array(':group_id' => $group_id));
			}

		}

		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看微擎文档来编写你的代码
	}
}