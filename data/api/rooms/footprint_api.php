<?php
include_once 'library/footprint.class.php';
$footPrint = new FootPrint();
$param = $_POST['param'];
$json = $_POST['extparam'];

switch($json['Tag']){
	case 'HistoryAccess':
		echo json_encode($footPrint->historyAccess($param['Uin'],$param['ChannelId']));
		break;
	case 'GetHistoryAccess':
		
		echo json_encode($footPrint->getHistoryAccess($json['Uin'],$json['GroupId']));
		break;
	case 'MyFavorite':
		echo json_encode($footPrint->myFavorite($param['Uin'],$param['ChannelId']));
		break;
	case 'GetMyFavorite':
		echo json_encode($footPrint->getMyFavorite($json['Uin'],$json['GroupId']));
		break;
	default :
		exit('{"Flag":"101","FlagString":"接口不存在"}');
		break;
}