<?php
namespace Home\Controller;
use Think\Controller;
class CatesController extends Controller {
    public function index(){
		$cates = M('vod_type')->select();
		$this->assign('cates',$cates);
		$this->display();
	}
}