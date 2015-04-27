<?php
require_once 'library/usermanage.class.php';
$json = $_POST['extparam'];
$user = new usermanage($json['GroupId']);

switch ($json['Tag']) {
	case 'ShowUserMessage':		//显示用户信息
		echo json_encode($user->showUserMessage( $json['mypost'] ));
		break;
	case 'GetUserDetail':		//获得对应用户详细信息
		echo json_encode($user->getUserDetail( $json['mypost'] ));
		break;
	case 'GetUserBankInfo':
		echo json_encode($user->getUserBankInfo( $json['Uin'] ));
		break;
}

