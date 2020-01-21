<?php
if (!defined('IN_DISCUZ')) {
  exit('Access Denied');
}
$p = DISCUZ_ROOT . './data/2th_chat';
if (!is_dir($p))
  mkdir($p, 0777);
$p .= '/2thchat';
if (!is_dir($p))
  mkdir($p, 0777);
$time = time();
$ip = $_SERVER['REMOTE_ADDR'];
$sql = <<<EOF

DROP TABLE IF EXISTS `pre_2th_chat`;
CREATE TABLE IF NOT EXISTS `pre_2th_chat` (
  `id` int(12) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL,
  `touid` mediumint(8) unsigned NOT NULL,
  `icon` varchar(15) NOT NULL,
  `text` mediumtext NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `ip` varchar(25) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `pre_2th_chat_nick`;
CREATE TABLE IF NOT EXISTS `pre_2th_chat_nick` (
  `uid` mediumint(8) unsigned NOT NULL,
  `name` mediumtext NOT NULL,
  `total` tinyint(1) unsigned NOT NULL default '0',
  `time` int(10) unsigned NOT NULL default '0',
  `point_time` int(10) unsigned NOT NULL default '0',
  `point_total` smallint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `pre_2th_chat_nick` (`uid`, `name`, `total`, `time`, `point_time`, `point_total`) VALUES (1, 'สวัสดีชาวโลก!', 0, 0, 0, 0);

INSERT INTO `pre_2th_chat` (`uid`, `touid`, `icon`, `text`, `time`,`ip`) VALUES (1, 0, 'macintosh', 'ยินดีต้อนรับสู่ห้องแชท คุณสามารถเริ่มพิมพ์ข้อความข้อคุณได้ด้านล่างนี้ ^_^', $time, '$ip');

EOF;
runquery($sql);
$finish = TRUE;
