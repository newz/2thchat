<?php if(!defined('IN_DISCUZ')) { exit('2.05'); }
$f = file_get_contents(DISCUZ_ROOT.'/source/plugin/th_chat/template/discuz.htm');
$a = file_get_contents(DISCUZ_ROOT.'/source/plugin/th_chat/template/big.htm');
if(strpos($f,'&copy; <a href="http://2th.me/" target="_blank">2th Chat</a> & <a href="http://www.weza.in/" target="_blank">Weza</a>')===false||strpos($a,'&copy; <a href="http://2th.me/" target="_blank">2th Chat</a> & <a href="http://www.weza.in/" target="_blank">Weza</a>')===false)die('no credit');
echo 'ok';
?>