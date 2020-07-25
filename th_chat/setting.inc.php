<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
if($_G['uid']<1){
	exit('Please Login');
}
if($_POST['sound_1']!=""&&$_POST['sound_2']!="")
{
	DB::query("INSERT INTO ".DB::table('newz_nick')." (uid,name,total,time,sound_1,sound_2) VALUES ('{$_G['uid']}','{$_G['username']}','0','".time()."',{$_POST['sound_1']},{$_POST['sound_2']}) ON DUPLICATE KEY UPDATE  sound_1='{$_POST['sound_1']}',sound_2='{$_POST['sound_2']}'");
	exit('<script type="text/javascript">window.close();</script>Updated!');
}else{
	$olddata = DB::fetch_first("SELECT sound_1,sound_2 FROM ".DB::table('newz_nick')." WHERE uid='{$_G['uid']}'");
	$olddata['sound_1']=='1'?$olddatas1='checked':$olddatas2='checked';
	$olddata['sound_2']=='0'?$olddatas4='checked':$olddatas3='checked';
}
?>
<html>
<title><?=lang('plugin/th_chat', 'jdj_th_chat_text_php_18');?></title>
<style>
.command {
padding: 1px;
border-radius: 3px;
color: #FFF;
background-color: gold;
}
a {
cursor: pointer;
}
</style>
<body style="font-size: 14px;background-color: #f5f5f5;">
<center><h3><?=lang('plugin/th_chat', 'jdj_th_chat_text_php_18');?></h3><br/>
<form action="" method="post">
<table style="font-size: 14px;">
<tr>
<td><?=lang('plugin/th_chat', 'jdj_th_chat_text_php_52');?></td>
<td><input type="radio" value="1" name="sound_1" <?=$olddatas1;?>><?=lang('plugin/th_chat', 'jdj_th_chat_text_php_37');?> <input type="radio" value="0" name="sound_1" <?=$olddatas2;?>><?=lang('plugin/th_chat', 'jdj_th_chat_text_php_31');?></td>
</tr>
<tr>
<td><?=lang('plugin/th_chat', 'jdj_th_chat_text_php_51');?></td>
<td><input type="radio" value="1" name="sound_2" <?=$olddatas3;?>><?=lang('plugin/th_chat', 'jdj_th_chat_text_php_37');?> <input type="radio" value="0" name="sound_2" <?=$olddatas4;?>><?=lang('plugin/th_chat', 'jdj_th_chat_text_php_31');?></td>
</tr>
<tr height="20"></tr>
<tr>
<td><input type="submit" value="<?=lang('plugin/th_chat', 'jdj_th_chat_text_php_24');?>" style="
font-size: 14px;
text-align: center;
border: 1px solid transparent;
border-radius: 2px;
margin-top: 2px;
width:90px;
height: 29px;
cursor:pointer;
background-color: #5cb85c;
border-color: #5cb85c;
color: #fff;"></td>
<td><input type="button" value="<?=lang('plugin/th_chat', 'jdj_th_chat_text_php_31');?>" style="
font-size: 14px;
text-align: center;
border: 1px solid transparent;
border-radius: 2px;
margin-top: 2px;
width:90px;
height: 29px;
cursor:pointer;
background-color: #ff0000;
border-color: #ff0000;
color: #fff;" onClick="window.close();"></td>
</tr>
</table>
</form>
<br>
<h3>คำสั่งในห้องแชท</h3>
<?
if(in_array($_G['adminid'],array(1,2,3)))
{
echo '
<span class="command"><strong>!clear</strong></span> ล้างข้อความทั้งหมดในห้องแชท<br/><br/>
<span class="command"><strong>!ban <a title="หมายเลข UID ของผู้ที่ต้องการแบน" style="color:red">UID</a></strong></span> แบนผู้ใช้<br/><br/>
<span class="command"><strong>!unban <a title="หมายเลข UID ของผู้ที่ต้องการปลดแบน" style="color:red">UID</a></strong></span> ปลดแบนผู้ใช้<br/><br/>
<span class="command"><strong>!del <a title="หมายเลข ID ของข้อความที่ต้องการลบ" style="color:red">ID</a></strong></span> ลบข้อความ<br/><br/>
<span class="command"><strong>!name <a title="หมายเลข UID ของผู้ที่ต้องเปลี่ยนชื่อให้" style="color:red">UID</a>|:|<a title="ชื่อที่ต้องการเปลี่ยนให้" style="color:red">NAME</a></strong></span> เปลี่ยนชื่อให้สมาชิก<br/><br/>
';
}
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
if($config['chat_point'])
{
echo '<span class="command"><strong>!point <a title="หมายเลข UID ของผู้ที่ต้องการให้คะแนน" style="color:red">UID</a>|<a title="จำนวนคะแนนที่ต้องการให้" style="color:blue">POINT</a>|<a title="เหตุผลของการให้คะแนน" style="color:#2E8B57">REASON</a></strong></span> เพิ่มคะแนนให้แก่ผู้ใช้<br/>';
if(!in_array($_G['adminid'],array(1,2,3)))
{
echo '<font color="blue">point</font> จะต้องเป็น "1" หรือ "-1" เท่านั้น<br/>
';
}
}
?>
</center>
</body>
</html>