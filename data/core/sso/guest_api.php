<?php
include_once 'library/guest_api.class.php';
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'GetRandNick':
		echo json_encode(Guest_api::getNick($json['Gender']));
		break;
	case 'SaveGuest':
		$guest_api = new Guest_api();
		$rst = $guest_api->guestRegister((array)$json['GuestInfo']);
		echo json_encode($rst);
		break;
	case 'GetRobot':
		$guest_api = new Guest_api();
		$rst = $guest_api->RobotRegister($json['Gender']);
		echo json_encode($rst);
		break;
	case 'GuestLogin':
		$guest_api = new Guest_api();
		$rst = $guest_api->guestLogin($param['Uin'],$param['SessionKey']);
		echo json_encode($rst);
		break;
	case 'EditNick':
		$guest_api = new Guest_api();
		$rst = $guest_api->editNick($param,$json['Nick']);
		echo json_encode($rst);
		break;
	default:
		exit('{"Flag":"101","FlagString":"接口不存在"}');
		break;
}
