<?php
/**
 * 全网VIP视频在线免费看模块定义
 *
 * @author cyl
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Cyl_vip_videoModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
			$data = $_GPC['data'];	
			$data['shuoming'] = htmlspecialchars_decode($data['shuoming']);	
			foreach($_GPC['member_title'] as $k => $v) {
                $v = trim($v);
                if(empty($v)) continue;
                $member[] = array(                    
                    'member_title' => $v,
                    'member_link' => $_GPC['member_link'][$k],
                );                
            }
            $data['member'] = iserializer($member);   
            foreach($_GPC['card_title'] as $k => $v) {
                $v = trim($v);
                if(empty($v)) continue;
                $card[] = array(                    
                    'card_title' => $v,
                    'card_day' => $_GPC['card_day'][$k],
                    'card_fee' => $_GPC['card_fee'][$k],
                    'card_credit' => $_GPC['card_credit'][$k],
                );                
            }
            $data['card'] = iserializer($card);      			
			if (!$this->saveSettings($data)) {
				message('保存信息失败','','error');   // 保存失败
			} else {
				message('保存信息成功','','success'); // 保存成功
			}
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}

}