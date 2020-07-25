<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$is_mod = in_array($_G['adminid'],array(1,2,3));
include 'functions.php';
$re = DB::query("SELECT n.*,m.username AS name,mt.username AS toname,g.color,gt.color AS tocolor,ni.name AS nick,nt.name AS tonick 
FROM ".DB::table('newz_data')." n 
LEFT JOIN ".DB::table('common_member')." m ON n.uid=m.uid 
LEFT JOIN ".DB::table('common_member')." mt ON n.touid=mt.uid 
LEFT JOIN ".DB::table('common_usergroup')." g ON m.groupid=g.groupid 
LEFT JOIN ".DB::table('common_usergroup')." gt ON mt.groupid=gt.groupid 
LEFT JOIN ".DB::table('newz_nick')." ni ON n.uid=ni.uid 
LEFT JOIN ".DB::table('newz_nick')." nt ON n.touid=nt.uid 
WHERE (n.touid='0' OR n.touid='$uid' OR n.uid='$uid') AND n.ip != 'changename' AND n.ip != 'delete'
ORDER BY id DESC LIMIT {$config['chat_init']}");
$body=array();
$lastid=0;
while($c = DB::fetch($re)){
	$c['text'] = preg_replace('/\[quota\](.*?)\[\/quota\]/', '$1', $c['text']);
	if($c['id']>$lastid)
	$lastid = $c['id'];
	if ($c['ip'] == 'changename'){
		continue;
	}elseif($c['ip'] == 'delete'){
		continue;
	}elseif($c['ip'] == 'edit'){
		continue;
	}elseif($c['ip'] == 'notice'){
		continue;
	}
	if($config['namemode']==1){$c['status'] = $c['nick'];}
	if((strval($c['nick'])===''&&$config['namemode']==2)||$config['namemode']!=2){$c['nick'] = $c['name'];}
	if((strval($c['tonick'])===''&&$config['namemode']==2)||$config['namemode']!=2){$c['tonick'] = $c['toname'];}
	$c['tonick'] = htmlspecialchars_decode($c['tonick']);
	if($c['ip']=='clear'){
		$seedd = $time.'_'.$uid.'_'.rand(1,999);
		$c['text'] = '<span style="color:red" id="del_'.$seedd.'">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_14').'</span> <span id="nzchatcontent'.$c['id'].'">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_46').'';
	}elseif($c['icon']=='alert'){
		$c['text'] = '<span style="color:red">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_14').'</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==0){
		$c['text'] = '<span style="color:#3366CC">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_38').'</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==$uid){
		$c['text'] = '<span style="color:#FF9900">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_03').' <a href="javascript:;" onClick="nzTouid('.$c['uid'].')">(ตอบกลับ)</a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['uid']==$uid){
		$c['text'] = '<span style="color:#FF9900">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_02').' <a href="home.php?mod=space&uid='.$c['touid'].'" class="nzca" target="_blank"><font color="'.$c['tocolor'].'"><span class="nzuname_'.$c['touid'].'">'.$c['tonick'].'</span></font></a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}
	if(!$config['showos']&&$c['icon']!='alert')$c['icon']='';
	$body[] = chatrow($c['id'],$c['text'],$c['uid'],$c['name'],$c['nick'],$c['time'],$c['color'],$c['touid'],1,$c['icon'],$is_mod,$c['status']);
	if($c['ip']=='clear'){
		break;
	}
}
$body[] = '<script>var formhash = "&formhash='.formhash().'";</script>';
include 'online.php';
if($config['chat_type']==2){
	$body = array_reverse($body);
}
$body = implode('',$body);
$body = array('lastid'=>$lastid,'datahtml'=>$body,'datachatonline'=>$body_online,'chat_online_total'=>$oltotal,'welcometext'=>$config['welcometext']);
echo json_encode($body);
?>