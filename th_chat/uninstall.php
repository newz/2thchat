<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE IF EXISTS `pre_2th_chat`;
DROP TABLE IF EXISTS `pre_2th_chat_nick`;

EOF;

runquery($sql);

$finish = TRUE;

?>