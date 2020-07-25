<?php if(!defined('IN_DISCUZ')) { exit('Access Denied'); }

loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];

$uid = $_G['uid'];

include 'functions.php';

$re = DB::query("SELECT n.*,m.username AS name,mt.username AS toname,g.color,ni.name AS nick,nt.name AS tonick 
FROM 2thchat_data n 
LEFT JOIN ".DB::table('common_member')." m ON n.uid=m.uid 
LEFT JOIN ".DB::table('common_member')." mt ON n.touid=mt.uid 
LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid 
LEFT JOIN 2thchat_nick ni ON n.uid=ni.uid 
LEFT JOIN 2thchat_nick nt ON n.touid=nt.uid 
WHERE n.touid='0' OR n.touid='{$uid}' OR n.uid='{$uid}' 
ORDER BY id DESC LIMIT 30");	

$body='';
$lastid=0;

while($c = DB::fetch($re)){
	
if($c['id']>$lastid)
	$lastid = $c['id'];

if ($c['ip'] == 'changename'){
        continue;
}
	
	if(strval($c['nick'])==='')
		$c['nick'] = $c['name'];
	if(strval($c['tonick'])==='')
		$c['tonick'] = $c['toname'];
	
	if($c['touid']==0){
			$c['text'] = '<span style="color:#3366CC">Says:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
		}elseif($c['touid']==$uid){
			$c['text'] = '<span style="color:#FF9900">กระซิบถึงคุณ <a href="javascript:;" onClick="nzTouid('.$c['uid'].')">reply</a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
		}elseif($c['uid']==$uid){
			$c['text'] = '<span style="color:#FF9900">กระซิบกับ <a href="space-uid-'.$c['touid'].'.html" target="_blank">'.$c['tonick'].'</a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
		}
	$body .= chatrow($c['id'],$c['text'],$c['uid'],$c['nick'],$c['time'],$c['color'],$c['touid'],1);

if($c['ip']=='clear'){
break;
}

}
include 'online.php';

$body = array('lastid'=>$lastid,'datahtml'=>$body,'datachatonline'=>$body_online,'chat_online_total'=>$oltotal);

echo json_encode($body);
 ?>