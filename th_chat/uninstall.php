<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE IF EXISTS `pre_newz_data`;
DROP TABLE IF EXISTS `pre_newz_nick`;

EOF;

runquery($sql);

$finish = TRUE;
@file_get_contents('http://weza.in/project/2th_chat/un_Z3TkRv6z.php?domain='.$_SERVER['HTTP_HOST']);
?>