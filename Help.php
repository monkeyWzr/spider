<?php

class Help{

    const LINK = "查询下载链接，用法：美剧 链接 剧名[ s=季数 e=集数 t=链接类型 f=视频格式]\n"
                        . "方括号内参数可选\n"
                        . "'链接'可用'link'、lj、-l代替"
                        . "t参数：目前可用ed2k（默认）或magnet\n"
                        . "f参数：目前可用HDTV、MP4、HR-HDTV（默认）、720P、WEB-DL、1080P";
    const SUBSCRIBE = "订阅剧集，用法：美剧 订阅[ 剧名]\n"
                             . "方括号内采纳书可选，默认返回已订阅剧集列表\n"
                             . "'订阅'可用dy、subscribe、-s代替";
    const HELP = "获取帮助，用法：美剧 帮助[ 指令名]\n"
                        . "方括号内参数可选，默认返回全部帮助\n"
                        . "'帮助'可用bz、help、-h代替";
    const ALL = self::LINK . "\n\n" . self::SUBSCRIBE;
}
