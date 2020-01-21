<?php
function chatrow($id,$text,$uid_p,$username,$time,$color,$touid,$is_init,$icon,$mod){
global $uid,$config;
$icon=$icon?'<img src="source/plugin/th_chat/images/os/'.$icon.'.png" title="'.$icon.'"> ':'';
return '<tr class="nzchatrow" id="nzrows_'.$id.'" onMouseOver="nzchatobj(\'#nzchatquota'.$id.'\').css(\'display\',\'inline\');" onMouseOut="nzchatobj(\'#nzchatquota'.$id.'\').css(\'display\',\'none\');" '.($is_init&&$touid?' style="background:#eef6ff;"':'').'>
<td class="nzavatart"><a href="'.avatar($uid_p,'big',1).'" target="_blank"><img src="'.avatar($uid_p,'small',1).'" alt="avatar" class="nzchatavatar" onError="this.src=\'uc_server/images/noavatar_small.gif\';" /></a></td>
<td class="nzcontentt"><div class="nzinnercontent"><span id="nzchatquota'.$id.'" class="nzcq"><a href="javascript:void(0);" onClick="nzchatobj(\'#nzchatmessage\').val(\'@'.strip_tags($username).' \');nzchatobj(\'#nzchatmessage\').focus();">@</a> <a href="javascript:void(0);" onClick="nzQuota('.$id.')">Quota</a>'.(($config['chat_point']&&($uid!=$uid_p))?' <a href="javascript:void(0);" onClick="nzPlusone('.$uid_p.',1);" style="color:green">+1</a> <a href="javascript:void(0);" onClick="nzPlusone('.$uid_p.',-1);" style="color:red">-1</a>':'').($mod?' <a href="javascript:;" onClick="nzDelete('.$id.');">Delete</a>':'').'</span>
<span><a href="home.php?mod=space.php&amp;uid='.$uid_p.'" class="nzca" target="_blank"><font color="'.$color.'"><span class="nzuname_'.$uid_p.'">'.$username.'</span></font></a> ('.get_date($time).')</span><br />
'.$icon.$text.'</span>'.($is_init?'':($touid?'<script>nzchatobj("#nzrows_'.$id.'").css(\'backgroundColor\',\'#BBB\').animate({ backgroundColor: \'#eef6ff\' }, 500,function(){nzchatobj("#nzrows_'.$id.'").css(\'background\',\'url(source/plugin/th_chat/images/bg.png) repeat\')});</script>':'<script>nzchatobj("#nzrows_'.$id.'").css(\'backgroundColor\',\'#DDD\').animate({ backgroundColor: \'#FFF\' }, 500 ,function(){nzchatobj("#nzrows_'.$id.'").css(\'backgroundColor\',\'transparent\')});</script>')).'</div></td>
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
function checkOs($ua=''){
$ua = $ua?$ua:$_SERVER['HTTP_USER_AGENT'];
$ua = strtolower($ua);
if(strpos($ua, 'android') !== false){
return 'android';
}elseif(strpos($ua, 'iphone') !== false){
return 'iphone';
}elseif(strpos($ua, 'windows phone os') !== false){
return 'windows_phone';
}elseif(strpos($ua, 'blackberry') !== false){
return 'blackberry';
}elseif(strpos($ua, 'ubuntu') !== false){
return 'ubuntu';
}elseif(strpos($ua, 'symbian') !== false){
return 'symbian';
}elseif(strpos($ua, 'mac') !== false){
return 'macintosh';
}elseif(strpos($ua, 'win') !== false){
return 'windows';
}
return '';
}
$time = time();
?>