<?php
include_once('Spider.php');
$redis = new Redis();
$redis->connect('127.0.0.1');
$redis->info();
$redis->flushDb();
$fuck = new Spider('小黑机', 'WZRJJ888');
$mh = curl_multi_init();

//初始化resource键值，插入10个itemid
//$url_list = $fuck->resource_list();
//foreach($url_list as $resource){
//	$redis->rPush('resource', $resource);
//}
//var_dump($redis->lrange('resource', 0, -1));

//初始偏移量
$s = 0;
$e = 10;
$multi = 10;

while(1){
	//从resource列表中读取10个元素
	//$url_list = $redis->lRange('resource', $s, $e);
	//初始化curl配置
	//foreach($url_list as $url){
	$url_list = array();
	for($i=$s; $i<$s+$multi; $i++){
		//$ch = curl_init("http://www.zimuzu.tv/resource/" . $url);
		$ch = curl_init("http://www.zimuzu.tv/eresourcelist?page={$i}&channel=tv&area=&category=&format=&year=&sort=rank");
		$options = array(CURLOPT_RETURNTRANSFER => TRUE,
        	             CURLOPT_COOKIEFILE => dirname(__FILE__).'/cookie.txt'
				 );
		curl_setopt_array($ch, $options);
		curl_multi_add_handle($mh, $ch);
	}
	//执行批处理，此处可优化
    do {
		$mrc = curl_multi_exec($mh, $active);
		//echo $active."\n";
	} while ($mrc == CURLM_CALL_MULTI_PERFORM);
	
	while ($active && $mrc == CURLM_OK) {
    	if (curl_multi_select($mh) != -1) {
        	do {
            	$mrc = curl_multi_exec($mh, $active);
        	} while ($mrc == CURLM_CALL_MULTI_PERFORM);
    	}
	}
	if ($mrc != CURLM_OK){
		echo "执行错误！";
		break;
	}
	
	//读取文本流，匹配新的itemid
	while ($done = curl_multi_info_read($mh)){
		if(!$done) break;
		$result = curl_multi_getcontent($done['handle']);
		$regex = '|.*<a href="http:.*resource/(.*)".*>|sU';
		preg_match_all($regex, $result, $matches);
		$url_child_list = array_unique($matches[1]);
		foreach($url_child_list as $key => &$value){
			if(strlen($value) > 6){
				unset($url_child_list[$key]);
			}
		}
		curl_multi_remove_handle($mh, $done['handle']);
		curl_close($done['handle']);
		$url_list = array_merge($url_list, $url_child_list);
	}

	//合并获取的所有itemid并删除重复
	$url_list = array_unique($url_list);
	//插入redis
	$finished = $redis->lRange('finished_resource', 0, -1);
	foreach($url_list as $resource){
		if (!in_array($resource, $finished))
			$redis->rPush('resource', $resource);
	}
	echo "当前已抓取" . $redis->lLen('resource') . "条记录\n";
	//偏移量向右移动10位
	$s += $multi;
//	$e += 10;
}
