<?
function getat($attextn){
	global $config;
	if(preg_match_all('/@(.*?)(\s|\z)/',$attextn,$atmatch)) {
        foreach ($atmatch[1] as $atvalue) {
    		$atuser = DB::query("SELECT m.uid,m.groupid,g.color,n.name FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.username='{$atvalue}' LIMIT 1");
			$atuser = DB::fetch($atuser);
			$ucolor = get_effective_colorinfo($atuser['uid'],$atuser['groupid'],$atuser['color']);
			if($atuser&&!empty($atuser['name'])&&$config['namemode']==2){
				$attext = paddslashes('<a class="nzca" onclick="nzAt(\''.$atvalue.'\');"><span style="'.$ucolor.'">'.htmlspecialchars_decode($atuser['name']).'</span></a> ');
			}else if($atuser){
				$attext = paddslashes('<a class="nzca" onclick="nzAt(\''.$atvalue.'\');"><span style="'.$ucolor.'">'.$atvalue.'</span></a> ');
			}else{
				$attext = '@'.$atvalue;
			}
			$attextn = str_replace("@".$atvalue,$attext,$attextn);
		}    
	}
	return $attextn;
}
function getquota($quota){
	global $config;
	if($quo = DB::query("SELECT uid,text FROM ".DB::table('newz_data')." WHERE id='".$quota."'"))
	{
		$quo = DB::fetch($quo);
		$quo['text'] = preg_replace('/\[quota\](.*?)\[\/quota\]/', '', $quo['text']);
		$atuser = DB::query("SELECT m.username,m.uid,m.groupid,g.color,n.name FROM ".DB::table('common_member')." m LEFT JOIN ".DB::table('newz_nick')." n ON m.uid=n.uid LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid WHERE m.uid='".$quo['uid']."' LIMIT 1");
		$ucolor = get_effective_colorinfo($atuser['uid'],$atuser['groupid'],$atuser['color']);
		$atuser = DB::fetch($atuser);
		if($atuser&&!empty($atuser['name'])&&$config['namemode']==2){
			$attext = paddslashes('<a class="nzca" onclick="nzAt(\''.$atuser['username'].'\');"><span style="'.$ucolor.'">'.htmlspecialchars_decode($atuser['name']).'</span></a>');
		}else if($atuser){
			$attext = paddslashes('<a class="nzca" onclick="nzAt(\''.$atuser['username'].'\');"><span style="'.$ucolor.'">'.$atuser['username'].'</span></a>');
		}else{
			$attext = '???';
		}
		$text = '[quota]'.paddslashes('<blockquote class="nzblockquote">'.$attext.': '.$quo['text'].'</blockquote>').'[/quota]';
	}
	return $text;
}
function chatrow($id,$text,$uid_p,$oldusername,$username,$time,$color,$touid,$is_init,$icon,$mod,$status){
	global $uid,$config,$_G;
	if($icon=='alert')
	{
		$icon=$icon?img('alert', '/', lang('plugin/th_chat', 'jdj_th_chat_text_php_14')).' ':'';
		$type='border-color: transparent #F00;';
		$type2='border-left: 3px solid #F00;';
	}else if($icon=='bot')
	{
		$icon=$icon?img('bot', '/', lang('plugin/th_chat', 'jdj_th_chat_text_php_58')).' ':'';
		$type='border-color: transparent #5C5C5C;';
		$type2='border-left: 3px solid #5C5C5C;';
	}else{
		$icons = explode("|", $icon,3);
		$icon=$icon?img($icons[2], '/'.$icons[0].'/', $icons[1]).' ':'';
	}
	if($touid){
		$type='border-color: transparent #F90;';
		$type2='border-left: 3px solid #F90;';
	}
	$username = htmlspecialchars_decode($username);
	$status = htmlspecialchars_decode($status);
	return '<tr class="nzchatrow" id="nzrows_'.$id.'" onMouseOver="nzchatobj(\'#nzchatquota'.$id.'\').show();" onMouseOut="nzchatobj(\'#nzchatquota'.$id.'\').hide();">
<td class="nzavatart"><a href="'.avatar($uid_p,'big',1).'" target="_blank"><img src="'.avatar($uid_p,'small',1).'" alt="avatar" class="nzchatavatar nzchatavatar'.$uid_p.'" onError="this.src=\'uc_server/images/noavatar_small.gif\';" /></a></td>
<td class="nzcontentt"><div class="nzinnercontent-before" style="'.$type.'"></div><div class="nzinnercontent" style="'.$type2.'"><span id="nzchatquota'.$id.'" class="nzcq">'.($uid!=$uid_p?'<a href="javascript:void(0);" onClick="nzAt(\''.$oldusername.'\');">@</a> ':'').'<a href="javascript:void(0);" onClick="nzQuota('.$id.')">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_59').'</a>'.($uid!=$uid_p?' <a href="javascript:void(0);" onClick="nzTouid('.$uid_p.')">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_01').'</a>':'').(($config['chat_point']&&($uid!=$uid_p))?' <a href="javascript:void(0);" onClick="nzPlusone('.$uid_p.',1);" style="color:green">+1</a> <a href="javascript:void(0);" onClick="nzPlusone('.$uid_p.',-1);" style="color:red">-1</a>':'').((($config['editmsg']==1)&&$mod)||(($config['editmsg']==2)&&$mod&&($uid==$uid_p))||(($config['editmsg']==3)&&($uid==$uid_p))?' <a href="javascript:;" onClick=\'nzCommand("edit","'.$id.'");\'>'.lang('plugin/th_chat', 'jdj_th_chat_text_php_08').'</a>':'').($mod?' <a href="javascript:;" onClick=\'nzCommand("del","'.$id.'");\'>'.lang('plugin/th_chat', 'jdj_th_chat_text_php_43').'</a>':'').'</span>
<span class="nztime">'.get_date($time).'</span>
<span><a href="home.php?mod=space&uid='.$uid_p.'" class="nzca" target="_blank"><span style="'.$color.'">'.$icon.'<span class="nzuname_'.$uid_p.'">'.$username.'</span></span></a> <span id="nzstatus" class="nzustatus_'.$uid_p.'">'.$status.'</span></span><br />
'.$text.'</span>'.($is_init?'':($touid?'<script></script>':'<script></script>')).'</div></td>
</tr>';
}
function get_date($timestamp) 
{
	$timestamp += 25200;
	$date = gmdate('M. d, Y', $timestamp);
	$today = gmdate('M. d, Y', time()+25200);
	if ($date == $today) $date = gmdate("H:i", $timestamp);
	return $date;
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
function get_effective_colorinfo($uid, $groupid, $color){
	global $_G;
	$color_info = '';
	if(in_array('colorful_name',$_G['setting']['plugins']['available'])){
		$colorful_name_user = C::t('#colorful_name#plugin_colorful_name_user');
		$userinfo = $colorful_name_user->get_row_by_uid($uid);
		if(!empty($userinfo)) {
			$color = strtoupper($userinfo['color']);
		}
		$colorful_name_group = C::t('#colorful_name#plugin_colorful_name_group');
		$groupcolors_row = $colorful_name_group->get_row_by_gid($groupid);
		if($color){
			$color_info .= 'color:'.$color.';';
		}
		if($groupcolors_row['use_italic']){
			$color_info .= 'font-style:italic;';
		}
		if($groupcolors_row['use_bold']){
			$color_info .= 'font-weight:bold;';
		}
	}else{
		$color_info .= 'color:'.$color.';';
	}
	return $color_info;
}
?>