<?php
//取客户端缓存
error_reporting(0);
if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && time() < $_SERVER['HTTP_IF_NONE_MATCH']){
	header("HTTP/1.0 304 Not Modified");
	exit;
}

//用户自定义背景
require '../library/global.fun.php';
header("Etag: ". (time() + 1800)); //缓存时间
$result = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'GetFile','where'=>array('roombg'=>(int)$_GET['roombg']))));
if($result['Flag'] == 100){
	$bytes = base64_decode($result['Bytes']);
	$thumb = new thumb($bytes);
	$thumb->zoom($_GET['w'],$_GET['h']);
	$thumb->display();
	exit;
}

//后台地域默认背景
$site = json_decode($_COOKIE['SiteData'],true);
$result = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'GetFile','where'=>array('md5'=>$site['video_url']))));
if($result['Flag'] == 100){
	$bytes = base64_decode($result['Bytes']);
	$thumb = new thumb($bytes);
	$thumb->zoom($_GET['w'],$_GET['h']);
	$thumb->display();
	exit;
}

//系统默认背景
$bytes = file_get_contents('roombg/1.jpg');
$thumb = new thumb($bytes);
$thumb->zoom($_GET['w'],$_GET['h']);
$thumb->display();

