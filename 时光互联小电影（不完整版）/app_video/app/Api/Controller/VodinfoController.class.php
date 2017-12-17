<?php
namespace Api\Controller;
use Think\Controller;

class VodinfoController extends Controller {
	
    public function index(){
		$token = $_GET['token'];
		if(md5(C('API_TOKEN')) != $token){
			$this->ajaxReturn(array('code' => 0 ,'reason' => 'TOKEN错误请求非法'));
		}	
		
		$id = $_GET['id'];
		if(!$id){
			$this->ajaxReturn(array('code' => 0 ,'reason' => 'ID参数错误'));
		}
    	$vod = M('vod')->find($id);

		$this->ajaxReturn(array('code' => 1 ,'reason' => 'OK' ,'result' => $vod));
		
	}
	

	
	
	
}