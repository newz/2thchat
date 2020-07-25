<?php
$time = time();
list($ip1, $ip2, $ip3, $ip4) = explode('.', $_G['clientip']);
$dataarr = array(
    'sid' => $_G['session']['sid'],
    'ip1' => $ip1,
    'ip2' => $ip2,
    'ip3' => $ip3,
    'ip4' => $ip4,
    'uid' => $_G['member']['uid'],
    'username' => paddslashes($_G['member']['username']),
    'groupid' => $_G['member']['groupid'],
    'invisible' => $_G['member']['invisible'],
    'action' => APPTYPEID,
    'lastactivity' => $time,
    'lastolupdate' => 0,
    'fid' => 0,
    'tid' => 0,
);
if ($_G['uid']) {
    if (DB::fetch_first('SELECT uid FROM ' . DB::table('common_session') . ' WHERE uid=\'' . $_G['uid'] . '\'')) {
        DB::update('common_session', $dataarr, "`uid`='" . $_G['uid'] . "'");
    } else {
        DB::insert('common_session', $dataarr, false, false, true);
    }
}

$timeout = 30;

$gid = $_G['groupid'];

$class = 'nzolnor';

$oltotal = 0;
$banned = DB::fetch_first("SELECT value FROM " . DB::table('common_pluginvar') . " WHERE variable='chat_ban' AND displayorder='16' LIMIT 1");
$banned = explode(",", $banned['value']);

if ($config['chat_point']) {
    $re = DB::query("SELECT s.uid,s.username,s.groupid,s.lastactivity,g.grouptitle,g.color,n.point_total" . ($config['chat_point'] != '9' ? ",p.extcredits{$config['chat_point']} AS point" : "") . " FROM " . DB::table('common_session') . " s LEFT JOIN " . DB::table('common_usergroup') . " g ON s.groupid=g.groupid LEFT JOIN " . DB::table('newz_nick') . " n ON s.uid=n.uid LEFT JOIN " . DB::table('common_member_count') . " p ON s.uid=p.uid WHERE s.uid>0 AND invisible=0 AND action IN (2,127) AND fid=0 AND tid=0");
    if (!empty($config['onlinebot'])) {
        $re2 = DB::query("SELECT s.uid,s.username,s.groupid,g.grouptitle,g.color,n.point_total" . ($config['chat_point'] != '9' ? ",p.extcredits{$config['chat_point']} AS point" : "") . " FROM " . DB::table('common_member') . " s LEFT JOIN " . DB::table('common_usergroup') . " g ON s.groupid=g.groupid LEFT JOIN " . DB::table('newz_nick') . " n ON s.uid=n.uid LEFT JOIN " . DB::table('common_member_count') . " p ON s.uid=p.uid WHERE s.uid IN (" . $config['onlinebot'] . ")");
    }
} else {
    $re = DB::query("SELECT s.uid,s.username,s.groupid,s.lastactivity,g.grouptitle,g.color,n.point_total FROM " . DB::table('common_session') . " s LEFT JOIN " . DB::table('common_usergroup') . " g ON s.groupid=g.groupid LEFT JOIN " . DB::table('newz_nick') . " n ON s.uid=n.uid WHERE s.uid>0 AND invisible=0 AND action IN (2,127) AND fid=0 AND tid=0");
    if (!empty($config['onlinebot'])) {
        $re2 = DB::query("SELECT s.uid,s.username,s.groupid,g.grouptitle,g.color,n.point_total FROM " . DB::table('common_member') . " s LEFT JOIN " . DB::table('common_usergroup') . " g ON s.groupid=g.groupid LEFT JOIN " . DB::table('newz_nick') . " n ON s.uid=n.uid WHERE s.uid IN (" . $config['onlinebot'] . ")");
    }
}
while ($r = DB::fetch($re) or $r = DB::fetch($re2)) {
    $r['name'] = $r['username'];
    $r['name'] = stripslashes($r['name']);
    if (in_array($r['uid'], $banned)) {
        $r['name'] = '<strike>' . $r['name'] . '</strike>';
    }
    if ($r['groupid'] > 9) {$r['groupid'] = 100 - $r['groupid'];} else if (in_array($r['groupid'], array(4, 5, 6, 9))) {$r['groupid'] = 100;} else if ($r['groupid'] == 7) {$r['groupid'] = 99;} else if ($r['groupid'] == 8) {$r['groupid'] = 98;}
    $botid = explode(",", $config['onlinebot']);
    if (in_array($r['uid'], $botid)) {if (empty($r['lastactivity'])) {$r['lastactivity'] = $time;} else {continue;}}
    $r['groupid'] += $time - $r['lastactivity'] > $timeout ? 100 : 0;
    if ($time - $r['lastactivity'] > $timeout) {
        $oltotal = $oltotal - 1;
    } else {
        $body_onlinein[$r['groupid']] .= '<div class="nzolcon"><div class="nzolname" onMouseOver="nzchatobj(\'#nzchatolc'.$r['uid'].'\').show();nzchatobj(\'#nzchatolr'.$r['uid'].'\').hide();" onMouseOut="nzchatobj(\'#nzchatolc'.$r['uid'].'\').hide();nzchatobj(\'#nzchatolr'.$r['uid'].'\').show();">
		<div style="display:inline-block">
		<img src="' . avatar($r['uid'], 'small', 1) . '" title="' . $r['username'] . '" onclick="showWindow(\'th_chat_profile\', \'plugin.php?id=th_chat:profile&uid=' . $r['uid'] . '\');return false;" class="nzchatavatar" onerror="this.src=\'uc_server/images/noavatar_small.gif\';" style="cursor:pointer;">
		</div><div style="display:inline-block;margin-left:10px;position:relative;top:-4px;height:30px;"><a class="nznametop2 nzat_'.$r['uid'].'" id="nzolpro_' . $r['uid'] . '" onclick="showWindow(\'th_chat_profile\', \'plugin.php?id=th_chat:profile&uid='.$r['uid'].'\');return false;" style="cursor:pointer'.($r['color']?';color:'.$r['color']:'').'">' . $r['username'] . '</a><br>
		<span id="nzchatolr'.$r['uid'].'" style="line-height: 15px;">'.$r['grouptitle'].'</span>
		<span  id="nzchatolc'.$r['uid'].'" style="display:none;line-height: 15px;">
			'.($uid==$r['uid']?'<a href="javascript:void(0);" onclick="showWindow(\'th_chat_setting\', \'plugin.php?id=th_chat:setting\');return false;">ตั้งค่าห้องแชท</a>':'<a href="javascript:void(0);" onclick="nzAt(\''.$r['username'].'\')">@</a> <a href="javascript:void(0);" onclick="nzTouid('.$r['uid'].')">กระซิบ</a>').'
		</span>
		</div>
		</div></div>';
    }
    $oltotal++;
}
ksort($body_onlinein);
foreach ($body_onlinein as $show) {
    $body_onlinez .= $show;
}
$body_online = $body_onlinez;
