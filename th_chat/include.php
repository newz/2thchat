<?php
if(!defined('IN_DISCUZ')) {
exit('Access Denied');
}
loadcache('plugin');
$chat = array();
$config = $_G['cache']['plugin']['th_chat'];
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
$chat['delay'] = intval($config['chat_delay']);
$chat['reload'] = intval($config['chat_reload']);
$chat['guest_show'] = $config['guest_show'];
$chat['chatrowmax'] = $config['chat_init'];
$chat['iscleardata'] = $config['typechatrow'];
$chat['editor'] = '';
if($config['usedzc']){
$chat['editor'] .= '<a href="javascript:void(0);" title="ตัวหนา" class="fbld" onclick="seditor_insertunit(\'nzchat\', \'[b]\', \'[/b]\')">B</a>
<a href="javascript:void(0);" title="ตัวเอียง" style="background-position: -20px 0px;" onclick="seditor_insertunit(\'nzchat\', \'[i]\', \'[/i]\')">I</a>
<a href="javascript:void(0);" title="ตัวขีดเส้นใต้" style="background-position: -40px 0px;" onclick="seditor_insertunit(\'nzchat\', \'[u]\', \'[/u]\')">U</a>
<a href="javascript:void(0);" title="สี" class="fclr" id="nzchatforecolor" onclick="showColorBox(this.id, 2, \'nzchat\');doane(event)">Color</a>
';
}
if($config['useimg']){
$chat['editor'] .= '<a id="nzchatimg" href="javascript:void(0);" title="ใส่รูป" class="fmg" onclick="seditor_menu(\'nzchat\', \'img\')">Image</a>
';
}
if($config['usedzc']){
$chat['editor'] .= '<a id="nzchaturl" href="javascript:void(0);" title="เชื่อมโยงลิ้งก์" class="flnk" onclick="seditor_menu(\'nzchat\', \'url\')">Link</a>
';
}
if($config['useemo']){
$chat['editor'] .= '<a href="javascript:void(0);" class="fsml" id="nzchatsml" title="ตัวยิ้ม" onclick="showMenu({\'ctrlid\':this.id,\'evt\':\'click\',\'layer\':2});return false;">Smilies</a>
';
}
$chat['welcometext'] = $config['welcometext'];
$chat['sort'] = $config['chat_type'];
?>