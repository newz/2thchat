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
$time = time();
$ip = $_SERVER['REMOTE_ADDR'];
$jdj_th_chat_text_php_42 = lang('plugin/th_chat', 'jdj_th_chat_text_php_42');
$sql = <<<EOF

DROP TABLE IF EXISTS `pre_newz_data`;
CREATE TABLE IF NOT EXISTS `pre_newz_data` (
`id` int(12) unsigned NOT NULL auto_increment,
`uid` mediumint(8) unsigned NOT NULL,
`touid` mediumint(8) unsigned NOT NULL,
`icon` mediumtext NOT NULL,
`text` mediumtext NOT NULL,
`time` int(10) unsigned NOT NULL,
`ip` varchar(25) NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pre_newz_nick`;
CREATE TABLE IF NOT EXISTS `pre_newz_nick` (
  `uid` mediumint(8) unsigned NOT NULL,
  `name` mediumtext NOT NULL,
  `total` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `point_time` int(10) unsigned NOT NULL DEFAULT '0',
  `point_total` smallint(3) NOT NULL DEFAULT '0',
  `sound_1` int(1) NOT NULL DEFAULT '0',
  `sound_2` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `pre_newz_data` (`uid`, `touid`, `icon`, `text`, `time`,`ip`) VALUES (1, 0, 'alert', '$jdj_th_chat_text_php_42', $time, '$ip');

EOF;
runquery($sql);
$finish = TRUE;
?>