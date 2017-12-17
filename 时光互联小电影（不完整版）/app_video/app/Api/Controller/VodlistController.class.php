<?php
namespace Api\Controller;
use Think\Controller;

class VodlistController extends Controller {
	
    public function index(){
		$token = $_GET['token'];

		if(md5(C('API_TOKEN')) != $token){
			$this->ajaxReturn(array('code' => 0 ,'reason' => 'TOKEN错误请求非法'));
		}	
		$keyword = $_GET['keyword'];
    	if(!empty($keyword)){
    		$map['d_name'] = array('LIKE', '%'.$keyword.'%');
    	}
    	
		$count = M('vod')->where($map)->count();
		$page = new \Think\AjaxPage($count,15);
    	$show = $page->show();
    	$list = M('vod')->where($map)->order('d_id desc')->limit($page->firstRow.','.$page->listRows)->select();
		
		foreach($list as $key => $val){
			$type_ids[$val['d_type']] = $val['d_type'];
		}
        if(!empty($type_ids)){
            $type_list = M('vod_type')->where(array('t_id' => array('IN',$type_ids)))->select();
            foreach ($type_list as $key => $val) {
                $types[$val['t_id']] = $val;
            }
            $this->assign('types',$types);
        }
		$this->assign('current',$_POST['p']);
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	
    public function delete(){
		$token = $_GET('token',false);
		if(md5(C('API_TOKEN')) != $token){
			$this->ajaxReturn(array('code' => 0 ,'reason' => 'TOKEN错误请求非法'));
		}	
		$vid = $_GET['vid'];
		if($vid == 0){
			$this->ajaxReturn(array('code' => 0 ,'reason' => '参数错误'));
		}
    	M('vod')->delete($vid);
		$this->ajaxReturn(array('code' => 1 ,'reason' => '删除成功'));
	}
	
	
	
	
	
	
	
}