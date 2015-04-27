<?php
include_once('library/common.php');

$user = checkLogin();

if($user['Flag'] == 100){
	$url = base64_decode($_GET['url']);
	$url .= (strpos($url, '?') === false) ? '?' : '&';
	$url .= 'uin='.$user['Uin'].'&session='.$user['Token'];
	header("Location:{$url}");
}else{
	alertMsg("请先登录","/passport/?account&login");
}
exit;