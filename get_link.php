<?php

include_once('Spider.php');
include_once('regex.php');
$redis = new Redis();
$redis->connect('127.0.0.1');
$redis->info();
$fuck = new Spider('小灰机灰上天', 'WZRJJ888');

$mh = curl_multi_init();

while($redis->exists('resource')){
	$resource_list = $redis->lRange('resource', 0, 10);
	if (empty($resource_list)) {
		echo "no value";
		exit;
	}

	foreach ($resource_list as $resource_id){
		$ch = curl_init("http://www.zimuzu.tv/resource/list/" . $resource_id);
		$options = array(CURLOPT_RETURNTRANSFER => TRUE,
        	             CURLOPT_COOKIEFILE => dirname(__FILE__).'/cookie.txt'
				 );
		curl_setopt_array($ch, $options);
		curl_multi_add_handle($mh, $ch);
	}

	do {
    	$mrc = curl_multi_exec($mh, $running);
    	curl_multi_select($mh);
	} while ($running > 0);
	if ($mrc != CURLM_OK){
		echo "执行错误！";
	//	break;
	}
	while ($done = curl_multi_info_read($mh)){
		$result = curl_multi_getcontent($done['handle']);
		$current_resource_id = substr(curl_getinfo($done['handle'], CURLINFO_EFFECTIVE_URL),-5);
		//var_dump($result);
		//获取到的链接为一部剧的所有下载链接
		$items = array($current_resource_id => $fuck->get_link_from_text($result));
		curl_multi_remove_handle($mh, $done['handle']);
		curl_close($done['handle']);
		//$url_list = array_merge($url_list, $url_child_list);
		$redis->rPush('item_info', json_encode($items, JSON_UNESCAPED_UNICODE));
	//	var_dump(json_encode($items),JSON_UNESCAPED_UNICODE);
		$redis->rPush('finished_resource', $current_resource_id);
		//此处应注意，phpredis中的lrem方法与redis文档中的方法参数顺序是不一样的
		$redis->lRem('resource', $current_resource_id, 0);
	}
	echo "当前已抓取" . $redis->lLen('finished_resource') . "条记录\n还剩" . $redis->lLen('resource') . "\n";

}
echo "抓取链接结束了。。。。";

