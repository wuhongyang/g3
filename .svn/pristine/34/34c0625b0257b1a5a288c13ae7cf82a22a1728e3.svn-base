<?php
//取客户端缓存
error_reporting(0);
if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && time() < $_SERVER['HTTP_IF_NONE_MATCH']){
	header("HTTP/1.0 304 Not Modified");
	exit;
}

//用户自定义头像
require '../library/global.fun.php';
header("Etag: ". (time() + 1800)); //缓存时间
$uin = $_GET['uin'];
if(preg_match('/^\d+$/', $uin)){
	$uin = (int)$uin;
}
$result = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'GetFile','where'=>array('uin'=>$uin))));
if($result['Flag'] == 100){
	$bytes = base64_decode($result['Bytes']);
	$thumb = new thumb($bytes);
	$thumb->zoom($_GET['w'],$_GET['h']);
	$thumb->display();
	exit;
}

//系统默认头像
$bytes = file_get_contents('images/uin.jpg');
$thumb = new thumb($bytes);
$thumb->zoom($_GET['w'],$_GET['h']);
$thumb->display();
