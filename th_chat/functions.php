<?php
function getat($attextn){
	global $config;
	if(preg_match_all('/@(.*?)(\s|\z)/',$attextn,$atmatch)) {
        foreach ($atmatch[1] as $atvalue) {
    		$atuser = DB::fetch_first("SELECT m.uid,m.groupid,g.color FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.username='{$atvalue}' LIMIT 1");
			if($atuser){
				$attext = addslashes('<a class="nzuserat nzat_'.$atuser['uid'].'" onclick="nzAt(\''.($atvalue).'\');"'.($atuser['color']?' style="color:'.$atuser['color'].'"':'').'>@'.stripslashes($atvalue).'</a> ');
			}else{
				$attext = '@'.$atvalue;
			}
			$attextn = str_replace("@".$atvalue,$attext,$attextn);
		}    
	}
	return $attextn;
}
function getat2($uid){
	global $config;
	$atuser = DB::fetch_first("SELECT m.uid,m.username,m.groupid,g.color FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.uid='{$uid}' LIMIT 1");
	$attext = '<a class="nzuserat2 nzat_'.$atuser['uid'].'" onclick="showWindow(\'th_chat_profile\', \'plugin.php?id=th_chat:profile&uid='.$uid.'\');return false;"'.($atuser['color']?' style="color:'.$atuser['color'].'"':'').'>'.$atuser['username'].'</a>';
	return $attext;
}
function getat3($uid){
	global $config;
	$atuser = DB::fetch_first("SELECT m.uid,m.username,m.groupid,g.color FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.uid='{$uid}' LIMIT 1");
	$attext = '<a class="nzuserat nzat_'.$atuser['uid'].'" onclick="nzAt(\''.addslashes($atuser['username']).'\');"'.($atuser['color']?' style="color:'.$atuser['color'].'"':'').'>'.$atuser['username'].'</a>';
	return $attext;
}
function getquota($quota){
	global $config;
	if($quo = DB::query("SELECT uid,text FROM ".DB::table('newz_data')." WHERE id='".$quota."'"))
	{
		$quo = DB::fetch($quo);
		$quo['text'] = preg_replace('/\[quota\](.*?)\[\/quota\]/', '', $quo['text']);
		$attext = getat3($quo['uid']);
		$text = '[quota]'.addslashes('<div class="nzblockquote">'.$attext.': '.$quo['text'].'</div>').'[/quota]';
	}
	return $text;
}
function chatrow($id,$text,$uid_p,$username,$time,$touid,$icon,$mod){
	global $uid,$config,$_G;
	$tag = '';
	if($icon=='alert'){
		$tag = '<span class="nztag" style="background:#e53935">แจ้งเตือน</span>';
	}elseif($icon=='bot'){
		$tag = '<span class="nztag" style="background:#546E7A">อัตโนมัติ</span>';
	}
	if($touid){
		if($touid == $_G['uid']){
			$tag = '<span class="nztag" style="background:#FB8C00;cursor:pointer;" onclick="nzTouid('.$uid_p.')">กระซิบ</span>';
		}else{
			$tag = '<span class="nztag" style="background:#FB8C00;border-radius:4px 0 0 4px;margin-right:0;cursor:pointer;" onclick="nzTouid('.$touid.')">กระซิบถึง</span><span class="nztag2">'.getat2($touid).'</span>';
		}
	}
	return '<tr class="nzchatrow" id="nzrows_'.$id.'" onMouseOver="nzchatobj(\'#nzchatquota'.$id.'\').css(\'opacity\',\'1\');" onMouseOut="nzchatobj(\'#nzchatquota'.$id.'\').css(\'opacity\',\'0\');">
<td class="nzavatart">
	<a href="javascript:void(0);" onclick="showWindow(\'th_chat_profile\', \'plugin.php?id=th_chat:profile&uid='.$uid_p.'\');return false;"><img src="'.avatar($uid_p,'small',1).'" title="'.$username.'" class="nzchatavatar" onError="this.src=\'uc_server/images/noavatar_small.gif\';" /></a>
</td>
<td class="nzcontentt">
	'.getat2($uid_p).'<span class="nztime" title="'.date("c",$time).'">'.get_date($time).'</span> <span id="nzchatquota'.$id.'" class="nzcq"><a href="javascript:void(0);" onClick="nzQuota('.$id.')">อ้างอิง</a>'.($uid!=$uid_p?' <a href="javascript:void(0);" onclick="nzAt(\''.addslashes($username).'\')">@</a> <a href="javascript:void(0);" onclick="nzTouid('.$uid_p.')">กระซิบ</a> ':'').((($config['editmsg']==1)&&$mod)||(($config['editmsg']==2)&&$mod&&($uid==$uid_p))||(($config['editmsg']==3)&&($uid==$uid_p))?' <a href="javascript:;" onClick=\'nzCommand("edit","'.$id.'");\'>แก้ไข</a>':'').($mod?' <a href="javascript:;" onClick=\'nzCommand("del","'.$id.'");\'>ลบ</a>':'').'</span>
	<br>
	<div class="nzinnercontent">'.$tag.$text.'</div>
</td>
</tr>
<script>nzchatobj("#nzrows_'.$id.' span.nztime").timeago();</script>';
}
function get_date($timestamp) 
{
	$strYear = date("Y",$timestamp)+543;
	$strMonth= date("n",$timestamp);
	$strDay= date("j",$timestamp);
	$strHour= date("H",$timestamp);
	$strMinute= date("i",$timestamp);
	$strSeconds= date("s",$timestamp);
	$strMonthCut = array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
	$strMonthThai=$strMonthCut[$strMonth];
	return "$strDay $strMonthThai $strYear $strHour:$strMinute";
}
$time = TIMESTAMP;
?>