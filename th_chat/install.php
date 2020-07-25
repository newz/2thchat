<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$p = DISCUZ_ROOT . './data/necz.net';

if(!is_dir($p))
	mkdir($p,0777);
	
$p .= '/2thchat';

if(!is_dir($p))
	mkdir($p,0777);
	

	
DB::Query("DROP TABLE IF EXISTS `2thchat_data`;");

DB::Query("CREATE TABLE IF NOT EXISTS `2thchat_data` (
  `id` int(12) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL,
  `touid` mediumint(8) unsigned NOT NULL,
  `text` mediumtext NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `ip` varchar(25) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

DB::Query("DROP TABLE IF EXISTS `2thchat_nick`;");

DB::Query("CREATE TABLE IF NOT EXISTS `2thchat_nick` (
  `uid` mediumint(8) unsigned NOT NULL,
  `name` mediumtext NOT NULL,
  `total` tinyint(1) unsigned NOT NULL default '0',
  `time` int(10) unsigned NOT NULL default '0',
  `point_time` int(10) unsigned NOT NULL default '0',
  `point_total` smallint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

DB::Query("INSERT INTO `2thchat_nick` (`uid`, `name`, `total`, `time`, `point_time`, `point_total`) VALUES (1, '2th Chat', 0, 0, 0, 0);");
@file_get_contents('http://necz.net/project/2th_chat/install.php?domain='.$_SERVER['HTTP_HOST']);


$finish = TRUE;

?>