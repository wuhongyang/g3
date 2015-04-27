<?php
require_once 'library/usersafe.class.php';
$json = $_POST['extparam'];
$param = $_POST['param'];
$user = new usersafe();
switch ($json['Tag']) {
	case 'EditPwd':		//密码修改
		echo json_encode($user->editPwd( $param['Uin'] , $json ));
		break;
	case 'UserRenzheng':	//用户认证
		echo json_encode($user->userRenzheng($json['Uin'],$json['Data']));
		break;
	case 'EmailValida' :	//发送邮件
		echo json_encode($user->emailValida( $json['mypost'] ));
		break;
	case 'BindEmail'  :	//验证邮箱
		echo json_encode($user->bindEmail( $json['mypost'] ));
		break;
	case 'SendPhoneCode' :	//发送短信
		echo json_encode($user->sendPhoneCode( $json['mypost'] ));
		break;
	case 'BindPhone'  :	//绑定用户手机号码
		echo json_encode($user->bindPhone( $json['mypost'] ));
		break;
	case 'ChangePhone' :	//修改用户手机号码
		echo json_encode($user->ChangePhone( $json['mypost'] ));
		break;
	case 'EmailChange' :	//修改邮箱绑定账号
		echo json_encode($user->changeEmail( $json['mypost'] ));
		break;
	case 'EmailChangePhone':	//通过邮箱修改手机号码
		echo json_encode($user->emailChangePhone( $json['mypost'] ));
		break;
	case 'PhoneChangeEmail':    //通过手机修改邮箱
		echo json_encode($user->phoneChangeEmail( $json['mypost'] ));
		break;
	//case 'YzEmail' :	//判断邮箱是否存在
	//	echo json_encode($user->yzEmail( $json['mypost'] ));
	//	break;
	//case 'YzPhone'	:	//判断手机号码是否存在
	//	echo json_encode($user->yzPhone( $json['mypost'] ));
	//	break;
}




