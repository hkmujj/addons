<?php
namespace Home\Controller;
use Think\Controller;

class SearchController extends Controller {
	
	
    public function index(){

		$this->display();
	}
	
	
	
    public function ajaxlist(){
	
		$map = array();
		
		$keyword = $_POST['keyword'];
        if (!empty($keyword)) {
            $map['d_name'] = array('LIKE', '%' . $keyword . '%');
        }
		
		$total = M('vod')->where($map)->count();
	
	
		$list = M('vod')->where($map)->field('d_id,d_name,d_pic,d_remarks,d_state,d_starring,d_year')->page($_POST['p'].',10')->order('d_time desc')->select();
	
		$this->ajaxReturn(array('code' => '1', 'total' => $total ,'result' =>$list,'map' => $cat));
	}
	
	
	
	
}