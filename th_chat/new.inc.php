<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$id = intval($_POST['lastid']);
$is_mod = in_array($_G['adminid'],array(1,2,3));
include 'functions.php';
$re = DB::query("SELECT n.*,m.username AS name,mt.username AS toname,g.color,gt.color AS tocolor 
FROM ".DB::table('newz_data')." n 
LEFT JOIN ".DB::table('common_member')." m ON n.uid=m.uid 
LEFT JOIN ".DB::table('common_member')." mt ON n.touid=mt.uid 
LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid 
LEFT JOIN ".DB::table('common_usergroup')." gt ON mt.groupid=gt.groupid 
WHERE  id>{$id} AND (n.touid='0' OR n.touid='{$uid}' OR n.uid='{$uid}') 
ORDER BY id DESC LIMIT 30");
$sounddata = DB::fetch_first("SELECT sound_1,sound_2 FROM ".DB::table('newz_nick')." WHERE uid='{$_G['uid']}'");
$sounddata['sound_1']=='1'?true:false;
$sounddata['sound_2']=='0'?false:true;
$body=array();
while($c = DB::fetch($re)){
	$c['text'] = preg_replace('/\[quota\](.*?)\[\/quota\]/', '$1', $c['text']);
	if($c['ip'] == 'delete'){
		$body[$c['id']] .= '<script>nzchatobj("#nzrows_'.$c['text'].'").fadeOut(200);</script>';
		continue;
	}elseif($c['ip'] == 'edit'){
		$body[$c['id']] .= '<script>nzchatobj("#nzchatcontent'.$c['icon'].'").html("'.addslashes($c['text']).'");</script>';
		continue;
	}elseif($c['ip'] == 'notice'){
		$body[$c['id']] .= '<script>nzchatobj("#nzchatnotice").html("'.addslashes($c['text']).'");</script>';
		continue;
	}
	if($c['ip']=='clear'){
$seedd = $time.'_'.$uid.'_'.rand(1,999);
		$c['text'] = '<span style="color:red" id="del_'.$seedd.'">แจ้งเตือน:</span> <span id="nzchatcontent'.$c['id'].'">ล้างข้อมูล<script type="text/javascript">nzchatobj("#del_'.$seedd.'").parent().parent().parent().'.($config['chat_type']==1?'next':'prev').'Until().remove();</script>';
	}elseif($c['icon']=='alert'){
		$c['text'] = '<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==0){
		$c['text'] = (($config['pm_sound']&&$sounddata['sound_1'])?'<audio autoplay><source src="'.$config['pm_sound'].'" type="audio/mpeg"></audio>':'').'<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==$uid){
		$c['text'] = (($config['pm_sound']&&$sounddata['sound_2'])?'<audio autoplay><source src="'.$config['pm_sound'].'" type="audio/mpeg"></audio>':'').'<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['uid']==$uid){
		$c['text'] = '<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}
	$body[$c['id']]  .= chatrow($c['id'],$c['text'],$c['uid'],$c['name'],$c['time'],$c['touid'],$c['icon'],$is_mod);
	if($c['ip']=='clear'){
		break;
	}
}
session_start();
if(TIMESTAMP-$_SESSION['th_chat_online']>15){
	$_SESSION['th_chat_online'] = TIMESTAMP;
	include 'online.php';
}
echo json_encode(array('chat_row'=>$body,'chat_online'=>$body_online,'chat_online_total'=>$oltotal));
?>