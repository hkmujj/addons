<?php
namespace Home\Controller;
use Think\Controller;

class PlayController extends Controller {
	
    public function index($id){
		$detail = M('vod')->find($id);		
		if(strpos($detail['d_pic'],'http') === false ){
			$detail['d_pic'] = 'http://www.zmpic.com/' . $detail['d_pic'];
		}	
		
		$playfrom = explode('$$$', $detail['d_playfrom']);				
		foreach ($playfrom as $key => $val) {		
			switch ($val) {
				case 'qq':
					$playurl = 'http://api.goudaitv.com/tz/qq.php?vid=';
					break;
				case '2mm':
					$playurl = 'http://app.zmpic.com/app/tv.html?url=';
					break;
				default:
					$playurl = 'http://www.zmpic.com/ckmov/index.php?type='.$detail['d_playfrom'].'&url=';
					break;
			}
			$arr['type'] = $val;
			$arr['url'] = $playurl;
			$playurls[] = $arr;
		}
		
		
		$urlss = explode('$$$', $detail['d_playurl']);
		foreach ($urlss as $k => $v) {
			$arr = $arrr= array();
			$urls = explode('#', $v);
			foreach ($urls as $key => $val) {
				$str = explode('$', $val);
				$arr['name'] = $str[0];
				$arr['url'] = $playurls[$k]['url'].$str[1];
				$arrr[] = $arr;
			}	
			$list[$k]['name'] = $playurls[$k]['type'];
			$list[$k]['data'] = $arrr;
		}
	
		$this->assign('list',$list);
		$this->assign('detail',$detail);
		$this->display();
	}
	
}