<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$id = intval($_POST['lastid']);
$is_mod = in_array($_G['adminid'], array(1, 2, 3));
include 'functions.php';
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
include 'online.php';
echo json_encode(array('chat_row' => $body, 'chat_online' => $body_online, 'chat_online_total' => $oltotal));
