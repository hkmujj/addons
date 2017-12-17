<?php
/**
 * 金梧桐模块模块订阅器
 *
 * @author dxf
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Dxf_jwtModuleReceiver extends WeModuleReceiver {
	public function receive() {
		$type = $this->message['type'];
		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看微信文档来编写你的代码
	}
}