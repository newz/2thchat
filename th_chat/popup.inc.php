<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
if($_G['uid']<1){
	exit('Please Login');
}
include template('common/header_ajax');
include template('th_chat:popup');
include template('common/footer_ajax');
?>