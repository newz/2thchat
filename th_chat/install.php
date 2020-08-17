<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$time = time();
$ip = $_SERVER['REMOTE_ADDR'];
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `pre_newz_nick`;
CREATE TABLE IF NOT EXISTS `pre_newz_nick` (
  `uid` mediumint(8) unsigned NOT NULL,
  `total` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `sound_1` int(1) NOT NULL DEFAULT '0',
  `sound_2` int(1) NOT NULL DEFAULT '1',
  `ban` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

INSERT INTO `pre_newz_data` (`uid`, `touid`, `icon`, `text`, `time`,`ip`) VALUES (1, 0, 'alert', 'ยินดีต้อนรับสู่ห้องแชท คุณสามารถเริ่มพิมพ์ข้อความของคุณได้ด้านล่างนี้~!', $time, '$ip');

EOF;
runquery($sql);
$finish = TRUE;
?>