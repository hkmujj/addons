<?php
/**
 * 便利店模块处理程序
 *
 * @author Gorden
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
include IA_ROOT . "/addons/cyl_vip_video/model.php";
class Cyl_vip_videoModuleProcessor extends WeModuleProcessor{
    public function respond(){
    	global $_W, $_GPC;
    	$content = $this->message['content'];
        if(!$this->inContext) {
			$news = '请输入关键词搜索';	
			$this->beginContext(1800);
			return $this->respText($news);	
			// 如果是按照规则触发到本模块, 那么先输出提示问题语句, 并启动上下文来锁定会话, 以保证下次回复依然执行到本模块
		} else {
						
	        // 这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码 
			if ($content) {
				$list = caiji_list($content);
			}
			if (empty($list)) {
				return $this->respText('未搜索到关键词，请重新输入全称');	
			}			
		    $news = array();
		    $i = 1;		    
	        foreach ($list as $key=>$item) {
	        	if ($item['type'] == '[动漫]') {
	        		$op = 'dongman';
	        	}elseif ($item['type'] == '[电视剧]') {
	        		$op = 'dianshi';
	        	}elseif ($item['type'] == '[综艺]') {
	        		$op = 'zongyi';
	        	}else{
	        		$op = 'dianying';
	        	}
	        	if ($item['title'] && $item['btn'] != '暂无播放资源') {
	        		if ($i <= 8) {
	        			$news[] = array(
		                'title' => strip_tags($item['type'].$item['title'].'-'.$item['actor'].'-'.$item['director']),
		                'description' => strip_tags($item['p1']),
		                'url' => $this->createMobileUrl('detail',array('op'=>$op,'url'=>$item['link'])),
		                'picurl' => $item['img']
		           	 	);
	        		}
	        		$i++;   
	        	}
	        }
	        $this->endContext();	          	
	        
        }
        return $this->respNews($news);
    }
}