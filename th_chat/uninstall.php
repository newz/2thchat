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
?>