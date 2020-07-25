<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$time = time();
$is_mod = in_array($_G['adminid'],array(1,2,3));
if($uid<1){
	die('Login');
}
if (!get_magic_quotes_gpc()) {
	$name = stripslashes($_POST['new']);
}
else {
	$name = $_POST['new'];
}
if($is_mod)
{
$name = paddslashes(htmlspecialchars(preg_replace("/<script[^\>]*?>(.*?)<\/script>/i","", htmlspecialchars_decode($name))));
}else{
$name= paddslashes(htmlspecialchars(strip_tags(htmlspecialchars_decode($name))));
}
if($name===''){
	die('กรุณาใส่ชื่อ');
}
if($config['namemax']!=0 && dstrlen($name) > $config['namemax']){
	die('ห้ามตั้งชื่อ/สถานะเกิน '.$config['namemax'].' ตัวอักษร');
}
if(dstrlen($name) < $config['namemin']){
	die('ห้ามตั้งชื่อ/สถานะต่ำกว่า '.$config['namemin'].' ตัวอักษร');
}
if(strpos($name, " ") !== FALSE){
	die('ห้ามใช้ตัวช่องว่าง!');
}
if(DB::fetch_first("SELECT uid FROM ".DB::table('newz_nick')." WHERE name='{$name}' AND uid!='{$uid}'")){
	die('ขออภัย ชื่อนี้มีคนอื่นใช้ไปแล้ว');
}
if(DB::fetch_first("SELECT uid FROM ".DB::table('common_member')." WHERE username='{$name}' AND uid!='{$uid}'")){
	die('ขออภัย ชื่อนี้มีคนอื่นใช้ไปแล้ว');
}
if($is_mod)
{
	DB::query("INSERT INTO ".DB::table('newz_nick')." (uid,name,total,time) VALUES ('{$uid}','{$name}','1','{$time}') ON DUPLICATE KEY UPDATE name='{$name}'");
}else{
$re = DB::query("SELECT uid,total,time FROM ".DB::table('newz_nick')." WHERE uid='{$uid}'");
if($re = DB::fetch($re)){
		DB::query("UPDATE ".DB::table('newz_nick')." SET name='{$name}',total=1,time='{$time}' WHERE uid='{$uid}' LIMIT 1");
}else{
	DB::query("INSERT INTO ".DB::table('newz_nick')." (uid,name,total,time) VALUES ('{$uid}','{$name}','1','{$time}')");
}
DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,text,time,ip) VALUES ('{$uid}','0','{$name}','{$time}','changename')");
echo 'ok';
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
?>