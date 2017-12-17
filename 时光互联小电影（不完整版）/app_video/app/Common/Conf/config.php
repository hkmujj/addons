<?php
return array(
	'LOAD_EXT_CONFIG' => 'database,router', 
	'DATA_CACHE_TYPE' => 'file',
	
	
	//模型定义
    'MODULE_ALLOW_LIST' => array('Home','Api'),
    'DEFAULT_MODULE' => 'Home',
    'URL_MODEL' => 2,
	
	//模版配置
    'TMPL_L_DELIM'          => '<{',
    'TMPL_R_DELIM'          => '}>',

	'SHOW_ERROR_MSG' =>  true,
	'URL_CASE_INSENSITIVE'  =>  true,
	'OUTPUT_ENCODE' => true,
	
	
	'FOOT_NAV' => array(
		array('name' => '电影' ,'id' => '1','icon' => 'movie'),
		array('name' => '电视剧' ,'id' => '2','icon' => 'tv'),
		array('name' => '综艺' ,'id' => '3','icon' => 'variety'),
	),

	
	'INDEX_SIDE' => array(
		array('name' => '幻灯1','pic' => 'http://www.luzhou4.com/Upload/advert/2017/02/26/58b26f5e85581.jpg' ,'id' => '1'),	//幻灯片名字 - 播放视频ID
		array('name' => '幻灯2','pic' => 'http://www.luzhou4.com/Upload/advert/2017/02/21/58abcb4a8cc2a.png' ,'id' => '2'),
		array('name' => '幻灯3','pic' => 'http://www.luzhou4.com/Upload/advert/2017/02/26/58b26f5e85581.jpg' ,'id' => '3'),
	),
	
	
);