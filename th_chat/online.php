<?

$time = time();
list($ip1,$ip2,$ip3,$ip4) = explode('.',$_G['clientip']);
$dataarr = array(
'sid'=>$_G['session']['sid'],
'ip1'=>$ip1,
'ip2'=>$ip2,
'ip3'=>$ip3,
'ip4'=>$ip4,
'uid'=>$_G['member']['uid'],
'username'=>paddslashes($_G['member']['username']),
'groupid'=>$_G['member']['groupid'],
'invisible'=>$_G['member']['invisible'],
'action'=>APPTYPEID,
'lastactivity'=>$time,
'lastolupdate'=>0,
'fid'=>0,
'tid'=>0
);
if($_G['uid']){
	if(DB::fetch_first('SELECT uid FROM '.DB::table('common_session').' WHERE uid=\''.$_G['uid'].'\'')){
		DB::update('common_session', $dataarr, "`uid`='".$_G['uid']."'");
	}else{
		DB::insert('common_session', $dataarr, false, false, true);
	}
}

$timeout = 30;
$timeout2 = 60;

$gid = $_G['groupid'];

$body_online='<table class="nzlist2">';
$class = 'nzolnor';

$oltotal = 0;
$banned = DB::fetch_first("SELECT value FROM ".DB::table('common_pluginvar')." WHERE variable='chat_ban' AND displayorder='16' LIMIT 1");
$banned = explode(",",$banned['value']);

if($config['chat_point']){
	$re = DB::query("SELECT s.uid,s.username,s.groupid,s.lastactivity,g.color,n.name,n.point_total".($config['chat_point']!='9'?",p.extcredits{$config['chat_point']} AS point":"")." FROM ".DB::table('common_session')." s LEFT JOIN ".DB::table('common_usergroup')." g ON s.groupid=g.groupid LEFT JOIN ".DB::table('newz_nick')." n ON s.uid=n.uid LEFT JOIN ".DB::table('common_member_count')." p ON s.uid=p.uid WHERE s.uid>0 AND invisible=0 AND action IN (2,127) AND fid=0 AND tid=0");
	if(!empty($config['onlinebot'])){
		$re2 = DB::query("SELECT s.uid,s.username,s.groupid,g.color,n.name,n.point_total".($config['chat_point']!='9'?",p.extcredits{$config['chat_point']} AS point":"")." FROM ".DB::table('common_member')." s LEFT JOIN ".DB::table('common_usergroup')." g ON s.groupid=g.groupid LEFT JOIN ".DB::table('newz_nick')." n ON s.uid=n.uid LEFT JOIN ".DB::table('common_member_count')." p ON s.uid=p.uid WHERE s.uid IN (".$config['onlinebot'].")");
	}
}else{
	$re = DB::query("SELECT s.uid,s.username,s.groupid,s.lastactivity,g.color,n.name,n.point_total FROM ".DB::table('common_session')." s LEFT JOIN ".DB::table('common_usergroup')." g ON s.groupid=g.groupid LEFT JOIN ".DB::table('newz_nick')." n ON s.uid=n.uid WHERE s.uid>0 AND invisible=0 AND action IN (2,127) AND fid=0 AND tid=0");
	if(!empty($config['onlinebot'])){
		$re2 = DB::query("SELECT s.uid,s.username,s.groupid,g.color,n.name,n.point_total FROM ".DB::table('common_member')." s LEFT JOIN ".DB::table('common_usergroup')." g ON s.groupid=g.groupid LEFT JOIN ".DB::table('newz_nick')." n ON s.uid=n.uid WHERE s.uid IN (".$config['onlinebot'].")");
	}
}
$avatar_update = '<script>nzchatobj(".nzchatavatar").css({"border-color":"#9e9e9e"});';
while($r = DB::fetch($re) OR $r = DB::fetch($re2)){
	$r['nameold'] = $r['username'];
	if($config['namemode']==0)
	{
		$r['name'] = $r['username'];
	}
	elseif($config['namemode']==1)
	{
		$status = htmlspecialchars_decode($r['name']);
		$r['name'] = $r['username'];
	}
	elseif(strval($r['name'])==='')
	{
		$r['name'] = $r['username'];
	}
	else
	{
		$r['name'] = htmlspecialchars_decode($r['name']);
	}
	$r['name'] = stripslashes($r['name']);
	if(in_array($r['uid'],$banned)){
		$r['name']  = '<strike>'.$r['name'].'</strike>';
	}
	if($r['groupid']>9){$r['groupid'] = 100-$r['groupid'];}
	else if(in_array($r['groupid'],array(4,5,6,9))){$r['groupid'] = 100;}
	else if($r['groupid']==7){$r['groupid'] = 99;}
	else if($r['groupid']==8){$r['groupid'] = 98;}
	$botid = explode(",", $config['onlinebot']);
	if(in_array($r['uid'],$botid)){if(empty($r['lastactivity'])){$r['lastactivity'] = $time;}else{continue;}}
	$r['groupid'] += $time-$r['lastactivity']>$timeout?100:0;
	if($time-$r['lastactivity']>$timeout2)
	{
		$oltotal = $oltotal - 1;
	}else{
		$avatar_update .= 'nzchatobj(".nzchatavatar'.$r['uid'].'").css({"border-color":"'.($time-$r['lastactivity']>$timeout?'#ffc107':'#4CAF50').'"});';
		$body_onlinein[$r['groupid']] .= '<tr onclick="showWindow(\'th_chat_profile\', \'plugin.php?id=th_chat:profile&uid='.$r['uid'].'\');return false;" style="cursor:pointer">
		<td><img src="'.avatar($r['uid'],'small',1).'" title="'.$r['username'].'" align="absmiddle" class="nzchatavatar nzchatavatar'.$r['uid'].'" onerror="this.src=\'uc_server/images/noavatar_small.gif\';" style="border-color:'.($time-$r['lastactivity']>$timeout?'#ffc107':'#4CAF50').'"></td>
		<td><a '.($r['color']?'style="background-color:'.$r['color'].'" ':'').'class="nznametop2" id="nzolpro_'.$r['uid'].'" target="_blank" style="margin-top: 8px;"><span>'.$r['username'].'</span></a></td>';
	}
	$body_onlineex[$r['groupid']] .= '</div>';
	$oltotal++;
}
ksort($body_onlinein);
ksort($body_onlineex);
foreach($body_onlinein as $show){  
    $body_onlinez.= $show;
}
foreach($body_onlineex as $show){  
    $body_online2z.= $show;
}  
$body_online .= $body_onlinez.'</table>'.$body_online2z.$avatar_update.'</script>';

?>