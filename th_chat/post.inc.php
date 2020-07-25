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
$banned = DB::fetch_first("SELECT value FROM ".DB::table('common_pluginvar')." WHERE variable='chat_ban' AND displayorder='16' LIMIT 1");
$banned = explode(",",$banned['value']);
if((in_array($gid,array(4,5))||in_array($uid,$banned))&&!$is_mod){
	die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_11'))));
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
if(preg_match("/([a-f0-9]{3}){1,2}\b/i",$_POST['color'])){
	$color = $_POST['color'];
}else{
	$color = 'default';
}
$color = str_replace(array('\'','\\','"','<','>'),'',$_POST['color']);
$ip = $_SERVER['REMOTE_ADDR'];
$a = file_get_contents(DISCUZ_ROOT.'/source/plugin/th_chat/template/big.htm');
	if(substr($text,0,4)=="!del" && $is_mod){
		$id = intval(substr($text,4));
		DB::query("DELETE FROM ".DB::table('newz_data')." WHERE id=$id LIMIT 1");
		DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,text,time,ip) VALUES ('$uid','0','$id','$time','delete')");
		die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_44'),'script_add'=>1,'script'=>'nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);nzLoadText();')));
	}
	elseif(substr($text,0,5)=="!name" && $is_mod){
		$icon = 'alert';
		$id = substr($text,5);
		$pieces = explode("|:|", $id,2);
		if (get_magic_quotes_gpc()) {
			$name = stripslashes($pieces[1]);
		}else {
			$name = $pieces[1];
		}
		$cid=intval($pieces[0]);
		$name = paddslashes(htmlspecialchars(preg_replace("/<script[^\>]*?>(.*?)<\/script>/i","", htmlspecialchars_decode($name))));
		if($name==''){
			die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_06'),'script_add'=>1,'script'=>'nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);')));
		}
		else if($config['namemax']!=0 && dstrlen($name) > $config['namemax']){
			die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_55').' '.$config['namemax'].' '.lang('plugin/th_chat', 'jdj_th_chat_text_php_22'),'script_add'=>1,'script'=>'nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);')));
		}else if(dstrlen($name) < $config['namemin']){
			die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_56').' '.$config['namemin'].' '.lang('plugin/th_chat', 'jdj_th_chat_text_php_22'),'script_add'=>1,'script'=>'nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);')));
		}
		else if(strpos($name, " ") !== FALSE){
			die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_54'),'script_add'=>1,'script'=>'nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);')));
		}
		else if(DB::fetch_first("SELECT uid FROM ".DB::table('newz_nick')." WHERE name='{$name}' AND uid!='{$cid}'")){
			die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_15'),'script_add'=>1,'script'=>'nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);')));
		}
		else if(DB::fetch_first("SELECT uid FROM ".DB::table('common_member')." WHERE username='{$name}' AND uid!='{$cid}'")){
			die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_15'),'script_add'=>1,'script'=>'nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);')));
		}
		else{
			DB::query("INSERT INTO ".DB::table('newz_nick')." (uid,name,total,time) VALUES ('{$cid}','{$name}','1','{$time}') ON DUPLICATE KEY UPDATE name='{$name}'");
			DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,text,time,ip) VALUES ('$cid','0','$name','$time','changename')");
			$name = htmlspecialchars_decode($name);
			DB::query("INSERT INTO ".DB::table('newz_data')." (uid,touid,icon,text,time,ip) VALUES ('$uid','0','$icon','".lang('plugin/th_chat', 'jdj_th_chat_text_php_35').":$cid ".lang('plugin/th_chat', 'jdj_th_chat_text_php_60')." $name','$time','$ip')");
			die(json_encode(array('type'=>1,'error'=>lang('plugin/th_chat', 'jdj_th_chat_text_php_34'),'script_add'=>1,'script'=>'nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);nzLoadText();')));
		}
	}elseif(substr($text,0,4)=="!ban"&&$is_mod){
		$uid_ban = intval(substr($text,4));
		if($uid_ban && !in_array($uid_ban,$banned) && $uid_ban != $uid){
			$banned[] = $uid_ban;
			$username_ban = DB::fetch_first("SELECT username FROM ".DB::table('common_member')." WHERE uid='{$uid_ban}' LIMIT 1");
			$username_ban = '@'.addslashes($username_ban['username']);
			$icon = 'alert';
			$touid = 0;
			$text = $username_ban.' [color=red]'.lang('plugin/th_chat', 'jdj_th_chat_text_php_23').'[/color]';
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
			$text = '[color=red]'.lang('plugin/th_chat', 'jdj_th_chat_text_php_28').'[/color] '.$username_ban;
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
			$this_username_name = DB::query("SELECT username FROM ".DB::table('common_member')." WHERE uid='{$uid_point}' LIMIT 1");
			$this_username_name = DB::fetch($this_username_name);
			if($config['chat_point']!='9'){
				DB::query("UPDATE ".DB::table('common_member_count')." SET extcredits{$config['chat_point']}=extcredits{$config['chat_point']}{$point} WHERE uid='{$uid_point}' LIMIT 1");
				$username_point = DB::query("SELECT extcredits{$config['chat_point']} AS point FROM ".DB::table('common_member_count')." WHERE uid='{$uid_point}' LIMIT 1");
				$username_point = DB::fetch($username_point);
			}else{
				DB::query("INSERT INTO ".DB::table('newz_nick')." (uid,name,point_total) VALUES ('{$uid_point}','{$this_username_name['username']}',{$point}) ON DUPLICATE KEY UPDATE point_total=point_total{$point}");
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
		$text .=' @'.get_date($time);
		if($user['uid']!=$uid){
			$text .=' '.lang('plugin/th_chat', 'jdj_th_chat_text_php_17').' '.$_G['username'];
		}
		$ip = 'edit';
		$icon = $editid;
	}
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
if($config['autourl']){
	$text= preg_replace('#(^|\s)([a-z]+://([^\s\w/]?[\w/])*)#is', '\\1[url]\\2[/url]', $text);
	$text = preg_replace('#(^|\s)((www|ftp)\.([^\s\w/]?[\w/])*)#is', '\\1[url]\\2[/url]', $text);
}

if($config['mediacode']){
$text = preg_replace("/\[media=([\w,]+)\]\s*([^\[\<\r\n]+?)\s*\[\/media\]/is", "", $text);
$text = str_replace("[media]", "[mpopup][media=x,640,480]", $text);
$text = str_replace("[/media]", "[/media][/mpopup]", $text);
}
if($config['useimg']){
$text = str_replace("[img]", "[ipopup][img]", $text);
$text = str_replace("[/img]", "[/img][/ipopup]", $text);
}

$query_bw = DB::query("SELECT * FROM ".DB::table('common_word'));
while ($bw = DB::fetch($query_bw))
{
	$text = str_replace($bw['find'],$bw['replacement'],$text);
}
$text = preg_replace('/\[quota\](.*?)\[\/quota\]/', '[quota]$1[[color=#fff][/color]/quota]', $text);
if($config['usemore']){$usemore = -$_G['groupid'];}else{$usemore = 1;}
$text = discuzcode($text,$config['useemo'],$config['usedzc'],$config['usehtml'],1,$usemore,$config['useimg'],1,0,$config['useunshowdzc'],0, $config['mediacode']);
$text = preg_replace('/\[ipopup\](.*?)\[\/ipopup\]/', '<div class="nzchatpopup" onclick="nzChatPopup(this)">คลิกเพื่อดูรูป</div><div class="nzchatpopuph">$1</div>', $text);
$text = paddslashes(preg_replace('/\[mpopup\](.*?)\[\/mpopup\]/', '<div class="nzchatpopup" onclick="nzChatPopup(this)">คลิกเพื่อดูวีดีโอ</div><div class="nzchatpopuph">$1</div>', $text));
if(($is_mod>0)&&$text=='!clear'){
$ip = 'clear';
$icon = 'alert';
$touid = 0;
$text = lang('plugin/th_chat', 'jdj_th_chat_text_php_46');
$needClear = 1;
}
$text = getat($text);
if($color!='default'){
	$text = '<span style="color:#'.$color.';">'.$text.'</span>';
}
if($ip == 'notice'){
	DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='{$text}' WHERE variable='welcometext' AND displayorder='1' LIMIT 1");
	include_once libfile('function/cache');
	updatecache('plugin');
}elseif($ip == 'edit'){
	DB::query("UPDATE ".DB::table('newz_data')." SET text='{$text}' WHERE id='{$icon}' LIMIT 1");
}
if($quota>0 && $config['quota'] && $ip != 'clear'){
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
$re = DB::query("SELECT n.*,m.username AS name,mt.username AS toname,g.color,g.groupid,gt.groupid AS togroupid,gt.color AS tocolor,ni.name AS nick,nt.name AS tonick 
FROM ".DB::table('newz_data')." n 
LEFT JOIN ".DB::table('common_member')." m ON n.uid=m.uid 
LEFT JOIN ".DB::table('common_member')." mt ON n.touid=mt.uid 
LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid 
LEFT JOIN ".DB::table('common_usergroup')." gt ON mt.groupid=gt.groupid 
LEFT JOIN ".DB::table('newz_nick')." ni ON n.uid=ni.uid 
LEFT JOIN ".DB::table('newz_nick')." nt ON n.touid=nt.uid 
WHERE  id>{$id} AND (n.touid='0' OR n.touid='{$uid}' OR n.uid='{$uid}') 
ORDER BY id DESC LIMIT 30");
$body=array();
while($c = DB::fetch($re)){
$c['text'] = preg_replace('/\[quota\](.*?)\[\/quota\]/', '$1', $c['text']);
	if ($c['ip'] == 'changename'){
		$body[$c['id']] .= '<script>nzchatobj(".nzu'.($config['namemode']==1?'status':'name').'_'.$c['uid'].'").html("'.addcslashes(htmlspecialchars_decode($c['text']),'"').'");</script>';
		continue;
	}elseif($c['ip'] == 'delete'){
		$body[$c['id']] .= '<script>nzchatobj("#nzrows_'.$c['text'].'").fadeOut(200);</script>';
		continue;
	}elseif($c['ip'] == 'notice'){
		DB::query("UPDATE ".DB::table('common_pluginvar')." SET value='".addslashes($c['text'])."' WHERE variable='welcometext' AND displayorder='1' LIMIT 1");
		include_once libfile('function/cache');
		updatecache('plugin');
		$body[$c['id']] .= '<script>nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);nzchatobj("#nzchatnotice").html("'.addcslashes($c['text'],'"').'");</script>';
		continue;
	}elseif($c['ip'] == 'edit'){
		$body[$c['id']] .= '<script>nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);nzchatobj("#nzchatcontent'.$c['icon'].'").html("'.addcslashes($c['text'],'"').'");</script>';
		continue;
	}
	if($config['namemode']==1){$c['status'] = $c['nick'];}
	if((strval($c['nick'])===''&&$config['namemode']==2)||$config['namemode']!=2){$c['nick'] = $c['name'];}
	if((strval($c['tonick'])===''&&$config['namemode']==2)||$config['namemode']!=2){$c['tonick'] = $c['toname'];}
	$c['tonick'] = htmlspecialchars_decode($c['tonick']);
	$c['text'] .='<script type="text/javascript">nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);</script>';
	if($c['ip']=='clear'){
		$seedd = $time.'_'.$uid.'_'.rand(1,999);
		$c['text'] = '<span style="color:red" id="del_'.$seedd.'">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_14').'</span> <span id="nzchatcontent'.$c['id'].'">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_46').'<script type="text/javascript">nzchatobj("#del_'.$seedd.'").parent().parent().parent().'.($config['chat_type']==1?'next':'prev').'Until().remove();nzchatobj("#nzsendingmsg").hide();nzchatobj("#nzcharnum").show();window.clearInterval(nzdot);</script>';
	}elseif($c['icon']=='alert'){
		$c['text'] = '<span style="color:red">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_14').'</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==0){
		$c['text'] = '<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==$uid){
		$c['text'] = ($config['pm_sound']?'<embed name="pmsoundplayer" width="0" height="0" src="source/plugin/th_chat/images/player.swf" flashvars="sFile='.$config['pm_sound'].'" menu="false" allowscriptaccess="sameDomain" swliveconnect="true" type="application/x-shockwave-flash"></embed>':'').'<span style="color:#FF9900">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_03').' <a href="javascript:;" onClick="nzTouid('.$c['uid'].')">(ตอบกลับ)</a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['uid']==$uid){
		$ucolor = get_effective_colorinfo($c['touid'],$c['togroupid'],$c['tocolor']);
		$c['text'] = '<span style="color:#FF9900">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_02').' <a href="home.php?mod=space&uid='.$c['touid'].'" class="nzca" target="_blank"><span style="'.$ucolor.'"><span class="nzuname_'.$c['touid'].'">'.$c['tonick'].'</span></span></a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}
	$ucolor = get_effective_colorinfo($c['uid'],$c['groupid'],$c['color']);
	$body[$c['id']]  .= chatrow($c['id'],$c['text'],$c['uid'],$c['name'],$c['nick'],$c['time'],$ucolor,$c['touid'],0,$c['icon'],$is_mod,$c['status']);
	if($c['ip']=='clear'){
		break;
	}
}
echo json_encode($body);
?>