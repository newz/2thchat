<?php
if(!defined('IN_DISCUZ')) { exit('Access Denied'); }
loadcache('plugin');
$config = $_G['cache']['plugin']['th_chat'];
$uid = $_G['uid'];
$time = time();
$is_mod = in_array($_G['adminid'],array(1,2,3));
if($uid<1){
	die('Login');
}

$fileSystemIterator = new FilesystemIterator(__DIR__.'/img_up');
$now = time();
foreach ($fileSystemIterator as $file) {
    if ($now - $file->getCTime() >= 604800) // 7 days 
        unlink(__DIR__.'/img_up'.$file->getFilename());
}

$files = glob(__DIR__.'/img_up/'.$_G['uid'].'_*');
if($files !== false){
    $filecount = count($files);
	if($filecount>49){
		echo json_encode(array('error'=>'ขออภัย คุณอัปโหลดภาพได้สูงสุด 30 ภาพต่อสัปดาห์เท่านั้น'));
		exit();
	}
}

require_once  "bulletproof.php";

$image = new Bulletproof\Image($_FILES);
$image->setSize(1, 1048576);
$image->setLocation(__DIR__ . "/img_up");
$image->setName($_G['uid'].'_'.time());
if($image["pictures"]){
  $upload = $image->upload();
  if($upload){
    echo json_encode(array('url'=>$_G['siteurl'].'source/plugin/th_chat/img_up/'.$image->getName().'.'.$image->getMime())); 
  }else{
    echo json_encode(array('error'=>$image->getError()));
  }
}
?>