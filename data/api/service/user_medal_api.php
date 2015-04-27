<?php
require_once 'library/user_medal.class.php';
$json = $_POST['extparam'];
$obj = new User_Medal();
switch($json['Tag']){
	case 'GetAllMedalType':
		echo json_encode($obj->GetAllMedalType($json['Id']));
		break;
	case 'GetMedalList':
		echo json_encode($obj->GetMedalList($json['Id']));
		break;
	case 'GetLevelRate':
		echo json_encode($obj->getLevelRate($json['BusinessId'],$json['Uin']));
		break;
	case 'GameLevelImfomation':
		echo json_encode($obj->GameLevelImfomation($json['BusinessId'],$json['Uin']));
		break;
}