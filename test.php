<?php

include_once('Spider.php');
//include_once('meiju.php');

// echo urlencode('权力的游戏');
// $result = '<div class="media-box>adfgkljafdgvskjfghksjdfhgkjhfgkskfvskdf</div>';
// $regex = '|<[div\sclass\="media\-box"]+>(.*)</[^>]+>|ims';
// $matches = array();
// preg_match_all($regex, $result, $matches);
// var_dump($matches);

$fuck = new Spider('小黑机', 'WZRJJ888');
//$fuck->ogin('小黑机', 'WZRJJ888');
$info = $fuck->name_to_itemid('生活大爆炸');
//var_dump($fuck->get_season($info[0]['itemid'], '6'));
var_dump($fuck->resource_list());
while(1){
	$url_list = $fuck->resource_list();
}
//phpinfo();
//$t = curl_init('http://www.baidu.com');
//var_dump(curl_exec($t));
//echo meiju('美剧 link 生活大爆炸 s=5 e=1');
// var_dump(meiju('美剧 help'));
// var_dump(subscribe('cdsf5svs1fdbs5df', '摩登家庭'));
// var_dump(get_subscribe('cdsf5svs1fdbs5df'));

// [{"title":"u300au6469u767bu5bb6u5eadu300b(Modern Family)","itemid":"11010","name":"u6469u767bu5bb6u5ead"},{"title":"u300au6743u529bu7684u6e38u620fu300b(Game of Thrones)[u51b0u4e0eu706bu4e4bu6b4c / u6743u529bu7684u6e38u620fu4e0bu8f7d / u6743u5229u7684u6e38u620f / u51b0u706b]","itemid":"10733","name":"u6743u529bu7684u6e38u620f"}]
