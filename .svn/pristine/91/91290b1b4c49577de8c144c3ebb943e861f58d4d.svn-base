<?php
//取客户端缓存
error_reporting(0);
if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && time() < $_SERVER['HTTP_IF_NONE_MATCH']){
	header("HTTP/1.0 304 Not Modified");
	exit;
}

//图片异常
if($_GET['w'] > 800 || $_GET['h'] > 600){
	$bytes = file_get_contents('images/exception.jpg');
	$thumb = new thumb($bytes);
	$thumb->zoom($_GET['w'],$_GET['h']);
	$thumb->display();
}

//显示图片
require '../library/global.fun.php';
header("Etag: ". (time() + 1800)); //缓存时间
$result = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'GetFile','where'=>array('md5'=>$_GET['md5']))));
if($result['Flag'] == 100){
	$bytes = base64_decode($result['Bytes']);
	$thumb = new thumb($bytes);
	$thumb->zoom($_GET['w'],$_GET['h']);
	$thumb->display();
	exit;
}

//图片异常或不存在
$bytes = file_get_contents('images/md5.jpg');
$thumb = new thumb($bytes);
$thumb->zoom($_GET['w'],$_GET['h']);
$thumb->display();
