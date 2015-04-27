<?php
require dirname(dirname(__FILE__)).'/library/global.fun.php';
define('THEMES_ROOT', $_SERVER['DOCUMENT_ROOT'].'/themes/g3');

$rst = array();
if(!empty($_FILES['upfile'])){
	$bytes = file_get_contents($_FILES['upfile']['tmp_name']);
	$index = md5($bytes);
	$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
	$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
	if($query['Flag']==100){
		$rst['Flag'] = $query['rst'];
		$rst['File'] = cdn_url(PIC_API_PATH.'/p/'.$index.'/0/0.jpg');
		$rst['FileName'] = $_POST['filename'];
		@unlink($_FILES['upfile']['tmp_name']);
	}
}

$rst = json_encode($rst);

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));

include template('feedback_upfile.html',$tpl);
