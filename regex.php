<?php


class Regex{
    public static $ED2K_LINK = '|.*<a\shref="(ed2k:.*)"\stype="ed2k".*<\/a>|sU';
	public static $MAGNET_LINK = '|.*<a\shref="(magnet:.*)"\stype="magnet".*<\/a>|sU';
	public static $ALL_LINK = '|.*<a\shref="(.*)"\stype="(.*)".*<\/a>.*|sU';
	const LINK_LIST = '|<li\sclass="clearfix"\sformat="(.*)"\sseason="(.*)"\sepisode="(.*)".*>.*<a\stitle=.*\sitemid="(.*)">(.*)<\/a>.*<font.*>(.*)<\/font>(.*)<\/li>|sU';
}
