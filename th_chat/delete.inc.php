<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
$uid = $_G['uid'];
$gid = $_G['groupid'];
$time = time();
$pid = intval($_POST['pid']);
if($uid<1){
die('Login');
}
if(!in_array($_G['adminid'],array(1,2,3))){
die('Login');
}
DB::Query("DELETE FROM ".DB::table('2th_chat')." WHERE id=$pid LIMIT 1");
DB::query("INSERT INTO ".DB::table('2th_chat')." (uid,touid,text,time,ip) VALUES ('$uid','0','$pid','$time','delete')");
echo 'ลบสำเร็จแล้ว!';
 ?>