<?php

//decode by QQ:270656184 http://www.yunlu99.com/
include IA_ROOT . "/addons/cyl_vip_video/QL/QueryList.class.php";
use QL\QueryList;
function caiji_list($keyword)
{
	$url = "http://so.360kan.com/index.php?kw={$keyword}";
	$html = file_get_contents($url);
	$data = QueryList::Query($html, array('link' => array('.b-mainpic a', 'href', '', function ($link) {
		$link = explode('http://www.360kan.com', $link);
		return $link['1'];
	}), 'title' => array('.title a', 'text', '', function ($title) {
		if ($title) {
			return $title;
		}
	}), 'p1' => array('ul:first', 'text'), 'p2' => array('ul:eq(1)', 'text'), 'p3' => array('ul:eq(2) li:first', 'text'), 'actor' => array('ul:eq(2) .actor', 'text'), 'director' => array('ul:eq(2) .director', 'text'), 'btn' => array('.button-container', 'text'), 'img' => array('img', 'src'), 'type' => array('h3 span', 'text'), 'tabs-items' => array('.active a:eq(0)', 'text')), '#js-longvideo .g-clear', '-.js-others')->data;
	return $data;
}
function dianying($num, $type)
{
	$url = "http://m.360kan.com/list/" . $type . "Data?pageno={$num}";
	$html = file_get_contents($url);
	$htmll = json_decode($html, true);
	$data = QueryList::Query($htmll['data']['list'], array('link' => array('li a', 'href'), 'html' => array('li a', 'html')))->data;
	return $data;
}
function caiji($url)
{
	$url = 'http://m.360kan.com' . $url;
	$html = file_get_contents($url);
	$data = QueryList::Query($html, array('nav' => array('.b-nav', 'text'), 'title' => array('.cp-detail-box', 'html'), 'play' => array('.btn-play', 'href'), 'desc' => array('.cp-detail-description', 'html')), '.p-body')->data;
	return $data;
}
function pc_caiji_detail($url)
{
	$url = 'http://www.360kan.com' . $url;
	$data = QueryList::Query($url, array('nav' => array('.b-nav', 'text'), 'title' => array('h1', 'text'), 'star' => array('.s', 'text'), 'thumb' => array('.top-left img', 'src'), 'director' => array('#js-desc-switch p:eq(3)'), 'year' => array('#js-desc-switch p:eq(0)', 'text'), 'area' => array('#js-desc-switch p:eq(1)', 'text'), 'type' => array('.tag', 'text'), 'actor' => array('#js-desc-switch p:eq(2)', 'text'), 'desc' => array('.js-close-wrap', 'text')), '.p-top')->data;
	return $data;
}
function pc_caiji_detail_tuijian($url)
{
	$url = 'http://www.360kan.com' . $url;
	$data = QueryList::Query($url, array('title' => array('.s1', 'text'), 'link' => array('a', 'href'), 'thumb' => array('img', 'data-src')), '.tuijian:eq(1) .tuijian-list li')->data;
	return $data;
}
function pc_caiji_detail_daoyan($url)
{
	$url = 'http://www.360kan.com' . $url;
	$data = QueryList::Query($url, array('title' => array('.s1', 'text'), 'link' => array('a', 'href'), 'thumb' => array('img', 'data-src')), '.tuijian:eq(0) .tuijian-list li')->data;
	return $data;
}
function str_substr($start, $end, $str)
{
	$temp = explode($start, $str, 2);
	$content = explode($end, $temp[1], 2);
	return $content[0];
}
function caiji_url($url)
{
	$url = 'http://www.360kan.com' . $url;
	$html = file_get_contents($url);
	$data = QueryList::Query($html, array('link' => array('.top-list-zd a', 'href'), 'title' => array('.top-list-zd a', 'text')))->data;
	return $data;
}
function zongyi_url($url)
{
	$url = 'http://www.360kan.com' . $url;
	$html = file_get_contents($url);
	$data = QueryList::Query($html, array('link' => array('#js-siteact a', 'href'), 'title' => array('#js-siteact a', 'text'), 'span' => array('#js-siteact .ea-site', 'text')))->data;
	return $data;
}
function zongyi_year_url($url)
{
	$url = 'http://www.360kan.com' . $url;
	$html = file_get_contents($url);
	$data = QueryList::Query($html, array('date' => array('#js-year a', 'text')))->data;
	return $data;
}
function zongyi_juji_url($url, $year = 'false')
{
	$id = explode('/', str_substr('/', '.', $url));
	$title = zongyi_url($url);
	if ($year == 'false') {
		$year = 'isByTime=false';
	} else {
		$year = 'year=' . $year;
	}
	foreach ($title as $value) {
		if ($value['title'] == '乐视' || $value['span'] == '乐视') {
			$url = 'http://www.360kan.com/cover/zongyilist?id=' . $id['1'] . '&site=leshi&do=switchyear&' . $year;
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '搜狐' || $value['span'] == '搜狐') {
			$url = 'http://www.360kan.com/cover/zongyilist?id=' . $id['1'] . '&site=sohu&do=switchyear&' . $year;
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '优酷' || $value['span'] == '优酷') {
			$url = 'http://www.360kan.com/cover/zongyilist?id=' . $id['1'] . '&site=youku&do=switchyear&' . $year;
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '芒果TV' || $value['span'] == '芒果TV') {
			$url = 'http://www.360kan.com/cover/zongyilist?id=' . $id['1'] . '&site=imgo&do=switchyear&' . $year;
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '爱奇艺' || $value['span'] == '爱奇艺') {
			$url = 'http://www.360kan.com/cover/zongyilist?id=' . $id['1'] . '&site=qiyi&do=switchyear&' . $year;
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == 'CNTV' || $value['span'] == 'CNTV') {
			$url = 'http://www.360kan.com/cover/zongyilist?id=' . $id['1'] . '&site=cntv&do=switchyear&' . $year;
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		}
	}
	if (!$html) {
		$url = 'http://www.360kan.com' . $url;
		$html = file_get_contents($url);
	}
	$data = QueryList::Query($html, array('link' => array('.js-year-page a', 'href'), 'year' => array('.js-year-page li .w-newfigure-hint', 'text'), 'title' => array('.js-year-page li .title', 'text')))->data;
	return $data;
}
function juji_url($url)
{
	$id = explode('/', str_substr('/', '.', $url));
	$title = caiji_url($url);
	foreach ($title as $value) {
		if ($value['title'] == '乐视') {
			$url = 'http://www.360kan.com/cover/switchsite?site=leshi&id=' . $id['1'] . '&category=2';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '搜狐') {
			$url = 'http://www.360kan.com/cover/switchsite?site=sohu&id=' . $id['1'] . '&category=2';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '优酷') {
			$url = 'http://www.360kan.com/cover/switchsite?site=youku&id=' . $id['1'] . '&category=2';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '芒果TV') {
			$url = 'http://www.360kan.com/cover/switchsite?site=imgo&id=' . $id['1'] . '&category=2';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '爱奇艺') {
			$url = 'http://www.360kan.com/cover/switchsite?site=qiyi&id=' . $id['1'] . '&category=2';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == 'CNTV') {
			$url = 'http://www.360kan.com/cover/switchsite?site=cntv&id=' . $id['1'] . '&category=2';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		}
	}
	if (!$html) {
		$url = 'http://www.360kan.com' . $url;
		$html = file_get_contents($url);
	}
	$data = QueryList::Query($html, array('link' => array('.num-tab-main:eq(1) a', 'href')))->data;
	if (empty($data)) {
		$data = QueryList::Query($html, array('link' => array('.js-tab a', 'href'), 'jishu' => array('.js-tab a', 'text')))->data;
	}
	return $data;
}
function dongman_url($url)
{
	$id = explode('/', str_substr('/', '.', $url));
	$title = caiji_url($url);
	foreach ($title as $value) {
		if ($value['title'] == '乐视') {
			$url = 'http://www.360kan.com/cover/switchsite?site=leshi&id=' . $id['1'] . '&category=4';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '搜狐') {
			$url = 'http://www.360kan.com/cover/switchsite?site=sohu&id=' . $id['1'] . '&category=4';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '优酷') {
			$url = 'http://www.360kan.com/cover/switchsite?site=youku&id=' . $id['1'] . '&category=4';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '芒果TV') {
			$url = 'http://www.360kan.com/cover/switchsite?site=imgo&id=' . $id['1'] . '&category=4';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == '爱奇艺') {
			$url = 'http://www.360kan.com/cover/switchsite?site=qiyi&id=' . $id['1'] . '&category=4';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		} elseif ($value['title'] == 'CNTV') {
			$url = 'http://www.360kan.com/cover/switchsite?site=cntv&id=' . $id['1'] . '&category=4';
			$html = file_get_contents($url);
			$html = json_decode($html, true);
			$html = $html['data'];
			break;
		}
	}
	if (!$html) {
		$url = 'http://www.360kan.com' . $url;
		$html = file_get_contents($url);
	}
	$data = QueryList::Query($html, array('link' => array('.num-tab-main:gt(0) a', 'href')))->data;
	if (empty($data)) {
		$data = QueryList::Query($html, array('link' => array('.num-tab-main a', 'href')))->data;
	}
	return $data;
}
function member($openid)
{
	global $_W, $_GPC;
	load()->model('mc');
	$member = pdo_get('cyl_vip_video_member', array('openid' => $openid, 'uniacid' => $_W['uniacid']));
	return $member;
}
function isUrl($s)
{
	return preg_match('/^http[s]?:\/\/' . '(([0-9]{1,3}\.){3}[0-9]{1,3}' . '|' . '([0-9a-z_!~*\'()-]+\.)*' . '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.' . '[a-z]{2,6})' . '(:[0-9]{1,4})?' . '((\/\?)|' . '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/', $s) == 1;
}
function category()
{
	$data = array('dianying' => '电影', 'dianshi' => '电视', 'zongyi' => '综艺', 'dongman' => '动漫');
	return $data;
}
function discover($url)
{
	$array = array();
	$data = QueryList::Query($url, array('link' => array('.item .js-tongjic', 'href'), 'img' => array('.list li img', 'src'), 'hint' => array('.hint', 'text'), 's2' => array('.item .s2', 'text'), 'title' => array('.item .s1', 'text'), 'star' => array('.item .star', 'text')))->data;
	$category = QueryList::Query($url, array('link' => array('.s-filter dl:eq(1) dd a', 'href'), 'title' => array('.s-filter dl:eq(1) dd a', 'text'), 'on' => array('.s-filter dl:eq(1) dd b', 'text')))->data;
	$year = QueryList::Query($url, array('link' => array('.s-filter dl:eq(2) dd a', 'href'), 'title' => array('.s-filter dl:eq(2) dd a', 'text'), 'on' => array('.s-filter dl:eq(2) dd b', 'text')))->data;
	$area = QueryList::Query($url, array('link' => array('.s-filter dl:eq(3) dd a', 'href'), 'title' => array('.s-filter dl:eq(3) dd a', 'text'), 'on' => array('.s-filter dl:eq(3) dd b', 'text')))->data;
	$star = QueryList::Query($url, array('link' => array('.s-filter dl:eq(4) dd a', 'href'), 'title' => array('.s-filter dl:eq(4) dd a', 'text'), 'on' => array('.s-filter dl:eq(4) dd b', 'text')))->data;
	$array = array($data, $category, $year, $area, $star);
	return $array;
}
function index_list($url, $rank)
{
	$array = array();
	$url = "http://www.360kan.com/{$rank}/list.php?rank=rankhot&cat=all&area=all&act=all&year=all";
	$data = QueryList::Query($url, array('link' => array('.item .js-tongjic', 'href'), 'img' => array('.list li img', 'src'), 'hint' => array('.hint', 'text'), 's2' => array('.item .s2', 'text'), 'title' => array('.item .s1', 'text'), 'star' => array('.item .star', 'text')))->data;
	return $data;
}
function youku($url)
{
	$data = QueryList::Query($url, array('link' => array('.title a', 'href'), 'thumb' => array('img', 'src'), 'hint' => array('.p-time', 'text'), 'title' => array('.title a', 'text'), 'star' => array('.actor a', 'text')), '.box-series>ul>li')->data;
	if (empty($data)) {
		$data = QueryList::Query($url, array('link' => array('.title a', 'href'), 'thumb' => array('img', 'src'), 'hint' => array('.p-time', 'text'), 'title' => array('.title a', 'text'), 'star' => array('.actor a', 'text'), 'qita' => 'qita'), '.yk-row .yk-col4')->data;
	}
	return $data;
}
function is_weixin()
{
	$agent = $_SERVER['HTTP_USER_AGENT'];
	if (!strpos($agent, "icroMessenger")) {
		return false;
	}
	return true;
}
function card($digit = 6, $num = 100)
{
	$numLen = $digit;
	$pwdLen = $digit;
	$c = $num;
	$sNumArr = range(0, 9);
	$sPwdArr = array_merge($sNumArr, range('a', 'z'));
	$cards = array();
	for ($x = 0; $x < $c; $x++) {
		$tempNumStr = array();
		for ($i = 0; $i < $numLen; $i++) {
			$tempNumStr[] = array_rand($sNumArr);
		}
		$tempPwdStr = array();
		for ($i = 0; $i < $pwdLen; $i++) {
			$tempPwdStr[] = $sPwdArr[array_rand($sPwdArr)];
		}
		$cards[$x] = implode('', $tempPwdStr);
	}
	array_unique($cards);
	return $cards;
}