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
		if(preg_match('/Android (\d+(?:\.\d+){1,2})]/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
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
			$code = 'win-mobile';
		} elseif (preg_match('/WindowsMobile/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows Mobile';
			$code = 'win-mobile';
		} elseif (preg_match('/Windows Phone OS/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows Phone';
			if (preg_match('/Windows Phone OS [\/|\ ]([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
			$code = 'win-p1';
		} elseif (preg_match('/Windows Phone/i', $_SERVER['HTTP_USER_AGENT'])) {
			$title = 'Windows Phone';
			if (preg_match('/Windows Phone [\/|\ ]([.0-9a-zA-Z]+)/i', $_SERVER['HTTP_USER_AGENT'], $regmatch)) $title .= ' ' . $regmatch[1];
			$code = 'win-5';
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