<?php
require_once 'common.php';
header('Content-Type: text/html; charset=utf-8');
$filename = '';
if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
	if(empty($_FILES)){
		echo '<script>alert("上传图片不能超过2M");parent.window.location="roommanage.php";</script>';
		exit;
	}
	if(empty($_FILES['cover']['name'])){
		echo '<script>alert("请上传图片");parent.window.location="roommanage.php";</script>';
		exit;
	}
	if($_FILES['cover']['size']/(1024*1024) > 2){
		echo '<script>alert("上传图片不能超过2M");parent.window.location="roommanage.php";</script>';
		exit;
	}
	$allow_type = array('gif', 'jpg', 'jpeg', 'png','pjpeg');
	$types = explode('/', $_FILES['cover']['type']);
	$type = $types[count($types)-1];
	if(!in_array($type, $allow_type)){
		echo '<script>alert("请上传JPG、JPEG、GIF和PNG文件，最大2M");parent.window.location="roommanage.php";</script>';
		exit;
	}
	$imgInfo = pathinfo($_FILES['cover']['name']);
	if(empty($imgInfo['extension'])){
		echo '<script>alert("图片上传失败,请重试");parent.window.location="roommanage.php";</script>';
		exit;
	}
	$filename = '/themes/compile/tmp_cover/'.microtime(true).'_'.mt_rand(1000,9999).'.'.$imgInfo['extension'];
	$savepath = dirname(dirname(__FILE__)).$filename;
	//$thumb = new thumb(file_get_contents($_FILES['cover']['tmp_name']));
	//$thumb->zoom(340,240);
	//$rst = $thumb->save($savepath);
	$rst = move_uploaded_file($_FILES['cover']['tmp_name'],$savepath);
	if( ! $rst){
		exit('上传失败');
	}
}
$themes=$GroupData['Template']!=''?$GroupData['Template']:'default';
if($themes=='default'){
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','service'));
}
else{
	$tmp_config=get_config('template','group_site');
	$tmp_config['template_dir'].=$themes.'/tpl/service/';
	$tmp_config['cache_dir'].=$themes.'/tpl/service/';
	$tpl = template::getInstance();
	$tpl->setOptions($tmp_config);
}
include template("roommanage/upcover.html",$tpl);
