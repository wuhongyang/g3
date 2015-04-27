<?php
function check_upload($file){
	if(empty($_POST)){
		return array('Flag'=>101,'FlagString'=>'上传图片不能大于2M，请重新上传');
	}
	if(!empty($file['tmp_name'])){
		if(strpos($file['type'], 'image') === false){
			$rst = array('Flag'=>101,'FlagString'=>'上传图片格式必须为jpg，png，gif格式');
		}
		$size = $file['size']/(pow(1024, 2));
		if($size > 2){
			$rst = array('Flag'=>102,'FlagString'=>'上传图片不能大于2M，请重新上传');
		}
		$rst = array('Flag'=>100,'FlagString'=>'ok');
	}else{
		$rst = array('Flag'=>103,'FlagString'=>'请上传图片文件');
	}
	return $rst;
}

function send_to_oss($dir){
	$bytes = file_get_contents($dir);
	$index = md5($bytes);
	$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
	$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
	$result = array('Flag'=>$query['rst'],'File'=>$index);
	return $result;
}