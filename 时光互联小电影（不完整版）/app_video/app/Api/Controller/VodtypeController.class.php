<?php
namespace Api\Controller;
use Think\Controller;

class VodtypeController extends Controller {
	
    public function index(){
		$token = $_GET['token'];
		if(md5(C('API_TOKEN')) != $token){
			$this->ajaxReturn(array('code' => 0 ,'reason' => 'TOKEN错误请求非法'));
		}
		
	
		$list = M('vod_type')->field('t_id,t_name,t_hide,t_enname,t_des,t_title,t_key,t_pid')->where($map)->order('t_id asc')->select();
		$this->ajaxReturn(array('code' => 1 ,'reason' => '查询成功', 'result' => $list));
	}
	
	
	
	
	
	
	
}