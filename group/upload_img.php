<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

header("Content-type: text/html; charset=utf-8");

if(!empty($_FILES['adv_pic'])){
	if(strpos($_FILES['adv_pic']['type'], 'image') === false){
		exit('<script>parent._show_msg("上传图片格式必须为jpg，png，gif格式",-1)</script>');
	}
	$size = $_FILES['adv_pic']['size']/(pow(1024, 2));
	if($size > 2){
		exit('<script>parent._show_msg("上传图片不能大于2M，请重新上传",-1)</script>');
	}
	$bytes = file_get_contents($_FILES['adv_pic']['tmp_name']);
	$index = md5($bytes);
	$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
	$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
	if($query['rst']==100){
		$rst['Flag'] = $query['rst'];
		$rst['File'] = $index;
		$rst['FileName'] = $_FILES['adv_pic']['name'];
		@unlink($_FILES['adv_pic']['tmp_name']);
	}
	$rst = json_encode($rst);
}elseif($_SERVER['REQUEST_METHOD'] == 'POST'){
	exit('<script>parent._show_msg("上传图片不能大于2M，请重新上传",-1)</script>');
}
$template = 'adv_pic_upload';



$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);