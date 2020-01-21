<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$gid = $_G['groupid'];
include 'functions.php';
if ($uid < 1) {
    die(json_encode(array('type' => 1, 'error' => 'กรุณาเข้าสู่ระบบ')));
}
$banned = DB::query("SELECT value FROM " . DB::table('common_pluginvar') . " WHERE variable='chat_ban' AND displayorder='9' LIMIT 1");
$banned = DB::fetch($banned);
eval("\$banned = array({$banned['value']});");
if (in_array($gid, array(4, 5)) || in_array($uid, $banned)) {
    die(json_encode(array('type' => 1, 'error' => 'คุณถูกแบน')));
}
if(!isset($_POST['text'])) {
    die();
}
if (get_magic_quotes_gpc()) {
    $text = stripslashes($_POST['text']);
} else {
    $text = $_POST['text'];
}
if($text === '') {
    die();
}
// หลบ Discuz ตรวจอักขระ เช่น (, )
$text = urldecode(urldecode($text));
$f = file_get_contents(DISCUZ_ROOT . '/source/plugin/th_chat/template/discuz.htm');
$id = intval($_POST['lastid']);
$touid = intval($_POST['touid']);
$color = str_replace(array('\'', '\\', '"', '<', '>'), '', $_POST['color']);
$ip = $_SERVER['REMOTE_ADDR'];
$is_mod = in_array($_G['adminid'], array(1, 2, 3));
$a = file_get_contents(DISCUZ_ROOT . '/source/plugin/th_chat/template/big.htm');
if (substr($text, 0, 4) == "/ban" && $is_mod) {
    $uid_ban = intval(substr($text, 4));
    if ($uid_ban && !in_array($uid_ban, $banned)) {
        $banned[] = $uid_ban;
        $username_ban = DB::query("SELECT m.username AS name,n.name AS nick FROM " . DB::table('common_member') . " m LEFT JOIN " . DB::table('2th_chat_nick') . " n ON m.uid=n.uid WHERE m.uid='{$uid_ban}' LIMIT 1");
        $username_ban = DB::fetch($username_ban);
        if ($username_ban['nick']) {
            $username_ban = $username_ban['nick'];
        } else {
            $username_ban = $username_ban['name'];
        }
        $text = '[color=red][url=home.php?mod=space.php&uid=' . $uid_ban . '][b]' . htmlspecialchars_decode($username_ban) . '[/b][/url] ถูกแบน[/color]';
        $banned_new = array();
        foreach ($banned as $uid_banned) {
            if ($uid_banned && !in_array($uid_banned, $banned_new)) {
                $banned_new[] = $uid_banned;
            }
        }
        $banned = implode(',', $banned_new);
        DB::query("UPDATE " . DB::table('common_pluginvar') . " SET value='{$banned}' WHERE variable='chat_ban' AND displayorder='9' LIMIT 1");
    }
} elseif (substr($text, 0, 6) == "/unban" && $is_mod) {
    $uid_ban = intval(substr($text, 6));
    if ($uid_ban && in_array($uid_ban, $banned)) {
        $key = array_search($uid_ban, $banned);
        if ($key !== FALSE) unset($banned[$key]);
        $username_ban = DB::query("SELECT m.username AS name,n.name AS nick FROM " . DB::table('common_member') . " m LEFT JOIN " . DB::table('2th_chat_nick') . " n ON m.uid=n.uid WHERE m.uid='{$uid_ban}' LIMIT 1");
        $username_ban = DB::fetch($username_ban);
        if ($username_ban['nick']) {
            $username_ban = $username_ban['nick'];
        } else {
            $username_ban = $username_ban['name'];
        }
        $text = '[color=red]ปลดแบน [url=home.php?mod=space.php&uid=' . $uid_ban . '][b]' . htmlspecialchars_decode($username_ban) . '[/b][/url][/color]';
        $banned_new = array();
        foreach ($banned as $uid_banned) {
            if ($uid_banned && !in_array($uid_banned, $banned_new)) {
                $banned_new[] = $uid_banned;
            }
        }
        $banned = implode(',', $banned_new);
        DB::query("UPDATE " . DB::table('common_pluginvar') . " SET value='{$banned}' WHERE variable='chat_ban' AND displayorder='9' LIMIT 1");
    }
} elseif (substr($text, 0, 6) == "/point" && $config['chat_point']) {
    $point = explode('|', substr($text, 6));
    $uid_point = intval($point[0]);
    $res = $point[2];
    $point = intval($point[1]);
    if ($uid_point && ($point == 1 || $point == -1) && ($uid_point != $uid) || $uid == 1) {
        $re = DB::query("SELECT uid,point_time FROM " . DB::table('2th_chat_nick') . " WHERE uid='{$uid}'");
        if ($re = DB::fetch($re)) {
            if ($time - $re['point_time'] < 10) {
                die(json_encode(array('type' => 1, 'error' => 'คุณสามารถให้คะแนนได้ 1 ครั้งภายใน 10 วินาที')));
            } else {
                DB::query("UPDATE " . DB::table('2th_chat_nick') . " SET point_time='{$time}' WHERE uid='{$uid}' LIMIT 1");
            }
        } else {
            DB::query("INSERT INTO " . DB::table('2th_chat_nick') . " (uid,point_time) VALUES ('{$uid}','{$time}')");
        }
        if ($point > 0) {
            $point = '+' . $point;
        }
        if ($touid != $uid_point) {
            $touid = 0;
        }
        DB::query("UPDATE " . DB::table('common_member_count') . " SET extcredits{$config['chat_point']}=extcredits{$config['chat_point']}{$point} WHERE uid='{$uid_point}' LIMIT 1");
        $username_point = DB::query("SELECT m.username AS name,n.name AS nick,p.extcredits{$config['chat_point']} AS point FROM " . DB::table('common_member') . " m LEFT JOIN " . DB::table('2th_chat_nick') . " n ON m.uid=n.uid LEFT JOIN " . DB::table('common_member_count') . " p ON m.uid=p.uid WHERE m.uid='{$uid_point}' LIMIT 1");
        $username_point = DB::fetch($username_point);
        $total_point = $username_point['point'];
        if ($username_point['nick']) {
            $username_point = $username_point['nick'];
        } else {
            $username_point = $username_point['name'];
        }
        if ($total_point >= 0) {
            $total_point = '[color=green]' . $total_point . '[/color]';
        } else {
            $total_point = '[color=red]' . $total_point . '[/color]';
        }
        if ($point > 0) {
            $point = '[color=green]' . $point . '[/color]';
        } else {
            $point = '[color=red]' . $point . '[/color]';
        }
        $text = '@[url=home.php?mod=space.php&uid=' . $uid_point . ']' . htmlspecialchars_decode($username_point) . '[/url] ' . $point . ' = ' . $total_point . ' ' . $res;
    }
}
if (strpos($f, '&copy; <a href="https://necz.net/projects/2th_chat" target="_blank">2TH Chat</a>') === false) die();
$txtlen = strlen($text);
if ($txtlen > $config['chat_strlen']) {
    $text = '... ' . substr($text, $txtlen - $config['chat_strlen']);
}
if ($uid == $touid) {
    die();
}
include(DISCUZ_ROOT . '/source/function/function_discuzcode.php');
$config['useemo'] = $config['useemo'] ? 0 : 1;
$config['usedzc'] = $config['usedzc'] ? 0 : 1;
if (strpos($f, '<div style="text-align:right;height:20px;"><span id="n_copyright">&copy; <a href="https://necz.net/projects/2th_chat">2TH Chat</a></span></div>') === false) die();
if ($config['autourl']) {
    $text = preg_replace('#(^|\s)([a-z]+://([^\s\w/]?[\w/])*)#is', '\\1[url]\\2[/url]', $text);
    $text = preg_replace('#(^|\s)((www|ftp)\.([^\s\w/]?[\w/])*)#is', '\\1[url]\\2[/url]', $text);
}
$text = paddslashes(discuzcode($text, $config['useemo'], $config['usedzc'], $config['usehtml'], 1, 1, $config['useimg'], 1));
if (($is_mod > 0) && $text == '/c') {
    $ip = 'clear';
    $touid = 0;
    $text = 'ล้างข้อมูล';
    $needClear = 1;
}
if ($color != 'default') {
    $text = '<span style="color:#' . $color . ';">' . $text . '</span>';
}
$icon = checkOs();
DB::query("INSERT INTO " . DB::table('2th_chat') . " (uid,touid,icon,text,time,ip) VALUES ('$uid','$touid','$icon','$text','" . time() . "','$ip')");
$last = DB::insert_id();
if ($needClear) {
    DB::query("DELETE FROM " . DB::table('2th_chat') . " WHERE id<" . $last);
} else {
    DB::query("DELETE FROM " . DB::table('2th_chat') . " WHERE id<" . ($last - $config['chat_log']));
}
$re = DB::query("SELECT n.*,m.username AS name,mt.username AS toname,g.color,ni.name AS nick,nt.name AS tonick 
FROM " . DB::table('2th_chat') . " n 
LEFT JOIN " . DB::table('common_member') . " m ON n.uid=m.uid 
LEFT JOIN " . DB::table('common_member') . " mt ON n.touid=mt.uid 
LEFT JOIN " . DB::table('common_usergroup') . " g ON m.groupid=g.groupid 
LEFT JOIN " . DB::table('2th_chat_nick') . " ni ON n.uid=ni.uid 
LEFT JOIN " . DB::table('2th_chat_nick') . " nt ON n.touid=nt.uid 
WHERE  id>{$id} AND (n.touid='0' OR n.touid='{$uid}' OR n.uid='{$uid}') 
ORDER BY id DESC LIMIT 30");
$body = array();
while ($c = DB::fetch($re)) {
    if ($c['ip'] == 'changename') {
        $body[$c['id']] .= '<script>nzchatobj(".nzuname_' . $c['uid'] . '").html("' . htmlspecialchars($c['text']) . '");</script>';
        continue;
    } elseif ($c['ip'] == 'delete') {
        $body[$c['id']] .= '<script>nzchatobj("#nzrows_' . $c['text'] . '").fadeOut(200);</script>';
        continue;
    }
    if (strval($c['nick']) === '')
        $c['nick'] = $c['name'];
    if (strval($c['tonick']) === '')
        $c['tonick'] = $c['toname'];
    if ($c['ip'] == 'clear') {
        $seedd = $time . '_' . $uid . '_' . rand(1, 999);
        $c['text'] = '<span style="color:red" id="del_' . $seedd . '">Alert:</span> <span id="nzchatcontent' . $c['id'] . '">ล้างข้อมูล<script type="text/javascript">nzchatobj("#del_' . $seedd . '").parent().parent().parent().' . ($config['chat_type'] == 1 ? 'next' : 'prev') . 'Until().remove();</script>';
    } elseif ($c['touid'] == 0) {
        $c['text'] = '<span style="color:#3366CC">Says:</span> <span id="nzchatcontent' . $c['id'] . '">' . $c['text'];
    } elseif ($c['touid'] == $uid) {
        $c['text'] = ($config['pm_sound'] ? '<embed name="pmsoundplayer" width="0" height="0" src="source/plugin/th_chat/images/player.swf" flashvars="sFile=' . $config['pm_sound'] . '" menu="false" allowscriptaccess="sameDomain" swliveconnect="true" type="application/x-shockwave-flash"></embed>' : '') . '<span style="color:#FF9900">กระซิบถึงคุณ <a href="javascript:;" onClick="nzTouid(' . $c['uid'] . ')">reply</a>:</span> <span id="nzchatcontent' . $c['id'] . '">' . $c['text'];
    } elseif ($c['uid'] == $uid) {
        $c['text'] = '<span style="color:#FF9900">กระซิบกับ <a href="space-uid-' . $c['touid'] . '.html" target="_blank">' . $c['tonick'] . '</a>:</span> <span id="nzchatcontent' . $c['id'] . '">' . $c['text'];
    }
    if (!$config['showos']) $c['icon'] = '';
    $body[$c['id']]  .= chatrow($c['id'], $c['text'], $c['uid'], $c['nick'], $c['time'], $c['color'], $c['touid'], 0, $c['icon'], $is_mod);
    if ($c['ip'] == 'clear') {
        break;
    }
}
echo json_encode($body);
