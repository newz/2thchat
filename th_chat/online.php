<?php
$time = TIMESTAMP;
if ($_G['setting']['version'] > "X3.4") {
    $dataarr = array(
        'sid' => $_G['session']['sid'],
        'ip' => $_G['clientip'],
        'uid' => $_G['member']['uid'],
        'username' => addslashes($_G['member']['username']),
        'groupid' => $_G['member']['groupid'],
        'invisible' => $_G['member']['invisible'],
        'action' => APPTYPEID,
        'lastactivity' => $time,
        'lastolupdate' => 0,
        'fid' => 0,
        'tid' => 0,
    );
} else {
    list($ip1, $ip2, $ip3, $ip4) = explode('.', $_G['clientip']);
    $dataarr = array(
        'sid' => $_G['session']['sid'],
        'ip1' => $ip1,
        'ip2' => $ip2,
        'ip3' => $ip3,
        'ip4' => $ip4,
        'uid' => $_G['member']['uid'],
        'username' => addslashes($_G['member']['username']),
        'groupid' => $_G['member']['groupid'],
        'invisible' => $_G['member']['invisible'],
        'action' => APPTYPEID,
        'lastactivity' => $time,
        'lastolupdate' => 0,
        'fid' => 0,
        'tid' => 0,
    );
}
if ($_G['uid']) {
    if (DB::fetch_first('SELECT uid FROM ' . DB::table('common_session') . ' WHERE uid=\'' . $_G['uid'] . '\'')) {
        DB::update('common_session', $dataarr, "`uid`='" . $_G['uid'] . "'");
    } else {
        DB::insert('common_session', $dataarr, false, false, true);
    }
}

DB::update('newz_nick', array('ban' => 0), "`ban`<'" . TIMESTAMP . "'");

$timeout = 30;

$gid = $_G['groupid'];

$class = 'nzolnor';

$oltotal = 0;

$re = DB::query("SELECT s.uid,s.username,s.groupid,s.lastactivity,g.grouptitle,g.color,n.ban FROM " . DB::table('common_session') . " s LEFT JOIN " . DB::table('common_usergroup') . " g ON s.groupid=g.groupid LEFT JOIN " . DB::table('newz_nick') . " n ON s.uid=n.uid WHERE s.uid>0 AND invisible=0 AND action IN (2,127) AND fid=0 AND tid=0");
if (!empty($config['onlinebot'])) {
	$re2 = DB::query("SELECT s.uid,s.username,s.groupid,g.grouptitle,g.color,n.ban FROM " . DB::table('common_member') . " s LEFT JOIN " . DB::table('common_usergroup') . " g ON s.groupid=g.groupid LEFT JOIN " . DB::table('newz_nick') . " n ON s.uid=n.uid WHERE s.uid IN (" . $config['onlinebot'] . ")");
}

while ($r = DB::fetch($re) or $r = DB::fetch($re2)) {
    if ($r['groupid'] > 9) {$r['groupid'] = 100 - $r['groupid'];} else if (in_array($r['groupid'], array(4, 5, 6, 9))) {$r['groupid'] = 100;} else if ($r['groupid'] == 7) {$r['groupid'] = 99;} else if ($r['groupid'] == 8) {$r['groupid'] = 98;}
    $botid = explode(",", $config['onlinebot']);
    if (in_array($r['uid'], $botid)) {if (empty($r['lastactivity'])) {$r['lastactivity'] = $time;} else {continue;}}
    $r['groupid'] += $time - $r['lastactivity'] > $timeout ? 100 : 0;
    if ($time - $r['lastactivity'] > $timeout) {
        $oltotal = $oltotal - 1;
    } else {
        $body_onlinein[$r['groupid']] .= '<div class="nzolcon"><div class="nzolname" onMouseOver="nzchatobj(\'#nzchatolc' . $r['uid'] . '\').show();nzchatobj(\'#nzchatolr' . $r['uid'] . '\').hide();" onMouseOut="nzchatobj(\'#nzchatolc' . $r['uid'] . '\').hide();nzchatobj(\'#nzchatolr' . $r['uid'] . '\').show();">
		<div style="display:inline-block;vertical-align: top;">
		<img src="' . avatar($r['uid'], 'small', 1) . '" title="' . $r['username'] . '" onclick="showWindow(\'th_chat_profile\', \'plugin.php?id=th_chat:profile&uid=' . $r['uid'] . '\');return false;" class="nzchatavatar" onerror="this.src=\'uc_server/images/noavatar_small.gif\';" style="cursor:pointer;">
		</div><div style="display:inline-block;vertical-align: top;margin-left:10px;position:relative;height:32px;line-height: 15px;"><a class="nznametop2 nzat_' . $r['uid'] . '" id="nzolpro_' . $r['uid'] . '" onclick="showWindow(\'th_chat_profile\', \'plugin.php?id=th_chat:profile&uid=' . $r['uid'] . '\');return false;" style="cursor:pointer' . ($r['color'] ? ';color:' . $r['color'] : '') . '">' . ($r['ban'] ? '<strike>' . $r['username'] . '</strike>' : $r['username']) . '</a><br>
		<span id="nzchatolr' . $r['uid'] . '">' . $r['grouptitle'] . '</span>
		<span  id="nzchatolc' . $r['uid'] . '" style="display:none;">
			' . ($uid == $r['uid'] ? '<a href="javascript:void(0);" onclick="showWindow(\'th_chat_setting\', \'plugin.php?id=th_chat:setting\');return false;">ตั้งค่าห้องแชท</a>' : '<a href="javascript:void(0);" onclick="nzAt(\'' . addslashes($r['username']) . '\')">@</a> <a href="javascript:void(0);" onclick="nzTouid(' . $r['uid'] . ')">กระซิบ</a>') . '
		</span>
		</div>
		</div></div>';
    }
    $oltotal++;
}
ksort($body_onlinein);
if ($_G['member']['newprompt']) {
    $body_onlinez .= '<div class="nzolcon"><a href="home.php?mod=space&do=notice" target="_blank"><div class="nzolname" style="text-align: center;font-weight:bold;">
	<img src="source/plugin/th_chat/images/alert.png" style="padding-right:5px" align="absmiddle">มีการแจ้งเตือนใหม่
	</div></a></div>';
}
if ($_G['member']['newpm']) {
    $body_onlinez .= '<div class="nzolcon"><a href="home.php?mod=space&do=pm" target="_blank"><div class="nzolname" style="text-align: center;font-weight:bold;">
	<img src="source/plugin/th_chat/images/pm.png" style="padding-right:5px" align="absmiddle">มีข้อความส่วนตัวใหม่
	</div></a></div>';
}
foreach ($body_onlinein as $show) {
    $body_onlinez .= $show;
}
$body_online = $body_onlinez;
