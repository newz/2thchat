<?
function chatrow($id,$text,$uid_p,$username,$time,$color,$touid,$is_init,$icon,$mod,$status){
	global $uid,$config;
	if($icon=='alert')
	{
		$icon=$icon?img('alert', '/', lang('plugin/th_chat', 'jdj_th_chat_text_php_14')).' ':'';
	}else if($icon=='bot')
	{
		$icon=$icon?img('bot', '/', lang('plugin/th_chat', 'jdj_th_chat_text_php_58')).' ':'';
	}else{
		$icons = explode("|", $icon,3);
		$icon=$icon?img($icons[2], '/'.$icons[0].'/', $icons[1]).' ':'';
	}
	$username = htmlspecialchars_decode($username);
	$status = htmlspecialchars_decode($status);
	return '<tr class="nzchatrow" id="nzrows_'.$id.'" onMouseOver="nzchatobj(\'#nzchatquota'.$id.'\').css(\'display\',\'inline\');" onMouseOut="nzchatobj(\'#nzchatquota'.$id.'\').css(\'display\',\'none\');" '.($is_init&&$touid?' style="background:#eef6ff;"':'').'>
<td class="nzavatart"><a href="'.avatar($uid_p,'big',1).'" target="_blank"><img src="'.avatar($uid_p,'small',1).'" alt="avatar" class="nzchatavatar" onError="this.src=\'uc_server/images/noavatar_small.gif\';" /></a></td>
<td class="nzcontentt"><div class="nzinnercontent"><span id="nzchatquota'.$id.'" class="nzcq">'.($uid!=$uid_p?'<a href="javascript:void(0);" onClick="nzAt('.$uid_p.');">@</a> ':'').'<a href="javascript:void(0);" onClick="nzQuota('.$id.')">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_59').'</a>'.($uid!=$uid_p?' <a href="javascript:void(0);" onClick="nzTouid('.$uid_p.')">'.lang('plugin/th_chat', 'jdj_th_chat_text_php_01').'</a>':'').(($config['chat_point']&&($uid!=$uid_p))?' <a href="javascript:void(0);" onClick="nzPlusone('.$uid_p.',1);" style="color:green">+1</a> <a href="javascript:void(0);" onClick="nzPlusone('.$uid_p.',-1);" style="color:red">-1</a>':'').((($config['editmsg']==1)&&$mod)||(($config['editmsg']==2)&&$mod&&($uid==$uid_p))||(($config['editmsg']==3)&&($uid==$uid_p))?' <a href="javascript:;" onClick=\'nzCommand("edit","'.$id.'");\'>'.lang('plugin/th_chat', 'jdj_th_chat_text_php_08').'</a>':'').($mod?' <a href="javascript:;" onClick=\'nzCommand("del","'.$id.'");\'>'.lang('plugin/th_chat', 'jdj_th_chat_text_php_43').'</a>':'').'</span>
<span><a href="home.php?mod=space&uid='.$uid_p.'" class="nzca" target="_blank"><font color="'.$color.'"><span class="nzuname_'.$uid_p.'">'.$username.'</span></font></a> <span id="nzstatus" class="nzustatus_'.$uid_p.'">'.$status.'</span> ('.get_date($time).')</span><br />
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
function checkOs(){
	return get_platform();
}
$time = time();
function get_browser_version($title) {
	$start = $title;
	if (strtolower($title) == strtolower('Opera') && preg_match('/Version/i', $_SERVER['HTTP_USER_AGENT'])) $start = 'Version';
	elseif (strtolower($title) == strtolower('Safari') && preg_match('/Version/i', $_SERVER['HTTP_USER_AGENT'])) $start = 'Version';
	elseif (strtolower($title) == strtolower('Maxthon') && preg_match('/Maxthon/i', $_SERVER['HTTP_USER_AGENT'])) $start = 'Maxthon';
	elseif (strtolower($title) == strtolower('Opera Mobi') && preg_match('/Version/i', $_SERVER['HTTP_USER_AGENT'])) $start = 'Version';
	elseif (strtolower($title) == strtolower('Pre') && preg_match('/Version/i', $_SERVER['HTTP_USER_AGENT'])) $start = 'Version';
	elseif (strtolower($title) == strtolower('Links')) $start = 'Links (';
	elseif (strtolower($title) == strtolower('UC Browser')) $start = 'UC Browse';
	preg_match('/' . $start . '[\ |\/]?([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch);
	$version = $regmatch[1];
	if (strtolower($title) == 'msie' && strtolower($version) == '7.0' && preg_match('/Trident\/[456].0/i', $_SERVER['HTTP_USER_AGENT'])) return ' > 8.0 (Compatibility Mode)';
	elseif (strtolower($title) == 'msie') return ' ' . $version;
	elseif (strtolower($title) == 'multi-browser') return 'Multi-Browser XP ' . $version;
	elseif (strtolower($title) == 'nf-browser') return 'NetFront ' . $version;
	elseif (strtolower($title) == 'semc-browser') return 'SEMC Browser ' . $version;
	elseif (strtolower($title) == 'ucweb') return 'UC Browser ' . $version;
	elseif (strtolower($title) == 'up.browser' || strtolower($title) == 'up.link') return 'Openwave Mobile Browser ' . $version;
	elseif (strtolower($title) == 'chromeframe') return 'Google Chrome Frame ' . $version;
	elseif (strtolower($title) == 'mozilladeveloperpreview') return 'Mozilla Developer Preview ' . $version;
	elseif (strtolower($title) == 'multi-browser') return 'Multi-Browser XP ' . $version;
	elseif (strtolower($title) == 'opera mobi') return 'Opera Mobile ' . $version;
	elseif (strtolower($title) == 'tablet browser') return 'MicroB ' . $version;
	elseif (strtolower($title) == 'tencenttraveler') return 'TT Explorer ' . $version;
	else return $title . ' ' . $version;
}
function get_webbrowser() {
	if (preg_match('/360se/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = '360 Explorer &#91;&#22909;&#22403;&#22334;&#65281;&#45;&#95;&#45;&#33;&#93; ';
		$code = '360se';
	} elseif (preg_match('/Browzar/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Browzar');
		$code = 'browzar';
	} elseif (preg_match('/Bunjalloo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Bunjalloo');
		$code = 'bunjalloo';
	} elseif (preg_match('/Camino/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Camino');
		$code = 'camino';
	} elseif (preg_match('/Cayman\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Cayman ' . get_browser_version('Browser');
		$code = 'caymanbrowser';
	} elseif (preg_match('/Abolimba/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Abolimba';
		$code = 'abolimba';
	} elseif (preg_match('/ABrowser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('ABrowser');
		$code = 'abrowser';
	} elseif (preg_match('/Acoo\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Acoo ' . get_browser_version('Browser');
		$code = 'acoobrowser';
	} elseif (preg_match('/Amaya/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Amaya');
		$code = 'amaya';
	} elseif (preg_match('/Amiga-AWeb/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Amiga ' . get_browser_version('AWeb');
		$code = 'amiga-aweb';
	} elseif (preg_match('/America\ Online\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'America Online ' . get_browser_version('Browser');
		$code = 'aol';
	} elseif (preg_match('/AmigaVoyager/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Amiga ' . get_browser_version('Voyager');
		$code = 'amigavoyager';
	} elseif (preg_match('/AOL/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('AOL');
		$code = 'aol';
	} elseif (preg_match('/Arora/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Arora');
		$code = 'arora';
	} elseif (preg_match('/Avant\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Avant ' . get_browser_version('Browser');
		$code = 'avantbrowser';
	} elseif (preg_match('/Beonex/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Beonex');
		$code = 'beonex';
	} elseif (preg_match('/BlackBerry/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('BlackBerry');
		$code = 'blackberry';
	} elseif (preg_match('/Blackbird/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Blackbird');
		$code = 'blackbird';
	} elseif (preg_match('/Blazer/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Blazer');
		$code = 'blazer';
	} elseif (preg_match('/Bolt/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Bolt');
		$code = 'bolt';
	} elseif (preg_match('/BonEcho/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('BonEcho');
		$code = 'firefoxdevpre';
	} elseif (preg_match('/BrowseX/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'BrowseX';
		$code = 'browsex';
	} elseif (preg_match('/Cheshire/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Cheshire');
		$code = 'aol';
	} elseif (preg_match('/Chimera/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Chimera');
		$code = 'null';
	} elseif (preg_match('/Maxthon/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Maxthon');
		preg_match('/Maxthon[\ |\/]?([0-9]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch);
		switch($regmatch[1]){
			case 4:		$code = 'maxthon-4';	break;
			case 3:		$code = 'maxthon-3';	break;
			case 2:		$code = 'maxthon-2';	break;
			default:	$code = 'maxthon';		break;
		}
	} elseif (preg_match('/chromeframe/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('chromeframe');
		$code = 'google';
	} elseif (preg_match('/ChromePlus/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('ChromePlus');
		$code = 'chromeplus';
	} elseif (preg_match('/Chrome/i', $_SERVER['HTTP_USER_AGENT']) && preg_match('/Iron/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'SRWare ' . get_browser_version('Iron');
		$code = 'srwareiron';
	} elseif (preg_match('/Chromium/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Chromium');
		$code = 'chromium';
	} elseif (preg_match('/CometBird/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('CometBird');
		$code = 'cometbird';
	} elseif (preg_match('/Comodo_Dragon/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Comodo ' . get_browser_version('Dragon');
		$code = 'comodo-dragon';
	} elseif (preg_match('/Conkeror/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Conkeror');
		$code = 'conkeror';
	} elseif (preg_match('/Crazy\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Crazy ' . get_browser_version('Browser');
		$code = 'crazybrowser';
	} elseif (preg_match('/Cruz/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Cruz');
		$code = 'cruz';
	} elseif (preg_match('/Cyberdog/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Cyberdog');
		$code = 'cyberdog';
	} elseif (preg_match('/Deepnet\ Explorer/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Deepnet Explorer');
		$code = 'deepnetexplorer';
	} elseif (preg_match('/Demeter/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Demeter');
		$code = 'demeter';
	} elseif (preg_match('/DeskBrowse/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('DeskBrowse');
		$code = 'deskbrowse';
	} elseif (preg_match('/Dillo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Dillo');
		$code = 'dillo';
	} elseif (preg_match('/DoCoMo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('DoCoMo');
		$code = 'null';
	} elseif (preg_match('/DocZilla/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('DocZilla');
		$code = 'doczilla';
	} elseif (preg_match('/Dolfin/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Dolfin');
		$code = 'samsung';
	} elseif (preg_match('/Dooble/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Dooble');
		$code = 'dooble';
	} elseif (preg_match('/Doris/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Doris');
		$code = 'doris';
	} elseif (preg_match('/Edbrowse/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Edbrowse');
		$code = 'edbrowse';
	} elseif (preg_match('/Elinks/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Elinks');
		$code = 'elinks';
	} elseif (preg_match('/Element\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Element ' . get_browser_version('Browser');
		$code = 'elementbrowser';
	} elseif (preg_match('/Enigma\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Enigma ' . get_browser_version('Browser');
		$code = 'enigmabrowser';
	} elseif (preg_match('/Epic/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Epic');
		$code = 'epicbrowser';
	} elseif (preg_match('/Epiphany/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Epiphany');
		$code = 'epiphany';
	} elseif (preg_match('/Escape/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Espial TV Browser - ' . get_browser_version('Escape');
		$code = 'espialtvbrowser';
	} elseif (preg_match('/Fennec/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Fennec');
		$code = 'fennec';
	} elseif (preg_match('/Firebird/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Firebird');
		$code = 'firebird';
	} elseif (preg_match('/Flock/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Flock');
		$code = 'flock';
	} elseif (preg_match('/Fluid/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Fluid');
		$code = 'fluid';
	} elseif (preg_match('/Galaxy/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Galaxy');
		$code = 'galaxy';
	} elseif (preg_match('/Galeon/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Galeon');
		$code = 'galeon';
	} elseif (preg_match('/GlobalMojo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('GlobalMojo');
		$code = 'globalmojo';
	} elseif (preg_match('/GoBrowser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'GO ' . get_browser_version('Browser');
		$code = 'gobrowser';
	} elseif (preg_match('/Google\ Wireless\ Transcoder/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Google Wireless Transcoder';
		$code = 'google';
	} elseif (preg_match('/GoSurf/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('GoSurf');
		$code = 'gosurf';
	} elseif (preg_match('/GranParadiso/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('GranParadiso');
		$code = 'firefoxdevpre';
	} elseif (preg_match('/GreenBrowser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('GreenBrowser');
		$code = 'greenbrowser';
	} elseif (preg_match('/Hana/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Hana');
		$code = 'hana';
	} elseif (preg_match('/HotJava/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('HotJava');
		$code = 'hotjava';
	} elseif (preg_match('/Hv3/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Hv3');
		$code = 'hv3';
	} elseif (preg_match('/Hydra\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Hydra Browser';
		$code = 'hydrabrowser';
	} elseif (preg_match('/Iris/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Iris');
		$code = 'iris';
	} elseif (preg_match('/IBM\ WebExplorer/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'IBM ' . get_browser_version('WebExplorer');
		$code = 'ibmwebexplorer';
	} elseif (preg_match('/IBrowse/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('IBrowse');
		$code = 'ibrowse';
	} elseif (preg_match('/iCab/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('iCab');
		$code = 'icab';
	} elseif (preg_match('/Ice Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Ice Browser');
		$code = 'icebrowser';
	} elseif (preg_match('/Iceape/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Iceape');
		$code = 'iceape';
	} elseif (preg_match('/IceCat/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'GNU ' . get_browser_version('IceCat');
		$code = 'icecat';
	} elseif (preg_match('/IceWeasel/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('IceWeasel');
		$code = 'iceweasel';
	} elseif (preg_match('/IEMobile/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('IEMobile');
		$code = 'msie-mobile';
	} elseif (preg_match('/iNet\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'iNet ' . get_browser_version('Browser');
		$code = 'null';
	} elseif (preg_match('/iRider/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('iRider');
		$code = 'irider';
	} elseif (preg_match('/Iron/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Iron');
		$code = 'iron';
	} elseif (preg_match('/InternetSurfboard/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('InternetSurfboard');
		$code = 'internetsurfboard';
	} elseif (preg_match('/Jasmine/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Jasmine');
		$code = 'samsung';
	} elseif (preg_match('/K-Meleon/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('K-Meleon');
		$code = 'kmeleon';
	} elseif (preg_match('/K-Ninja/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('K-Ninja');
		$code = 'kninja';
	} elseif (preg_match('/Kapiko/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Kapiko');
		$code = 'kapiko';
	} elseif (preg_match('/Kazehakase/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Kazehakase');
		$code = 'kazehakase';
	} elseif (preg_match('/Strata/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Kirix ' . get_browser_version('Strata');
		$code = 'kirix-strata';
	} elseif (preg_match('/KKman/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('KKman');
		$code = 'kkman';
	} elseif (preg_match('/KMail/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('KMail');
		$code = 'kmail';
	} elseif (preg_match('/KMLite/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('KMLite');
		$code = 'kmeleon';
	} elseif (preg_match('/Konqueror/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Konqueror');
		$code = 'konqueror';
	} elseif (preg_match('/LBrowser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('LBrowser');
		$code = 'lbrowser';
	} elseif (preg_match('/LeechCraft/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'LeechCraft';
		$code = 'null';
	} elseif (preg_match('/Links/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Links');
		$code = 'links';
	} elseif (preg_match('/Lobo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Lobo');
		$code = 'lobo';
	} elseif (preg_match('/lolifox/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('lolifox');
		$code = 'lolifox';
	} elseif (preg_match('/Lorentz/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Lorentz');
		$code = 'firefoxdevpre';
	} elseif (preg_match('/Lunascape/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Lunascape');
		$code = 'lunascape';
	} elseif (preg_match('/Lynx/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Lynx');
		$code = 'lynx';
	} elseif (preg_match('/Madfox/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Madfox');
		$code = 'madfox';
	} elseif (preg_match('/Maemo\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Maemo Browser');
		$code = 'maemo';
	} elseif (preg_match('/\ MIB\//i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('MIB');
		$code = 'mib';
	} elseif (preg_match('/Tablet\ browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Tablet browser');
		$code = 'microb';
	} elseif (preg_match('/Midori/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Midori');
		$code = 'midori';
	} elseif (preg_match('/Minefield/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Minefield');
		$code = 'minefield';
	} elseif (preg_match('/Minimo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Minimo');
		$code = 'minimo';
	} elseif (preg_match('/Mosaic/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Mosaic');
		$code = 'mosaic';
	} elseif (preg_match('/MozillaDeveloperPreview/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('MozillaDeveloperPreview');
		$code = 'firefoxdevpre';
	} elseif (preg_match('/Multi-Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Multi-Browser');
		$code = 'multi-browserxp';
	} elseif (preg_match('/MultiZilla/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('MultiZilla');
		$code = 'mozilla';
	} elseif (preg_match('/myibrow/i', $_SERVER['HTTP_USER_AGENT']) && preg_match('/My\ Internet\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('myibrow');
		$code = 'my-internet-browser';
	} elseif (preg_match('/MyIE2/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('MyIE2');
		$code = 'myie2';
	} elseif (preg_match('/Namoroka/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Namoroka');
		$code = 'firefoxdevpre';
	} elseif (preg_match('/Navigator/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Netscape ' . get_browser_version('Navigator');
		$code = 'netscape';
	} elseif (preg_match('/NetBox/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('NetBox');
		$code = 'netbox';
	} elseif (preg_match('/NetCaptor/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('NetCaptor');
		$code = 'netcaptor';
	} elseif (preg_match('/NetFront/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('NetFront');
		$code = 'netfront';
	} elseif (preg_match('/NetNewsWire/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('NetNewsWire');
		$code = 'netnewswire';
	} elseif (preg_match('/NetPositive/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('NetPositive');
		$code = 'netpositive';
	} elseif (preg_match('/Netscape/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Netscape');
		$code = 'netscape';
	} elseif (preg_match('/NetSurf/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('NetSurf');
		$code = 'netsurf';
	} elseif (preg_match('/NF-Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('NF-Browser');
		$code = 'netfront';
	} elseif (preg_match('/Novarra-Vision/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Novarra ' . get_browser_version('Vision');
		$code = 'novarra';
	} elseif (preg_match('/Obigo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Obigo');
		$code = 'obigo';
	} elseif (preg_match('/OffByOne/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Off By One';
		$code = 'offbyone';
	} elseif (preg_match('/OmniWeb/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('OmniWeb');
		$code = 'omniweb';
	} elseif (preg_match('/Opera Mini/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Opera Mini');
		$code = 'opera-2';
	} elseif (preg_match('/Opera Mobi/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Opera Mobi');
		$code = 'opera-2';
	} elseif (preg_match('/Opera/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Opera');
		$code = 'opera-1';
		if (preg_match('/Version/i', $_SERVER['HTTP_USER_AGENT'])) $code = 'opera-2';
	} elseif (preg_match('/Orca/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Orca');
		$code = 'orca';
	} elseif (preg_match('/Oregano/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Oregano');
		$code = 'oregano';
	} elseif (preg_match('/Origyn\ Web\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Oregano Web Browser';
		$code = 'owb';
	} elseif (preg_match('/\ Pre\//i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Palm ' . get_browser_version('Pre');
		$code = 'palmpre';
	} elseif (preg_match('/Palemoon/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Pale ' . get_browser_version('Moon');
		$code = 'palemoon';
	} elseif (preg_match('/Phaseout/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Phaseout';
		$code = 'phaseout';
	} elseif (preg_match('/Phoenix/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Phoenix');
		$code = 'phoenix';
	} elseif (preg_match('/Pogo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Pogo');
		$code = 'pogo';
	} elseif (preg_match('/Polaris/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Polaris');
		$code = 'polaris';
	} elseif (preg_match('/Prism/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Prism');
		$code = 'prism';
	} elseif (preg_match('/QtWeb\ Internet\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'QtWeb Internet ' . get_browser_version('Browser');
		$code = 'qtwebinternetbrowser';
	} elseif (preg_match('/rekonq/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'rekonq';
		$code = 'rekonq';
	} elseif (preg_match('/retawq/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('retawq');
		$code = 'terminal';
	} elseif (preg_match('/RockMelt/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('RockMelt');
		$code = 'rockmelt';
	} elseif (preg_match('/SaaYaa/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'SaaYaa Explorer';
		$code = 'saayaa';
	} elseif (preg_match('/SeaMonkey/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('SeaMonkey');
		$code = 'seamonkey';
	} elseif (preg_match('/SEMC-Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('SEMC-Browser');
		$code = 'semcbrowser';
	} elseif (preg_match('/SEMC-java/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('SEMC-java');
		$code = 'semcbrowser';
	} elseif (preg_match('/Series60/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/Symbian/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Nokia ' . get_browser_version('Series60');
		$code = 's60';
	} elseif (preg_match('/S60/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/Symbian/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Nokia ' . get_browser_version('S60');
		$code = 's60';
	} elseif (preg_match('/SE\ /i', $_SERVER['HTTP_USER_AGENT']) && preg_match('/MetaSr/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = '&#25628;&#29399;&#27983;&#35272;&#22120;';
		$code = 'sogou';
	} elseif (preg_match('/Shiira/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Shiira');
		$code = 'shiira';
	} elseif (preg_match('/Shiretoko/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Shiretoko');
		$code = 'firefoxdevpre';
	} elseif (preg_match('/SiteKiosk/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('SiteKiosk');
		$code = 'sitekiosk';
	} elseif (preg_match('/SkipStone/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('SkipStone');
		$code = 'skipstone';
	} elseif (preg_match('/Skyfire/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Skyfire');
		$code = 'skyfire';
	} elseif (preg_match('/Sleipnir/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Sleipnir');
		$code = 'sleipnir';
	} elseif (preg_match('/SlimBrowser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('SlimBrowser');
		$code = 'slimbrowser';
	} elseif (preg_match('/Songbird/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Songbird');
		$code = 'songbird';
	} elseif (preg_match('/Stainless/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Stainless');
		$code = 'stainless';
	} elseif (preg_match('/Sulfur/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Flock ' . get_browser_version('Sulfur');
		$code = 'flock';
	} elseif (preg_match('/Sunrise/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Sunrise');
		$code = 'sunrise';
	} elseif (preg_match('/Surf/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Surf');
		$code = 'surf';
	} elseif (preg_match('/Swiftfox/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Swiftfox');
		$code = 'swiftfox';
	} elseif (preg_match('/Swiftweasel/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Swiftweasel');
		$code = 'swiftweasel';
	} elseif (preg_match('/tear/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Tear';
		$code = 'tear';
	} elseif (preg_match('/TeaShark/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('TeaShark');
		$code = 'teashark';
	} elseif (preg_match('/Teleca/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version(' Teleca');
		$code = 'obigo';
	} elseif (preg_match('/TheWorld/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'TheWorld Browser';
		$code = 'theworld';
	} elseif (preg_match('/Thunderbird/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Thunderbird');
		$code = 'thunderbird';
	} elseif (preg_match('/Tjusig/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Tjusig');
		$code = 'tjusig';
	} elseif (preg_match('/TencentTraveler/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('TencentTraveler');
		$code = 'tt-explorer';
	} elseif (preg_match('/uBrowser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('uBrowser');
		$code = 'ubrowser';
	} elseif (preg_match('/UC\ Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('UC Browser');
		$code = 'ucbrowser';
	} elseif (preg_match('/UCWEB/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('UCWEB');
		$code = 'ucweb';
	} elseif (preg_match('/UltraBrowser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('UltraBrowser');
		$code = 'ultrabrowser';
	} elseif (preg_match('/UP.Browser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('UP.Browser');
		$code = 'openwave';
	} elseif (preg_match('/UP.Link/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('UP.Link');
		$code = 'openwave';
	} elseif (preg_match('/uZardWeb/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('uZardWeb');
		$code = 'uzardweb';
	} elseif (preg_match('/uZard/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('uZard');
		$code = 'uzardweb';
	} elseif (preg_match('/uzbl/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'uzbl';
		$code = 'uzbl';
	} elseif (preg_match('/Vonkeror/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Vonkeror');
		$code = 'null';
	} elseif (preg_match('/w3m/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('W3M');
		$code = 'w3m';
	} elseif (preg_match('/WeltweitimnetzBrowser/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Weltweitimnetz ' . get_browser_version('Browser');
		$code = 'weltweitimnetzbrowser';
	} elseif (preg_match('/wKiosk/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'wKiosk';
		$code = 'wkiosk';
	} elseif (preg_match('/WorldWideWeb/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('WorldWideWeb');
		$code = 'worldwideweb';
	} elseif (preg_match('/Wyzo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Wyzo');
		$code = 'Wyzo';
	} elseif (preg_match('/X-Smiles/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('X-Smiles');
		$code = 'x-smiles';
	} elseif (preg_match('/Xiino/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Xiino');
		$code = 'null';
	} elseif (preg_match('/Chrome/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Google ' . get_browser_version('Chrome');
		$code = 'chrome';
	} elseif (preg_match('/Safari/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/Nokia/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Safari';
		if (preg_match('/Version/i', $_SERVER['HTTP_USER_AGENT'])) $title = get_browser_version('Safari');
		if (preg_match('/Mobile Safari/i', $_SERVER['HTTP_USER_AGENT'])) $title = 'Mobile ' . $title;
		$code = 'safari';
	} elseif (preg_match('/Nokia/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Nokia Web Browser';
		$code = 'maemo';
	} elseif (preg_match('/Firefox/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = get_browser_version('Firefox');
		$code = 'firefox';
	} elseif (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Internet Explorer' . get_browser_version('MSIE');
		preg_match('/MSIE[\ |\/]?([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch);
		if ($regmatch[1] >= 10) {
			$code = 'msie10';
		} elseif ($regmatch[1] >= 9) {
			$code = 'msie9';
		} elseif ($regmatch[1] >= 7) {
			$code = 'msie7';
		} elseif ($regmatch[1] >= 6) {
			$code = 'msie6';
		} elseif ($regmatch[1] >= 4) {
			$code = 'msie4';
		} elseif ($regmatch[1] >= 3) {
			$code = 'msie3';
		} elseif ($regmatch[1] >= 2) {
			$code = 'msie2';
		} elseif ($regmatch[1] >= 1) {
			$code = 'msie1';
		} else {
			$code = 'msie';
		}
	} elseif (preg_match('/Mozilla/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Mozilla Compatible';
		if (preg_match('/rv:([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title = 'Mozilla ' . $regmatch[1];
		$code = 'mozilla';
	} else {
		$title = 'Unknown';
		$code = 'null';
	}
	return 'net'.'|'.$title.'|'.$code;
}
function detect_device() {
	if (preg_match('/iPad/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'iPad';
		if (preg_match('/CPU\ OS\ ([._0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' iOS ' . str_replace('_', '.', $regmatch[1]);
		$code = 'ipad';
	} elseif (preg_match('/iPod/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'iPod';
		if (preg_match('/iPhone\ OS\ ([._0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' iOS ' . str_replace('_', '.', $regmatch[1]);
		$code = 'iphone';
	} elseif (preg_match('/iPhone/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'iPhone';
		if (preg_match('/iPhone\ OS\ ([._0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' iOS ' . str_replace('_', '.', $regmatch[1]);
		$code = 'iphone';
	} elseif (preg_match('/[^M]SIE/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'BenQ-Siemens';
		if (preg_match('/[^M]SIE-([.0-9a-zA-Z]+)\//i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'benq-siemens';
	} elseif (preg_match('/BlackBerry/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'BlackBerry';
		if (preg_match('/blackberry([.0-9a-zA-Z]+)\//i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'blackberry';
	} elseif (preg_match('/Dell Mini 5/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Dell Mini 5';
		$code = 'dell';
	} elseif (preg_match('/Dell Streak/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Dell Streak';
		$code = 'dell';
	} elseif (preg_match('/Dell/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Dell';
		$code = 'dell';
	} elseif (preg_match('/Nexus One/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Nexus One';
		$code = 'google-nexusone';
	} elseif (preg_match('/Desire/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'HTC Desire';
		$code = 'htc';
	} elseif (preg_match('/Rhodium/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/HTC[_|\ ]Touch[_|\ ]Pro2/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/WMD-50433/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'HTC Touch Pro2';
		$code = 'htc';
	} elseif (preg_match('/HTC[_|\ ]Touch[_|\ ]Pro/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'HTC Touch Pro';
		$code = 'htc';
	} elseif (preg_match('/HTC/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'HTC';
		if (preg_match('/HTC[\ |_|-]8500/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' Startrek';
		} elseif (preg_match('/HTC[\ |_|-]Hero/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' Hero';
		} elseif (preg_match('/HTC[\ |_|-]Legend/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' Legend';
		} elseif (preg_match('/HTC[\ |_|-]Magic/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' Magic';
		} elseif (preg_match('/HTC[\ |_|-]P3450/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' Touch';
		} elseif (preg_match('/HTC[\ |_|-]P3650/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' Polaris';
		} elseif (preg_match('/HTC[\ |_|-]S710/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' S710';
		} elseif (preg_match('/HTC[\ |_|-]Tattoo/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' Tattoo';
		} elseif (preg_match('/HTC[\ |_|-]?([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) {
			$title .= ' ' . $regmatch[1];
		} elseif (preg_match('/HTC([._0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) {
			$title .= str_replace('_', ' ', $regmatch[1]);
		}
		$code = 'htc';
	} elseif (preg_match('/LG/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'LG';
		if (preg_match('/LG[E]?[\ |-|\/]([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'lg';
	} elseif (preg_match('/\ Droid/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title .= 'Motorola Droid';
		$code = 'motorola';
	} elseif (preg_match('/XT720/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title .= 'Motorola Motoroi (XT720)';
		$code = 'motorola';
	} elseif (preg_match('/MOT-/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/MIB/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Motorola';
		if (preg_match('/MOTO([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		if (preg_match('/MOT-([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'motorola';
	} elseif (preg_match('/Nintendo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Nintendo';
		if (preg_match('/Nintendo DSi/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' DSi';
			$code = 'nintendodsi';
		} elseif (preg_match('/Nintendo DS/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' DS';
			$code = 'nintendods';
		} elseif (preg_match('/Nintendo Wii/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' Wii';
			$code = 'nintendowii';
		} else {
			$code = 'nintendo';
		}
	} elseif (preg_match('/Nokia/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/S(eries)?60/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Nokia';
		if (preg_match('/Nokia(E|N)?([0-9]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1] . $regmatch[2];
		$code = 'nokia';
	} elseif (preg_match('/S(eries)?60/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Nokia Series60';
		$code = 'nokia';
	} elseif (preg_match('/OLPC/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'OLPC (XO)';
		$code = 'olpc';
	} elseif (preg_match('/\ Pixi\//i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Palm Pixi';
		$code = 'palm';
	} elseif (preg_match('/\ Pre\//i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Palm Pre';
		$code = 'palm';
	} elseif (preg_match('/Palm/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Palm';
		$code = 'palm';
	} elseif (preg_match('/Playstation/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Playstation';
		if (preg_match('/[PS|Playstation\ ]3/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' 3';
		} elseif (preg_match('/[Playstation Portable|PSP]/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title .= ' Portable';
		}
		$code = 'playstation';
	} elseif (preg_match('/Samsung/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Samsung';
		if (preg_match('/Samsung-([.\-0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'samsung';
	} elseif (preg_match('/SonyEricsson/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'SonyEricsson';
		if (preg_match('/SonyEricsson([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) {
			if (strtolower($regmatch[1]) == strtolower('U20i')) $title .= ' Xperia X10 Mini Pro';
			else $title .= ' ' . $regmatch[1];
		}
		$code = 'sonyericsson';
	} else {
		return '';
	}
	return 'os'.'|'.$title.'|'.$code;
	return $detected_device;
}
function detect_os() {
	if (preg_match('/AmigaOS/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'AmigaOS';
		if (preg_match('/AmigaOS\ ([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'amigaos';
	} elseif (preg_match('/Android/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Android';
		$code = 'android';
	} elseif (preg_match('/Arch/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Arch Linux';
		$code = 'archlinux';
	} elseif (preg_match('/BeOS/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'BeOS';
		$code = 'beos';
	} elseif (preg_match('/CentOS/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'CentOS';
		if (preg_match('/.el([.0-9a-zA-Z]+).centos/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'centos';
	} elseif (preg_match('/CrOS/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Google Chrome OS';
		$code = 'chromeos';
	} elseif (preg_match('/Debian/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Debian GNU/Linux';
		$code = 'debian';
	} elseif (preg_match('/DragonFly/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'DragonFly BSD';
		$code = 'dragonflybsd';
	} elseif (preg_match('/Edubuntu/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Edubuntu';
		if (preg_match('/Edubuntu[\/|\ ]([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $version .= ' ' . $regmatch[1];
		if ($regmatch[1] < 10) $code = 'edubuntu-1';
		else $code = 'edubuntu-2';
		if (strlen($version) > 1) $title .= $version;
	} elseif (preg_match('/Fedora/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Fedora';
		if (preg_match('/.fc([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'fedora';
	} elseif (preg_match('/Foresight\ Linux/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Foresight Linux';
		if (preg_match('/Foresight\ Linux\/([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'foresight';
	} elseif (preg_match('/FreeBSD/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'FreeBSD';
		$code = 'freebsd';
	} elseif (preg_match('/Gentoo/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Gentoo';
		$code = 'gentoo';
	} elseif (preg_match('/IRIX/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'IRIX Linux';
		if (preg_match('/IRIX(64)?\ ([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) {
			if ($regmatch[1]) $title .= ' x' . $regmatch[1];
			if ($regmatch[2]) $title .= ' ' . $regmatch[2];
		}
		$code = 'irix';
	} elseif (preg_match('/Kanotix/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Kanotix';
		$code = 'kanotix';
	} elseif (preg_match('/Knoppix/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Knoppix';
		$code = 'knoppix';
	} elseif (preg_match('/Kubuntu/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Kubuntu';
		if (preg_match('/Kubuntu[\/|\ ]([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $version .= ' ' . $regmatch[1];
		if ($regmatch[1] < 10) $code = 'kubuntu-1';
		else $code = 'kubuntu-2';
		if (strlen($version) > 1) $title .= $version;
	} elseif (preg_match('/LindowsOS/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'LindowsOS';
		$code = 'lindowsos';
	} elseif (preg_match('/Linspire/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Linspire';
		$code = 'lindowsos';
	} elseif (preg_match('/Linux\ Mint/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Linux Mint';
		if (preg_match('/Linux\ Mint\/([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'linuxmint';
	} elseif (preg_match('/Lubuntu/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Lubuntu';
		if (preg_match('/Lubuntu[\/|\ ]([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $version .= ' ' . $regmatch[1];
		if ($regmatch[1] < 10) $code = 'lubuntu-1';
		else $code = 'lubuntu-2';
		if (strlen($version) > 1) $title .= $version;
	} elseif (preg_match('/Mac/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Darwin/i', $_SERVER['HTTP_USER_AGENT'])) {
		if (preg_match('/Mac OS X/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = substr($_SERVER['HTTP_USER_AGENT'], strpos(strtolower($_SERVER['HTTP_USER_AGENT']), strtolower('Mac OS X')));
			$title = substr($title, 0, strpos($title, ';'));
			$title = str_replace('_', '.', $title);
			$code = 'mac-3';
		} elseif (preg_match('/Mac OSX/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = substr($_SERVER['HTTP_USER_AGENT'], strpos(strtolower($_SERVER['HTTP_USER_AGENT']), strtolower('Mac OSX')));
			$title = substr($title, 0, strpos($title, ';'));
			$title = str_replace('_', '.', $title);
			$code = 'mac-2';
		} elseif (preg_match('/Darwin/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Mac OS Darwin';
			$code = 'mac-1';
		} else {
			$title = 'Macintosh';
			$code = 'mac-1';
		}
	} elseif (preg_match('/Mandriva/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Mandriva';
		if (preg_match('/mdv([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'mandriva';
	} elseif (preg_match('/MorphOS/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'MorphOS';
		$code = 'morphos';
	} elseif (preg_match('/NetBSD/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'NetBSD';
		$code = 'netbsd';
	} elseif (preg_match('/OpenBSD/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'OpenBSD';
		$code = 'openbsd';
	} elseif (preg_match('/Oracle/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Oracle';
		if (preg_match('/.el([._0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' Enterprise Linux ' . str_replace('_', '.', $regmatch[1]);
		else $title .= ' Linux';
		$code = 'oracle';
	} elseif (preg_match('/PCLinuxOS/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'PCLinuxOS';
		if (preg_match('/PCLinuxOS\/[.\-0-9a-zA-Z]+pclos([.\-0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . str_replace('_', '.', $regmatch[1]);
		$code = 'pclinuxos';
	} elseif (preg_match('/Red\ Hat/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/RedHat/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Red Hat';
		if (preg_match('/.el([._0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' Enterprise Linux ' . str_replace('_', '.', $regmatch[1]);
		$code = 'mandriva';
	} elseif (preg_match('/Sabayon/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Sabayon Linux';
		$code = 'sabayon';
	} elseif (preg_match('/Slackware/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Slackware';
		$code = 'slackware';
	} elseif (preg_match('/Solaris/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Solaris';
		$code = 'solaris';
	} elseif (preg_match('/SunOS/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Solaris';
		$code = 'solaris';
	} elseif (preg_match('/Suse/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'SuSE';
		$code = 'suse';
	} elseif (preg_match('/Symb[ian]?[OS]?/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'SymbianOS';
		if (preg_match('/Symb[ian]?[OS]?\/([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
		$code = 'symbianos';
	} elseif (preg_match('/Ubuntu/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Ubuntu';
		if (preg_match('/Ubuntu[\/|\ ]([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $version .= ' ' . $regmatch[1];
		if ($regmatch[1] < 10) $code = 'ubuntu-1';
		else $code = 'ubuntu-2';
		if (strlen($version) > 1) $title .= $version;
	} elseif (preg_match('/Unix/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Unix';
		$code = 'unix';
	} elseif (preg_match('/VectorLinux/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'VectorLinux';
		$code = 'vectorlinux';
	} elseif (preg_match('/Venenux/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Venenux GNU Linux';
		$code = 'venenux';
	} elseif (preg_match('/webOS/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Palm webOS';
		$code = 'palm';
	} elseif (preg_match('/Windows/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/WinNT/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Win32/i', $_SERVER['HTTP_USER_AGENT'])) {
		if (preg_match('/Windows NT 6.3/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows 8.1';
			$code = 'win-5';
		} elseif (preg_match('/Windows NT 6.2/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows 8';
			$code = 'win-5';
		} elseif (preg_match('/Windows NT 6.1; Win64; x64;/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Windows NT 6.1; WOW64;/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows 7 x64 Edition';
			$code = 'win-4';
		} elseif (preg_match('/Windows NT 6.1/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows 7';
			$code = 'win-4';
		} elseif (preg_match('/Windows NT 6.0/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows Vista';
			$code = 'win-3';
		} elseif (preg_match('/Windows NT 5.2 x64/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows XP x64 Edition';
			$code = 'win-2';
		} elseif (preg_match('/Windows NT 5.2/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows Server 2003';
			$code = 'win-2';
		} elseif (preg_match('/Windows NT 5.1/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Windows XP/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows XP';
			$code = 'win-2';
		} elseif (preg_match('/Windows NT 5.01/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows 2000, Service Pack 1 (SP1)';
			$code = 'win-1';
		} elseif (preg_match('/Windows NT 5.0/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Windows 2000/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows 2000';
			$code = 'win-1';
		} elseif (preg_match('/Windows NT 4.0/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/WinNT4.0/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Microsoft Windows NT 4.0';
			$code = 'win-1';
		} elseif (preg_match('/Windows NT 3.51/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/WinNT3.51/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Microsoft Windows NT 3.11';
			$code = 'win-1';
		} elseif (preg_match('/Windows 3.11/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Win3.11/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Win16/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Microsoft Windows 3.11';
			$code = 'win-1';
		} elseif (preg_match('/Windows 3.1/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Microsoft Windows 3.1';
			$code = 'win-1';
		} elseif (preg_match('/Windows 98; Win 9x 4.90/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Win 9x 4.90/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Windows ME/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows Millennium Edition (Windows Me)';
			$code = 'win-1';
		} elseif (preg_match('/Win98/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows 98 SE';
			$code = 'win-1';
		} elseif (preg_match('/Windows 98/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Windows\ 4.10/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows 98';
			$code = 'win-1';
		} elseif (preg_match('/Windows 95/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Win95/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows 95';
			$code = 'win-1';
		} elseif (preg_match('/Windows CE/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows CE';
			$code = 'win-2';
		} elseif (preg_match('/WM5/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows Mobile 5';
			$code = 'win-phone';
		} elseif (preg_match('/WindowsMobile/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows Mobile';
			$code = 'win-phone';
		} else {
			$title = 'Windows';
			$code = 'win-2';
		}
	} elseif (preg_match('/Xandros/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Xandros';
		$code = 'xandros';
	} elseif (preg_match('/Xubuntu/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Xubuntu';
		if (preg_match('/Xubuntu[\/|\ ]([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $version .= ' ' . $regmatch[1];
		if ($regmatch[1] < 10) $code = 'xubuntu-1';
		else $code = 'xubuntu-2';
		if (strlen($version) > 1) $title .= $version;
	} elseif (preg_match('/Zenwalk/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'Zenwalk GNU Linux';
		$code = 'zenwalk';
	} elseif (preg_match('/Linux/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'GNU/Linux';
		$code = 'linux';
		if (preg_match('/x86_64/i', $_SERVER['HTTP_USER_AGENT'])) $title .= ' x64';
	} elseif (preg_match('/J2ME\/MIDP/i', $_SERVER['HTTP_USER_AGENT'])) {
		$title = 'J2ME/MIDP Device';
		$code = 'java';
	} else {
		return '';
	}
	return 'os'.'|'.$title.'|'.$code;
}
function get_platform() {
	if (strlen($detected_platform = detect_device()) > 0) {
		return $detected_platform;
	} elseif (strlen($detected_platform = detect_os()) > 0) {
		return $detected_platform;
	} else {
		$title = 'Unknown';
		$code = 'null';
	}
	return 'os'.'|'.$title.'|'.$code;
}
function img($code, $type, $title) {
	$src = $type . $code;
	$img = "<img src=\"source/plugin/th_chat/images{$src}.png\" id=\"nzosicon\" alt=\"{$title}\" title=\"{$title}\">";
	return $img;
}
?>