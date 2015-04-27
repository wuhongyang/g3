<?php
require_once 'common.php';
$filename = '';
if($_GET['module'] == 'upload'){
	$tmp_cover = dirname(dirname(__FILE__)).$_POST['room_cover'];
	if(file_exists($tmp_cover) && is_file($tmp_cover)){
		$size = json_decode($_POST['room_cover_coords'],true);
		$bytes = file_get_contents($tmp_cover);
		//$thumb = new thumb($bytes);
		//$thumb->crop($size['w'],$size['h'],$size['x'],$size['y']);
		//if($thumb->save($tmp_cover)){
			$bytes = file_get_contents($tmp_cover);
			$index = md5($bytes.$_POST['room_cover_coords']); //防止上传同一张图md5值相同导致覆盖
			$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index,'crop'=>$size);
			$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
			$result = array('Flag'=>$query['rst'],'File'=>$index);
			if($result['Flag'] != 100){
				ShowMsg('艺人靓照上传失败');
			}
		//}else{
		//	ShowMsg('艺人靓照上传失败');
		//}
	}
}else{
	if(!empty($_FILES)){
		$filename = '/themes/compile/tmp_cover/'.time().'_'.$_FILES['cover']['name'];
		$savepath = dirname(dirname(__FILE__)).$filename;
		//$thumb = new thumb(file_get_contents($_FILES['cover']['tmp_name']));
		//$thumb->zoom(340,240);
		//$rst = $thumb->save($savepath);
		$rst = move_uploaded_file($_FILES['cover']['tmp_name'],$savepath);
		if( ! $rst){
			exit('上传失败');
		}
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
include template("personal/upcover.html",$tpl);
