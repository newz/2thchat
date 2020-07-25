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

if(DB::fetch_first('SELECT uid FROM '.DB::table('common_session').' WHERE uid=\''.$_G['uid'].'\'')){
	DB::update('common_session', $dataarr, "`uid`='".$_G['uid']."'");
}else{
	DB::insert('common_session', $dataarr, false, false, true);
}

$timeout = $config['reload']+10;
$timeout2 = $config['reload']+60;

$gid = $_G['groupid'];

$body_online='<ul class="nzolrow">';
$class = 'nzolnor';

$oltotal = 0;
$banned = DB::query("SELECT value FROM ".DB::table('common_pluginvar')." WHERE variable='chat_ban' AND displayorder='15' LIMIT 1");
$banned = DB::fetch($banned);
eval("\$banned = array({$banned['value']});");

$re = DB::query("SELECT s.uid,s.username,s.groupid,s.lastactivity,g.color,n.name FROM ".DB::table('common_session')." s LEFT JOIN ".DB::table('common_usergroup')." g ON s.groupid=g.groupid LEFT JOIN ".DB::table('newz_nick')." n ON s.uid=n.uid WHERE s.uid>0 AND invisible=0 AND action IN (2,127) AND fid=0 AND tid=0");
if(!empty($config['onlinebot'])){
	$re2 = DB::query("SELECT s.uid,s.username,s.groupid,g.color,n.name FROM ".DB::table('common_member')." s LEFT JOIN ".DB::table('common_usergroup')." g ON s.groupid=g.groupid LEFT JOIN ".DB::table('newz_nick')." n ON s.uid=n.uid WHERE s.uid IN (".$config['onlinebot'].")");
}
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
	}
	else if($uid==$r['uid']){
		$body_onlinein[$r['groupid']] .= '<li class="nzolac"><p style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width:180px;">
		<img src="'.avatar($r['uid'],'small',1).'" alt="" align="absmiddle" class="nzavsm" onError="this.src=\'uc_server/images/noavatar_small.gif\';" style="border-right: 3px #0c0 solid"/>
		<span id="nzolpro_'.$r['uid'].'" '.($is_banned?'style="font-style: oblique;"':'').' onMouseOver="showMenu(this.id)"><a href="home.php?mod=space&amp;uid='.$r['uid'].'" target="_blank" style="margin-left:-3px;" class="nzca" ><font color="'.$r['color'].'"><span class="nzuname_'.$r['uid'].'">'.$r['name'].'</span></font></a></span></p></li>';
		$body_onlineex[$r['groupid']] .= '<div id="nzolpro_'.$r['uid'].'_menu" class="nzchatpro" style="display:none;"><img src="'.avatar($r['uid'],'middle',1).'" alt="" onError="this.src=\'uc_server/images/noavatar_middle.gif\';" /><br /><a href="home.php?mod=space&amp;uid='.$r['uid'].'" style="color:'.$r['color'].';" target="_blank" class="nzca" >'.stripslashes($r['nameold']).'</a>'.($config['namemode']==1?'<br/><span id="nzstatus" class="nzustatus_'.$r['uid'].'">'.$status.'</span>':'').'<div style="text-align:left;padding-left:20px;margin-bottom:7px;"><img src="source/plugin/th_chat/images/av.gif" align="absmiddle" alt="" /> <a href="home.php?mod=spacecp&ac=avatar">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_36').'</a></div></div>';
	}else{
		$body_onlinein[$r['groupid']] .= '<li class="nzolnor" style="overflow: hidden;text-overflow: ellipsis;"><p style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width:180px;"><p  style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width:180px;">
		<img src="'.avatar($r['uid'],'small',1).'" alt="" align="absmiddle" class="nzavsm2" onError="this.src=\'uc_server/images/noavatar_small.gif\';" onMouseOver="nzchatobj(\'#nzatname_'.$r['uid'].'\').show();" onMouseOut="nzchatobj(\'#nzatname_'.$r['uid'].'\').hide();" onClick="nzAt(\''.stripslashes($r['nameold']).'\');" style="border-right: 3px '.($time-$r['lastactivity']>$timeout?'gold':'#0c0').' solid;"/>
		<span id="nzatname_'.$r['uid'].'" style="margin-left:-20px;padding-right:6px;cursor:pointer;display: none;"><strong>@</strong></span>
		<span id="nzolpro_'.$r['uid'].'" onMouseOver="showMenu(this.id)"><a href="home.php?mod=space&amp;uid='.$r['uid'].'" target="_blank" class="nzca"><font color="'.$r['color'].'"><span class="nzuname_'.$r['uid'].'">'.($is_banned?'<strike>'.$r['name'].'</strike>':$r['name']).'</span></font></a></span></li>';
		$body_onlineex[$r['groupid']] .= '<div id="nzolpro_'.$r['uid'].'_menu" class="nzchatpro" style="display:none;"><img src="'.avatar($r['uid'],'middle',1).'" alt="" onError="this.src=\'uc_server/images/noavatar_middle.gif\';" /><br /><a href="home.php?mod=space&amp;uid='.$r['uid'].'" style="color:'.$r['color'].';" target="_blank" class="nzca" >'.stripslashes($r['nameold']).'</a>'.($config['namemode']==1?'<br/><span id="nzstatus" class="nzustatus_'.$r['uid'].'">'.$status.'</span>':'').'<div style="text-align:left;padding-left:20px;margin-bottom:7px;"> <img src="source/plugin/th_chat/images/pmto.gif" align="absmiddle" alt="" /> <a href="home.php?mod=spacecp&amp;ac=pm&amp;op=showmsg&amp;handlekey=showmsg_'.$r['uid'].'&amp;touid='.$r['uid'].'&amp;pmid=0&amp;daterange=2" onClick="showWindow(\'showMsgBox\', this.href, \'get\', 0)" id="a_sendpm_'.$r['uid'].'" style="color:#333333;" class="xi2">ส่ง PM</a></div>';
	}

	if(in_array($_G['adminid'],array(1,2,3))&&!($uid==$r['uid'])&&!($time-$r['lastactivity']>$timeout2)){
		$body_onlineex[$r['groupid']] .= ($config['namemode']==0?'':'<a href="javascript:void(0);" onClick=\'nzCommand("name","'.$r['uid'].'");\'>'.lang('plugin/th_chat', 'jdj_th_chat_text_php_32').'</a><br>').(in_array($r['gourpid'],array(1,2,3))?'':(!in_array($r['uid'],$banned)?'<a href="javascript:void(0);" onClick=\'nzCommand("ban","'.$r['uid'].'");\'>แบน</a>':'<a href="javascript:void(0);" onClick=\'nzCommand("unban","'.$r['uid'].'");\'>'.lang('plugin/th_chat', 'jdj_th_chat_text_php_28').'</a>')).'</div>';
	}else{
		$body_onlineex[$r['groupid']] .= '</div>';
	}
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
$body_online .= $body_onlinez.'</ul>'.$body_online2z;
?>