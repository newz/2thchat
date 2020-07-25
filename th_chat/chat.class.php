<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class plugin_th_chat_forum{
	function index_top() {
		global $_G;
		include 'include.php';
		include template('th_chat:discuz');
		return $return;
	}
}
?>