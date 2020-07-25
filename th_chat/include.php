<?
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
loadcache('plugin');
$chat = array();
$config = $_G['cache']['plugin']['th_chat'];
$chat['bgcss'] = 'background:#eaeaea';
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
$chat['autoconnect'] = $config['autoconnect'];
$chat['upicme'] = $config['upicme'];
$chat['quota'] = $config['quota'];
$chat['chat_strlen'] = $config['chat_strlen'];
$chat['namemode'] = $config['namemode'];
$chat['editor'] = '';
if($config['usedzc']){
	$chat['editor'] .= '<a href="javascript:void(0);" title="ตัวหนา" class="fbld" onclick="seditor_insertunit(\'nzchat\', \'[b]\', \'[/b]\')">B</a>
<a href="javascript:void(0);" title="ตัวขีดเส้นใต้" style="background-position: -40px 0px;" onclick="seditor_insertunit(\'nzchat\', \'[u]\', \'[/u]\')">U</a>
';
	$chat['editor'] .= '<a href="javascript:void(0);" title="สี" class="fclr" id="nzchatforecolor" onclick="showColorBox(this.id, 2, \'nzchat\');doane(event)">Color</a>
';
}
if($config['useimg']){
	$chat['editor'] .= '<a id="nzchatimg" href="javascript:void(0);" title="ใส่รูป" class="fmg" onclick="seditor_insertunit(\'nzchat\', \'[img]\', \'[/img]\')">Image</a>
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
if($config['useunshowdzc']){
	$chat['editor'] .= '<a id="nzchatquote" href="javascript:void(0);" title="อ้างอิง" class="fqt" onclick="seditor_menu(\'nzchat\', \'quote\')">Quote</a>
';
	$chat['editor'] .= '<a id="nzchatcode" href="javascript:void(0);" title="โค้ด" class="fcd" onclick="seditor_menu(\'nzchat\', \'code\')">Code</a>
';
	$chat['editor'] .= '<a id="nzchatat" href="javascript:void(0);" title="@" class="fat" onclick="seditor_menu(\'nzchat\', \'at\')">At</a>
';
}
if($config['mediacode']){
	$chat['editor'] .= '<a id="nzchataudio" class="nzbbcode" href="javascript:void(0);" style="background: url(static/image/editor/editor.gif) no-repeat;
background-position: -220px -20px;" title="เพลง" onclick="seditor_insertunit(\'nzchat\', \'[audio]\', \'[/audio]\')">Audio</a>
<a id="nzchatvideo" class="nzbbcode" href="javascript:void(0);" style="background: url(static/image/editor/editor.gif) no-repeat;
background-position: -240px -20px;" title="วีดีโอ" onclick="seditor_insertunit(\'nzchat\', \'[media]\', \'[/media]\')">Video</a>
<a id="nzchatflash" class="nzbbcode" href="javascript:void(0);" style="
background: url(static/image/editor/editor.gif) no-repeat;
background-position: -260px -20px;" title="แฟลช" onclick="seditor_insertunit(\'nzchat\', \'[flash]\', \'[/flash]\')">Flash</a>
';
}
if($config['usemore']){
	loadcache('bbcodes_display');
	foreach($_G['cache']['bbcodes_display'][$_G['groupid']] as $tag => $bbcode){
		$chat['editor'] .= '<a id="nzchat'.$tag.'" class="nzbbcode" href="javascript:void(0);" style="
	background: url('.STATICURL.'image/common/'.$bbcode['icon'].') no-repeat;" title="'.$tag.'" onclick="seditor_insertunit(\'nzchat\', \'['.$tag.']\', \'[/'.$tag.']\')">'.$tag.'</a>';
	}
}
if(in_array($_G['adminid'],array(1,2,3))){
		$chat['editor'] .= '<a href="javascript:void(0);" onClick="nzCommand(\'clear\',\'\');" title="ล้างห้องแชท" style="width:20px;height:20px;background:url(source/plugin/th_chat/images/clear.png) no-repeat center">Clear</a>';
}
$chat['welcometext'] = $config['welcometext'];
?>