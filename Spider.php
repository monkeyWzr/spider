<?php
include_once('regex.php');

/**
 * PHP美剧爬虫
 * @author WZR <monkeywzr@gmail.com>
 * @date 2016/4/29
 */
class Spider{

    public $cookie_file_path;
    private $login_status = FALSE;
    public $lb;

    function __construct($username, $password){
		$this->cookie_file_path = dirname(__FILE__).'/cookie.txt';
		echo $this->cookie_file_path;
        $this->lb = ((PHP_SAPI == "cli") ? "\n" : "<br />");
        if(file_exists($this->cookie_file_path) && (time() - filemtime($this->cookie_file_path) < 600)){
            echo (filemtime($this->cookie_file_path) - time()) . '检测到cookie' . $this->lb;
            $this->login_status = TRUE;
        }
        else{
        //cookie文件超过10min未更改
            echo date('Y m d', filemtime($this->cookie_file_path)) . '未检测到cookie或cookie无效，正在尝试登录' . $this->lb;
            $this->login($username, $password);
        }
    }

    function login($username, $password){
		if(file_exists($this->cookie_file_path))
			unlink($this->cookie_file_path);
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_COOKIEJAR => $this->cookie_file_path,
                        CURLOPT_POSTFIELDS => 'account=' . urlencode($username) . '&password=' . $password . '&remember=1&url_back=http%3A%2F%2Fwww.zimuzu.tv%2F'
                    );

        $login = curl_init("http://www.zimuzu.tv/User/Login/ajaxLogin");
        curl_setopt_array($login, $options);

        // $json_rtn格式示例：
        /**
        object(stdClass)[1]
            public 'status' => int 1
            public 'info' => string '登录成功！' (length=15)
            public 'data' =>
            object(stdClass)[2]
                public 'url_back' => string 'http://www.zimuzu.tv/' (length=21)
        */
        //json_decode第二个参数设为TRUE则返回数组格式
        $json_rtn = json_decode(curl_exec($login), FALSE);
        curl_close($login);
        $this->login_status = $json_rtn->status;
        echo $json_rtn->info;
        return $json_rtn->status;
    }

    /**
     * 剧集名称转换成itemid
     * @param  string $name 剧集名称
     * @return array        剧集具体名称 => itemid
     */
    public static function name_to_itemid($name){
        // $name = urlencode($name);
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                        // CURLOPT_COOKIEFILE => $this->cookie_file_path
                    );
        $search = curl_init('http://www.zimuzu.tv/search/api?keyword='.urlencode($name).'&type=');
        curl_setopt_array($search, $options);
        $result = json_decode(curl_exec($search), TRUE);
        curl_close($search);
        $item_info = array();
        if ($result['data'] == 'False')
            return FALSE;
        foreach ($result['data'] as $rtn) {
            if ($rtn['channel'] == 'tv'){
                $item_info[] = array('title' => $rtn['title'],
                                'itemid' => $rtn['itemid'],
                                'name' => $name);
            }

        }
        if(!empty($item_info)){
            return $item_info;
        }
        else{
            return FALSE;
        }
    }

    public function get_link($itemid, $season='', $episode='', $link_type='ed2k', $format='HR-HDTV'){
        if($this->login_status == FALSE){
            echo '  未登录状态，无法获取资源列表！' . $this->lb;
            return;
        }
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_COOKIEFILE => $this->cookie_file_path
                    );
        $ch = curl_init('http://www.zimuzu.tv/resource/list/'.$itemid);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        $regex = '|<li\sclass="clearfix"\sformat="' . $format . '"\sseason="' . $season . '"\sepisode="' . $episode . '".*>.*<a\stitle=.*\sitemid="(.*)">(.*)<\/a>.*<font.*>(.*)<\/font>(.*)<\/li>|sU';
        $match = array();
        preg_match($regex, $result, $match);
        //已定义为$MAGNET_LINK常量
        // $regex2 = '|.*<a\shref="(magnet:.*)"\stype="magnet".*<\/a>|sU';
        // var_dump($match);
        $link = array();
        if($link_type == 'ed2k'){
            preg_match(Regex::$ED2K_LINK, $match[4], $link);
        }
        elseif($link_type == 'magnet'){
            preg_match(Regex::$MAGNET_LINK, $match[4], $link);
        }
        $rtn = array();
        $rtn['episode_id'] = $match[1];
        $rtn['title'] = $match[2];
        $rtn['size'] = $match[3];
        $rtn['link'] = $link[1];
        $rtn['link_type'] = $format;

        return $rtn;
    }

    public function get_season($itemid, $season='', $link_type='ed2k', $format='HR-HDTV'){
        if($this->login_status == FALSE){
            echo '  未登录状态，无法获取资源列表！' . $this->lb;
            return;
        }
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_COOKIEFILE => $this->cookie_file_path
                    );
        $ch = curl_init('http://www.zimuzu.tv/resource/list/'.$itemid);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        $regex = '|<li\sclass="clearfix"\sformat="' . $format . '"\sseason="' . $season . '".*>.*<a\stitle=.*\sitemid="(.*)">(.*)<\/a>.*<font.*>(.*)<\/font>(.*)<\/li>|sU';
        $matches = array();
        preg_match_all($regex, $result, $matches);
       // var_dump($matches);
        $links = array('season' => $season, 'links' => array());
        $type = '';
        if($link_type == 'ed2k'){
            $type = Regex::$ED2K_LINK;
        }
        elseif($link_type == 'magnet'){
            $type = Regex::$MAGNET_LINK;
        }

        foreach($matches[4] as $match){
            preg_match(Regex::$ED2K_LINK, $match, $link);
            $links['links'][] = $link[1];
        }

        return $links;
    }

    public function go($item_name, $season='', $episode='', $link_type='ed2k', $format='HR-HDTV'){
        $items = self::name_to_itemid($item_name);
        $rtn = array();
        foreach($items as $key => $item){
            $rtn[] = $this->get_link($item['itemid'], $season, $episode, $link_type, $format);
        }
        if(empty($rtn)){
            return FALSE;
        }
        return $rtn;
	}
	public function go2($url){
		if($this->login_status == FALSE){
            echo '  未登录状态，无法获取资源列表！' . $this->lb;
            return;
        }
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_COOKIEFILE => $this->cookie_file_path
                    );
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
	
		$regex = '|<li\sclass="clearfix"\sformat="(.*)"\sseason="(.*)"\sepisode="(.*)".*>.*<a\stitle=.*\sitemid="(.*)">(.*)<\/a>.*<font.*>(.*)<\/font>(.*)<\/li>|sU';
        $matches = array();
        preg_match_all($regex, $result, $matches);
        var_dump($matches);
       // $links = array('season' => $season, 'links' => array());
		for($i=0;$i<count($matches[4]);$i++){
            preg_match(Regex::$ALL_LINK, $matches[7][$i], $link);
			//var_dump($link);
			$items = array('itemid' => $matches[4][$i],
						  'season' => $matches[2][$i],
						  'episode' => $matches[3][$i],
						  'title' => $matches[5][$i],
						  'size' => $matches[6][$i],
						  'format' => $matches[1][$i],
						  'link' => array($link[2] => $link[1]));
			print_r($items);

		}
        //foreach($matches[7] as $match){
          //  preg_match(Regex::$ALL_LINK, $match, $link);
			//$links['links'][] = $link[1];
			//echo $link[1]."\n".$link[2]."\n";
		//}
	}
	/**
	 * 获取剧集主页链接
	 * @param  string $url 基础链接
	 * @return array       传入链接页面中剧集链接包含的剧集id
	 */
	public function resource_list($url='http://www.zimuzu.tv/eresourcelist'){
		if($this->login_status == FALSE){
            echo '  未登录状态，无法获取资源列表！' . $this->lb;
            return;
		}  		
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_COOKIEFILE => $this->cookie_file_path
                    );
        $ch = curl_init($url);
		curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
	
		$regex = '|.*<a href=".*resource/(.*)".*>|sU';
		preg_match_all($regex, $result, $matches);
		//var_dump($matches);
		$url_list = array_unique($matches[1]);
		foreach($url_list as $key => &$value){
			if(strlen($value) > 10){
				unset($url_list[$key]);
			}
		}
		return $url_list;
	
	}

    /**
     * 获取剧集信息，包括名称、导演、主演、图片等
     * @return [type] [description]
     */
    public function get_info(){}
    public function search(){}
	
	/**
	 * 获取今日更新列表
	 *@return array resource_id => resource_name
	 */
	public static function get_schedule(){
		$options = array(CURLOPT_RETURNTRANSFER => TRUE,
                        //CURLOPT_COOKIEFILE => $this->cookie_file_path
                    );
        $ch = curl_init('http://www.zimuzu.tv/tv/eschedule');
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
		$regex = '|<td\sclass="ihbg\scur">(.*)<\/td>|sU';
		if(preg_match($regex, $result, $list_block)){
			if(preg_match_all('|<a\shref=".*resource\/(.*)".*>(.*)<\/a>|sU', $list_block[1], $matches)){
				$rtn = array_combine($matches[1], $matches[2]);
			}
		}
		return $rtn;
	}

##################################################################
############################    工具方法    ######################
###################################################################

	public function get_pic($itemid){}
	/**
	 * 获取下载链接
	 * @param string $text 要搜索的文本
	 * @return array 每一项为array('itemid'  =>
	 * 							   'season'  =>
	 * 							   'episode' =>
	 * 							   'title'   =>
	 * 							   'size     => 
	 * 							   'format'  => 
	 * 							   'link'    => array(type => link))
	 */
	public function get_link_from_text($text){
        $matches = array();
        preg_match_all(Regex::LINK_LIST, $text, $matches);
		$items = array();
		if (empty($matches[4]))
			return FALSE;
		for($i=0;$i<count($matches[4]);$i++){
            preg_match(Regex::$ALL_LINK, $matches[7][$i], $link);
			//var_dump($link);
			$items[] = array('itemid' => $matches[4][$i],
						  'season' => $matches[2][$i],
						  'episode' => isset($matches[3][$i]) ? $matches[3][$i] : 0,
						  'title' => $matches[5][$i],
						  'size' => $matches[6][$i],
						  'format' => $matches[1][$i],
						  'link' => empty($link) ? NULL : array($link[2] => $link[1]));

		}	
		//print_r($items);
		return $items;
	}
}

