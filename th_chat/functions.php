<?

DB::query("UPDATE ".DB::table('common_session')." SET action='2',fid='0',tid='0' WHERE uid='{$uid}'");

function chatrow($id,$text,$uid_p,$username,$time,$color,$touid,$is_init){
global $uid,$config;
	return '<tr class="nzchatrow" id="nzrows_'.$id.'" onMouseOver="nzchatobj(\'#nzchatquota'.$id.'\').css(\'display\',\'inline\');" onMouseOut="nzchatobj(\'#nzchatquota'.$id.'\').css(\'display\',\'none\');" '.($is_init&&$touid?' style="background:#FFF0F5;"':'').'>
	<td class="nzavatart"><a href="'.avatar($uid_p,'big',1).'" target="_blank"><img src="'.avatar($uid_p,'small',1).'" alt="avatar" class="nzchatavatar" onError="this.src=\'uc_server/images/noavatar_small.gif\';" /></a></td>
	<td class="nzcontentt"><div class="nzinnercontent"><span id="nzchatquota'.$id.'" class="nzcq"><a href="javascript:void(0);" onClick="nzchatobj(\'#nzchatmessage\').val(\'@'.strip_tags($username).' \');nzchatobj(\'#nzchatmessage\').focus();">@</a> <a href="javascript:void(0);" onClick="nzQuota('.$id.')">Quota</a>'.(($config['chat_point']&&($uid!=$uid_p))?' <a href="javascript:void(0);" onClick="nzPlusone('.$uid_p.',1);" style="color:green">+1</a> <a href="javascript:void(0);" onClick="nzPlusone('.$uid_p.',-1);" style="color:red">-1</a>':'').'</span>
	<span><a href="home.php?mod=space.php&amp;uid='.$uid_p.'" class="nzca" style="color:'.$color.';" target="_blank"><span class="nzuname_'.$uid_p.'">'.$username.'</span></a> ('.get_date($time).')</span><br />
	'.$text.'</span></div></td>
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

$time = time();

?>