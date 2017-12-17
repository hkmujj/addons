<?php
/**
 * 生活记账小助手模块处理程序
 *
 * @author dxf
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Dxf_jizhangModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$type = $this->message['type'];
		WeUtility::logging('debug', '我关注了1');
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
	}
}