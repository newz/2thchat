<?php

$time = time();
list($ip1,$ip2,$ip3,$ip4) = explode('.',$_G['clientip']);
$dataarr = array(
'sid'=>$_G['session']['sid'],
'ip1'=>$ip1,
'ip2'=>$ip2,
'ip3'=>$ip3,
'ip4'=>$ip4,
'uid'=>$_G['member']['uid'],
'username'=>daddslashes($_G['member']['username']),
'groupid'=>$_G['member']['groupid'],
'invisible'=>$_G['member']['invisible'],
'action'=>APPTYPEID,
'lastactivity'=>$time,
'lastolupdate'=>0,
'fid'=>0,
'tid'=>0
);

if(DB::fetch_first('SELECT uid FROM '.DB::table('common_session').' WHERE uid=\''.$_G['uid'].'\'')){
DB::update('common_session', $dataarr, "sid='$dataarr[sid]'");
}else{
DB::insert('common_session', $dataarr, false, false, true);
}

$gid = $_G['groupid'];

$body_online='<ul class="nzolrow">';
$body_online2 = '';
$class = 'nzolnor';

$oltotal = 0;
$banned = DB::query("SELECT value FROM ".DB::table('common_pluginvar')." WHERE variable='chat_ban' AND displayorder='9' LIMIT 1");
$banned = DB::fetch($banned);
eval("\$banned = array({$banned['value']});");

if($config['chat_point']){
$re = DB::query("SELECT s.uid,s.username,g.color,n.name,p.extcredits{$config['chat_point']} AS point FROM ".DB::table('common_session')." s LEFT JOIN ".DB::table('common_usergroup')." g ON s.groupid=g.groupid LEFT JOIN ".DB::table('2th_chat_nick')." n ON s.uid=n.uid LEFT JOIN ".DB::table('common_member_count')." p ON s.uid=p.uid WHERE s.uid>0 AND invisible=0 AND action IN (2,127) AND fid=0 AND tid=0");
}else{
$re = DB::query("SELECT s.uid,s.username,g.color,n.name FROM ".DB::table('common_session')." s LEFT JOIN ".DB::table('common_usergroup')." g ON s.groupid=g.groupid LEFT JOIN ".DB::table('2th_chat_nick')." n ON s.uid=n.uid WHERE s.uid>0 AND invisible=0 AND action IN (2,127) AND fid=0 AND tid=0");
}

while($r = DB::fetch($re)){
	
	if(strval($r['name'])==='')
		$r['name'] = $r['username'];
	
if($config['chat_point']){
if($r['point']<0){
$r['point'] = '<font color="red">'.$r['point'].'</font>';
}elseif($r['point']>0){
$r['point'] = '<font color="green">+'.$r['point'].'</font>';
}
}

if(in_array($r['uid'],$banned)){
	$r['name']  = '<strike>'.$r['name'].'</strike>';
}
	
	if($uid==$r['uid']){
		$body_online .= '<li class="'.$class.'"><p style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width:180px;">
		<img src="'.avatar($r['uid'],'small',1).'" alt="" align="absmiddle" class="nzavsm" onError="this.src=\'uc_server/images/noavatar_small.gif\';" />
		<span id="nzolpro_'.$r['uid'].'" onMouseOver="showMenu(this.id)"><a href="home.php?mod=space&amp;uid='.$r['uid'].'" target="_blank" class="nzca" ><font color="'.$r['color'].'">'.($is_banned?'<strike>'.$r['name'].'</strike>':$r['name']).'</font></a></span></p></li>';
		$body_online2 .= '<div id="nzolpro_'.$r['uid'].'_menu" class="nzchatpro" style="display:none;"><img src="'.avatar($r['uid'],'middle',1).'" alt="" onError="this.src=\'uc_server/images/noavatar_middle.gif\';" /><br /><a href="home.php?mod=space&amp;uid='.$r['uid'].'" style="color:'.$r['color'].';" target="_blank" class="nzca" >'.$r['username'].'</a>'.($config['chat_point']?'<br />Point: '.$r['point']:'').'<div style="text-align:left;padding-left:20px;margin-bottom:7px;"><img src="source/plugin/th_chat/images/av.gif" align="absmiddle" alt="" /> <a href="home.php?mod=spacecp&ac=avatar">เปลี่ยนรูป</a><br /><img src="source/plugin/th_chat/images/edit.gif" align="absmiddle" alt="" /> <a href="javascript:void(0);" onClick="nzName();">แก้ไขชื่อ</a></div></div>';
		}else{
		$body_online .= '<li class="'.$class.'" style="overflow: hidden;text-overflow: ellipsis;"><p style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width:180px;"><p  style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width:180px;">
		<img src="'.avatar($r['uid'],'small',1).'" alt="" align="absmiddle" class="nzavsm" onError="this.src=\'uc_server/images/noavatar_small.gif\';" />
		<span id="nzolpro_'.$r['uid'].'" onMouseOver="showMenu(this.id)"><a href="home.php?mod=space&amp;uid='.$r['uid'].'" target="_blank" class="nzca"><font color="'.$r['color'].'">'.($is_banned?'<strike>'.$r['name'].'</strike>':$r['name']).'</font></a></span></li>';
		$body_online2 .= '<div id="nzolpro_'.$r['uid'].'_menu" class="nzchatpro" style="display:none;"><img src="'.avatar($r['uid'],'middle',1).'" alt="" onError="this.src=\'uc_server/images/noavatar_middle.gif\';" /><br /><a href="home.php?mod=space&amp;uid='.$r['uid'].'" style="color:'.$r['color'].';" target="_blank" class="nzca" >'.$r['username'].'</a>'.($config['chat_point']?'<br />Point: '.$r['point']:'').'<div style="text-align:left;padding-left:20px;margin-bottom:7px;"> <img src="source/plugin/th_chat/images/useron_2.gif" align="absmiddle" alt="" /> <a href="javascript:void(0);" onClick="nzTouid('.$r['uid'].')">กระซิบ</a><br /><img src="source/plugin/th_chat/images/addbuddy.gif" align="absmiddle" alt="" /> <a href="home.php?mod=spacecp&amp;ac=friend&amp;op=add&amp;uid='.$r['uid'].'&amp;handlekey=addfriendhk_'.$r['uid'].'" id="a_friend_li_'.$r['uid'].'" onClick="showWindow(this.id, this.href, \'get\', 0);">เพิ่มเพื่อน</a><br /><img src="source/plugin/th_chat/images/pmto.gif" align="absmiddle" alt="" /> <a href="home.php?mod=spacecp&amp;ac=pm&amp;op=showmsg&amp;handlekey=showmsg_'.$r['uid'].'&amp;touid='.$r['uid'].'&amp;pmid=0&amp;daterange=2" onClick="showWindow(\'showMsgBox\', this.href, \'get\', 0)" id="a_sendpm_'.$r['uid'].'" style="color:#333333;" class="xi2">ส่งข้อความ</a></div>';
}

if(in_array($_G['adminid'],array(1,2,3))&&!($uid==$r['uid'])){
$body_online2 .= '<a href="javascript:void(0);" onClick="nzchatobj(\'#nzchatmessage\').val(\'/ban '.$r['uid'].'\');nzchatobj(\'#nzchatmessage\').focus();">แบน</a> <a href="javascript:void(0);" onClick="nzchatobj(\'#nzchatmessage\').val(\'/unban '.$r['uid'].'\');nzchatobj(\'#nzchatmessage\').focus();">ปลดแบน</a></div>';
}else{
$body_online2 .= '</div>';
}
		
	if($class=='nzolnor'){
			$class = 'nzolac';
		}else{
			$class = 'nzolnor';
		}
$oltotal++;
}

$body_online.='</ul>'.$body_online2;

?>