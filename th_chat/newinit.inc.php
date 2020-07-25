<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$is_mod = in_array($_G['adminid'],array(1,2,3));
include 'functions.php';
$re = DB::query("SELECT n.*,m.username AS name,mt.username AS toname,g.color,gt.color AS tocolor 
FROM ".DB::table('newz_data')." n 
LEFT JOIN ".DB::table('common_member')." m ON n.uid=m.uid 
LEFT JOIN ".DB::table('common_member')." mt ON n.touid=mt.uid 
LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid 
LEFT JOIN ".DB::table('common_usergroup')." gt ON mt.groupid=gt.groupid 
WHERE (n.touid='0' OR n.touid='$uid' OR n.uid='$uid') AND n.ip NOT IN ('delete','edit','notice')
ORDER BY id DESC LIMIT {$config['chat_init']}");
$body=array();
$lastid=0;
while($c = DB::fetch($re)){
	$c['text'] = preg_replace('/\[quota\](.*?)\[\/quota\]/', '$1', $c['text']);
	if($c['id']>$lastid)
	$lastid = $c['id'];
	if($c['ip'] == 'delete'){
		continue;
	}elseif($c['ip'] == 'edit'){
		continue;
	}elseif($c['ip'] == 'notice'){
		continue;
	}
	if($c['ip']=='clear'){
		$seedd = $time.'_'.$uid.'_'.rand(1,999);
		$c['text'] = '<span style="color:red" id="del_'.$seedd.'">แจ้งเตือน:</span> <span id="nzchatcontent'.$c['id'].'">ล้างข้อมูล';
	}elseif($c['icon']=='alert'){
		$c['text'] = '<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==0){
		$c['text'] = '<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==$uid){
		$c['text'] = '<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['uid']==$uid){
		$c['text'] = '<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}
	$body[] = chatrow($c['id'],$c['text'],$c['uid'],$c['name'],$c['time'],$c['touid'],$c['icon'],$is_mod);
	if($c['ip']=='clear'){
		break;
	}
}
$body[] = '<script>var formhash = "&formhash='.formhash().'";</script>';
include 'online.php';
$body = array_reverse($body);
$body = implode('',$body);
$body = array('lastid'=>$lastid,'datahtml'=>$body,'datachatonline'=>$body_online,'chat_online_total'=>$oltotal,'welcometext'=>$config['welcometext']);
echo json_encode($body);
?>