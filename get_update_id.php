<?php

include_once("Spider.php");

$mysqli = new mysqli('localhost', 'root', '', 'test');
if ($mysqli->connect_error) {
	die("Mysql数据库连接失败：" . $mysqli->connect_error);
}
$mysqli->query("SET NAMES 'utf8'");
//$redis = new Redis();
//$redis->connect('127.0.0.1');
//$redis->info();
//$redis->flushDb();
$fuck = new Spider('小黑机', 'WZRJJ888');

$schedule = Spider::get_schedule();
$update_resource_id = array_keys($schedule);
//var_dump($update_resource_id);
foreach($update_resource_id as $resource_id){
	$ch = curl_init("http://www.zimuzu.tv/resource/list/" . $resource_id);
	$options = array(CURLOPT_RETURNTRANSFER => TRUE,
        	             CURLOPT_COOKIEFILE => dirname(__FILE__).'/cookie.txt'
					);

	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	curl_close($ch);
	$links = Spider::get_link_from_text($result);
	foreach($links as $info){
		var_dump($info['itemid']);
		$query = $mysqli->query("select * from zimuzu where item_id='{$info['itemid']}'");
		if($query->num_rows){
			echo "already exists\n";
		}else{
			$sql = "insert into zimuzu(`item_id`,`resource_id`,`season`,`episode`,`link`,`format`,`size`,`title`,`created_date`) values"
			   		. "('" . $info['itemid'] . "','"
					. $resource_id . "',"
		   		    . ($info['season'] ? $info['season'] : 0) . ","
				    . ($info['episode'] ? $info['episode'] : 0) . ",'"
				    . ($info['link'] ? $mysqli->real_escape_string(array_values($info['link'])[0]):'') . "','"
				    . ($info['format'] ? $info['format'] : 'unknown') . "','"
				    . ($info['size'] ? $info['size'] : 'unknown') . "','"
				    . ($info['title'] ? $mysqli->real_escape_string($info['title']) : 'unknown') . "','"
				    . date('Y-m-d h:i:s') . "')";

			//echo $sql;
			if($mysqli->query($sql)){
				echo "ok\n";
			}
			else{
				echo $mysqli->error . "\n";
			}
		}
	}	
	
}
