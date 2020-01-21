<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$uid = $_G['uid'];
$time = time();
if ($uid < 1) {
    die('Login');
}
if (get_magic_quotes_gpc()) {
    $name = stripslashes($_POST['new']);
} else {
    $name = $_POST['new'];
}
$name = paddslashes(htmlspecialchars($name));
if ($name === '') {
    die('กรุณาใส่ชื่อ');
}
if (strpos($name, " ") !== FALSE) {
    die('ห้ามใช้ตัวช่องว่าง');
}
if (DB::fetch_first("SELECT uid FROM " . DB::table('2th_chat_nick') . " WHERE name='{$name}' AND uid!='{$uid}'")) {
    die('ชื่อนี้มีคนอื่นใช้ไปแล้ว');
}
if (DB::fetch_first("SELECT uid FROM " . DB::table('common_member') . " WHERE username='{$name}' AND uid!='{$uid}'")) {
    die('ชื่อนี้มีคนอื่นใช้ไปแล้ว');
}
$re = DB::query("SELECT uid,total,time FROM " . DB::table('2th_chat_nick') . " WHERE uid='{$uid}'");
if ($re = DB::fetch($re)) {
    if ($time - $re['time'] < 86400) {
        if ($re['total'] > 1) {
            die('เปลี่ยนชื่อได้ 2 ครั้ง/1 วัน');
        } else {
            DB::query("UPDATE " . DB::table('2th_chat_nick') . " SET name='{$name}',total=2 WHERE uid='{$uid}' LIMIT 1");
        }
    } else {
        DB::query("UPDATE " . DB::table('2th_chat_nick') . " SET name='{$name}',total=1,time='{$time}' WHERE uid='{$uid}' LIMIT 1");
    }
} else {
    DB::query("INSERT INTO " . DB::table('2th_chat_nick') . " (uid,name,total,time) VALUES ('{$uid}','{$name}','1','{$time}')");
}
DB::query("INSERT INTO " . DB::table('2th_chat') . " (uid,touid,text,time,ip) VALUES ('{$uid}','0','{$name}','{$time}','changename')");
echo 'ok';
function paddslashes($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            $data[paddslashes($key)] = paddslashes($val);
        }
    } else {
        $data = str_replace(array('\\', '\''), array('\\\\', '\\\''), $data);
    }
    return $data;
}
