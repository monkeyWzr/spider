<?php

$mysqli = new mysqli('localhost', 'root', '', 'test');
if ($mysqli->connect_error) {
	die("Mysql数据库连接失败：" . $conn->connect_error);
}

$mysqli->query("SET NAMES 'utf8'");

$redis = new Redis();
$redis->connect('127.0.0.1');
$redis->info();
//$s = 0;
//$e = 20;

while($redis->exists('item_info')){
	$sql_base = "insert into zimuzu(`item_id`,`resource_id`,`season`,`episode`,`link`,`format`,`size`,`title`,`created_date`) values";
	$sql = $sql_base;
	$items = $redis->lrange('item_info', 0, 5);
	if(empty($items))
		break;
	
	foreach ($items as $item){
		$item = json_decode($item, TRUE);
		foreach($item as $resource_id => $infos){
			if(empty($infos)) continue;
			foreach($infos as $info){
				if(empty($info['link'])) continue;
				$link = json_encode($info['link'], JSON_UNESCAPED_UNICODE);
			//	foreach($info['link'] as $link_type => $link){
					$sql = $sql . "('" . $info['itemid'] . "','"
									  . $resource_id . "',"
									  . ($info['season'] ? $info['season'] : 0) . ","
									  . ($info['episode'] ? $info['episode'] : 0) . ",'"
									  . $mysqli->real_escape_string($link) . "','"
									  . ($info['format'] ? $info['format'] : 'unknown') . "','"
									  . ($info['size'] ? $info['size'] : 'unknown') . "','"
									  . ($info['title'] ? $mysqli->real_escape_string($info['title']) : 'unknown') . "','"
									  . date('Y-m-d h:i:s') . "'),";
			//	}
			}	
		}
		$sql = rtrim($sql, ',');
		if($sql == $sql_base){
			echo "啥也没有啊\n";
			continue;
		}
		//echo $sql;
		if($mysqli->query($sql)){
			echo "保存成功\n";
			//$redis->lPop('item_info');
		} else {
			echo "Error:" . $mysqli->error . "\n" . $sql . "\n";
		}
		$sql = $sql_base;	
		$redis->lPop('item_info');
	}
}
