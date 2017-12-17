<?php


/**
* 取两个字符串中间
* @param $sting 待查找的字符串
* @param $mark1 开始字符串
* @param $mark2 结束字符串
* @return 中间
*/
function getNeedBetween($sting,$mark1,$mark2){
	$str1 = explode($mark1, $sting);
	$str2 = explode($mark2, $str1[1]);
	return $str2[0];
}	
	






?>