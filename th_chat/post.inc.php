<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$gid = $_G['groupid'];
$is_mod = in_array($_G['adminid'],array(1,2,3));
include 'functions.php';
if($uid<1){
	die(json_encode(array('type'=>1,'error'=>''.lang('plugin/th_chat', 'jdj_th_chat_text_php_05').'')));
}
$banned = DB::query("SELECT value FROM ".DB::table('common_pluginvar')." WHERE variable='chat_ban' AND displayorder='15' LIMIT 1");
$banned = DB::fetch($banned);
eval("\$banned = array({$banned['value']});");
if((in_array($gid,array(4,5))||in_array($uid,$banned))&&!$is_mod){
	die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_11'))));
}
if (get_magic_quotes_gpc()) {
	$text = stripslashes($_POST['text']);
}
else {
	$text = $_POST['text'];
}
$f = file_get_contents(DISCUZ_ROOT.'/source/plugin/th_chat/template/discuz.htm');
$id = intval($_POST['lastid']);
$touid = intval($_POST['touid']);
$quota = intval($_POST['quota']);
$at = intval($_POST['at']);
$color = str_replace(array('\'','\\','"','<','>'),'',$_POST['color']);
$ip = $_SERVER['REMOTE_ADDR'];
$a = file_get_contents(DISCUZ_ROOT.'/source/plugin/th_chat/template/big.htm');
if($config['oldcommand']==1){
	if(substr($text,0,4)=="!ban"&&$is_mod){
		$uid_ban = intval(substr($text,4));
		if($uid_ban && !in_array($uid_ban,$banned) && $uid_ban != $uid){
			$banned[] = $uid_ban;
			$username_ban = DB::query("SELECT m.username AS name,m.groupid,g.color,n.name AS nick FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.uid='{$uid_ban}' LIMIT 1");
			$username_ban = DB::fetch($username_ban);
			if($username_ban['nick']&&$config['namemode']==2){
				$username_banz = $username_ban['nick'];
			}else{
				$username_banz = $username_ban['name'];
			}
			$icon = 'alert';
			$touid = 0;
			$username_ban = '[color='.$username_ban['color'].']'.htmlspecialchars_decode($username_banz).'[/color]';
			$text = '[url=home.php?mod=space&uid='.$uid_ban.'][b]'.$username_ban.'[/b][/url] [color=red]'.lang('plugin/th_chat', 'jdj_th_chat_text_php_23').'[/color]';
			$banned_new = array();
			foreach($banned as $uid_banned){
				if($uid_banned&&!in_array($uid_banned,$banned_new)){
					$banned_new[] = $uid_banned;
				}
			}
			$banned = implode(',',$banned_new);
			DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$banned}' WHERE variable='chat_ban' AND displayorder='15' LIMIT 1");
		}
	}elseif(substr($text,0,6)=="!unban"&&$is_mod){
		$uid_ban = intval(substr($text,6));
		if($uid_ban && in_array($uid_ban,$banned)){
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
			$touid = 0;
			$username_ban = '[color='.$username_ban['color'].']'.htmlspecialchars_decode($username_banz).'[/color]';
			$text = '[color=red]'.lang('plugin/th_chat', 'jdj_th_chat_text_php_28').'[/color] [url=home.php?mod=space.php&uid='.$uid_ban.'][b]'.$username_ban.'[/b][/url]';
			$banned_new = array();
			foreach($banned as $uid_banned){
				if($uid_banned&&!in_array($uid_banned,$banned_new)){
					$banned_new[] = $uid_banned;
				}
			}
			$banned = implode(',',$banned_new);
			DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$banned}' WHERE variable='chat_ban' AND displayorder='15' LIMIT 1");
		}
	}elseif(substr($text,0,6)=="!point"&&$config['chat_point']){
		$point = explode('|',substr($text,6));
		$uid_point = intval($point[0]);
		$res = $point[2];
		$point = intval($point[1]);
		if($uid_point&&($point==1||$point==-1)&&($uid_point!=$uid)||$uid==1){
			$re = DB::query("SELECT uid,point_time FROM ".DB::table('newz_nick')." WHERE uid='{$uid}'");
			if($re = DB::fetch($re)){
				if($time-$re['point_time']<10){
					die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_12'))));
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
			$username_point = DB::query("SELECT extcredits{$config['chat_point']} AS point FROM ".DB::table('common_member_count')." WHERE uid='{$uid_point}' LIMIT 1");
			$username_point = DB::fetch($username_point);
			$total_point = $username_point['point'];
			if($point>0){
				$point='[color=green]'.$point.'[/color]';
			}else{
				$point='[color=red]'.$point.'[/color]';
			}
			$icon = 'alert';
			$touid = 0;
			$text = ' '.$point.' = '.$total_point.' '.$res;
			$at = $uid_point;
			$quota = 0;
		}
	}else if(substr($text,0,7)=="!notice"&&$is_mod){
		$notice = substr($text,8);
		$icon = 'alert';
		$touid = 0;
		$text = $notice;
		$ip = 'notice';
	}
}
if(strpos($f,'&copy; <a href="http://2th.me/" target="_blank">2th Chat</a>')===false||strpos($a,'&copy; <a href="http://2th.me/" target="_blank">2th Chat</a>')===false)die();
$txtlen = strlen($text);
if($txtlen>$config['chat_strlen']){
	$text = '... '.substr($text,$txtlen-$config['chat_strlen']);
}
if($uid==$touid){
	die();
}
include(DISCUZ_ROOT.'/source/function/function_discuzcode.php');
$config['useemo'] = $config['useemo']?0:1;
$config['usedzc'] = $config['usedzc']?0:1;
$config['useunshowdzc'] = $config['useunshowdzc']?0:1;
if(strpos($f,'&copy; <a href="http://2th.me">2th</a>')===false)die();
if($config['autourl']){
	$text= preg_replace('#(^|\s)([a-z]+://([^\s\w/]?[\w/])*)#is', '\\1[url]\\2[/url]', $text);
	$text = preg_replace('#(^|\s)((www|ftp)\.([^\s\w/]?[\w/])*)#is', '\\1[url]\\2[/url]', $text);
}
if($config['mediacode']){
$text = preg_replace("/\[media=([\w,]+)\]\s*([^\[\<\r\n]+?)\s*\[\/media\]/ies", "", $text);
if($config['spoiler']){
$text = str_replace("[media]", "[spoil][media=x,480,360]", $text);
$text = str_replace("[/media]", "[/media][/spoil]", $text);
}else{
$text = str_replace("[media]", "[media=x,252,189]", $text);
}
}
$text = paddslashes(discuzcode($text,$config['useemo'],$config['usedzc'],$config['usehtml'],1,1,$config['useimg'],1,0,$config['useunshowdzc'],0, $config['mediacode']));
if(($is_mod>0)&&$text=='!clear'&&$config['oldcommand']==1){
$ip = 'clear';
$icon = 'alert';
$touid = 0;
$text = lang('plugin/th_chat', 'jdj_th_chat_text_php_46');
$needClear = 1;
}
if($color!='default'){
	$text = '<span style="color:#'.$color.';">'.$text.'</span>';
}
if($quota>0 && $config['quota'] && $ip != 'clear'){
	if($quo = DB::query("SELECT text FROM ".DB::table('newz_data')." WHERE id='{$quota}'"))
	{
		$quo = DB::fetch($quo);
		$text = addslashes($quo['text']).' // '.$text;
		$txtlen = strlen($text);
		if($txtlen>$config['chat_strlen']){
			$text = '... '.substr($text,$txtlen-$config['chat_strlen']);
		}
	}
}else if($at>0 && $ip != 'clear'){
	$user = DB::query("SELECT m.username,m.groupid,g.color,n.name FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.uid='{$at}' LIMIT 1");
	$user = DB::fetch($user);
	if($user['name']&&$config['namemode']==2){
		$userz = $user['name'];
	}else{
		$userz = $user['username'];
	}
	$userz = addslashes(htmlspecialchars_decode($userz));
	$text = '@<a class="nzca"><font color="'.$user['color'].'">'.$userz.'</font></a> '.$text;
}
$icon==''?$icon=checkOs():$icon=$icon;
DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','$touid','$icon','$text','".time()."','$ip')");

/*RESEND*/

$last = DB::insert_id();
if($needClear){
	DB::query("DELETE FROM ".DB::table('newz_data')." WHERE id<".$last);
}else{
	DB::query("DELETE FROM ".DB::table('newz_data')." WHERE id<".($last-$config['chat_log']));
}
$re = DB::query("SELECT n.*,m.username AS name,mt.username AS toname,g.color,ni.name AS nick,nt.name AS tonick 
FROM ".DB::table('newz_data')." n 
LEFT JOIN ".DB::table('common_member')." m ON n.uid=m.uid 
LEFT JOIN ".DB::table('common_member')." mt ON n.touid=mt.uid 
LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid 
LEFT JOIN ".DB::table('newz_nick')." ni ON n.uid=ni.uid 
LEFT JOIN ".DB::table('newz_nick')." nt ON n.touid=nt.uid 
WHERE  id>{$id} AND (n.touid='0' OR n.touid='{$uid}' OR n.uid='{$uid}') 
ORDER BY id DESC LIMIT 30");
$body=array();
while($c = DB::fetch($re)){
	if ($c['ip'] == 'changename'){
		$body[$c['id']] .= '<script>nzchatobj(".nzu'.$config['namemode']==1?'status':'name'.'_'.$c['uid'].'").html("'.htmlspecialchars_decode($c['text']).'");</script>';
		continue;
	}elseif($c['ip'] == 'delete'){
		$body[$c['id']] .= '<script>nzchatobj("#nzrows_'.$c['text'].'").fadeOut(200);</script>';
		continue;
	}elseif($c['ip'] == 'notice'){
		DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$c['text']}' WHERE variable='welcometext' AND displayorder='1' LIMIT 1");
	}
	if($config['namemode']==1){$c['status'] = $c['nick'];}
	if((strval($c['nick'])===''&&$config['namemode']!=1)||$config['namemode']!=2){$c['nick'] = $c['name'];}
	if(strval($c['tonick'])==='')
	$c['tonick'] = $c['toname'];
	$c['tonick'] = htmlspecialchars_decode($c['tonick']);
	$c['text'] .='<script type="text/javascript">nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);</script>';
	if($c['ip']=='clear'){
		$seedd = $time.'_'.$uid.'_'.rand(1,999);
		$c['text'] = '<span style="color:red" id="del_'.$seedd.'">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_14').'</span> <span id="nzchatcontent'.$c['id'].'">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_46').'<script type="text/javascript">nzchatobj("#del_'.$seedd.'").parent().parent().parent().'.($config['chat_type']==1?'next':'prev').'Until().remove();</script>';
	}elseif($c['icon']=='alert'){
		$c['text'] = '<span style="color:red">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_14').'</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==0){
		$c['text'] = '<span style="color:#3366CC">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_38').'</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==$uid){
		$c['text'] = ($config['pm_sound']?'<embed name="pmsoundplayer" width="0" height="0" src="source/plugin/th_chat/images/player.swf" flashvars="sFile='.$config['pm_sound'].'" menu="false" allowscriptaccess="sameDomain" swliveconnect="true" type="application/x-shockwave-flash"></embed>':'').'<span style="color:#FF9900">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_03').' <a href="javascript:;" onClick="nzTouid('.$c['uid'].')">reply</a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['uid']==$uid){
		$c['text'] = '<span style="color:#FF9900">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_02').' <a href="space-uid-'.$c['touid'].'.html" target="_blank">'.$c['tonick'].'</a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}
	if(!$config['showos']&&$c['icon']!='alert')$c['icon']='';
	$body[$c['id']]  .= chatrow($c['id'],$c['text'],$c['uid'],$c['nick'],$c['time'],$c['color'],$c['touid'],0,$c['icon'],$is_mod,$c['status']);
	if($c['ip']=='clear'){
		break;
	}
}
echo json_encode($body);
?>