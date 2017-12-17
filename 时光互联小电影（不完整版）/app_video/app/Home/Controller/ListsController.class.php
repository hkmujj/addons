<?php
namespace Home\Controller;
use Think\Controller;

class ListsController extends Controller {
	
	
    public function index(){
    	$id = (int) I('id'); 
		$cate = M('vod_type')->find($id);
		if($cate['t_pid'] == 0){
			$childs = M('vod_type')->where(array('t_pid' => $id))->select();
		}
		
		
		$this->assign('cate',$cate);
		$this->assign('childs',$childs);
		
		
		$this->display();
	}
	
	
	
    public function ajaxlist(){
	
		$map = array();

		$cat = (int) $_POST['cat'];
		if($cat != 0){
			$cate = M('vod_type')->find($cat);
			if($cate['t_pid'] == 0){
				$childs = M('vod_type')->where(array('t_pid' => $cat))->select();
				foreach ($childs as $key => $val) {
					$cate_ids[] = $val['t_id'];
				}
				$map['d_type'] = array('IN',$cate_ids);
			}else{
				$map['d_type'] = $cat;
			}
		}
		
		$total = M('vod')->where($map)->count();
	
	
		$list = M('vod')->where($map)->field('d_id,d_name,d_pic,d_remarks,d_state,d_starring,d_year')->page($_POST['p'].',10')->order('d_time desc')->select();
	
		foreach ($list as $key => $val) {
			if(strpos($val['d_pic'],'http') === false ){
				$list[$key]['d_pic'] = 'http://www.zmpic.com/' . $val['d_pic'];
			}	
		}

		$this->ajaxReturn(array('code' => '1', 'total' => $total ,'result' =>$list,'map' => $map));
	}
	
	
	
	
}