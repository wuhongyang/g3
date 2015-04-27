<?php
include_once dirname(dirname(dirname(__FILE__))).'/library/global.fun.php';
include_once dirname(__FILE__).'/library/passport_factory.class.php';

//group_id,group_name

$json = $_POST['extparam'];
$param = $_POST['param'];

/*
$json['Type'] = 3;
$json['Data'] = array(
	'username' =>'abb',
	'password' => 123456,
	'repassword' => 123456,
	'group_id' => 5711238,
	'group_name' => '二样二样的',
	'type' => 3
	);
*/
$passportFactory = PassportFactory::getInstance($json['Type']);

//$rst = $passportFactory->register($json['Data']);
//$rst = $passportFactory->active('b51b%2BMuLcg3fj%2Fd5fVHnK4eAKocwtSzqKOsRrKPuyp0j45QzVk7SmT8blBnoezKGQqZ22QoaxyI%2F%2BX4QK1mwtHd%2BwLkuJ5lKpnfX%2FbG3g8zoFYvIOyiRbPfKdfuPfIx8qS%2F7jbW%2FG0NYDE1h4sDQbISRhnj20UcGPm%2FubjHPGsb31o6QeB1j7JJwkj6UJ9Kwf%2BO6WY26Zcgnjkt6yJwKuJoNWQ');
switch($json['Tag']){
	case 'Register': 
		$array = $passportFactory->register($json['Data']);
		break;
	case 'Login':
		$array = $passportFactory->login($json['Data']);
		break;
	case 'UserLogOut':
		$array = $passportFactory->userLogOut();
		break;
	case 'ResetPassword':
		$array = $passportFactory->resetPassword($json['User'],$json['Data']);
		break;
	case 'Active': //邮箱注册真正的注册
		$array = $passportFactory->active($json['Data']);
		break;
	case 'SendCode4Reg':
		$array = $passportFactory->sendCode4Reg($json['Phone']);
		break;
	case 'Forget':
		$array = $passportFactory->forget($json['Data']);
		break;
	case 'UserExist':
		$array = $passportFactory->userExist($json['User']);
		break;
	case 'GetPasswdByEmail':
		$array = $passportFactory->getPasswdByEmail($json['UserName']);
		break;
	case 'NewpassIndex':
		$array = $passportFactory->newpassIndex($json['UserName']);
		break;
	case 'GetBackPassword':
		$array = $passportFactory->getBackPassword($json['Data']);
		break;
	case 'GetPasswdByPhone':
		$array = $passportFactory->getPasswdByPhone($json['Data']);
		break;
	case 'SendCode4GetPwd':
		$array = $passportFactory->sendCode4GetPwd($json['Phone']);
		break;
}

echo json_encode($array);