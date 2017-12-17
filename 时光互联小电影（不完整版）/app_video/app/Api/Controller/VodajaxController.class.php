<?php
namespace Api\Controller;
use Think\Controller;

class VodajaxController extends Controller {
	
    public function index(){
		$token = $_GET['token'];
		if(md5(C('API_TOKEN')) != $token){
			$this->ajaxReturn(array('code' => 0 ,'reason' => 'TOKEN错误请求非法'));
		}	
		$keyword = $_GET['keyword'];
    	if(!empty($keyword)){
    		$map['d_name'] = array('LIKE', '%'.$keyword.'%');
    	}
		
		
		$cat = $_GET['id'];
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
		$this->ajaxReturn(array('code' => 1 ,'total' => $count ,'result' => $list));
	}
	
}