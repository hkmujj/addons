<?php

//decode by QQ:270656184 http://www.yunlu99.com/
defined('IN_IA') or die('Access Denied');
include IA_ROOT . "/addons/cyl_vip_video/model.php";
class Cyl_vip_videoModuleSite extends WeModuleSite
{
	public function __construct()
	{
		global $_W, $_GPC;
		load()->model('mc');
		if (empty($_W['fans']['nickname'])) {
			$fans = mc_oauth_userinfo();
		}
		$member = member($_W['openid']);
		if (empty($member) && $_W['openid']) {
			$data = array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'uid' => $_W['fans']['uid'], 'nickname' => $_W['fans']['tag']['nickname'], 'avatar' => $_W['fans']['tag']['avatar'], 'time' => TIMESTAMP);
			if (empty($data['avatar'])) {
				$data['avatar'] = $fans['headimgurl'];
			}
			if (empty($data['nickname'])) {
				$data['nickname'] = $fans['nickname'];
			}
			if ($data['avatar'] && $data['nickname']) {
				pdo_insert('cyl_vip_video_member', $data);
			}
		}
	}
	public function doMobileDiscover()
	{
		global $_W, $_GPC;
		$settings = $this->module['config'];
		$acc = WeAccount::create();
		$member = member($_W['openid']);
		$num = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('cyl_vip_video') . " WHERE uniacid = :uniacid AND openid = :openid ", array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
		$account_api = WeAccount::create();
		$info = $account_api->fansQueryInfo($_W['openid']);
		$hdp = pdo_getall('cyl_vip_video_hdp', array('uniacid' => $_W['uniacid']), array(), '', 'sort DESC , id DESC');
		$record = pdo_fetch("SELECT * FROM " . tablename('cyl_vip_video') . " WHERE uniacid = :uniacid AND openid = :openid ORDER BY id DESC", array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
		if (TIMESTAMP > $member['end_time'] && $member['is_pay'] == 1) {
			pdo_update('cyl_vip_video_member', array('end_time' => null, 'is_pay' => 0), array('openid' => $member['openid']));
			$data = array('first' => array('value' => '您好,' . $member['nickname'] . '您的会员已到期', 'color' => '#ff510'), 'keyword1' => array('value' => '会员到期', 'color' => '#ff510'), 'keyword2' => array('value' => '到期提醒', 'color' => '#ff510'), 'remark' => array('value' => '点击详情开通', 'color' => '#ff510'));
			$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'member', 'm' => 'cyl_vip_video')), '.');
			$acc->sendTplNotice($member['openid'], $settings['tpl_id'], $data, $url, $topcolor = '#FF683F');
		}
		if (checksubmit()) {
			$url = $_GPC['url'];
			$c = explode('m.v.qq', $url);
			if (count($c) > 1) {
				$url = 'https://v.qq' . $c['1'];
			}
			if (!isUrl($url)) {
				message('输入的网页地址错误，请重新输入,检查是否含有http://');
			}
			if ($num >= $settings['free_num'] && $member['is_pay'] == 0) {
				message('您的免费观看次数已用完，请点击确定开通会员，无限制观看', $this->createMobileUrl('member', array('op' => 'open')), 'error');
			}
			$video = pdo_get('cyl_vip_video', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'video_url' => $url));
			if (!$url) {
				message('请输入链接');
			}
			if ($video) {
				message('这个视频您之前提交过了，点击确定跳转继续观看', $this->createMobileUrl('detail', array('url' => $url, 'index' => 1)), 'success');
			}
			$html = file_get_contents($url);
			$title = str_substr("<title>", "</title>", $html);
			$res = pdo_insert('cyl_vip_video', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'uid' => $_W['fans']['uid'], 'title' => $title, 'video_url' => $url, 'time' => TIMESTAMP, 'share' => $_GPC['share'], 'index' => 1));
			$video_url = $this->createMobileUrl('detail', array('url' => $url, 'index' => 1));
			Header("Location: {$video_url}");
			die;
		}
		include $this->template('discover');
	}
	public function doMobileIndex()
	{
		global $_W, $_GPC;
		$account_api = WeAccount::create();
		$op = $_GPC['op'] ? $_GPC['op'] : 'index';
		$pid = $_GPC['pid'];
		$settings = $this->module['config'];
		$num = $settings['list'] ? $settings['list'] : 6;
		$member = member($_W['openid']);
		//统计浏览量
        $time=date('Ymd',time());
        pdo_query("UPDATE ".tablename('dxf_ycj_ip')." SET ip_dy=ip_dy+1  WHERE time = :time ", array( ':time' => $time));
		
		$jilu = pdo_getall('cyl_vip_video', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']), array(), '', 'id DESC limit 10');
		$hdp = pdo_getall('cyl_vip_video_hdp', array('uniacid' => $_W['uniacid'], 'type' => $op), array(), '', 'sort DESC , id DESC');
		$category = pdo_fetchall("SELECT * FROM " . tablename('cyl_vip_video_category') . " WHERE uniacid = '{$_W['uniacid']}' AND parentid = 0 ORDER BY parentid ASC, displayorder ASC, id ASC ", array(), 'id');
		$parent = array();
		$children = array();
		if (!empty($category)) {
			$children = '';
			foreach ($category as $cid => $cate) {
				if (!empty($cate['parentid'])) {
					$children[$cate['parentid']][] = $cate;
				} else {
					$parent[$cate['id']] = $cate;
				}
			}
		}
		if ($op == 'index') {
			$time = cache_load('cyl_vip_video:time');
			$data = pdo_getall('cyl_vip_video_manage', array('uniacid' => $_W['uniacid']), array(), '', 'id DESC');
			if (TIMESTAMP - $time > 3600) {
				$dianying = index_list($url, 'dianying');
				$dianshi = index_list($url, 'dianshi');
				$zongyi = index_list($url, 'zongyi');
				$dongman = index_list($url, 'dongman');
				cache_write('cyl_vip_video:time', TIMESTAMP);
				cache_write('cyl_vip_video:dianying', $dianying);
				cache_write('cyl_vip_video:dianshi', $dianshi);
				cache_write('cyl_vip_video:zongyi', $zongyi);
				cache_write('cyl_vip_video:dongman', $dongman);
			} else {
				$dianying = cache_load('cyl_vip_video:dianying');
				$dianshi = cache_load('cyl_vip_video:dianshi');
				$zongyi = cache_load('cyl_vip_video:zongyi');
				$dongman = cache_load('cyl_vip_video:dongman');
			}
			include $this->template('news/index');
		} else {
			if ($op > 0) {
				$url = $category[$op]['url'];
				if ($url) {
					$data = youku($url);
				} else {
					$data = pdo_getall('cyl_vip_video_manage', array('uniacid' => $_W['uniacid'], 'cid' => $op), array(), '', 'id DESC');
				}
				$cat = pdo_getall('cyl_vip_video_category', array('uniacid' => $_W['uniacid'], 'parentid' => $op), array(), '', 'id DESC');
			} else {
				$url = $_GPC['url'];
				$num = $_GPC['num'] ? $_GPC['num'] : 0;
				$rank = $_GPC['rank'] ? $_GPC['rank'] : 'rankhot';
				if ($_GPC['cat'] || $_GPC['act'] || $_GPC['year'] || $_GPC['area'] || $rank) {
					$url = "http://www.360kan.com/{$op}/list.php?rank={$rank}&year={$_GPC['year']}&area={$_GPC['area']}&act={$_GPC['act']}&cat={$_GPC['cat']}&pageno={$num}";
				} else {
					$url = "http://www.360kan.com/{$op}/list.php?rank={$rank}&cat=all&area=all&act=all&year=all&pageno={$num}";
				}
				$data = discover($url);
				$discover_time = cache_load('discover:time' . $op . $rank);
				if (TIMESTAMP - $discover_time > 7200) {
					$cat = $data['1'];
					$year = $data['2'];
					$area = $data['3'];
					$star = $data['4'];
					cache_write('discover:time' . $op . $rank, TIMESTAMP);
					cache_write('discover:cat' . $op . $rank, $cat);
					cache_write('discover:year' . $op . $rank, $year);
					cache_write('discover:area' . $op . $rank, $area);
					cache_write('discover:star' . $op . $rank, $star);
				} else {
					$cat = cache_load('discover:cat' . $op . $rank);
					$year = cache_load('discover:year' . $op . $rank);
					$area = cache_load('discover:area' . $op . $rank);
					$star = cache_load('discover:star' . $op . $rank);
				}
			}
			if ($_GPC['type'] == 'json') {
				if ($op > 0) {
					$num = isset($_GPC['page']) ? $_GPC['page'] : 2;
					$pageindex = 50;
					if (!empty($_GPC['keyword'])) {
						$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
					}
					if (!empty($_GPC['pcate'])) {
						$pcate = intval($_GPC['pcate']);
						$condition .= " AND pcate = {$pcate}";
					}
					if (!empty($_GPC['ccate'])) {
						$ccate = $_GPC['ccate'];
						$condition .= " AND ccate = {$ccate}";
					}
					$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('cyl_vip_video_manage') . " WHERE uniacid = {$_W['uniacid']} {$condition}");
					$data = pdo_fetchall("SELECT * FROM " . tablename('cyl_vip_video_manage') . " WHERE uniacid = '{$_W['uniacid']}' {$condition} ORDER BY id DESC LIMIT " . ($num - 1) * $pageindex . ',' . $pageindex);
				} else {
					$url = $_GPC['url'];
					$num = $_GPC['num'];
					if ($_GPC['cat'] || $_GPC['year'] || $_GPC['act']) {
						$url = "http://www.360kan.com/{$op}/list.php?rank={$rank}&year={$_GPC['year']}&area={$_GPC['area']}&act={$_GPC['act']}&cat={$_GPC['cat']}&pageno={$num}";
					} else {
						$url = "http://www.360kan.com/{$op}/list.php?rank={$rank}&cat=all&area=all&act=all&year=all&pageno={$num}";
					}
					$data = discover($url);
					$category = $data['1'];
					$year = $data['2'];
					$area = $data['3'];
					$star = $data['4'];
					$data = $data['0'];
				}
				include $this->template('discover_json');
				die;
			}
			if (TIMESTAMP > $member['end_time'] && $member['is_pay'] == 1) {
				pdo_update('cyl_vip_video_member', array('end_time' => null, 'is_pay' => 0), array('openid' => $member['openid']));
				$data = array('first' => array('value' => '您好,' . $member['nickname'] . '您的会员已到期', 'color' => '#ff510'), 'keyword1' => array('value' => '会员到期', 'color' => '#ff510'), 'keyword2' => array('value' => '到期提醒', 'color' => '#ff510'), 'remark' => array('value' => '点击详情开通', 'color' => '#ff510'));
				$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'member', 'm' => 'cyl_vip_video')), '.');
				$account_api->sendTplNotice($member['openid'], $settings['tpl_id'], $data, $url, $topcolor = '#FF683F');
			}
			include $this->template('news/index');
		}
	}
	public function doMobileDetail()
	{
		global $_W, $_GPC;
		$op = $_GPC['op'];
		$id = $_GPC['id'];
		$account_api = WeAccount::create();
		$info = $account_api->fansQueryInfo($_W['openid']);
		$settings = $this->module['config'];
		$member = member($_W['openid']);
		if (!is_weixin()) {
			message('暂时只支持微信,请使用微信观看视频');
		}
		$hdp = pdo_getall('cyl_vip_video_hdp', array('uniacid' => $_W['uniacid'], 'type' => $_GPC['do']), array(), '', 'sort DESC , id DESC');
		$category = pdo_fetchall("SELECT * FROM " . tablename('cyl_vip_video_category') . " WHERE uniacid = '{$_W['uniacid']}' AND parentid = 0 ORDER BY parentid ASC, displayorder ASC, id ASC ", array(), 'id');
		$url = $_GPC['url'];
		$yurl = $_GPC['url'];
		$num = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('cyl_vip_video') . " WHERE uniacid = :uniacid AND openid = :openid ", array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
		if ($num >= $settings['free_num'] && $member['is_pay'] == 0) {
			message('您的免费观看次数已用完，请点击确定开通会员，无限制观看', $this->createMobileUrl('member', array('op' => 'open')), 'error');
		}
		$jilu = pdo_getall('cyl_vip_video', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']), array(), '', 'id DESC limit 10');
		if ($id) {
			$content = pdo_fetch("SELECT * FROM " . tablename('cyl_vip_video_manage') . " WHERE id=:id", array(':id' => $id));
			$juji = iunserializer($content['video_url']);
			if (count($juji) < 2) {
				$url = $juji['0']['link'];
			} else {
				$url = $_GPC['url'];
				if (!$url) {
					$url = $juji['0']['link'];
				}
			}
		} else {
			$url_time = cache_load('pc_caiji_detail:' . $url);
			if (TIMESTAMP - $url_time > 86400) {
				$content = pc_caiji_detail($url);
				$tuijian = pc_caiji_detail_tuijian($url);
				$daoyan = pc_caiji_detail_daoyan($url);
				cache_write('pc_caiji_detail:' . $url, TIMESTAMP);
				cache_write('content:' . $url, $content);
				cache_write('tuijian:' . $url, $tuijian);
				cache_write('daoyan:' . $url, $daoyan);
			} else {
				$content = cache_load('content:' . $url);
				$tuijian = cache_load('tuijian:' . $url);
				$daoyan = cache_load('daoyan:' . $url);
			}
			$content = $content['0'];
			if ($op == 'dianying') {
				if (TIMESTAMP - $url_time > 86400) {
					$link = caiji_url($url);
					cache_write('caiji_url:' . $url, $link);
				} else {
					$link = cache_load('caiji_url:' . $url);
				}
				foreach ($link as $value) {
					if (!strpos($value['link'], 'youku')) {
						if (strpos($value['link'], 'qq')) {
							$url = $value['link'];
							break;
						} elseif (strpos($value['link'], 'le')) {
							$url = $value['link'];
							break;
						} elseif (strpos($value['link'], 'pptv')) {
							$url = $value['link'];
							break;
						} elseif (strpos($value['link'], 'iqiyi')) {
							$url = $value['link'];
							break;
						} elseif (strpos($value['link'], 'tudou')) {
							$url = $value['link'];
							break;
						} elseif (strpos($value['link'], 'sohu')) {
							$url = $value['link'];
							break;
						}
					} else {
						$url = $value['link'];
					}
				}
			}
			if ($op == 'dianshi') {
				if (TIMESTAMP - $url_time > 86400) {
					$juji = juji_url($url);
					cache_write('juji_url:' . $url, $juji);
				} else {
					$juji = cache_load('juji_url:' . $url);
				}
				$url = $_GPC['link'];
				if (!$_GPC['link']) {
					$url = $juji['0']['link'];
				}
			}
			if ($op == 'dongman') {
				if (TIMESTAMP - $url_time > 86400) {
					$juji = dongman_url($url);
					cache_write('dongman_url:' . $url, $juji);
				} else {
					$juji = cache_load('dongman_url:' . $url);
				}
				$url = $_GPC['link'];
				if (!$_GPC['link']) {
					$url = $juji['0']['link'];
				}
			}
			if ($op == 'zongyi') {
				$year = $_GPC['year'];
				if ($year) {
					$ss = '/([\x80-\xff]*)/i';
					$year = preg_replace($ss, '', $year);
					if (TIMESTAMP - $url_time > 86400) {
						$juji = zongyi_juji_url($url);
						cache_write('zongyi_juji_url:' . $url, $juji);
					} else {
						$juji = cache_load('zongyi_juji_url:' . $url);
					}
				} else {
					if (TIMESTAMP - $url_time > 86400) {
						$juji = zongyi_juji_url($url);
						cache_write('zongyi_juji_url:' . $url, $juji);
					} else {
						$juji = cache_load('zongyi_juji_url:' . $url);
					}
				}
				$year = zongyi_year_url($url);
				if (!$_GPC['year']) {
					$_GPC['year'] = $year['0']['date'];
				}
				$url = $_GPC['link'];
				if (!$_GPC['link']) {
					$url = $juji['0']['link'];
				}
			}
			$click = pdo_fetchcolumn('SELECT * FROM ' . tablename('cyl_vip_video') . " WHERE uniacid = :uniacid AND yvideo_url = :yvideo_url ", array(':uniacid' => $_W['uniacid'], ':yvideo_url' => $yurl));
		}
		$video = pdo_get('cyl_vip_video', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'video_url' => $url));
		if (!$video) {
			if ($id) {
				pdo_insert('cyl_vip_video', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'title' => $content['title'], 'video_url' => $url, 'video_id' => $id, 'type' => $op, 'time' => TIMESTAMP, 'share' => $_GPC['jishu']));
			} else {
				pdo_insert('cyl_vip_video', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'title' => $content['title'], 'video_url' => $url, 'yvideo_url' => $yurl, 'type' => $op, 'time' => TIMESTAMP, 'share' => $_GPC['jishu']));
			}
		}
		if ($settings['api']) {
			if (strexists($url, 'baidu')) {
				$baidu = $url;
				$baidu = explode('path=', $baidu);
				$baidu = explode('&', $baidu['1']);
				$api = $settings['baidu_api'] . $baidu['0'];
			} else {
				$api = $settings['api'] . $url . '&link=' . $_GPC['link'];
			}
		} else {
			if ($id) {
				if (strexists($url, 'baidu')) {
					$baidu = $url;
					$baidu = explode('path=', $baidu);
					$baidu = explode('&', $baidu['1']);
					$api = $settings['baidu_api'] . $baidu['0'];
				} else {
					$api = 'http://cyl.go8goo.com/vip/api.php?url=' . $url . '&link=' . $_GPC['link'];
				}
			} else {
				$api = 'http://cyl.go8goo.com/vip/vip.php?url=' . $url . '&link=' . $_GPC['link'];
			}
		}
		if ($_GPC['index'] == 1) {
			$id = $_GPC['id'];
			$data = array('uniacid' => $_W['uniacid'], 'id' => $id);
			$item = pdo_get('cyl_vip_video', $data);
			include $this->template('news/detail');
			die;
		}
		include $this->template('news/detail');
	}
	public function doMobileSearch()
	{
		global $_W, $_GPC;
		$settings = $this->module['config'];
		$member = member($_W['openid']);
		if (TIMESTAMP > $member['end_time'] && $member['is_pay'] == 1) {
			pdo_update('cyl_vip_video_member', array('end_time' => null, 'is_pay' => 0), array('openid' => $member['openid']));
			$data = array('first' => array('value' => '您好,' . $member['nickname'] . '您的会员已到期', 'color' => '#ff510'), 'keyword1' => array('value' => '会员到期', 'color' => '#ff510'), 'keyword2' => array('value' => '到期提醒', 'color' => '#ff510'), 'remark' => array('value' => '点击详情开通', 'color' => '#ff510'));
			$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'member', 'm' => 'cyl_vip_video')), '.');
			$acc->sendTplNotice($member['openid'], $settings['tpl_id'], $data, $url, $topcolor = '#FF683F');
		}
		$op = $_GPC['op'] ? $_GPC['op'] : 'search';
		$key = $_GPC['key'];
		if ($key) {
			$list = caiji_list($key);
		}
		include $this->template('search');
	}
	public function doMobileClean()
	{
		global $_W, $_GPC;
		$res = pdo_delete('cyl_vip_video', array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
		echo "清空成功";
		die;
	}
	public function doMobileMember()
	{
		global $_W, $_GPC;
		load()->model('mc');
		$acc = WeAccount::create();
		$settings = $this->module['config'];
		$op = $_GPC['op'] ? $_GPC['op'] : 'member';
		$member = member($_W['openid']);
		$uid = mc_openid2uid($member['openid']);
		$credit = mc_credit_fetch($uid);
		if ($_W['account']['level'] >= ACCOUNT_SUBSCRIPTION_VERIFY) {
			checkauth();
		}
		if ($op == 'open') {
			$day = $_GPC['day'];
			$fee = $_GPC['card_fee'];
			$day = $_GPC['card_day'];
			$jifen = $_GPC['card_credit'];
			if (checksubmit('credit')) {
				$fee = $jifen;
				if ($fee > $credit['credit1']) {
					message('积分不足', '', 'error');
				}
				if (empty($fee)) {
					message('管理员未设置积分，请使用微信支付兑换', '', 'error');
				}
				$data = array('uniacid' => $_W['uniacid'], 'openid' => $member['openid'], 'uid' => $uid, 'tid' => '积分兑换', 'fee' => $fee, 'status' => 1, 'day' => $day, 'time' => TIMESTAMP);
				pdo_insert('cyl_vip_video_order', $data);
				mc_credit_update($uid, 'credit1', -$fee, array($uid, '视频会员开通-' . $fee . '积分', 'cyl_vip_video'));
				if ($member['end_time']) {
					pdo_update('cyl_vip_video_member', array('is_pay' => 1, 'end_time' => strtotime("+{$day} days", $member['end_time'])), array('openid' => $data['openid'], 'uniacid' => $data['uniacid']));
					$time = date('Y-m-d H:i:s', strtotime("+{$day} days", $member['end_time']));
				} else {
					pdo_update('cyl_vip_video_member', array('is_pay' => 1, 'end_time' => strtotime("+{$day} days")), array('openid' => $data['openid'], 'uniacid' => $data['uniacid']));
					$time = date('Y-m-d H:i:s', strtotime("+{$day} days"));
				}
				if ($settings['tpl_id']) {
					$data = array('first' => array('value' => '您好,' . $member['nickname'] . '开通了' . $day . '天会员', 'color' => '#ff510'), 'keyword1' => array('value' => '会员开通', 'color' => '#ff510'), 'keyword2' => array('value' => '开通提醒', 'color' => '#ff510'), 'remark' => array('value' => '花费' . $fee . '积分,到期时间' . $time, 'color' => '#ff510'));
					$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'member', 'm' => 'cyl_vip_video')), '.');
					$acc->sendTplNotice($member['openid'], $settings['tpl_id'], $data, $url, $topcolor = '#FF683F');
					$data = array('first' => array('value' => $member['nickname'] . '开通了' . $day . '天会员', 'color' => '#ff510'), 'keyword1' => array('value' => '会员开通', 'color' => '#ff510'), 'keyword2' => array('value' => '开通提醒', 'color' => '#ff510'), 'remark' => array('value' => '花费【' . $fee . '】积分，到期时间' . $time . '请进入后台查看', 'color' => '#ff510'));
					$acc->sendTplNotice($settings['kf_id'], $settings['tpl_id'], $data, $url, $topcolor = '#FF683F');
				}
				message('会员兑换成功', $this->createMobileUrl('member'), 'success');
			}
			if (checksubmit('submit')) {
				$url = $this->createMobileUrl('pay', array('day' => $day, 'fee' => $fee));
				Header("Location: {$url}");
				die;
			}
		}
		if ($op == 'my') {
			$data = array('uniacid' => $_W['uniacid'], 'openid' => $member['openid']);
			$list = pdo_getall('cyl_vip_video', $data, array(), '', 'id DESC');
		}
		if ($op == 'card') {
			if (checksubmit()) {
				$data = array('uniacid' => $_W['uniacid'], 'pwd' => $_GPC['card']);
				$card = pdo_get('cyl_vip_video_keyword_id', $data, array(), '', 'id DESC');
				if (!$card) {
					message('兑换码无效', '', 'error');
				} elseif ($card['status']) {
					message('兑换码已使用', '', 'error');
				} else {
					$res = pdo_update('cyl_vip_video_keyword_id', array('status' => 1, 'openid' => $_W['openid']), array('id' => $card['id']));
					if ($res) {
						if ($member['end_time']) {
							pdo_update('cyl_vip_video_member', array('is_pay' => 1, 'end_time' => strtotime("+{$card['day']} days", $member['end_time'])), array('openid' => $_W['openid'], 'uniacid' => $data['uniacid']));
							$time = date('Y-m-d H:i:s', strtotime("+{$card['day']} days", $member['end_time']));
						} else {
							pdo_update('cyl_vip_video_member', array('is_pay' => 1, 'end_time' => strtotime("+{$card['day']} days")), array('openid' => $_W['openid'], 'uniacid' => $data['uniacid']));
							$time = date('Y-m-d H:i:s', strtotime("+{$card['day']} days"));
						}
						if ($settings['tpl_id']) {
							$data = array('first' => array('value' => '您好,' . $member['nickname'] . '开通了' . $card['day'] . '天会员', 'color' => '#ff510'), 'keyword1' => array('value' => '会员开通', 'color' => '#ff510'), 'keyword2' => array('value' => '开通提醒', 'color' => '#ff510'), 'remark' => array('value' => '卡密兑换' . $card['day'] . '天,到期时间' . $time, 'color' => '#ff510'));
							$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'member', 'm' => 'cyl_vip_video')), '.');
							$acc->sendTplNotice($member['openid'], $settings['tpl_id'], $data, $url, $topcolor = '#FF683F');
							$data = array('first' => array('value' => $member['nickname'] . '开通了' . $card['day'] . '天会员', 'color' => '#ff510'), 'keyword1' => array('value' => '会员开通', 'color' => '#ff510'), 'keyword2' => array('value' => '开通提醒', 'color' => '#ff510'), 'remark' => array('value' => '卡密兑换' . $card['day'] . '天，到期时间' . $time . '请进入后台查看', 'color' => '#ff510'));
							$acc->sendTplNotice($settings['kf_id'], $settings['tpl_id'], $data, $url, $topcolor = '#FF683F');
						}
						$data = array('uniacid' => $_W['uniacid'], 'openid' => $member['openid'], 'uid' => $uid, 'tid' => '卡密兑换', 'fee' => '', 'status' => 1, 'day' => $card['day'], 'time' => TIMESTAMP);
						pdo_insert('cyl_vip_video_order', $data);
						message('兑换成功', $this->createMobileUrl('member'), 'success');
					}
				}
			}
		}
		if ($op == 'order') {
			$data = array('uniacid' => $_W['uniacid'], 'openid' => $member['openid']);
			$list = pdo_getall('cyl_vip_video_order', $data, array(), '', 'id DESC');
		}
		include $this->template('news/member');
	}
	public function doMobileTv()
	{
		global $_W, $_GPC;
		$settings = $this->module['config'];
		$url = $_GPC['url'];
		include $this->template('tv');
	}
	public function doMobilePay()
	{
		global $_GPC, $_W;
		$acc = WeAccount::create();
		$settings = $this->module['config'];
		$member = member($_W['openid']);
		if ($_GPC['fee']) {
			$fee = $_GPC['fee'];
		} else {
			$fee = floatval($settings['fee']);
		}
		$id = $_GPC['id'];
		if ($fee <= 0) {
			message('支付错误, 金额小于0');
		}
		if (empty($member['openid'])) {
			message('非法进入');
		}
		if ($id) {
			$order = pdo_fetch("SELECT * FROM " . tablename('cyl_vip_video_order') . " WHERE id = :id", array(':id' => $id));
			$day = $order['day'];
			$snid = $order['tid'];
		} else {
			$day = $_GPC['day'];
			$snid = date('YmdHis') . str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT);
		}
		if ($_GPC['fee']) {
			$amount = $fee;
		} else {
			$amount = $fee * $day;
		}
		$data = array('uniacid' => $_W['uniacid'], 'openid' => $member['openid'], 'uid' => $member['uid'], 'tid' => $snid, 'fee' => $amount, 'status' => 0, 'day' => $day, 'time' => TIMESTAMP);
		if ($id) {
			pdo_update('cyl_vip_video_order', $data, array('id' => $id));
		} else {
			pdo_insert('cyl_vip_video_order', $data);
		}
		$params = array('tid' => $snid, 'ordersn' => 'SN' . $snid, 'title' => '开通会员', 'fee' => $amount, 'user' => $member['uid']);
		$this->pay($params);
	}
	public function payResult($params)
	{
		global $_W, $_GPC;
		$acc = WeAccount::create();
		$order = pdo_fetch("SELECT * FROM " . tablename('cyl_vip_video_order') . " WHERE tid = :tid", array(':tid' => $params['tid']));
		$member = pdo_get('cyl_vip_video_member', array('openid' => $order['openid'], 'uniacid' => $order['uniacid']));
		$settings = $this->module['config'];
		if ($params['result'] == 'success' && $params['from'] == 'notify') {
			pdo_update('cyl_vip_video_order', array('status' => 1), array('tid' => $order['tid']));
			if ($member['end_time']) {
				$day = $order['day'];
				pdo_update('cyl_vip_video_member', array('is_pay' => 1, 'end_time' => strtotime("+{$day} days", $member['end_time'])), array('openid' => $order['openid'], 'uniacid' => $order['uniacid']));
				$time = date('Y-m-d H:i:s', strtotime("+{$day} days", $member['end_time']));
			} else {
				$day = $order['day'];
				pdo_update('cyl_vip_video_member', array('is_pay' => 1, 'end_time' => strtotime("+{$day} days")), array('openid' => $order['openid'], 'uniacid' => $order['uniacid']));
				$time = date('Y-m-d H:i:s', strtotime("+{$day} days"));
			}
			if ($_W['account']['level'] == 4 && $settings['tpl_id']) {
				$data = array('first' => array('value' => '您好,' . $member['nickname'], 'color' => '#ff510'), 'keyword1' => array('value' => '会员开通', 'color' => '#ff510'), 'keyword2' => array('value' => '开通提醒', 'color' => '#ff510'), 'remark' => array('value' => '花费金额【' . $order['fee'] . '】，到期时间' . $time, 'color' => '#ff510'));
				$url = $_W['siteroot'] . 'app' . ltrim(murl('entry', array('do' => 'member', 'm' => 'cyl_vip_video')), '.');
				$acc->sendTplNotice($order['openid'], $settings['tpl_id'], $data, $url, $topcolor = '#FF683F');
				$data = array('first' => array('value' => $member['nickname'] . '开通了' . $day . '天会员', 'color' => '#ff510'), 'keyword1' => array('value' => '会员开通', 'color' => '#ff510'), 'keyword2' => array('value' => '开通提醒', 'color' => '#ff510'), 'remark' => array('value' => '花费金额【' . $order['fee'] . '】元，到期时间' . $time . '请进入后台查看', 'color' => '#ff510'));
				$acc->sendTplNotice($settings['kf_id'], $settings['tpl_id'], $data, $url, $topcolor = '#FF683F');
			} else {
				$info = "您好，【{$member['nickname']}】会员开通通知\n";
				$info .= "花费金额【{$order['fee']}】元，到期时间【{$time}】。\n";
				$message = array('msgtype' => 'text', 'text' => array('content' => urlencode($info)), 'touser' => $order['openid']);
				$account_api = WeAccount::create();
				$status = $account_api->sendCustomNotice($message);
				$info = "【{$member['nickname']}】会员开通了{$day}天会员\n";
				$info .= "花费金额【{$order['fee']}】元，到期时间【{$time}】。\n";
				$message = array('msgtype' => 'text', 'text' => array('content' => urlencode($info)), 'touser' => $settings['kf_id']);
				$account_api = WeAccount::create();
				$status = $account_api->sendCustomNotice($message);
				if (is_error($status)) {
					message('发送失败，原因为' . $status['message']);
				}
			}
		}
		if (empty($params['result']) || $params['result'] != 'success') {
		}
		if ($params['from'] == 'return') {
			if ($params['result'] == 'success') {
				message('您已支付成功！', $this->createMobileUrl('member'), 'success');
			} else {
				message('支付失败！', 'error');
			}
		}
	}
	public function doWebManage()
	{
		global $_W, $_GPC;
		$op = $_GPC['op'] ? $_GPC['op'] : 'list';
		$category = pdo_fetchall("SELECT * FROM " . tablename('cyl_vip_video_category') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder ASC, id ASC ", array(), 'id');
		$parent = array();
		$children = array();
		if (!empty($category)) {
			$children = '';
			foreach ($category as $cid => $cate) {
				if (!empty($cate['parentid'])) {
					$children[$cate['parentid']][] = $cate;
				} else {
					$parent[$cate['id']] = $cate;
				}
			}
		}
		if ($op == 'list') {
			$pageindex = max(intval($_GPC['page']), 1);
			$pagesize = 20;
			$starttime = empty($_GPC['time']['start']) ? strtotime('-90 days') : strtotime($_GPC['time']['start']);
			$endtime = empty($_GPC['time']['end']) ? TIMESTAMP + 86399 : strtotime($_GPC['time']['end']) + 86399;
			$where = ' WHERE uniacid = :uniacid AND time >= :starttime AND time <= :endtime';
			$params = array(':uniacid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('cyl_vip_video_manage') . $where, $params);
			$pager = pagination($total, $pageindex, $pagesize);
			$sql = ' SELECT * FROM ' . tablename('cyl_vip_video_manage') . $where . ' ORDER BY id DESC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			$list = pdo_fetchall($sql, $params, 'id');
		}
		if ($op == 'record') {
			$pageindex = max(intval($_GPC['page']), 1);
			$pagesize = 20;
			$starttime = empty($_GPC['time']['start']) ? strtotime('-90 days') : strtotime($_GPC['time']['start']);
			$endtime = empty($_GPC['time']['end']) ? TIMESTAMP + 86399 : strtotime($_GPC['time']['end']) + 86399;
			$where = ' WHERE uniacid = :uniacid AND time >= :starttime AND time <= :endtime AND length(title) <> 0 ';
			$params = array(':uniacid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime);
			$total = pdo_fetchcolumn('SELECT * FROM ' . tablename('cyl_vip_video') . $where . ' GROUP BY video_url ', $params);
			$pager = pagination($total, $pageindex, $pagesize);
			$sql = ' SELECT *,count(video_url) as num FROM ' . tablename('cyl_vip_video') . $where . ' GROUP BY video_url ORDER BY num DESC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			$list = pdo_fetchall($sql, $params, 'id');
		}
		if ($op == 'single') {
			$pageindex = max(intval($_GPC['page']), 1);
			$pagesize = 20;
			$starttime = empty($_GPC['time']['start']) ? strtotime('-90 days') : strtotime($_GPC['time']['start']);
			$endtime = empty($_GPC['time']['end']) ? TIMESTAMP + 86399 : strtotime($_GPC['time']['end']) + 86399;
			$where = ' WHERE uniacid = :uniacid AND time >= :starttime AND time <= :endtime AND length(title) <> 0';
			$params = array(':uniacid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime);
			if ($_GPC['openid']) {
				$where .= ' AND openid = :openid ';
				$params[':openid'] = $_GPC['openid'];
			}
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('cyl_vip_video') . $where, $params);
			$pager = pagination($total, $pageindex, $pagesize);
			$sql = ' SELECT * FROM ' . tablename('cyl_vip_video') . $where . ' ORDER BY id DESC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			$list = pdo_fetchall($sql, $params, 'id');
		}
		if ($op == 'add') {
			$id = $_GPC['id'];
			if ($id) {
				$item = pdo_get('cyl_vip_video_manage', array('id' => $id));
				$pcate = $item['cid'];
				$ccate = $item['pid'];
			}
			if (checksubmit()) {
				$data = $_GPC['data'];
				$data['cid'] = intval($_GPC['category']['parentid']);
				$data['pid'] = intval($_GPC['category']['childid']);
				$data['thumb'] = $_GPC['thumb'];
				$data['uniacid'] = $_W['uniacid'];
				$data['time'] = TIMESTAMP;
				foreach ($_GPC['link'] as $k => $v) {
					$v = trim($v);
					if (empty($v)) {
						continue;
					}
					$video_url[] = array('title' => $_GPC['title'][$k], 'link' => $v);
				}
				$data['video_url'] = iserializer($video_url);
				if ($item) {
					pdo_update('cyl_vip_video_manage', $data, array('id' => $id));
				} else {
					pdo_insert('cyl_vip_video_manage', $data);
				}
				message('更新成功', $this->createWebUrl('manage'), 'success');
			}
		}
		if ($op == 'huoqu') {
			$url = $_GPC['url'];
			$url = explode('http://www.360kan.com', $url);
			$data = pc_caiji_detail($url['1']);
			$data = $data['0'];
			echo json_encode($data);
			die;
		}
		if ($op == 'delete') {
			$id = $_GPC['id'];
			$res = pdo_delete('cyl_vip_video_manage', array('id' => $id));
			if ($res) {
				message('删除成功！', $this->createWebUrl('manage'), 'success');
			}
		}
		include $this->template('manage');
	}
	public function doWebMember()
	{
		global $_W, $_GPC;
		$op = $_GPC['op'] ? $_GPC['op'] : 'member';
		if ($op == 'member') {
			$pageindex = max(intval($_GPC['page']), 1);
			$pagesize = 20;
			$starttime = empty($_GPC['time']['start']) ? strtotime('-90 days') : strtotime($_GPC['time']['start']);
			$endtime = empty($_GPC['time']['end']) ? TIMESTAMP + 86399 : strtotime($_GPC['time']['end']) + 86399;
			$where = ' WHERE uniacid = :uniacid AND time >= :starttime AND time <= :endtime';
			$params = array(':uniacid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime);
			if ($_GPC['keyword']) {
				$where .= ' AND nickname LIKE :keyword ';
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			if ($_GPC['is_pay']) {
				$where .= ' AND is_pay = :is_pay ';
				$params[':is_pay'] = "{$_GPC['is_pay']}";
			}
			if ($_GPC['is_pay'] == 2) {
				$where .= ' AND is_pay = :is_pay ';
				$params[':is_pay'] = 0;
			}
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('cyl_vip_video_member') . $where, $params);
			$total_member = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('cyl_vip_video_member') . " WHERE uniacid = :uniacid AND is_pay = :is_pay ", array(':uniacid' => $_W['uniacid'], ':is_pay' => 1));
			$pager = pagination($total, $pageindex, $pagesize);
			$sql = ' SELECT * FROM ' . tablename('cyl_vip_video_member') . $where . ' ORDER BY time ASC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			$list = pdo_fetchall($sql, $params, 'id');
		}
		if ($op == 'add') {
			$id = $_GPC['id'];
			if ($id) {
				$item = pdo_get('cyl_vip_video_member', array('id' => $id));
			}
			if (checksubmit()) {
				$data = $_GPC['data'];
				$data['avatar'] = $_GPC['avatar'];
				$data['end_time'] = strtotime($_GPC['end_time']);
				pdo_update('cyl_vip_video_member', $data, array('id' => $id));
				message('更新成功', $this->createWebUrl('member'), 'success');
			}
		}
		if ($op == 'delete') {
			$id = $_GPC['id'];
			pdo_delete('cyl_vip_video_member', array('id' => $id));
			message('删除成功', $this->createWebUrl('member'), 'success');
		}
		include $this->template('member');
	}
	public function doWebOrder()
	{
		global $_W, $_GPC;
		load()->model('mc');
		$op = $_GPC['op'];
		$settings = $this->module['config'];
		$starttime = empty($_GPC['time']['start']) ? strtotime('-90 days') : strtotime($_GPC['time']['start']);
		$endtime = empty($_GPC['time']['end']) ? TIMESTAMP + 86399 : strtotime($_GPC['time']['end']) + 86399;
		$pindex = max(intval($_GPC['page']), 1);
		$psize = 20;
		$condition = ' WHERE uniacid=:uniacid AND time >= :starttime AND time <= :endtime ';
		$params = array(':uniacid' => $_W['uniacid'], ':starttime' => $starttime, ':endtime' => $endtime);
		if ($_GPC['status']) {
			$condition .= ' AND status = :status ';
			$params[':status'] = $_GPC['status'];
		}
		$sql = ' SELECT * FROM ' . tablename('cyl_vip_video_order') . $condition . ' ORDER BY id DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
		$list = pdo_fetchall($sql, $params, 'id');
		$total_amount = pdo_fetchcolumn('SELECT SUM(fee) as fee FROM ' . tablename('cyl_vip_video_order') . $condition . " AND status = 1 AND tid != '积分兑换' ", $params);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('cyl_vip_video_order') . $condition, $params);
		$pager = pagination($total, $pindex, $psize);
		if ($op == 'qingli') {
			pdo_delete('cyl_vip_video_order', array('status' => 0, 'uniacid' => $_W['uniacid']));
			message('清理成功', $this->createWebUrl('order'), 'success');
		}
		include $this->template('order');
	}
	public function doWebHdp()
	{
		global $_W, $_GPC;
		$op = $_GPC['op'] ? $_GPC['op'] : 'list';
		if ($op == 'list') {
			if (checksubmit('submit')) {
				if (!empty($_GPC['sort'])) {
					foreach ($_GPC['sort'] as $key => $d) {
						pdo_update('cyl_vip_video_hdp', array('sort' => $d), array('id' => $_GPC['id'][$key]));
					}
					message('批量更新排序成功！', $this->createWebUrl('hdp', array('op' => 'list')), 'success');
				}
			}
			$list = pdo_fetchall("SELECT * FROM " . tablename('cyl_vip_video_hdp') . " WHERE uniacid = '{$_W['uniacid']}' {$condition} ORDER BY sort DESC,id DESC");
		} elseif ($op == 'post') {
			$id = intval($_GPC['id']);
			$adv = pdo_fetch("select * from " . tablename('cyl_vip_video_hdp') . " where id=:id and uniacid=:uniacid limit 1", array(":id" => $id, ":uniacid" => $_W['uniacid']));
			if (checksubmit('submit')) {
				$data = array('uniacid' => $_W['uniacid'], 'sort' => $_GPC['sort'], 'title' => $_GPC['title'], 'thumb' => $_GPC['thumb'], 'type' => $_GPC['type'], 'link' => $_GPC['link'], 'out_link' => $_GPC['out_link']);
				$link = explode('http://www.360kan.com', $data['link']);
				if (count($link) == 1) {
					$data['link'] = $data['link'];
				} else {
					$data['link'] = $link['1'];
				}
				if (!empty($id)) {
					pdo_update('cyl_vip_video_hdp', $data, array('id' => $id));
				} else {
					pdo_insert('cyl_vip_video_hdp', $data);
					$id = pdo_insertid();
				}
				message('更新幻灯片成功！', $this->createWebUrl('hdp', array('op' => 'list')), 'success');
			}
		} elseif ($op == 'delete') {
			$id = intval($_GPC['id']);
			$adv = pdo_fetch("SELECT id  FROM " . tablename('cyl_vip_video_hdp') . " WHERE id = '{$id}' AND uniacid=" . $_W['uniacid'] . "");
			if (empty($adv)) {
				message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('hdp', array('op' => 'display')), 'error');
			}
			pdo_delete('cyl_vip_video_hdp', array('id' => $id));
			message('幻灯片删除成功！', $this->createWebUrl('hdp', array('op' => 'list')), 'success');
		}
		include $this->template('hdp');
	}
	public function doWebCategory()
	{
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			if (!empty($_GPC['displayorder'])) {
				foreach ($_GPC['displayorder'] as $id => $displayorder) {
					pdo_update('cyl_vip_video_category', array('displayorder' => $displayorder), array('id' => $id, 'uniacid' => $_W['uniacid']));
				}
				message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
			}
			$children = array();
			$category = pdo_fetchall("SELECT * FROM " . tablename('cyl_vip_video_category') . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
			foreach ($category as $index => $row) {
				if (!empty($row['parentid'])) {
					$children[$row['parentid']][] = $row;
					unset($category[$index]);
				}
			}
			include $this->template('category');
		} elseif ($operation == 'post') {
			$parentid = intval($_GPC['parentid']);
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$category = pdo_fetch("SELECT * FROM " . tablename('cyl_vip_video_category') . " WHERE id = :id AND uniacid = :weid", array(':id' => $id, ':weid' => $_W['uniacid']));
			} else {
				$category = array('displayorder' => 0);
			}
			if (!empty($parentid)) {
				$parent = pdo_fetch("SELECT * FROM " . tablename('cyl_vip_video_category') . " WHERE id = '{$parentid}'");
				if (empty($parent)) {
					message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
				}
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['catename'])) {
					message('抱歉，请输入分类名称！');
				}
				$data = array('uniacid' => $_W['uniacid'], 'name' => $_GPC['catename'], 'url' => $_GPC['url'], 'displayorder' => intval($_GPC['displayorder']), 'parentid' => intval($parentid));
				if (!empty($id)) {
					unset($data['parentid']);
					pdo_update('cyl_vip_video_category', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
					load()->func('file');
					file_delete($_GPC['thumb_old']);
				} else {
					pdo_insert('cyl_vip_video_category', $data);
					$id = pdo_insertid();
				}
				message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
			}
			include $this->template('category');
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$category = pdo_fetch("SELECT id, parentid FROM " . tablename('cyl_vip_video_category') . " WHERE id = '{$id}'");
			if (empty($category)) {
				message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
			}
			pdo_delete('cyl_vip_video_category', array('id' => $id, 'parentid' => $id), 'OR');
			message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
		}
	}
	public function doWebCard()
	{
		global $_W, $_GPC;
		$op = $_GPC['op'] ? $_GPC['op'] : 'display';
		$id = $_GPC['id'];
		$card = pdo_get('cyl_vip_video_keyword', array('id' => $id), array(), '', 'id DESC');
		if ($op == 'display') {
			$pageindex = max(intval($_GPC['page']), 1);
			$pagesize = 20;
			$where = ' WHERE uniacid = :uniacid ';
			$params = array(':uniacid' => $_W['uniacid']);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('cyl_vip_video_keyword') . $where, $params);
			$pager = pagination($total, $pageindex, $pagesize);
			$sql = ' SELECT * FROM ' . tablename('cyl_vip_video_keyword') . $where . ' ORDER BY id DESC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			$list = pdo_fetchall($sql, $params, 'id');
		}
		if ($op == 'post') {
			if (checksubmit('submit')) {
				$data = $_GPC['data'];
				if (empty($data['card_id'])) {
					message('抱歉，请输入卡密标识！');
				}
				$data['uniacid'] = $_W['uniacid'];
				$card = card($_GPC['weishu'], $data['num']);
				pdo_insert('cyl_vip_video_keyword', $data);
				$id = pdo_insertid();
				foreach ($card as $value) {
					pdo_insert('cyl_vip_video_keyword_id', array('card_id' => $id, 'pwd' => $data['card_id'] . $value, 'uniacid' => $_W['uniacid'], 'day' => $data['day']));
				}
				message('生成成功！', $this->createWebUrl('card'), 'success');
			}
		}
		if ($op == 'delete') {
			$id = intval($_GPC['id']);
			pdo_delete('cyl_vip_video_keyword_id', array('card_id' => $id));
			pdo_delete('cyl_vip_video_keyword', array('id' => $id));
			message('删除成功！', $this->createWebUrl('card'), 'success');
		}
		if ($op == 'card') {
			$pageindex = max(intval($_GPC['page']), 1);
			$pagesize = 20;
			$where = ' WHERE uniacid = :uniacid AND card_id = :card_id ';
			$params = array(':uniacid' => $_W['uniacid'], ':card_id' => $id);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('cyl_vip_video_keyword_id') . $where, $params);
			$pager = pagination($total, $pageindex, $pagesize);
			$sql = ' SELECT * FROM ' . tablename('cyl_vip_video_keyword_id') . $where . ' ORDER BY id DESC LIMIT ' . ($pageindex - 1) * $pagesize . ',' . $pagesize;
			$list = pdo_fetchall($sql, $params, 'id');
			if (checksubmit('export_submit', true)) {
				$sql = "SELECT * FROM " . tablename('cyl_vip_video_keyword_id') . $where . " ORDER BY id DESC";
				$listexcel = pdo_fetchall($sql, $params);
				$header = array('card_id' => '卡密名称', 'pwd' => '卡密密码', 'status' => '使用状态');
				$keys = array_keys($header);
				$html = "\xEF\xBB\xBF";
				foreach ($header as $li) {
					$html .= $li . "\t ";
				}
				$html .= "\n";
				if (!empty($listexcel)) {
					$size = ceil(count($listexcel) / 500);
					for ($i = 0; $i < $size; $i++) {
						$buffer = array_slice($listexcel, $i * 500, 500);
						foreach ($buffer as $row) {
							$row['card_id'] = $card['title'];
							$row['status'] = $card['status'] ? '已兑换' : '未兑换';
							foreach ($keys as $key) {
								$data[] = $row[$key];
							}
							$user[] = implode("\t ", $data) . "\t ";
							unset($data);
						}
					}
					$html .= implode("\n", $user);
				}
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
				header("Content-Type:application/force-download");
				header("Content-Type:application/vnd.ms-execl");
				header("Content-Type:application/octet-stream");
				header("Content-Type:application/download");
				header('Content-Disposition:attachment;filename="卡密.xls"');
				header("Content-Transfer-Encoding:binary");
				echo $html;
				die;
			}
		}
		include $this->template('card');
	}
}