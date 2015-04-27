<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
$user = checkLogin();
$filename = '';
if($_GET['module'] == 'upload'){
	$tmp_cover = dirname(dirname(__FILE__)).$_POST['room_cover'];
	if(file_exists($tmp_cover) && is_file($tmp_cover)){
		$size = json_decode($_POST['room_cover_coords'],true);
		$bytes = file_get_contents($tmp_cover);
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$user['Uin'].'big','crop'=>$size);
		$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
		if($query['rst'] != 100){
			ShowMsg('上传头像失败1','?'.time());
		}
		
		$s = ($size['w'] > $size['h'])? $size['h'] : $size['w'];
		$size = array('w'=>$s,'h'=>$s,'x'=>abs($size['w']-$s)/2,'y'=>0);
		$bytes = socket_request(cdn_url(PIC_API_PATH."/uin/{$user['Uin']}big/0/0.jpg")); //加时间戳防止读取缓存内容
		if(empty($bytes)) ShowMsg('上传头像失败2','?'.time());
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$user['Uin'],'crop'=>$size);
		$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
		if($query['rst'] != 100){
			ShowMsg('上传头像失败2','?'.time());
		}

		$width = intval(($s / 5) * 4);
		$size = array('w'=>$width,'h'=>$s,'x'=>($s-$width)/2,'y'=>0);
		$bytes = socket_request(cdn_url(PIC_API_PATH."/uin/{$user['Uin']}/0/0.jpg")); //加时间戳防止读取缓存内容
		if(empty($bytes)) ShowMsg('上传头像失败3','?'.time());
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$user['Uin'].'middle','crop'=>$size);
		$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
		$result = array('Flag'=>$query['rst']);
		if($result['Flag'] != 100){
			ShowMsg('上传头像失败3','?'.time());
		}
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
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template("reg/upload.html",$tpl);
