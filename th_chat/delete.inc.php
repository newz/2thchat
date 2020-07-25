<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
$uid = $_G['uid'];
$gid = $_G['groupid'];
$time = time();
$id = $_GET['xid'];
$command = $_GET['command'];
$ip = $_SERVER['REMOTE_ADDR'];
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
include 'functions.php';
if($uid<1){
	die('Login');
}
if(!in_array($_G['adminid'],array(1,2,3))){
	die('Login');
}
if($command == 'del')
{
	DB::Query("DELETE FROM ".DB::table('newz_data')." WHERE id=$id LIMIT 1");
	DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,text,time,ip) VALUES ('$uid','0','$id','$time','delete')");
	echo lang('plugin/th_chat', 'jdj_th_chat_text_php_44');
}else if($command == 'ban'){
	$icon = checkOs();
	$banned = DB::query("SELECT value FROM ".DB::table('common_pluginvar')." WHERE variable='chat_ban' AND displayorder='9' LIMIT 1");
	$banned = DB::fetch($banned);
	$uid_ban = $id;
	if($uid_ban && !in_array($uid_ban,$banned)){
		$banned[] = $uid_ban;
		$username_ban = DB::query("SELECT m.username AS name,n.name AS nick FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid WHERE m.uid='{$uid_ban}' LIMIT 1");
		$username_ban = DB::fetch($username_ban);
		if($username_ban['nick']){
			$username_ban = $username_ban['nick'];
		}else{
			$username_ban = $username_ban['name'];
		}
		$text = '<font color="red"><a href="home.php?mod=space&uid='.$uid_ban.'"><strong>'.htmlspecialchars_decode($username_ban).'</strong></a> '.lang('plugin/th_chat', 'jdj_th_chat_text_php_23').'</font>';
		$banned_new = array();
		foreach($banned as $uid_banned){
			if($uid_banned&&!in_array($uid_banned,$banned_new)){
				$banned_new[] = $uid_banned;
			}
		}
		$banned = implode(',',$banned_new);
		DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$banned}' WHERE variable='chat_ban' AND displayorder='9' LIMIT 1");
		$icon = checkOs();
		DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','$touid','$icon','$text','$time','$ip')");
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_27');
	}else{
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_26');
	}
}else if($command == 'unban'){
	$icon = checkOs();
	$uid_ban = $id;
	$banned = DB::query("SELECT value FROM ".DB::table('common_pluginvar')." WHERE variable='chat_ban' AND displayorder='9' LIMIT 1");
	$banned = DB::fetch($banned);
	if($uid_ban && in_array($uid_ban,$banned)){
		$key = array_search($uid_ban, $banned);
		if($key !== FALSE) unset($banned[$key]);
		$username_ban = DB::query("SELECT m.username AS name,n.name AS nick FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid WHERE m.uid='{$uid_ban}' LIMIT 1");
		$username_ban = DB::fetch($username_ban);
		if($username_ban['nick']){
			$username_ban = $username_ban['nick'];
		}else{
			$username_ban = $username_ban['name'];
		}
		$text = '<font color="red">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_28').' <a href="home.php?mod=space&uid='.$uid_ban.'"><strong>'.htmlspecialchars_decode($username_ban).'</strong></a></font>';
		$banned_new = array();
		foreach($banned as $uid_banned){
			if($uid_banned&&!in_array($uid_banned,$banned_new)){
				$banned_new[] = $uid_banned;
			}
		}
		$banned = implode(',',$banned_new);
		DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$banned}' WHERE variable='chat_ban' AND displayorder='9' LIMIT 1");
		DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','$touid','$icon','$text','$time','$ip')");
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_30');
	}else{
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_29');
	}
}else if($command=='clear'){
	$icon = checkOs();
	$touid= $time.'_'.$uid.'_'.rand(1,999);
	$ip = 'clear';
	$q = DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','$touid','$icon','$text','$time','$ip')");
	$last = DB::insert_id($q);
	DB::query("DELETE FROM ".DB::table('newz_data')." WHERE id<".$last);
	echo lang('plugin/th_chat', 'jdj_th_chat_text_php_45');
}else{
	echo lang('plugin/th_chat', 'jdj_th_chat_text_php_10');
}
?>