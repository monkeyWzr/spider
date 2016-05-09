<?php


class Regex{
    public static $ED2K_LINK = '|.*<a\shref="(ed2k:.*)"\stype="ed2k".*<\/a>|sU';
    public static $MAGNET_LINK = '|.*<a\shref="(magnet:.*)"\stype="magnet".*<\/a>|sU';
}

