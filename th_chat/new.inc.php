<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$id = intval($_POST['lastid']);
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
WHERE  id>{$id} AND (n.touid='0' OR n.touid='{$uid}' OR n.uid='{$uid}') 
ORDER BY id DESC LIMIT 30");
$sounddata = DB::fetch_first("SELECT sound_1,sound_2 FROM ".DB::table('newz_nick')." WHERE uid='{$_G['uid']}'");
$sounddata['sound_1']=='1'?true:false;
$sounddata['sound_2']=='0'?false:true;
$body=array();
while($c = DB::fetch($re)){
	$c['text'] = preg_replace('/\[quota\](.*?)\[\/quota\]/', '$1', $c['text']);
	if ($c['ip'] == 'changename'){
		$body[$c['id']] .= '<script>nzchatobj(".nzu'.($config['namemode']==1?'status':'name').'_'.$c['uid'].'").html("'.addcslashes(htmlspecialchars_decode($c['text']), '"').'");</script>';
		continue;
	}elseif($c['ip'] == 'delete'){
		$body[$c['id']] .= '<script>nzchatobj("#nzrows_'.$c['text'].'").fadeOut(200);</script>';
		continue;
	}elseif($c['ip'] == 'edit'){
		$body[$c['id']] .= '<script>nzchatobj("#nzchatcontent'.$c['icon'].'").html("'.addcslashes($c['text'],'"').'");</script>';
		continue;
	}elseif($c['ip'] == 'notice'){
		$body[$c['id']] .= '<script>nzchatobj("#nzchatnotice").html("'.addcslashes($c['text'],'"').'");</script>';
		continue;
	}
	if($config['namemode']==1){$c['status'] = $c['nick'];}
	if((strval($c['nick'])===''&&$config['namemode']==2)||$config['namemode']!=2){$c['nick'] = $c['name'];}
	if((strval($c['tonick'])===''&&$config['namemode']==2)||$config['namemode']!=2){$c['tonick'] = $c['toname'];}
	$c['tonick'] = htmlspecialchars_decode($c['tonick']);
	if($c['ip']=='clear'){
$seedd = $time.'_'.$uid.'_'.rand(1,999);
		$c['text'] = '<span style="color:red" id="del_'.$seedd.'">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_14').'</span> <span id="nzchatcontent'.$c['id'].'">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_46').'<script type="text/javascript">nzchatobj("#del_'.$seedd.'").parent().parent().parent().'.($config['chat_type']==1?'next':'prev').'Until().remove();</script>';
	}elseif($c['icon']=='alert'){
		$c['text'] = '<span style="color:red">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_14').'</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==0){
		$c['text'] = (($config['pm_sound']&&$sounddata['sound_1'])?'<audio autoplay><source src="'.$config['pm_sound'].'" type="audio/mpeg"></audio>':'').'<span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['touid']==$uid){
		$c['text'] = (($config['pm_sound']&&$sounddata['sound_2'])?'<audio autoplay><source src="'.$config['pm_sound'].'" type="audio/mpeg"></audio>':'').'<span style="color:#FF9900">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_03').' <a href="javascript:;" onClick="nzTouid('.$c['uid'].')">(ตอบกลับ)</a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}elseif($c['uid']==$uid){
		$c['text'] = '<span style="color:#FF9900">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_02').' <a href="home.php?mod=space&uid='.$c['touid'].'" class="nzca" target="_blank"><font color="'.$c['tocolor'].'"><span class="nzuname_'.$c['touid'].'">'.$c['tonick'].'</span></font></a>:</span> <span id="nzchatcontent'.$c['id'].'">' . $c['text'];
	}
	$body[$c['id']]  .= chatrow($c['id'],$c['text'],$c['uid'],$c['name'],$c['nick'],$c['time'],$c['color'],$c['touid'],0,$c['icon'],$is_mod,$c['status']);
	if($c['ip']=='clear'){
		break;
	}
}
session_start();
if(time()-$_SESSION['th_chat_online']>15){
	$_SESSION['th_chat_online'] = time();
	include 'online.php';
}
echo json_encode(array('chat_row'=>$body,'chat_online'=>$body_online,'chat_online_total'=>$oltotal));
?>