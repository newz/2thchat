<?
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

loadcache('plugin');
$chat = array();
$config = $_G['cache']['plugin']['th_chat'];
if(!$config['active'])
	return '';
		
$chat['bgcss'] = 'background:#FFF';
if($config['chat_bg']){
	$chat['bgcss'] .= ' url('.$config['chat_bg'].')';
	switch($config['chat_bgpos']){
		case 1: $x = 'left top';break;
		case 2: $x = 'center top';break;
		case 3: $x = 'right top';break;
		case 4: $x = 'left center';break;
		case 5: $x = 'center center';break;
		case 6: $x = 'right center';break;
		case 7: $x = 'left buttom';break;
		case 8: $x = 'center buttom';break;
		case 9: $x = 'right buttom';break;
	}
	switch($config['chat_bgrepeat']){
		case 1: $y = 'no-repeat';break;
		case 2: $y = 'repeat-x';break;
		case 3: $y = 'repeat-y';break;
		case 4: $y = 'repeat';break;
	}
	$chat['bgcss'] .= ' '.$x.' '.$y;
}
$chat['bgcss'] .= ';';

$chat['delay'] = $config['chat_delay'];

?>