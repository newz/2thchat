<?php
if (! defined ( 'IN_DISCUZ' )) {
	exit ( 'Access Denied' );
}

if ($_GET['fromversion'] <= "1.11") {
	$sql = <<<EOF
ALTER TABLE  `pre_newz_data` CHANGE  `icon`  `icon` MEDIUMTEXT NOT NULL;
ALTER TABLE  `pre_newz_nick` ADD  `sound_1` INT(1) NOT NULL DEFAULT 0, ADD  `sound_2` INT(1) NOT NULL DEFAULT 1;
EOF;
	runquery($sql);
}
if ($_GET['fromversion'] <= "2.10") {
	$sql = <<<EOF
ALTER TABLE  `pre_newz_nick` CHANGE  `point_total`  `point_total` SMALLINT(3) NOT NULL DEFAULT 0;
EOF;
	runquery($sql);
}

if ($_GET['fromversion'] <= "2.15") {
	$sql = <<<EOF
ALTER TABLE `pre_newz_nick` DROP `name`;
ALTER TABLE `pre_newz_data` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `pre_newz_nick` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF;
	runquery($sql);
}

if ($_GET['fromversion'] < "2.19") {
	$sql = <<<EOF
ALTER TABLE `pre_newz_nick` ADD `ban` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `sound_2`;
ALTER TABLE `pre_newz_nick` DROP `point_time`, DROP `point_total`;
EOF;
	runquery($sql);
}

$finish = TRUE;
?>