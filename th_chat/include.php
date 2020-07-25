<?
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
loadcache('plugin');
$chat = array();
$config = $_G['cache']['plugin']['th_chat'];
$chat['bgcss'] = 'background:#EEE';
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
	$chat['editor'] .= '<a href="javascript:void(0);" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_21').'" class="fbld" onclick="seditor_insertunit(\'nzchat\', \'[b]\', \'[/b]\')">B</a>
<a href="javascript:void(0);" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_19').'" style="background-position: -40px 0px;" onclick="seditor_insertunit(\'nzchat\', \'[u]\', \'[/u]\')">U</a>
';
	$chat['editor'] .= '<a href="javascript:void(0);" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_50').'" class="fclr" id="nzchatforecolor" onclick="showColorBox(this.id, 2, \'nzchat\');doane(event)">Color</a>
';
}
if($config['useimg']){
	$chat['editor'] .= '<a id="nzchatimg" href="javascript:void(0);" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_53').'" class="fmg" onclick="seditor_insertunit(\'nzchat\', \'[img]\', \'[/img]\')">Image</a>
';
}
if($config['usedzc']){
	$chat['editor'] .= '<a id="nzchaturl" href="javascript:void(0);" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_16').'" class="flnk" onclick="seditor_menu(\'nzchat\', \'url\')">Link</a>
';
}
if($config['useemo']){
	$chat['editor'] .= '<a href="javascript:void(0);" class="fsml" id="nzchatsml" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_20').'" onclick="showMenu({\'ctrlid\':this.id,\'evt\':\'click\',\'layer\':2});return false;">Smilies</a>
';
}
if($config['useunshowdzc']){
	$chat['editor'] .= '<a id="nzchatquote" href="javascript:void(0);" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_59').'" class="fqt" onclick="seditor_menu(\'nzchat\', \'quote\')">Quote</a>
';
	$chat['editor'] .= '<a id="nzchatcode" href="javascript:void(0);" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_13').'" class="fcd" onclick="seditor_menu(\'nzchat\', \'code\')">Code</a>
';
	$chat['editor'] .= '<a id="nzchatat" href="javascript:void(0);" title="@" class="fat" onclick="seditor_menu(\'nzchat\', \'at\')">At</a>
';
}
if($config['mediacode']){
	$chat['editor'] .= '<a id="nzchataudio" href="javascript:void(0);" style="
float: left;
margin: 2px 5px 0 0;
width: 20px;
height: 20px;
background: url(static/image/editor/editor.gif) no-repeat;
background-position: -220px -20px;
text-indent: -9999px;
line-height: 20px;
overflow: hidden;
" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_39').'" onclick="seditor_insertunit(\'nzchat\', \'[audio]\', \'[/audio]\')">Audio</a>
<a id="nzchatvideo" href="javascript:void(0);" style="
float: left;
margin: 2px 5px 0 0;
width: 20px;
height: 20px;
background: url(static/image/editor/editor.gif) no-repeat;
background-position: -240px -20px;
text-indent: -9999px;
line-height: 20px;
overflow: hidden;
" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_48').'" onclick="seditor_insertunit(\'nzchat\', \'[media]\', \'[/media]\')">Video</a>
<a id="nzchatflash" href="javascript:void(0);" style="
float: left;
margin: 2px 5px 0 0;
width: 20px;
height: 20px;
background: url(static/image/editor/editor.gif) no-repeat;
background-position: -260px -20px;
text-indent: -9999px;
line-height: 20px;
overflow: hidden;
" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_41').'" onclick="seditor_insertunit(\'nzchat\', \'[flash]\', \'[/flash]\')">Flash</a>
';
}
if($config['usemore']){
	loadcache('bbcodes_display');
	foreach($_G['cache']['bbcodes_display'][$_G['groupid']] as $tag => $bbcode){
		$chat['editor'] .= '<a id="nzchat'.$tag.'" href="javascript:void(0);" style="
	float: left;
	margin: 2px 5px 0 0;
	width: 20px;
	height: 20px;
	background: url('.STATICURL.'image/common/'.$bbcode['icon'].') no-repeat;
	text-indent: -9999px;
	line-height: 20px;
	overflow: hidden;
	" title="'.$tag.'" onclick="seditor_insertunit(\'nzchat\', \'['.$tag.']\', \'[/'.$tag.']\')">'.$tag.'</a>';
	}
}
if(in_array($_G['adminid'],array(1,2,3))){
		$chat['editor'] .= '<a href="javascript:void(0);" onClick="nzCommand(\'clear\',\'\');" title="'.lang('plugin/th_chat', 'jdj_th_chat_text_php_47').'" style="width:20px;height:20px;background:url(source/plugin/th_chat/images/clear.png) no-repeat center">Clear</a>';
}
$chat['welcometext'] = $config['welcometext'];
$chat['sort'] = $config['chat_type'];
?>