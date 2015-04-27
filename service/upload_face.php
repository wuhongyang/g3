<?php
require_once 'common.php';

$filename = '';
if($_GET['module'] == 'upload'){
	$tmp_cover = dirname(dirname(__FILE__)).$_POST['room_cover'];
	if(file_exists($tmp_cover) && is_file($tmp_cover)){
		$size = json_decode($_POST['room_cover_coords'],true);
		$bytes = file_get_contents($tmp_cover);
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$user['Uin'].'big','crop'=>$size);
		$query = json_decode(socket_request(UPLOAD_API_PATH.'/upload',$opt,true,600),true);
		if($query['rst'] != 100){
			ShowMsg('上传头像失败1','?'.time());
		}
		$urls = array(
			PIC_API_PATH."/uin/{$user['Uin']}big/0/0.jpg",
			PIC_API_PATH."/uin/{$user['Uin']}big/320/240.jpg"
		);
		cdn_url($urls, 'CDN_UPLOAD_CLEAR');
		
		$s = ($size['w'] > $size['h'])? $size['h'] : $size['w'];
		$size = array('w'=>$s,'h'=>$s,'x'=>abs($size['w']-$s)/2,'y'=>0);
		$bytes = socket_request(cdn_url(PIC_API_PATH."/uin/{$user['Uin']}big/0/0.jpg")); //加时间戳防止读取缓存内容
		if(empty($bytes)) ShowMsg('上传头像失败2','?'.time());
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$user['Uin'],'crop'=>$size);
		$query = json_decode(socket_request(UPLOAD_API_PATH.'/upload',$opt,true,600),true);
		if($query['rst'] != 100){
			ShowMsg('上传头像失败2','?'.time());
		}
		cdn_url(PIC_API_PATH."/uin/{$user['Uin']}/0/0.jpg", 'CDN_UPLOAD_CLEAR');
		
		$width = intval(($s / 5) * 4);
		$size = array('w'=>$width,'h'=>$s,'x'=>($s-$width)/2,'y'=>0);
		$bytes = socket_request(cdn_url(PIC_API_PATH."/uin/{$user['Uin']}/0/0.jpg")); //加时间戳防止读取缓存内容
		if(empty($bytes)) ShowMsg('上传头像失败3','?'.time());
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$user['Uin'].'middle','crop'=>$size);
		$query = json_decode(socket_request(UPLOAD_API_PATH.'/upload',$opt,true,600),true);
		$result = array('Flag'=>$query['rst']);
		if($result['Flag'] != 100){
			ShowMsg('上传头像失败3','?'.time());
		}
		$urls = array(
			PIC_API_PATH."/uin/{$user['Uin']}middle/160/200.jpg",
			PIC_API_PATH."/uin/{$user['Uin']}/100/100.jpg",
			PIC_API_PATH."/uin/{$user['Uin']}/50/50.jpg",
			PIC_API_PATH."/uin/{$user['Uin']}/40/40.jpg",
			PIC_API_PATH."/uin/{$user['Uin']}/160/160.jpg",
			PIC_API_PATH."/uin/{$user['Uin']}/70/70.jpg",
		);
		cdn_url($urls, 'CDN_UPLOAD_CLEAR');
		$big_face =  cdn_url(PIC_API_PATH."/uin/{$user['Uin']}big/320/240.jpg");
		$middle_face =  cdn_url(PIC_API_PATH."/uin/{$user['Uin']}middle/160/200.jpg");
		$face =  cdn_url(PIC_API_PATH."/uin/{$user['Uin']}/100/100.jpg");
	}
}else{
	if(!empty($_FILES)){
		if(strpos($_FILES['cover']['type'], 'image') === false){
			ShowMsg('上传图片格式必须为jpg，png，gif格式','?'.time());
		}
		$filename = '/themes/compile/tmp_cover/'.time().'_'.$_FILES['cover']['name'];
		$savepath = dirname(dirname(__FILE__)).$filename;
		//$thumb = new thumb(file_get_contents($_FILES['cover']['tmp_name']));
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
include template("account/upload_face.html",$tpl);
