<?
function getat($attextn){
	global $config;
	if(preg_match_all('/@(.*?)(\s|\z)/',$attextn,$atmatch)) {
        foreach ($atmatch[1] as $atvalue) {
    		$atuser = DB::query("SELECT m.uid,m.groupid,g.color,n.name FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.username='{$atvalue}' LIMIT 1");
			$atuser = DB::fetch($atuser);
			if($atuser&&!empty($atuser['name'])&&$config['namemode']==2){
				$attext = paddslashes('<a class="nzuserat nzat_'.$atuser['uid'].'" onclick="nzAt(\''.$atvalue.'\');"><font color="'.$atuser['color'].'">'.htmlspecialchars_decode($atuser['name']).'</font></a> ');
			}else if($atuser){
				$attext = paddslashes('<a class="nzuserat nzat_'.$atuser['uid'].'" onclick="nzAt(\''.$atvalue.'\');"><font color="'.$atuser['color'].'">'.$atvalue.'</font></a> ');
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
	$atuser = DB::query("SELECT m.uid,m.username,m.groupid,g.color,n.name FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.uid='{$uid}' LIMIT 1");
	$atuser = DB::fetch($atuser);
	if(!empty($atuser['name'])&&$config['namemode']==2){
		$attext = '<a class="nzuserat nzat_'.$atuser['uid'].'" onclick="nzAt(\''.$atuser['username'].'\');"><font color="'.$atuser['color'].'">'.htmlspecialchars_decode($atuser['name']).'</font></a>';
	}else{
		$attext = '<a class="nzuserat nzat_'.$atuser['uid'].'" onclick="nzAt(\''.$atuser['username'].'\');"><font color="'.$atuser['color'].'">'.$atuser['username'].'</font></a>';
	}
	return $attext;
}
function getquota($quota){
	global $config;
	if($quo = DB::query("SELECT uid,text FROM ".DB::table('newz_data')." WHERE id='".$quota."'"))
	{
		$quo = DB::fetch($quo);
		$quo['text'] = preg_replace('/\[quota\](.*?)\[\/quota\]/', '', $quo['text']);
		$attext = getat2($quo['uid']);
		$text = '[quota]'.paddslashes('<blockquote class="nzblockquote">'.$attext.': '.$quo['text'].'</blockquote>').'[/quota]';
	}
	return $text;
}
function chatrow($id,$text,$uid_p,$oldusername,$username,$time,$color,$touid,$is_init,$icon,$mod,$status){
	global $uid,$config,$_G;
	if($icon=='alert')
	{
		$icon=$icon?img('alert', '/', 'แจ้งเตือน:').' ':'';
		$type2='border-color: #F00;';
	}else if($icon=='bot')
	{
		$icon=$icon?img('bot', '/', 'อัตโนมัติ').' ':'';
		$type2='border-color: #5C5C5C;';
	}else{
		$icons = explode("|", $icon,3);
		$icon=$icon?img($icons[2], '/'.$icons[0].'/', $icons[1]).' ':'';
	}
	if($touid){
		$type2='border-color: #F90;';
	}
	$username = htmlspecialchars_decode($username);
	$status = htmlspecialchars_decode($status);
	return '<tr class="nzchatrow" id="nzrows_'.$id.'" onMouseOver="nzchatobj(\'#nzchatquota'.$id.'\').show();" onMouseOut="nzchatobj(\'#nzchatquota'.$id.'\').hide();">
<td class="nzavatart"><a href="javascript:void(0);" onclick="showWindow(\'th_chat_profile\', \'plugin.php?id=th_chat:profile&uid='.$uid_p.'\');return false;"><img src="'.avatar($uid_p,'small',1).'" title="'.$username.'" class="nzchatavatar nzchatavatar'.$uid_p.'" onError="this.src=\'uc_server/images/noavatar_small.gif\';" /></a></td>
<td class="nzcontentt"></div><div class="nzinnercontent" style="'.$type2.'">
'.$text.'</span>'.($is_init?'':($touid?'<script></script>':'<script></script>')).'</div>
<br>'.getat2($uid_p).'<span class="nztime" title="'.date("c",$time).'">'.get_date($time).'</span> <span id="nzchatquota'.$id.'" class="nzcq"><a href="javascript:void(0);" onClick="nzQuota('.$id.')">อ้างอิง</a>'.((($config['editmsg']==1)&&$mod)||(($config['editmsg']==2)&&$mod&&($uid==$uid_p))||(($config['editmsg']==3)&&($uid==$uid_p))?' <a href="javascript:;" onClick=\'nzCommand("edit","'.$id.'");\'>แก้ไข</a>':'').($mod?' <a href="javascript:;" onClick=\'nzCommand("del","'.$id.'");\'>ลบ</a>':'').'</span></td>
</tr><script>nzchatobj("#nzrows_'.$id.' span.nztime").timeago();</script>';
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
$time = time();
function img($code, $type, $title) {
	$src = $type . $code;
	$img = "<img src=\"source/plugin/th_chat/images{$src}.png\" id=\"nzosicon\" alt=\"{$title}\" title=\"{$title}\">";
	return $img;
}
?>