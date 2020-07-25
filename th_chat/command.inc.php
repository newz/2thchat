<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
$uid = $_G['uid'];
$gid = $_G['groupid'];
$id = $_POST['xid'];
$command = $_POST['command'];
$ip = $_SERVER['REMOTE_ADDR'];
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
include 'functions.php';
if($uid<1){
	die('Access Denied');
}
$is_mod = in_array($_G['adminid'],array(1,2,3));
if($command == 'del' && $is_mod)
{
	DB::Query("DELETE FROM ".DB::table('newz_data')." WHERE id=$id LIMIT 1");
	DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,text,time,ip) VALUES ('$uid','0','$id','$time','delete')");
	echo lang('plugin/th_chat', 'jdj_th_chat_text_php_44');
}else if($command == 'ban' && $is_mod){
	$icon = 'alert';
	$banned = DB::query("SELECT value FROM ".DB::table('common_pluginvar')." WHERE variable='chat_ban' AND displayorder='15' LIMIT 1");
	$banned = DB::fetch($banned);
	$uid_ban = $id;
	if(!in_array($uid_ban,$banned)  && $uid_ban != $uid){
		$banned[] = $uid_ban;
			$username_ban = DB::query("SELECT m.username AS name,m.groupid,g.color,n.name AS nick FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.uid='{$uid_ban}' LIMIT 1");
			$username_ban = DB::fetch($username_ban);
			if($username_ban['nick']&&$config['namemode']==2){
				$username_banz = $username_ban['nick'];
			}else{
				$username_banz = $username_ban['name'];
			}
			$icon = 'alert';
			$username_ban = '<font color="'.$username_ban['color'].'">'.addslashes(htmlspecialchars_decode($username_banz)).'</font>';
			$text = '<a href ="home.php?mod=space&uid='.$uid_ban.'" class="nzca" target="_blank">'.$username_ban.'</a> <font color="red">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_23').'</font>';
		$banned_new = array();
		foreach($banned as $uid_banned){
			if($uid_banned&&!in_array($uid_banned,$banned_new)){
				$banned_new[] = $uid_banned;
			}
		}
		$banned = implode(',',$banned_new);
		DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$banned}' WHERE variable='chat_ban' AND displayorder='15' LIMIT 1");
		DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','$touid','$icon','$text','$time','$ip')");
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_27');
	}else{
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_26');
	}
}else if($command == 'unban' && $is_mod){
	$icon = 'alert';
	$banned = DB::query("SELECT value FROM ".DB::table('common_pluginvar')." WHERE variable='chat_ban' AND displayorder='15' LIMIT 1");
	$banned = DB::fetch($banned);
	$uid_ban = $id;
	if(in_array($uid_ban,$banned)){
		$key = array_search($uid_ban, $banned);
		if($key !== FALSE) unset($banned[$key]);
			$username_ban = DB::query("SELECT m.username AS name,m.groupid,g.color,n.name AS nick FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.uid='{$uid_ban}' LIMIT 1");
			$username_ban = DB::fetch($username_ban);
			if($username_ban['nick']&&$config['namemode']==2){
				$username_banz = $username_ban['nick'];
			}else{
				$username_banz = $username_ban['name'];
			}
			$icon = 'alert';
			$username_ban = '<font color="'.$username_ban['color'].'">'.addslashes(htmlspecialchars_decode($username_banz)).'</font>';
			$text = '<font color="red">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_28').'</font> <a href ="home.php?mod=space&uid='.$uid_ban.'" class="nzca" target="_blank">'.$username_ban.'</a>';
		$banned_new = array();
		foreach($banned as $uid_banned){
			if($uid_banned&&!in_array($uid_banned,$banned_new)){
				$banned_new[] = $uid_banned;
			}
		}
		$banned = implode(',',$banned_new);
		DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$banned}' WHERE variable='chat_ban' AND displayorder='15' LIMIT 1");
		DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','$touid','$icon','$text','$time','$ip')");
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_30');
	}else{
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_29');
	}
}else if($command=='clear' && $is_mod){
	$icon = 'alert';
	$touid= 0;
	$ip = 'clear';
	$q = DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','$touid','alert','$text','$time','$ip')");
	$last = DB::insert_id($q);
	DB::query("DELETE FROM ".DB::table('newz_data')." WHERE id<".$last);
	echo lang('plugin/th_chat', 'jdj_th_chat_text_php_45');
}else if($command=='point'&&$config['chat_point']){
	$icon = 'alert';
	$point = explode('|',$id);
	$uid_point = intval($point[0]);
	$res = $point[2];
	$point = intval($point[1]);
	if($uid_point&&($point==1||$point==-1)&&($uid_point!=$uid)||$uid==1){
		$re = DB::query("SELECT uid,name,point_time FROM ".DB::table('newz_nick')." WHERE uid='{$uid}'");
		if($re = DB::fetch($re)){
			if($time-$re['point_time']<10){
				die(json_encode(array('type'=>1,'error'=>''.lang('plugin/th_chat', 'jdj_th_chat_text_php_12').'')));
			}else{
				DB::query("UPDATE ".DB::table('newz_nick')." SET point_time='{$time}' WHERE uid='{$uid}' LIMIT 1");
			}
		}else{
			DB::query("INSERT INTO ".DB::table('newz_nick')." (uid,point_time) VALUES ('{$uid}','{$time}')");
		}
		if($point>0){
			$point = '+'.$point;
		}
		if($touid!=$uid_point){
			$touid=0;
		}
		DB::query("UPDATE ".DB::table('common_member_count')." SET extcredits{$config['chat_point']}=extcredits{$config['chat_point']}{$point} WHERE uid='{$uid_point}' LIMIT 1");
		$username_point = DB::query("SELECT m.username AS name,m.groupid,g.color,n.name AS nick,p.extcredits{$config['chat_point']} AS point FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid LEFT JOIN ".DB::table('common_member_count')." p ON m.uid=p.uid WHERE m.uid='{$uid_point}' LIMIT 1");
		$username_point = DB::fetch($username_point);
		$total_point = $username_point['point'];
		if($username_point['nick']&&$config['namemode']==2){
			$username_pointz = $username_point['nick'];
		}else{
			$username_pointz = $username_point['name'];
		}
		$username_point = '<font color="'.$username_point['color'].'">'.addslashes(htmlspecialchars_decode($username_pointz)).'</font>';
		if($total_point>=0){
			$total_point='<font color="green">'.$total_point.'</font>';
		}else{
			$total_point='<font color="red">'.$total_point.'</font>';
		}
		if($point>0){
			$point='<font color="green">'.$point.'</font>';
		}else{
			$point='<font color="red">'.$point.'</font>';
		}
		$text = '@<a href="home.php?mod=space&uid='.$uid_point.'" class="nzca" target="_blank">'.$username_point.'</a> '.$point.' = '.$total_point.' '.$res;
		DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','$touid','$icon','$text','$time','$ip')");
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_57');
	}
}else if($command=='name' && $is_mod){
	$icon = 'alert';
	$pieces = explode("|:|", $id,2);
	if (get_magic_quotes_gpc()) {
		$name = stripslashes($pieces[1]);
	}else {
		$name = $pieces[1];
	}
	$cid=$pieces[0];
	$name = paddslashes(htmlspecialchars(preg_replace("/<script[^\>]*?>(.*?)<\/script>/i","", htmlspecialchars_decode($name))));
	if($name==''){
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_06');
	}
	else if($config['namemax']!=0 && dstrlen($name) > $config['namemax']){
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_55').' '.$config['namemax'].' '.lang('plugin/th_chat', 'jdj_th_chat_text_php_22');
	}else if(dstrlen($name) < $config['namemin']){
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_56').' '.$config['namemin'].' '.lang('plugin/th_chat', 'jdj_th_chat_text_php_22');
	}
	else if(strpos($name, " ") !== FALSE){
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_54');
	}
	else if(DB::fetch_first("SELECT uid FROM ".DB::table('newz_nick')." WHERE name='{$name}' AND uid!='{$cid}'")){
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_15');
	}
	else if(DB::fetch_first("SELECT uid FROM ".DB::table('common_member')." WHERE username='{$name}' AND uid!='{$cid}'")){
		echo lang('plugin/th_chat', 'jdj_th_chat_text_php_15');
	}
	else{
	DB::query("INSERT INTO ".DB::table('newz_nick')." (uid,name,total,time) VALUES ('{$cid}','{$name}','1','{$time}') ON DUPLICATE KEY UPDATE name='{$name}'");
	DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,text,time,ip) VALUES ('$cid','0','$name','$time','changename')");
	$name = htmlspecialchars_decode($name);
	DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','0','$icon','".lang('plugin/th_chat', 'jdj_th_chat_text_php_35').":$cid ".lang('plugin/th_chat', 'jdj_th_chat_text_php_60')." $name','$time','$ip')");
	echo lang('plugin/th_chat', 'jdj_th_chat_text_php_34');
	}
}else{
	echo lang('plugin/th_chat', 'jdj_th_chat_text_php_10');
}
?>