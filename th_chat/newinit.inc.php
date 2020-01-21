<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$is_mod = in_array($_G['adminid'], array(1, 2, 3));
include 'functions.php';
$re = DB::query("SELECT n.*,m.username AS name,mt.username AS toname,g.color,ni.name AS nick,nt.name AS tonick 
FROM " . DB::table('2th_chat') . " n 
LEFT JOIN " . DB::table('common_member') . " m ON n.uid=m.uid 
LEFT JOIN " . DB::table('common_member') . " mt ON n.touid=mt.uid 
LEFT JOIN " . DB::table('common_usergroup') . " g ON m.groupid=g.groupid 
LEFT JOIN " . DB::table('2th_chat_nick') . " ni ON n.uid=ni.uid 
LEFT JOIN " . DB::table('2th_chat_nick') . " nt ON n.touid=nt.uid 
WHERE (n.touid='0' OR n.touid='$uid' OR n.uid='$uid') AND n.ip != 'changename' AND n.ip != 'delete'
ORDER BY id DESC LIMIT {$config['chat_init']}");
$body = array();
$lastid = 0;
while ($c = DB::fetch($re)) {
    if ($c['id'] > $lastid)
        $lastid = $c['id'];
    if ($c['ip'] == 'changename') {
        continue;
    } elseif ($c['ip'] == 'delete') {
        continue;
    }
    if (strval($c['nick']) === '')
        $c['nick'] = $c['name'];
    if (strval($c['tonick']) === '')
        $c['tonick'] = $c['toname'];
    if ($c['ip'] == 'clear') {
        $seedd = $time . '_' . $uid . '_' . rand(1, 999);
        $c['text'] = '<span style="color:red" id="del_' . $seedd . '">Alert:</span> <span id="nzchatcontent' . $c['id'] . '">ล้างข้อมูล';
    } elseif ($c['touid'] == 0) {
        $c['text'] = '<span style="color:#3366CC">Says:</span> <span id="nzchatcontent' . $c['id'] . '">' . $c['text'];
    } elseif ($c['touid'] == $uid) {
        $c['text'] = '<span style="color:#FF9900">กระซิบถึงคุณ <a href="javascript:;" onClick="nzTouid(' . $c['uid'] . ')">reply</a>:</span> <span id="nzchatcontent' . $c['id'] . '">' . $c['text'];
    } elseif ($c['uid'] == $uid) {
        $c['text'] = '<span style="color:#FF9900">กระซิบกับ <a href="space-uid-' . $c['touid'] . '.html" target="_blank">' . $c['tonick'] . '</a>:</span> <span id="nzchatcontent' . $c['id'] . '">' . $c['text'];
    }
    if (!$config['showos']) $c['icon'] = '';
    $body[] = chatrow($c['id'], $c['text'], $c['uid'], $c['nick'], $c['time'], $c['color'], $c['touid'], 1, $c['icon'], $is_mod);
    if ($c['ip'] == 'clear') {
        break;
    }
}
include 'online.php';
if ($config['chat_type'] == 2) {
    $body = array_reverse($body);
}
$body = implode('', $body);
$body = array('lastid' => $lastid, 'datahtml' => $body, 'datachatonline' => $body_online, 'chat_online_total' => $oltotal);
echo json_encode($body);
