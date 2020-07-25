<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
if($_G['uid']<1){
	exit('Please Login');
}
function paddslashes($data) {
	if(is_array($data)) {
		foreach($data as $key => $val) {
			$data[paddslashes($key)] = paddslashes($val);
		}
	} else {
		$data = str_replace(array('\\','\''),array('\\\\','\\\''),$data);
	}
	return $data;
}
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
if($_POST['sound_1']!=""&&$_POST['sound_2']!="")
{
	$uid = $_G['uid'];
	$time = time();
	$_POST['sound_1'] = intval($_POST['sound_1']);
	$_POST['sound_2'] = intval($_POST['sound_2']);
	if($uid<1){
		die('Login');
	}
	$re = DB::query("SELECT uid,total,time FROM ".DB::table('newz_nick')." WHERE uid='{$uid}'");
	if($re = DB::fetch($re)){
		DB::query("UPDATE ".DB::table('newz_nick')." SET total=1,time='{$time}',sound_1='{$_POST['sound_1']}',sound_2='{$_POST['sound_2']}' WHERE uid='{$uid}' LIMIT 1");
	}else{
		DB::query("INSERT INTO ".DB::table('newz_nick')." (uid,total,time,sound_1,sound_2) VALUES ('{$uid}','1','{$time}',{$_POST['sound_1']},{$_POST['sound_2']})");
	}
	exit('เปลี่ยนการตั้งค่าสำเร็จ!<script>hideWindow("th_chat_setting", 0, 1);nzalert("เปลี่ยนการตั้งค่าสำเร็จ!");</script>');
}else{
	$olddata = DB::fetch_first("SELECT sound_1,sound_2 FROM ".DB::table('newz_nick')." WHERE uid='{$_G['uid']}'");
	$olddata['sound_1'] = $olddata['sound_1']=='1'?$olddatas1='checked':$olddatas2='checked';
	$olddata['sound_2'] = $olddata['sound_2']=='0'?$olddatas4='checked':$olddatas3='checked';
}
include template('common/header_ajax');
include template('th_chat:window');
include template('common/footer_ajax');
?>