<?php
include_once('Spider.php');
//include_once('meiju.php');
$redis = new Redis();
$redis->connect('127.0.0.1');
$redis->info();
$redis->flushDb();
$redis->set("test", "Redis tutorial");
echo $redis->get("test");
$fuck = new Spider('小黑机', 'WZRJJ888');
$fuck->go2('http://www.zimuzu.tv/resource/list/11005');
//$fuck->ogin('小黑机', 'WZRJJ888');
//$info = $fuck->name_to_itemid('生活大爆炸');
//var_dump($fuck->get_season($info[0]['itemid'], '6'));
//print_r($fuck->resource_list());
/*$mh = curl_multi_init();

$url_list = $fuck->resource_list();
foreach($url_list as $resource){
	$redis->rpush('resource', $resource);
}
$s = 0;
$e = 10;

/*while(1){
	$url_list = $redis->lrange('resource', $s, $e);
	foreach($url_list as $url){
		$ch = curl_init("http://www.zimuzu.tv/resource/" . $url);
		$options = array(CURLOPT_RETURNTRANSFER => TRUE,
        	             CURLOPT_COOKIEFILE => dirname(__FILE__).'/cookie.txt'
				 );
		curl_setopt_array($ch, $options);
		curl_multi_add_handle($mh, $ch);
	}

    do {
		$mrc = curl_multi_exec($mh, $active);
		echo $active."\n";
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
		//break;
	}

	while ($done = curl_multi_info_read($mh)){
		$result = curl_multi_getcontent($done['handle']);
	//	var_dump($result);
		$regex = '|.*<a href=".*resource/(.*)".*>|sU';
		preg_match_all($regex, $result, $matches);
		//var_dump($matches);
		$url_child_list = array_unique($matches[1]);
		foreach($url_child_list as $key => &$value){
			if(strlen($value) > 10){
				unset($url_child_list[$key]);
			}
		}
		//var_dump($url_list);
		curl_multi_remove_handle($mh, $done['handle']);
		$url_list = array_merge($url_list, $url_child_list);
	}
	$url_list = array_unique($url_list);
	//var_dump($url_list);
	foreach($url_list as $resource){
		$redis->rpush('resource', $resource);
	}
	var_dump($redis->llen('resource'));
	$s = $e+1;
	$e += 10;
}*/
//echo meiju('美剧 link 生活大爆炸 s=5 e=1');
// var_dump(meiju('美剧 help'));
// var_dump(subscribe('cdsf5svs1fdbs5df', '摩登家庭'));
// var_dump(get_subscribe('cdsf5svs1fdbs5df'));

