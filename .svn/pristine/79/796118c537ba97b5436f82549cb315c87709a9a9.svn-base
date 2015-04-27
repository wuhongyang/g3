<?php
require_once 'library/join.class.php';
$join = new join();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'SaveOpenInfo'://保存通行证信息
		echo json_encode($join->saveOpenInfo($param['Uin'],$json['Info']));
		break;
	case 'ApplyArtistAndRoomer':
		echo json_encode($join->applyArtistAndRoomer($json['Info']));
		break;
	case 'BasicInfo':
		echo json_encode($join->basicInfo($param['Uin'],$json['Info']));
		break;
	case 'JoinInfo':
		echo json_encode($join->joinInfo($param['Uin'],$json['RoleType']));
		break;
	case 'CheckApply':
		echo json_encode($join->checkApply($json['Uin']));
		break;
	default :
		exit('{"Flag":"101","FlagString":"接口不存在"}');
		break;
}