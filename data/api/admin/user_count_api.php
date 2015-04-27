<?php
// require dirname(dirname(dirname(__FILE__))).'/library/global.fun.php';
include 'library/user_count.class.php';
$UserCount = new UserCount();
$json = $_POST['extparam'];

// date_default_timezone_set('Asia/Shanghai');
// $json['Tag'] = 'GetList';
// $json['Data']['Start'] = strtotime(date("Ymd"))-24*60*60;
// $json['Data']['End'] = strtotime(date("Ymd"));
// $json['Data']['Page'] = 1;

switch($json['Tag']){
	case 'GetList':
		echo json_encode($UserCount->getList($json['Data']));
		break;
}