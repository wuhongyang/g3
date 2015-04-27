<?php
//取客户端缓存
error_reporting(0);

/*if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && time() < $_SERVER['HTTP_IF_NONE_MATCH']){
	header("HTTP/1.0 304 Not Modified");
	exit;
}*/
//用户自定义背景
require '../library/global.fun.php';
header("Etag: ". (time() + 1800)); //缓存时间
$result = httpPOST(GRIDFS_API_PATH,array('extparam'=>array('Tag'=>'GetFile','where'=>array('groupbg'=>(int)$_GET['groupbg']))));
if($result['Flag'] == 100){
	$bytes = base64_decode($result['Bytes']);
	$thumb = new thumb($bytes);
	if($_GET['w']>0 || $_GET['h']>0){
		$thumb->zoom($_GET['w'],$_GET['h']);
	}
	$thumb->display();
	exit;
}

//系统默认背景
$bytes = file_get_contents('group/bg1.jpg');
$thumb = new thumb($bytes);
$thumb->zoom($_GET['w'],$_GET['h']);
$thumb->display();

