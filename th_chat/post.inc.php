<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$gid = $_G['groupid'];
$is_mod = in_array($_G['adminid'],array(1,2,3));
include 'functions.php';
if($uid<1){
	die(json_encode(array('type'=>1,'error'=>'กรุณาเข้าสู่ระบบ')));
}
$banned = DB::fetch_first("SELECT value FROM ".DB::table('common_pluginvar')." WHERE variable='chat_ban' AND displayorder='16' LIMIT 1");
$banned = explode(",",$banned['value']);
if((in_array($gid,array(4,5))||in_array($uid,$banned))&&!$is_mod){
	die(json_encode(array('type'=>1,'error'=>'ขออภัย คุณถูกแบนอยู่')));
}
if (get_magic_quotes_gpc()) {
	$text = stripslashes($_POST['text']);
}
else {
	$text = $_POST['text'];
}
$id = intval($_POST['lastid']);
$touid = intval($_POST['touid']);
$quota = intval($_POST['quota']);
$command = $_POST['command'];
$ip = $_SERVER['REMOTE_ADDR'];
$a = file_get_contents(DISCUZ_ROOT.'/source/plugin/th_chat/template/big.htm');
	if(substr($text,0,4)=="!del" && $is_mod){
		$id = intval(substr($text,4));
		DB::query("DELETE FROM ".DB::table('newz_data')." WHERE id=$id LIMIT 1");
		DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,text,time,ip) VALUES ('$uid','0','$id','$time','delete')");
		die(json_encode(array('type'=>1,'error'=>'ลบสำเร็จแล้ว!')));
	}elseif(substr($text,0,4)=="!ban"&&$is_mod){
		$uid_ban = intval(substr($text,4));
		if($uid_ban && !in_array($uid_ban,$banned) && $uid_ban != $uid){
			$banned[] = $uid_ban;
			$username_ban = DB::fetch_first("SELECT username FROM ".DB::table('common_member')." WHERE uid='{$uid_ban}' LIMIT 1");
			$username_ban = '@'.addslashes($username_ban['username']);
			$icon = 'alert';
			$touid = 0;
			$text = $username_ban.' [color=red]ถูกแบน[/color]';
			$bannedq = implode(',',$banned);
			DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$bannedq}' WHERE variable='chat_ban' AND displayorder='16' LIMIT 1");
		}
	}elseif(substr($text,0,6)=="!unban"&&$is_mod){
		$uid_ban = intval(substr($text,6));
		if($uid_ban && in_array($uid_ban,$banned)){
			$key = array_search($uid_ban, $banned);
			if($key !== FALSE) unset($banned[$key]);
			$username_ban = DB::fetch_first("SELECT username FROM ".DB::table('common_member')." WHERE uid='{$uid_ban}' LIMIT 1");
			$username_ban = '@'.addslashes($username_ban['username']);
			$icon = 'alert';
			$touid = 0;
			$text = '[color=red]ปลดแบน[/color] '.$username_ban;
			$bannedq = implode(',',$banned);
			DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$bannedq}' WHERE variable='chat_ban' AND displayorder='16' LIMIT 1");
		}
	}elseif(substr($text,0,6)=="!point"&&$config['chat_point']){
		$point = explode('|',substr($text,6));
		$uid_point = intval($point[0]);
		$res = $point[2];
		$point = intval($point[1]);
		if($uid_point&&($point==1||$point==-1)&&($uid_point!=$uid)||$uid==1){
			$re = DB::query("SELECT uid,point_time FROM ".DB::table('newz_nick')." WHERE uid='{$uid}'");
			if($re = DB::fetch($re)){
				if($time-$re['point_time']<$config['point_time']){
					die(json_encode(array('type'=>1,'error'=>'คุณสามารถให้คะแนนได้ 1 ครั้งภายใน 10 วินาที')));
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
			$this_username_name = DB::query("SELECT username FROM ".DB::table('common_member')." WHERE uid='{$uid_point}' LIMIT 1");
			$this_username_name = DB::fetch($this_username_name);
			if($config['chat_point']!='9'){
				DB::query("UPDATE ".DB::table('common_member_count')." SET extcredits{$config['chat_point']}=extcredits{$config['chat_point']}{$point} WHERE uid='{$uid_point}' LIMIT 1");
				$username_point = DB::query("SELECT extcredits{$config['chat_point']} AS point FROM ".DB::table('common_member_count')." WHERE uid='{$uid_point}' LIMIT 1");
				$username_point = DB::fetch($username_point);
			}else{
				DB::query("INSERT INTO ".DB::table('newz_nick')." (uid,point_total) VALUES ('{$uid_point}',{$point}) ON DUPLICATE KEY UPDATE point_total=point_total{$point}");
				$username_point = DB::query("SELECT point_total AS point FROM ".DB::table('newz_nick')." WHERE uid='{$uid_point}' LIMIT 1");
				$username_point = DB::fetch($username_point);
			}
			$total_point = $username_point['point'];
			if($point>0||$point==0){
				$point='[color=green]'.$point.'[/color]';
			}else{
				$point='[color=red]'.$point.'[/color]';
			}
			$icon = 'alert';
			$touid = 0;
			$text = '@'.$this_username_name['username'].' '.$point.' = '.$total_point.' '.$res;
			$quota = 0;
		}
	}
	if($command=="notice"&&$is_mod){
		$icon = 'alert';
		$touid = 0;
		$ip = 'notice';
	}elseif(substr($command,0,4)=="edit"&&($config['editmsg']!=0)){
		$editid = intval(substr($command,5));
		if($config['editmsg']==1&&!$is_mod){
			die(json_encode(array('type'=>1,'error'=>'Access Denied')));
		}
		$user = DB::fetch(DB::query("SELECT uid FROM ".DB::table('newz_data')." WHERE id='{$editid}'"));
		if($config['editmsg']==2&&(!$is_mod||$user['uid']!=$uid)){
			die(json_encode(array('type'=>1,'error'=>'Access Denied')));
		}else if($config['editmsg']==3&&($user['uid']!=$uid)){
			die(json_encode(array('type'=>1,'error'=>'Access Denied')));
		}
		$edittext = 'ถูกแก้ไข';
		if($user['uid']!=$uid){
			$edittext .='โดย '.$_G['username'];
		}
		$ip = 'edit';
		$icon = $editid;
	}
$txtlen = mb_strlen($text);
if($txtlen>$config['chat_strlen']){
	die(json_encode(array('type'=>1,'error'=>'ขออภัย ข้อความยาวเกินไป')));
}
if($uid==$touid){
	die();
}
include(DISCUZ_ROOT.'/source/function/function_discuzcode.php');
$config['useemo'] = $config['useemo']?0:1;
$config['usedzc'] = $config['usedzc']?0:1;
$config['useunshowdzc'] = $config['useunshowdzc']?0:1;
if($config['autourl']){
	$text= preg_replace('#(^|\s)([a-z]+://([^\s\w/]?[\w/])*)#is', '\\1[url]\\2[/url]', $text);
	$text = preg_replace('#(^|\s)((www|ftp)\.([^\s\w/]?[\w/])*)#is', '\\1[url]\\2[/url]', $text);
}

if($config['mediacode']){
	$text = preg_replace("/\[media=([\w,]+)\]\s*([^\[\<\r\n]+?)\s*\[\/media\]/is", "", $text);
	$text = str_replace("[media]", "[mpopup][media=x,640,480]", $text);
	$text = str_replace("[/media]", "[/media][/mpopup]", $text);
}

$query_bw = DB::query("SELECT * FROM ".DB::table('common_word'));
while ($bw = DB::fetch($query_bw))
{
	$text = str_replace($bw['find'],$bw['replacement'],$text);
}
$text = preg_replace('/\[quota\](.*?)\[\/quota\]/', '[quota]$1[[color=#fff][/color]/quota]', $text);
if($config['usemore']){$usemore = -$_G['groupid'];}else{$usemore = 1;}
$text = discuzcode($text,$config['useemo'],$config['usedzc'],$config['usehtml'],1,$usemore,$config['useimg'],1,0,$config['useunshowdzc'],0, $config['mediacode']);
$text = paddslashes(preg_replace('/\[mpopup\](.*?)\[\/mpopup\]/', '<div class="nzchatpopup" onclick="nzChatPopup(this)">คลิกเพื่อดูวีดีโอ</div><div class="nzchatpopuph">$1</div>', $text));
if($txtlen==1){
	if(preg_match('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F415}](?:\x{200D}\x{1F9BA})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9BD})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9AF})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F471}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F9CF}\x{1F647}\x{1F926}\x{1F937}\x{1F46E}\x{1F482}\x{1F477}\x{1F473}\x{1F9B8}\x{1F9B9}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F486}\x{1F487}\x{1F6B6}\x{1F9CD}\x{1F9CE}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}\x{1F9D8}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F471}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F9CF}\x{1F647}\x{1F926}\x{1F937}\x{1F46E}\x{1F482}\x{1F477}\x{1F473}\x{1F9B8}\x{1F9B9}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F486}\x{1F487}\x{1F6B6}\x{1F9CD}\x{1F9CE}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}\x{1F9D8}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{265F}-\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6D5}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6FA}\x{1F7E0}-\x{1F7EB}\x{1F90D}-\x{1F93A}\x{1F93C}-\x{1F945}\x{1F947}-\x{1F971}\x{1F973}-\x{1F976}\x{1F97A}-\x{1F9A2}\x{1F9A5}-\x{1F9AA}\x{1F9AE}-\x{1F9CA}\x{1F9CD}-\x{1F9FF}\x{1FA70}-\x{1FA73}\x{1FA78}-\x{1FA7A}\x{1FA80}-\x{1FA82}\x{1FA90}-\x{1FA95}]/u', $text)){
		$text = '<span style="font-size:30px">'.$text.'</span>';
	}
}
if(($is_mod>0)&&$text=='!clear'){
	$ip = 'clear';
	$icon = 'alert';
	$touid = 0;
	$text = 'ล้างข้อมูล';
	$needClear = 1;
}
$text = getat($text);
if($ip == 'notice'){
	DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$text}' WHERE variable='welcometext' AND displayorder='1' LIMIT 1");
	include_once libfile('function/cache');
	updatecache('plugin');
}elseif($ip == 'edit'){
	$text .=' <span style="color: #F4511E;border: 1px solid #F4511E;border-radius: 20px;padding: 0 4px;" title="'.get_date($time).'">'.$edittext.'</span>';
	DB::query("UPDATE ".DB::table('newz_data')." SET text='{$text}' WHERE id='{$icon}' LIMIT 1");
}
if($quota>0 && $ip != 'clear'){
	$text = getquota($quota).$text;
}
DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','$touid','$icon','$text','".time()."','$ip')");

/*RESEND*/

$last = DB::insert_id();
if($needClear){
	DB::query("DELETE FROM ".DB::table('newz_data')." WHERE id<".$last);
}else{
	DB::query("DELETE FROM ".DB::table('newz_data')." WHERE id<".($last-$config['chat_log']));
}
$re = DB::query("SELECT n.*,m.username AS name,mt.username AS toname,g.color,gt.color AS tocolor 
FROM ".DB::table('newz_data')." n 
LEFT JOIN ".DB::table('common_member')." m ON n.uid=m.uid 
LEFT JOIN ".DB::table('common_member')." mt ON n.touid=mt.uid 
LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid 
LEFT JOIN ".DB::table('common_usergroup')." gt ON mt.groupid=gt.groupid 
WHERE  id>{$id} AND (n.touid='0' OR n.touid='{$uid}' OR n.uid='{$uid}') 
ORDER BY id DESC LIMIT 30");
$body=array();
while($c = DB::fetch($re)){
$c['text'] = preg_replace('/\[quota\](.*?)\[\/quota\]/', '$1', $c['text']);
	if($c['ip'] == 'delete'){
		$body[$c['id']] .= '<script>nzchatobj("#nzrows_'.$c['text'].'").fadeOut(200);</script>';
		continue;
	}elseif($c['ip'] == 'notice'){
		DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='".addslashes($c['text'])."' WHERE variable='welcometext' AND displayorder='1' LIMIT 1");
		include_once libfile('function/cache');
		updatecache('plugin');
		$body[$c['id']] .= '<script>nzchatobj("#nzchatnotice").html("'.addcslashes($c['text'],'"').'");</script>';
		continue;
	}elseif($c['ip'] == 'edit'){
		$body[$c['id']] .= '<script>nzchatobj("#nzchatcontent'.$c['icon'].'").html("'.addcslashes($c['text'],'"').'");</script>';
		continue;
	}
	if($c['ip']=='clear'){
		$seedd = $time.'_'.$uid.'_'.rand(1,999);
		$c['text'] = '<span style="color:red" id="del_'.$seedd.'">แจ้งเตือน:</span> <span id="nzchatcontent'.$c['id'].'">ล้างข้อมูล<script type="text/javascript">nzchatobj("#del_'.$seedd.'").parent().parent().parent().'.($config['chat_type']==1?'next':'prev').'Until().remove();</script>';
	}elseif($c['icon']=='alert'){
		$c['text'] = '<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==0){
		$c['text'] = '<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==$uid){
		$c['text'] = ($config['pm_sound']?'<embed name="pmsoundplayer" width="0" height="0" src="source/plugin/th_chat/images/player.swf" flashvars="sFile='.$config['pm_sound'].'" menu="false" allowscriptaccess="sameDomain" swliveconnect="true" type="application/x-shockwave-flash"></embed>':'').'<span style="color:#FF9900">กระซิบถึงคุณ <a href="javascript:;" onClick="nzTouid('.$c['uid'].')">(ตอบกลับ)</a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['uid']==$uid){
		$c['text'] = '<span style="color:#FF9900">กระซิบกับ <a href="home.php?mod=space&uid='.$c['touid'].'" class="nzca" target="_blank"><font color="'.$c['tocolor'].'"><span class="nzuname_'.$c['touid'].'">'.$c['toname'].'</span></font></a>: </span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}
	$body[$c['id']]  .= chatrow($c['id'],$c['text'],$c['uid'],$c['name'],$c['time'],$c['touid'],$c['icon'],$is_mod);
	if($c['ip']=='clear'){
		break;
	}
}
echo json_encode($body);
?>