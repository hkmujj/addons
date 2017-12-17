<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {
    public function index(){
		$cates = M('vod_type')->select();
		$this->assign('cates',$cates);
		
		
		$new = M('vod')->field('d_id,d_name,d_pic,d_remarks,d_state,d_starring,d_year')->order('d_time desc')->limit('0,10')->select();
		foreach ($new as $key => $val) {
			if(strpos($val['d_pic'],'http') === false ){
				$new[$key]['d_pic'] = 'http://www.zmpic.com/' . $val['d_pic'];
			}	
		}
		
		$hot = M('vod')->field('d_id,d_name,d_pic,d_remarks,d_state,d_starring,d_year')->order('d_monthhits desc')->limit('0,10')->select();
		foreach ($hot as $key => $val) {
			if(strpos($val['d_pic'],'http') === false ){
				$hot[$key]['d_pic'] = 'http://www.zmpic.com/' . $val['d_pic'];
			}	
		}
		
		
		$slide = C('INDEX_SIDE');
		$this->assign('slide',$slide);
		
		
		$nav = C('FOOT_NAV');
		foreach ($nav as $key => $val) {
			$navlist[$val['id']]['hot'] =  M('vod')->field('d_id,d_name,d_pic,d_remarks,d_state,d_starring,d_year')->order('d_time desc')->limit('0,10')->select();
			$navlist[$val['id']]['new'] =  M('vod')->field('d_id,d_name,d_pic,d_remarks,d_state,d_starring,d_year')->order('d_time desc')->limit('0,10')->select();
			$navlist[$val['id']]['fen'] =  M('vod')->field('d_id,d_name,d_pic,d_remarks,d_state,d_starring,d_year')->order('d_time desc')->limit('0,10')->select();
		}
		$this->assign('navlist',$navlist);
		
		

		$this->assign('new',$new);
	
		$this->assign('hot',$hot);

		$this->assign('navbar',$nav);
		$this->display();
	}
}