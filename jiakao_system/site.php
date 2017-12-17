<?php
/**
 * 科目一模块微站定义
 *
 * @author 穿越的一只小猪
 * sir_vip@126.com
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Jiakao_systemModuleSite extends WeModuleSite {
	// 此处为科目一模拟考试的题库的图片的云存储地址，也可以配置为自己站点
	public $img_path = 'http://osingertest.qiniudn.com/';// 七牛云存储图片地址
	// public $img_path = 'addons/jiakao_system/pic/';
	public function __construct()
	{
        global $_W;
        // 加载模块配置
        $share_title = $this->getSettings('share_title');
        $_W['jiakao_share_title'] = $share_title ? $share_title : '【全国统一科目一模拟】-'.$_W['account']['name'].'公众号';
	}
	// 首页
	public function doMobileIndex() {
		global $_W;
        // 图片轮播加载
        $imglist = pdo_fetchall("select * from ".tablename('jiakao_ad_info')." where is_del = 'n' and is_show='y' order by show_order asc");
        if($imglist)
        {
            foreach ($imglist as $key=>$item) {
                $imglist[$key]['img_path'] = str_replace($_W['siteroot'], '', $item['img_path']);
                $imglist[$key]['img_path'] = $_W['siteroot'].$imglist[$key]['img_path'];
            }
        }
        else
        {
            $imglist = array();
            $imglist[] = array(
                'title' => 'qq:947233713',
                'link' => 'www.osinger.com',
                'img_path' => $_W['siteroot'].'addons/jiakao_system/common/mine_white.jpg'
                );
            $imglist[] = array(
                'title' => 'qq:947233713',
                'link' => 'www.osinger.com',
                'img_path' => $_W['siteroot'].'addons/jiakao_system/common/car1.jpg'
                );
            $imglist[] = array(
                'title' => 'qq:947233713',
                'link' => 'www.osinger.com',
                'img_path' => $_W['siteroot'].'addons/jiakao_system/common/mine2.jpg'
                );
            $imglist[] = array(
                'title' => 'qq:947233713',
                'link' => 'www.osinger.com',
                'img_path' => $_W['siteroot'].'addons/jiakao_system/common/car2.jpg'
                );
        }
		// $this->check_login();
		include $this->template('index');
	}
	// 顺序答题
	public function doMobileSequent()
	{
		global $_W;
        $this->check_login();
		include $this->template('fn_sequent');
	}
	// 模拟考试 随机100题目
	public function doMobileImitate() {
		global $_W;
		$this->check_login();
		//这个操作被定义用来呈现 功能封面
		//生成100个题目的id
		$id_arr = array();
		for($i=0;$i<100;$i++)
		{
			$id= rand(1,898);
			if($id_arr[$id])
			{
			continue;
				$i--;
			}
			else
			{
				$id_arr[] = $id;
			}
		}
		
		$t_id_arr = json_encode($id_arr);
		include $this->template('fn_imitate');
	}
	// 随机10题
	public function doMobileRandomten() {
		global $_W;
		$this->check_login();
		//这个操作被定义用来呈现 功能封面
		$id_arr = array();
		for($i=0;$i<10;$i++)
		{
			$id= rand(1,898);
			if($id_arr[$id])
			{
				continue;
				$i--;
			}
			else 
			{
				$id_arr[] = $id;
			}
		}
		
		$t_id_arr = json_encode($id_arr);
		include $this->template('fn_random_ten');
	}
	// 考试的易错题
	public function doMobileMyct() {
		global $_W;
		$this->check_login();
		//这个操作被定义用来呈现 规则列表
		include $this->template('fn_my_ct');
	}
	// 我的易错题
	public function doMobileMywrong()
	{
		global $_W;
		$this->check_login();
		//这个操作被定义用来呈现 规则列表
		include $this->template('fn_my_wrong');
	}

	/*
	根据题号得到题目信息
	*/
	protected function getinfo($id)
	{
		//$r = pdo_fetchall("SELECT * FROM ".tablename('jiakao_tiku')." WHERE t_id = :t_id", array(':t_id' => '$id'));
		$r = pdo_fetchall("select * from ".tablename('jiakao_tiku')." where t_id='$id' ");
		return $r[0];
	}
	/*
	用于获取顺序答题的内容信息的处理ajax返回
	*/
	public function doMobileSequentAjax()
	{
		global $_W;
		// $this->check_login();
		$t_id = $_POST["t_id"];
		$t_id<0 && $t_id =0;
		$t_id>898 && $t_id =0;

		$info = $this->getinfo($t_id);

		if($info === false)
		{
			//获取题目信息失败
		}
		else 
		{
			$item['id'] = $info['id'];
			$item['t_id'] = $info['t_id'];//试题编号
			$item['sort'] = $info['sort'];//试题类别
			$item['type'] = $info['type'];//试题类型
			$item['title'] = $info['title'];//题目
			$item['pic'] = $info['pic'];//图片名称
			if($item['pic'])
			{
				// $item['pic_url'] = $_W['siteroot'].$this->img_path.$info['pic'].".png";//图片地址
				$item['pic_url'] = $this->img_path.$info['pic'].".png";//图片地址
			}
			else 
			{
				$item['pic_url'] = '';
			}
			
			$item['s_a'] = $info['s_a'];
			$item['s_b'] = $info['s_b'];
			$item['s_c'] = $info['s_c'];
			$item['s_d'] = $info['s_d'];
			if($item['type'] == "选择题")
			{
				$item['type_desc'] = "0";
			}
			elseif($item['type'] == "判断题")
			{
				$item['type_desc'] = "1";
			}
			$item['answer'] = $info['answer'];//答案
			$item['belong'] = $info['belong'];//试题大类
			$item['percent'] = $info['percent'];
		}
		
		echo json_encode($item);
	}    
    /**
     * 判断是否关注了该公众号
     * A分享了连接a，A点击a可以进入
     * A分享了连接a给B，B未关注，B点击a无法进入
     * A分享了连接a给B，B关注，B点击a无法进入
     * A分享了连接a给B，B关注，并且触发过模块，B点击a可以进入
     */
    public function check_login()
    {
        global $_W;
        $keyword = $this->getKeyWord();

        if(empty($_W['openid']))
        {
            include $this->template('share');
            exit();
        }
        // 如果存在openid，要从mc_mapping_fans表中查询是否在当前子公众号下已关注
        $info = pdo_fetch("select * from ".tablename('mc_mapping_fans')." where openid=:openid and uniacid=:uniacid and acid=:acid", array(':openid'=>$_W['openid'],':uniacid'=>$_W['account']['uniacid'],':acid'=>$_W['account']['acid']));

        if($info === false)
        {
            include $this->template('share');
            exit();
        }
    }
    /*
    得到当前模块的回复关键字
    */
    public function getKeyWord()
    {
    	 $keyinfo = pdo_fetch("select rk.content from ".tablename('cover_reply')." as cr inner join ".tablename('rule_keyword')." as rk on rk.rid=cr.rid where cr.module = :module",array(":module"=>'jiakao_system'));
    	 if($keyinfo === false)
    	 {
    	 	return '';
    	 }
    	 else
    	 {
    	 	return $keyinfo['content'];
    	 }
    }

    /* =================================
    广告管理模块入口
    1、上传图片
    2、图片设置地址
    */
    public function doWebAd_manage()
    {
    	global $_W,$_GPC;

    	// echo realpath('../addons/jiakao_system/upload/images');
        // 获取广告图片列表
        $list = pdo_fetchall("select * from ".tablename('jiakao_ad_info')." where is_del = 'n' and is_show='y' order by show_order asc");
        $list_n = pdo_fetchall("select * from ".tablename('jiakao_ad_info')." where is_del = 'n' and is_show='n'");
        $list = array_merge($list,$list_n);
        if($list)
        {
            foreach ($list as $key => $item) {
                $list[$key]['img_path'] = str_replace($_W['siteroot'], '', $item['img_path']);
                $list[$key]['img_path'] = $_W['siteroot'].$list[$key]['img_path'];
            }
        }

        // 操作验证码
        $token = md5(date('Y-m-d H:i:s'));
        $_SESSION['ad_token'] = $token;
    	include $this->template('ad_manage');
    }
    /*
    图片列表页面
    */
    public function doWebAd_list()
    {
    	global $_W,$_GPC;
    	include $this->template('ad_list');
    }
    /*
    广告图片上传方法
    array(1) {
	  ["ad_img"]=>
	  array(5) {
	    ["name"]=>
	    string(40) "C617E294-E90F-4EBD-BD6C-074DA9CF04BE.jpg"
	    ["type"]=>
	    string(24) "application/octet-stream"
	    ["tmp_name"]=>
	    string(24) "/data/home/tmp/phpDbTTtm"
	    ["error"]=>
	    int(0)
	    ["size"]=>
	    int(43991)
	  }
	}

    */
    public function doWebAd_ajax()
    {
    	global $_GPC,$_W;
    	error_reporting(E_ALL);
    	// 上传文件ad_img
    	if(!isset($_FILES['ad_img']))
    	{
    		header("HTTP/1.1 500 Internal Server Error");
    		header("Content-type:text/json");
    		echo json_encode(array('res'=>'101','msg'=>'upload failure'));
    		exit;
    	}
    	// 判断文件的大小
    	if($_FILES['ad_img']['size'] > 1024*1024)
    	{
    		header("HTTP/1.1 500 Internal Server Error");
    		header("Content-type:text/json");
    		echo json_encode(array('res'=>'101','msg'=>'upload failure'));
    		exit;
    	}
    	// 生成文件名称
    	$prefix = substr($_FILES['ad_img']['name'], strpos($_FILES['ad_img']['name'],'.'));
    	$filename = date('YmdHis').$prefix;
    	// 上传文件
    	if(file_exists('../addons/jiakao_system/upload/images/'.$filename))
    	{
    		$filename = date('YmdHis').'_'.rand(0,9).$prefix;
    	}
    	// 移动文件upload
    	// header("Content-type : text/json");
    	// echo realpath('../addons/jiakao_system/upload/images/');
    	// Warning: move_uploaded_file(): open_basedir restriction in effect. File() 
    	// is not within the allowed path(s): (/data/home/hxu0180476/:/usr/home/hxu0180476/:/data/home/tmp/:/usr/home/tmp/:/var/www/disablesite/:/tmp/) in /data/home/hxu0180476/htdocs/addons/jiakao_system/site.php on line 268
    	// echo realpath('../addons/jiakao_system/upload/images/');
    	// $r = move_uploaded_file($_FILES['ad_img']['tmp_name'], realpath('../addons/jiakao_system/upload/images/').'/'.$filename);
    	$r = move_uploaded_file($_FILES['ad_img']['tmp_name'], '../addons/jiakao_system/upload/images/'.$filename);
    	if($r)
    	{
    		$url = $_W['siteroot'].'addons/jiakao_system/upload/images/'.$filename;
    		header("Content-type:text/json");
    		echo json_encode(array('res'=>'100','msg'=>'upload success','url'=>$url));
    		exit;
    	}
    	else
    	{
    		header("HTTP/1.1 500 Internal Server Error");
    		header("Content-type:text/json");
    		echo json_encode(array('res'=>'101','msg'=>'upload failure'));
    		exit;
    	}
    }
    /*
    保存广告信息
    add
    edit
    id,type,imgurl,link,title
    create table ims_jiakao_ad_info
    (
		id int(10) not null auto_increment,
		title varchar(100) not null comment '标题',
		link varchar(1000) not null comment '跳转链接',
		img_path varchar(200) not null comment '图片地址',
		is_show enum('y','n') default 'n' comment '上架状态',
		view_times int(10) not null comment '点击访问次数',
		create_time datetime,
		primary key (`id`)
    )engine=innodb default charset=utf8 comment '驾考广告轮播信息表'
    */
    public function doWebAd_save()
    {
    	header("Content-type:text/json");
    	
    	global $_GPC,$_W;

    	$addData['title'] = $_GPC['title'];
    	$addData['link'] = $_GPC['link'];
    	$addData['img_path'] = $_GPC['imgurl'] ? str_replace($_W['siteroot'],'', $_GPC['imgurl']) : '';
    	$addData['create_time'] = date('Y-m-d H:i:s');
    	$addData['is_show'] = 'n'; // 上架状态默认：n
    	$addData['view_times'] = '0'; // 点击次数统计
        // 排序
        $res = $this->createOrder('add');
        if($res && $res['res'] == '100')
        {
            $addData['show_order'] = $res['max_order'] + 1;
        }
        else
        {
            echo json_encode(array('res'=>'101','msg'=>$res['msg']));
            exit;
        }

    	// 为空判断
    	if($_GPC['type'] == '' || $_GPC['imgurl'] == '')
    	{
    		echo json_encode(array('res'=>'101','msg'=>'param is error'));
    		exit;
    	}

    	// 新增广告
    	if($_GPC['type'] == 'add')
    	{
    		$r = pdo_insert('jiakao_ad_info',$addData);
    		if($r)
    		{
    			echo json_encode(array('res'=>'100','msg'=>'add success'));
    			exit;
    		}
    		else
    		{
    			echo json_encode(array('res'=>'101','msg'=>'insert error'));
    			exit;
    		}
    	}
    	elseif($_GPC['type'] == 'edit') // 更新广告
    	{
    		if($_GPC['id'] == '')
    		{
    			echo json_encode(array('res'=>'101','msg'=>'missing id'));
    			exit;
    		}
    		$updateData['title'] = $_GPC['title'];
    		$updateData['link'] = $_GPC['link'];// 更新只能更新标题和链接，不允许更新图片地址

    		$r = pdo_update('jiakao_ad_info',$updateData,array('id'=>$_GPC['id']));
    		if($r === false)
    		{
    			echo json_encode(array('res'=>'101','msg'=>'update error'));
                exit;
    		}
    		else
    		{
                echo json_encode(array('res'=>'100','msg'=>'update success'));
                exit;
    		}
    	}
    }
    /*
    上下架操作
    */
    public function doWebAd_show()
    {
        global $_W,$_GPC;

        header("Content-type:text/json");
        
        // id检测
        if($_GPC['id'] == '')
        {
            echo json_encode(array('res'=>'101','msg'=>'id error'));
            exit;
        }
        // type检测
        if($_GPC['type'] == 'on' || $_GPC['type'] == 'off')
        {
            $is_show = $_GPC['type'] == 'on' ? 'y' : 'n';

            // 排序
            $res = $this->createOrder('show_'.$is_show,$_GPC['id']);
            if($res && $res['res'] == '100')
            {
                // 如果是show_n是不会返回max_order的，这时候$show_order默认为0
                // 如果是show_y，返回max_order，将值+1
                $show_order = isset($res['max_order']) ? $res['max_order']+1 : '0';
            }
            else
            {
                echo json_encode(array('res'=>'101','msg'=>$res['msg']));
                exit;
            }
            // 更新记录
            $r = pdo_update('jiakao_ad_info',array('is_show'=>$is_show,'show_order'=>$show_order),array('id'=>$_GPC['id']));
            if($r !== false)
            {
                echo json_encode(array('res'=>'100','msg'=>'success'));
                exit;
            }
            else
            {
                echo json_encode(array('res'=>'101','msg'=>'operate error'));
                exit;
            }
        }
        else
        {
            echo json_encode(array('res'=>'101','msg'=>'type error'));
            exit;
        }
    }
    /*
    删除广告位
    */
    public function doWebAd_delete()
    {
        global $_W,$_GPC;
        header("Content-type:text/json");
        // token验证
        /*
        if($_GPC['token'] != $_SESSION['ad_token'])
        {
            echo json_encode(array('res'=>'101','msg'=>'token error'));
            exit;
        }
        */
        if($_GPC['id'] == '')
        {
            echo json_encode(array('res'=>'101','msg'=>'id error'));
            exit;
        }
        // 排序
        $res = $this->createOrder('delete',$_GPC['id']);
        if($res && $res['res'] != '100')
        {
            echo json_encode(array('res'=>'101','msg'=>$res['msg']));
            exit;
        }
        $r = pdo_update('jiakao_ad_info',array('is_del'=>'y','is_show'=>'n'),array('id'=>$_GPC['id']));
        if($r === false)
        {
            echo json_encode(array('res'=>'101','msg'=>'delete error'));
            exit;
        }
        else
        {
            echo json_encode(array('res'=>'100','msg'=>'success'));
            exit;
        }
    }
    /*
    排序模块
    */
    public function doWebAd_order()
    {
        global $_W,$_GPC;
        header("Content-type:text/json");

        if($_GPC['id'] == '' || $_GPC['type'] == '')
        {
            echo json_encode(array('res'=>'101','msg'=>'param error'));
            exit;
        }

        // 排序实现，获取当前的排序
        $info = pdo_fetch("select * from ".tablename('jiakao_ad_info')." where id=:id",array(':id'=>$_GPC['id']));
        if($info === false)
        {
            echo json_encode(array('res'=>'101','msg'=>'get native id failure'));
            exit;
        }
        // 获取当前的排序(未删除、已上架)
        $orderlist = pdo_fetchall("select id,show_order from ".tablename('jiakao_ad_info')." where is_del = 'n' and is_show='y' order by show_order asc");
        if($orderlist === false)
        {
            echo json_encode(array('res'=>'101','msg'=>'get native orderlist failure'));
            exit;
        }
        // 最后一个元素
        $end = end($orderlist);
        // 是否可以移动
        if($info['show_order'] == $orderlist[0]['show_order'] && $_GPC['type'] == 'up')
        {
            echo json_encode(array('res'=>'101','msg'=>'已经是第一位'));
            exit;
        }
        elseif($info['show_order'] == $end['show_order'] && $_GPC['type'] == 'down')
        {
            echo json_encode(array('res'=>'101','msg'=>''));
            exit;
        }
        
        // 向上移动
        if($_GPC['type'] == 'up')
        {
            // 将大于show_order的值进行+1操作
            $r = pdo_update('jiakao_ad_info',array('show_order'=>$info['show_order']),array('show_order'=>$info['show_order']-1));
            if($r === false)
            {
                echo json_encode(array('res'=>'101','msg'=>'move up : update show_order+1 failure'));
                exit;
            }
            // 将自己设置为new_order
            $r = pdo_update('jiakao_ad_info',array('show_order'=>$info['show_order']-1),array('id'=>$_GPC['id']));
            if($r === false)
            {
                echo json_encode(array('res'=>'101','msg'=>'move up : update show_order self failure'));
                exit;
            }
            echo json_encode(array('res'=>'100','msg'=>'move up : success'));
            exit;
        }
        elseif($_GPC['type'] == 'down')
        {
            // 将大于show_order的值进行-1操作
            $r = pdo_update('jiakao_ad_info',array('show_order'=>$info['show_order']),array('show_order'=>$info['show_order']+1));
            if($r === false)
            {
                echo json_encode(array('res'=>'101','msg'=>'move down : update show_order+1 failure'));
                exit;
            }
            // 将自己设置为new_order
            $r = pdo_update('jiakao_ad_info',array('show_order'=>$info['show_order']+1),array('id'=>$_GPC['id']));
            if($r === false)
            {
                echo json_encode(array('res'=>'101','msg'=>'move down : update show_order self failure'));
                exit;
            }
            echo json_encode(array('res'=>'100','msg'=>'move down : success'));
            exit;
        }
    }
    // 排序功能支持
    public function createOrder($type,$id = '')
    {
        // 添加广告，重新排序（排序倒最末尾）
        // 上架重新排序（排序倒最末尾）

        if($type == 'add' || $type == 'show_y')
        {
            // 获取当前最大的showorder
            $order_max_info = pdo_fetch("select show_order from ".tablename('jiakao_ad_info')." where is_del = 'n' and is_show = 'y' order by show_order desc limit 1",array());
            $max_order = $order_max_info['show_order'] ? $order_max_info['show_order'] : '0';
            return array('res'=>'100','msg'=>'success','max_order'=>$max_order);
        }
        // 删除广告，重新排序(从排序中剔除)
        if($type=='delete')
        {
            if($id == '')
            {
                return array('res'=>'101','msg'=>'delete :id error');
            }
            // 获取该条记录排序
            $info = pdo_fetch("select show_order from ".tablename('jiakao_ad_info')." where is_del='n' and id=:id",array(':id'=>$id));
            if($info === false)
            {
                return array('res'=>'101','msg'=>'delete :get native show_order failure');
            }
            // 将该条记录的show_order置为0
            $updateinfo = pdo_update('jiakao_ad_info',array('show_order'=>0),array('id'=>$id));
            if($updateinfo === false)
            {
                return array('res'=>'101','msg'=>'delete :update native show_order failure');
            }
            // 排序1，2，3，4，5，其中1，排在最前面;将所有大于当前的show_order-1
            $updateinfo_all = pdo_query("update ".tablename('jiakao_ad_info')." set show_order = show_order-1 where is_del='n' and is_show='y' and show_order>'{$info['show_order']}'");
            if($updateinfo_all === false)
            {
                return array('res'=>'101','msg'=>'delete :update  show_order failure');
            }
            return array('res'=>'100','msg'=>'delete :udpate  show_order success');
        }
        // 下架重新排序
         if($type=='show_n')
        {
            if($id == '')
            {
                return array('res'=>'101','msg'=>'show_n :id error');
            }
            // 获取该条记录排序
            $info = pdo_fetch("select show_order from ".tablename('jiakao_ad_info')." where is_del='n' and id=:id",array(':id'=>$id));

            // 将该条记录的show_order置为0
            $updateinfo = pdo_update('jiakao_ad_info',array('show_order'=>'0'),array('id'=>$id));
            if($updateinfo === false)
            {
                return array('res'=>'101','msg'=>'show_n :update native show_order failure');
            }
            // 排序1，2，3，4，5，其中1，排在最前面;将所有大于当前的show_order-1
            $updateinfo_all = pdo_query("update ".tablename('jiakao_ad_info')." set show_order = show_order-1 where is_del='n' and is_show='y' and show_order>'{$info['show_order']}'");
            if($updateinfo_all === false)
            {
                return array('res'=>'101','msg'=>'show_n :update  show_order failure');
            }
            return array('res'=>'100','msg'=>'show_n :udpate  show_order success');
        }

    }

    /*
    高级配置
    配置分享title图片，以及其他
    */
    public function doWebAd_settings()
    {
        global $_W,$_GPC;
        include $this->template('ad_settings');
    }
    public function doWebAd_settings_ajax()
    {
        global $_GPC,$_W;
        header("content-type:text/json");
        // 
        if($_GPC['key'] == '')
        {
            echo json_encode(array('res'=>'101','msg'=>'key error'));
            exit;
        }
        if($this->setSettings($_GPC['key'],$_GPC['val']))
        {
            echo json_encode(array('res'=>'100','msg'=>'set success'));
        }
        else
        {
            echo json_encode(array('res'=>'101','msg'=>'insert error'));
        }
    }
    // 设置配置
    public function setSettings($key,$val)
    {
        $time = date("Y-m-d H:i:s");
        return pdo_query("replace into ".tablename('jiakao_settings')." (s_key,s_val,add_time) values ('{$key}','{$val}','{$time}')");
    }
    // 读取配置
    public function getSettings($key)
    {
        $r = pdo_fetch("select s_val from ".tablename('jiakao_settings')." where s_key=:s_key",array(":s_key"=>$key));
        if($r)
        {
            return $r['s_val'];
        }
        return $r;
    }
    //---end

}
















