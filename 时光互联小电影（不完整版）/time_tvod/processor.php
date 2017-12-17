<?php
/**
 * 小电影模块处理程序
 *
 * @author 时光互联
 * @url http://www.timecms.com/
 */
defined('IN_IA') or exit('Access Denied');

class Time_tvodModuleProcessor extends WeModuleProcessor {
	
	
	public function respond() {
		global $_W,$_GPC;
		load()->model('mc');
		$content = $this->message['content'];
		$uniacid = intval($_W['uniacid']);
		$acid = $_W['acid'];
		$acc = WeAccount::create($acid);
		

		$setting = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_setting') . " WHERE  uniacid = {$uniacid}");
		$config = unserialize($setting['config']);
		
		
		$chekarr = explode('_', $this->message['content']);
		if($chekarr[0] == 'TVOD'){
			$inviteuser = pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE uniacid = {$uniacid} AND uid = {$chekarr[1]}");			
			if(!empty($inviteuser)){									
				$uid = mc_openid2uid($this->message['from']);
				if(!empty($uid)){		
					$actuser =  pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE  uniacid = {$uniacid} AND uid = {$uid}");
					if(empty($actuser) && $actuser['uid'] != $inviteuser['uid']){
						$user['vip_time'] = $_W['timestamp'];
						$user['uid'] = $uid;
						$user['freenum'] = (int) $config['vip_freenum'];
						$user['uniacid'] = $uniacid;
						$user['leader1'] = $inviteuser['uid'];
						$user['leader2'] = $inviteuser['leader1'];
						$user['leader3'] = $inviteuser['leader2'];
						pdo_insert($this->modulename . '_user', $user);
						if(!empty($config['fx_point'])){
							$fxpoint = explode('|',$config['fx_point']);
							if(intval($fxpoint[0]) > 0){
								$leaderpoint0 = $inviteuser['point'] + intval($fxpoint[0]);
								pdo_update($this->modulename . '_user', array('point' => $leaderpoint0), array('id' => $inviteuser['id']));
								
								//模版通知
								$url = $_W['siteroot'] . $this->createMobileUrl('index');
								$leaderfan0 = pdo_fetch("SELECT * FROM " . tablename('mc_mapping_fans') . " WHERE  uid = {$inviteuser['uid']}");
					        	$postdata = array(
									'first'=>array(
										'value'=>'亲，您成功邀请了一位朋友加入'.$config['title'],
										'color'=>'#173177'
									),
									'keyword1'=>array(
										'value'=> date('Y-m-d H:i:s'),
										'color'=>'#173177'
									),
									'keyword2'=>array(
										'value'=> intval($fxpoint[0]),
										'color'=>'#173177'
									),
									'keyword3'=>array(
										'value'=> '推荐奖励',
										'color'=>'#173177'
									),
									'keyword4'=>array(
										'value'=> $leaderpoint0,
										'color'=>'#173177'
									),
									'remark'=>array(
										'value'=> '感谢有您，如有问题请直接在微信留言，我们将第一时间为您服务！',
										'color'=>'#173177'
									)
								);
								$acc -> sendTplNotice($leaderfan0['openid'], $config['mbxx_point'], $postdata, $url, $topcolor = '#FF683F');
							}
							if($inviteuser['leader1'] != 0 && intval($fxpoint[1])  > 0){
								$leader1 =  pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE  uniacid = {$uniacid} AND uid = {$inviteuser['leader1']}");
								$leaderpoint1 = $leader1['point'] + intval($fxpoint[1]);
								pdo_update($this->modulename . '_user', array('point' => $leaderpoint1), array('id' => $leader1['id']));
								
								//模版通知
								$url = $_W['siteroot'] . $this->createMobileUrl('index');
								$leaderfan1 = pdo_fetch("SELECT * FROM " . tablename('mc_mapping_fans') . " WHERE  uid = {$leader1['uid']}");
					        	$postdata = array(
									'first'=>array(
										'value'=>'亲，您成功邀请了一位朋友加入'.$config['title'],
										'color'=>'#173177'
									),
									'keyword1'=>array(
										'value'=> date('Y-m-d H:i:s'),
										'color'=>'#173177'
									),
									'keyword2'=>array(
										'value'=> intval($fxpoint[1]),
										'color'=>'#173177'
									),
									'keyword3'=>array(
										'value'=> '下级推荐奖励',
										'color'=>'#173177'
									),
									'keyword4'=>array(
										'value'=> $leaderpoint0,
										'color'=>'#173177'
									),
									'remark'=>array(
										'value'=> '感谢有您，如有问题请直接在微信留言，我们将第一时间为您服务！',
										'color'=>'#173177'
									)
								);
								$acc -> sendTplNotice($leaderfan1['openid'], $config['mbxx_point'], $postdata, $url, $topcolor = '#FF683F');
									
							}	
							if($inviteuser['leader2'] != 0 && intval($fxpoint[2])  > 0){
								$leader2 =  pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE  uniacid = {$uniacid} AND uid = {$inviteuser['leader2']}");
								$leaderpoint2 = $leader1['point'] + intval($fxpoint[2]);
								pdo_update($this->modulename . '_user', array('point' => $leaderpoint2), array('id' => $leader2['id']));
								
								//模版通知
								$url = $_W['siteroot'] . $this->createMobileUrl('index');
								$leaderfan2 = pdo_fetch("SELECT * FROM " . tablename('mc_mapping_fans') . " WHERE  uid = {$leader2['uid']}");
					        	$postdata = array(
									'first'=>array(
										'value'=>'亲，您成功邀请了一位朋友加入'.$config['title'],
										'color'=>'#173177'
									),
									'keyword1'=>array(
										'value'=> date('Y-m-d H:i:s'),
										'color'=>'#173177'
									),
									'keyword2'=>array(
										'value'=> intval($fxpoint[2]),
										'color'=>'#173177'
									),
									'keyword3'=>array(
										'value'=> '二级推荐奖励',
										'color'=>'#173177'
									),
									'keyword4'=>array(
										'value'=> $leaderpoint0,
										'color'=>'#173177'
									),
									'remark'=>array(
										'value'=> '感谢有您，如有问题请直接在微信留言，我们将第一时间为您服务！',
										'color'=>'#173177'
									)
								);
								$acc -> sendTplNotice($leaderfan2['openid'], $config['mbxx_point'], $postdata, $url, $topcolor = '#FF683F');
														
							}
				 		}
						return $this->respText('欢迎您成为【'.$config['title'].'】的会员,这里有超多的VIP电影供您欣赏，COMEON BABY!');
					}			
				}
			}
		}

		if($this->checkUrl($content) == false){
			load()->func('communication');
			$remote_url = $config['api_url'].'/index.php?m=api&c=vodajax&a=index&p=1&keyword=' . $content .'&token='.md5($config['api_token']); 
			$data_list = ihttp_get($remote_url);
			$array_list = $data_list['content'];
			$array_list = json_decode($array_list,true);
			if(!empty($array_list['result'])){
				$reply = array();
				$i = 0;
				foreach ($array_list['result'] as $key => $val) {
					if($i < 8){
						if(strpos($val['d_pic'],'http') == false){
							$arr['picurl'] = $config['api_web'].'/' . $val['d_pic'];
						}else{
							$arr['picurl'] = $val['d_pic'];
						}
						$arr['title'] = $val['d_name'];
						$arr['description'] = '';
						$arr['url'] = $this->createMobileUrl('play', array('id' => $val['d_id']));
						$reply[] = $arr;	
					}
					$i++;
				}
				return $this->respNews($reply);
			}
		}else{
			if($config['wn_vip'] == 1){			
				$uid = mc_openid2uid($this->message['from']);
				$user =  pdo_fetch("SELECT * FROM " . tablename($this->modulename . '_user') . " WHERE  uid = {$uid} ");
				if($user['vip_time'] < $_W['timestamp']){
					return $this->respText('VIP会员可以将视频地址发送给我，点击我给您的回复就可以无限制观看啦！');
				}	
			}	
			$arr['picurl'] = $_W['attachurl'].$config['wn_image'];
			$arr['title'] = '亲爱的会员，您要的地址解析好了';
			$arr['description'] = $config['wn_desc'];
			
			//针对各网站地址格式处理
			$url = $content;

			//芒果TV	
			if(strpos($url,'m.mgtv.com') > 0){
				$extarr = explode('/#/', $url);
				$url = 'http://www.mgtv.com/b/'.$extarr[1].'.html';
			}
			
			//爱奇艺
			if(strpos($url,"m.iqiyi.com") > 0){
				$url = str_replace('m.iqiyi.com', 'www.iqiyi.com', $url);
			}
			
			//优酷
			if(strpos($url,"youku") > 0){
				$extarr = explode('?', $url);
				$url = $extarr[0];
				$url = str_replace('m.youku.com', 'www.youku.com', $url);
			}
			
			//腾讯
			if(strpos($url,"m.v.qq.com") > 0){
				$url = str_replace('m.v.qq.com', 'v.qq.com', $url);
			}		
			
			//搜狐
			if(strpos($url,"wx.film.sohu.com") > 0){
				$url = str_replace('wx.film.sohu.com', 'film.sohu.com', $url);
			}
				
			$arr['url'] = $this->createMobileUrl('prise', array('op' => 'play','url' => base64_encode($url)));
			$reply[] = $arr;
			return $this->respNews($arr);
		}

	}


	private function checkUrl($url){
	    if(!preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$url)){
	    	if(!preg_match('/https:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$url)){
	        	return false;
			}else{
				return true;
			}
	    }else{
	    	return true;
	    }
    	
	}
	




}