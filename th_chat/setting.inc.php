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
	$is_mod = in_array($_G['adminid'],array(1,2,3));
	$_POST['sound_1'] = intval($_POST['sound_1']);
	$_POST['sound_2'] = intval($_POST['sound_2']);
	$name = $_POST['chatname'];
	if($uid<1){
		die('Login');
	}
	if (!get_magic_quotes_gpc()) {
		$name = stripslashes($name);
	}
	if($is_mod)
	{
	$name = paddslashes(htmlspecialchars(preg_replace("/<script[^\>]*?>(.*?)<\/script>/i","", htmlspecialchars_decode($name))));
	}else{
	$name= paddslashes(htmlspecialchars(strip_tags(htmlspecialchars_decode($name))));
	}
	if($name===''){
		die(lang('plugin/th_chat', 'jdj_th_chat_text_php_06'));
	}
	if($config['namemax']!=0 && dstrlen($name) > $config['namemax']){
		die(lang('plugin/th_chat', 'jdj_th_chat_text_php_55').' '.$config['namemax'].' '.lang('plugin/th_chat', 'jdj_th_chat_text_php_22'));
	}
	if(dstrlen($name) < $config['namemin']){
		die(lang('plugin/th_chat', 'jdj_th_chat_text_php_56').' '.$config['namemin'].' '.lang('plugin/th_chat', 'jdj_th_chat_text_php_22'));
	}
	if(strpos($name, " ") !== FALSE){
		die(lang('plugin/th_chat', 'jdj_th_chat_text_php_54'));
	}
	if(DB::fetch_first("SELECT uid FROM ".DB::table('newz_nick')." WHERE name='{$name}' AND uid!='{$uid}'")){
		die(lang('plugin/th_chat', 'jdj_th_chat_text_php_15'));
	}
	if(DB::fetch_first("SELECT uid FROM ".DB::table('common_member')." WHERE username='{$name}' AND uid!='{$uid}'")){
		die(lang('plugin/th_chat', 'jdj_th_chat_text_php_15'));
	}
	if($is_mod)
	{
		$re = DB::query("SELECT uid,total,time FROM ".DB::table('newz_nick')." WHERE uid='{$uid}'");
		if($re = DB::fetch($re)){
			if($time-$re['time']<86400){
				if($re['total']>1&&$config['namemode']==2){
					die(lang('plugin/th_chat', 'jdj_th_chat_text_php_33'));
				}else{
					DB::query("UPDATE ".DB::table('newz_nick')." SET name='{$name}',total=2,sound_1='{$_POST['sound_1']}',sound_2='{$_POST['sound_2']}' WHERE uid='{$uid}' LIMIT 1");
				}
			}else{
				DB::query("UPDATE ".DB::table('newz_nick')." SET name='{$name}',total=1,time='{$time}',sound_1='{$_POST['sound_1']}',sound_2='{$_POST['sound_2']}' WHERE uid='{$uid}' LIMIT 1");
			}
		}else{
			DB::query("INSERT INTO ".DB::table('newz_nick')." (uid,name,total,time,sound_1,sound_2) VALUES ('{$uid}','{$name}','1','{$time}',{$_POST['sound_1']},{$_POST['sound_2']})");
		}
	}
	DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,text,time,ip) VALUES ('{$uid}','0','{$name}','{$time}','changename')");
	exit('อัพเดทการตั้งค่าสำเร็จ!');
}else{
	$olddata = DB::fetch_first("SELECT name,sound_1,sound_2 FROM ".DB::table('newz_nick')." WHERE uid='{$_G['uid']}'");
	$olddata['sound_1'] = $olddata['sound_1']=='1'?$olddatas1='checked':$olddatas2='checked';
	$olddata['sound_2'] = $olddata['sound_2']=='0'?$olddatas4='checked':$olddatas3='checked';
	$olddata['name'] = $olddata['name']?$olddata['name']:$_G['username'];
}
include template('common/header_ajax');
include template('th_chat:window');
include template('common/footer_ajax');
?>