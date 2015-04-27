<?php
require dirname(dirname(__FILE__)).'/library/global.fun.php';
define('THEMES_ROOT', $_SERVER['DOCUMENT_ROOT'].'/themes/g3');
$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));

if(!empty($_POST)){
	if(mb_strlen($_POST['content']) > 1500){
		alertMsg('最多500字');
	}
	if(empty($_POST['qq']) && empty($_POST['phone'])) alertMsg('联系方式至少填一个');
	if(!is_numeric($_POST['qq']) && !is_numeric($_POST['phone'])) alertMsg('联系方式不正确');
	$insert = array('db'=>'kkyoo_action','table'=>'g3_feedback','record'=>
		array('uin'=>$user['Uin'],'content'=>$_POST['content'],'img'=>(array)$_POST['img'],'qq'=>$_POST['qq'],'phone'=>$_POST['phone'],'dispose'=>'','status'=>0,'uptime'=>time())
	);
	$rst = json_decode(socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($insert))),true);
	if($rst['success'] == 100){
		exit('<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script type="text/javascript">if(confirm("提交成功，我们会在1-2个工作日内为您处理，非常感谢您对VV酷的支持！\r\n继续提交？")){location.href="feedback.php"}else{location.href="/"}</script></html>');
	}else{
		alertMsg('提交失败，您也可以到10000客服房间进行反馈，非常感谢您对VV酷的支持！');
	}
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));

include template('feedback.html',$tpl);