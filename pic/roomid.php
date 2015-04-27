<?php
error_reporting(0);
if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && time() < $_SERVER['HTTP_IF_NONE_MATCH']){
	header("HTTP/1.0 304 Not Modified");
	exit;
}

if(($_GET['w'] > 240 && $_GET['w'] < 80) || ($_GET['h'] > 180 && $_GET['h'] < 60)){
	$_GET['w'] = 80;
	$_GET['h'] = 60;
}

//用户自定义封面
require '../library/global.fun.php';
header("Etag: ". (time() + 1800)); //缓存时间
$result = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'GetFile','where'=>array('roomid'=>(int)$_GET['roomid']))));
if($result['Flag'] == 100){
	$bytes = base64_decode($result['Bytes']);
	$thumb = new thumb($bytes);
	$thumb->zoom($_GET['w'],$_GET['h']);
	$thumb->display();
	exit;
}

//后台地域默认封面
/*
$site = json_decode($_COOKIE['SiteData'],true);
$result = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'GetFile','where'=>array('md5'=>$site['rooms_bg_url']))));
if($result['Flag'] == 100){
	$bytes = base64_decode($result['Bytes']);
	$thumb = new thumb($bytes);
	$thumb->zoom($_GET['w'],$_GET['h']);
	$thumb->display();
	exit;
}*/

//系统默认封面
$bytes = file_get_contents('images/roomid.jpg');
$thumb = new thumb($bytes);
$thumb->zoom($_GET['w'],$_GET['h']);
$thumb->display();

