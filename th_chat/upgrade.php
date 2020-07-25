<?php
// Added by Jaideejung007
// Files Upgrade for th_chat plugin v.1.11 to v.2.04.2

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

$finish = TRUE;
?>