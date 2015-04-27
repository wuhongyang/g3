<?php
require dirname(dirname(__FILE__)).'/library/global.fun.php';
define('THEMES_ROOT', $_SERVER['DOCUMENT_ROOT'].'/themes/g3');

$name = !empty($_GET['name']) ? $_GET['name'] : $_POST['name'];
$id = !empty($_GET['id']) ? $_GET['id'] : $_POST['id'];

$file = array(
	'front' => '身份证正面',
	'back' => '身份证反面',
	'user_photo' => '个 人 靓 照'
);

$rst = array();

if(!empty($_FILES['upfile'])){
	$bytes = file_get_contents($_FILES['upfile']['tmp_name']);
	$index = md5($bytes);
	$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
	$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
	if($query['Flag']==100){
		$rst['Flag'] = $query['rst'];
		$rst['File'] = $index;
		$rst['FileName'] = $_POST['filename'];
		@unlink($_FILES['upfile']['tmp_name']);
	}
	$rst = json_encode($rst);
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));

include template('join/upfile.html',$tpl);
