<?php
namespace Api\Controller;
use Think\Controller;

class VodindexController extends Controller {
	
    public function index(){
		$token = $_GET['token'];
		if(md5(C('API_TOKEN')) != $token){
			$this->ajaxReturn(array('code' => 0 ,'reason' => 'TOKEN错误请求非法'));
		}	
	    
		$list['hot'] = M('vod')->field('d_id,d_pic,d_name,d_level,d_time')->where(array('d_level' => array('GT',0)))->order('d_level desc,d_hits desc')->limit('0,12')->select();
		
		$list['new'] = M('vod')->field('d_id,d_pic,d_name,d_level,d_time')->order('d_id desc')->limit('0,12')->select();
		
		$this->ajaxReturn(array('code' => 1 ,'reason' => 'OK' ,'result' => $list));
	
	}
	

	
	
	
}