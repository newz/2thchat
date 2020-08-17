<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
if($_G['uid']<1){
	if(!$_G['uid']){showmessage('not_loggedin', NULL, array(), array('login' => 1));}
}
$uid = intval($_GET['uid']);
if(!$uid){
	exit('Error');
}
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$avatar = avatar($uid,'big',1);
$r = DB::fetch_first("SELECT s.uid,s.username,s.groupid,g.grouptitle,g.color,n.ban,p.extcredits1,p.extcredits2,p.extcredits3,p.extcredits4,p.extcredits5,p.extcredits6,p.extcredits7,p.extcredits8 FROM ".DB::table('common_member')." s LEFT JOIN ".DB::table('common_usergroup')." g ON s.groupid=g.groupid LEFT JOIN ".DB::table('newz_nick')." n ON s.uid=n.uid LEFT JOIN ".DB::table('common_member_count')." p ON s.uid=p.uid WHERE s.uid=".$uid);
if($uid==$_G['uid']){
	$mlist .= '<tr><td colspan="2"><img src="source/plugin/th_chat/images/avatar.png" align="absmiddle" style="padding-right:5px"> <a href="home.php?mod=spacecp&ac=avatar">เปลี่ยนรูป</a></td></tr><tr><td colspan="2"><img src="source/plugin/th_chat/images/settings.png" align="absmiddle" style="padding-right:5px"> <a href="javascript:void(0);" onclick="showWindow(\'th_chat_setting\', \'plugin.php?id=th_chat:setting\');return false;">ตั้งค่าห้องแชท</a></td></tr>';
}else{
	$mlist .= '<tr><td colspan="2"><img src="source/plugin/th_chat/images/message.png" align="absmiddle" style="padding-right:5px"> <a href="javascript:void(0);" onClick="nzTouid('.$r['uid'].')">กระซิบ</a></td></tr><tr><td colspan="2"><img src="source/plugin/th_chat/images/addfriend.png" align="absmiddle" style="padding-right:5px"> <a href="home.php?mod=spacecp&amp;ac=friend&amp;op=add&amp;uid='.$r['uid'].'&amp;handlekey=addfriendhk_'.$r['uid'].'" id="a_friend_li_'.$r['uid'].'" onClick="showWindow(this.id, this.href, \'get\', 0);">เพิ่มเพื่อน</a></td></tr><tr><td colspan="2"><img src="source/plugin/th_chat/images/pm.png" align="absmiddle" style="padding-right:5px"> <a href="home.php?mod=spacecp&amp;ac=pm&amp;op=showmsg&amp;handlekey=showmsg_'.$r['uid'].'&amp;touid='.$r['uid'].'&amp;pmid=0&amp;daterange=2" onClick="showWindow(\'showMsgBox\', this.href, \'get\', 0)" id="a_sendpm_'.$r['uid'].'" class="xi2">ส่งข้อความ</a></td></tr>';
}
$banuntil = '';
if($r['ban']){
	$banuntil = '<span style="color:#f00"><b>ถูกแบนถึง</b><br>'.date("d/m/Y H:i:s",$r['ban']).'</span><br>';
}
$groupcolor = ($r['color']?'style="background-color:'.$r['color'].'" ':'');
$banned = explode(",", $config['chat_ban']);
if(in_array($_G['adminid'],array(1,2,3))&&!($uid==$_G['uid'])){
	$mlist .= (in_array($r['groupid'],array(1,2,3))?'':(!$r['ban']?'<tr><td colspan="2">แบน: <a href="javascript:void(0);" onClick="nzchatobj(\'#nzchatmessage\').val(\'!ban '.$r['uid'].' 3600\');nzSend();">[1 ชม.]</a> <a href="javascript:void(0);" onClick="nzchatobj(\'#nzchatmessage\').val(\'!ban '.$r['uid'].' 86400\');nzSend();">[1 วัน]</a> <a href="javascript:void(0);" onClick="nzchatobj(\'#nzchatmessage\').val(\'!ban '.$r['uid'].'\');nzSend();">[ถาวร]</a></td></tr>':'<tr><td colspan="2"><a href="javascript:void(0);" onClick=\'nzCommand("unban","'.$uid.'");\'>ปลดแบน</a></td></tr>'));
}
include template('common/header_ajax');
include template('th_chat:profile');
include template('common/footer_ajax');
?>