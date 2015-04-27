<?php
include_once dirname(__FILE__).'/library/intentional_customer.class.php';

$json = $_POST['extparam'];
$param = $_POST['param'];
$intentionalCustomer = new IntentionalCustomer();

switch($json['Tag']){
	case 'SendCode4Reg':
		echo json_encode($intentionalCustomer->sendCode4Reg($json['Phone']));
		break;
	case 'IntendReg':
		echo json_encode($intentionalCustomer->IntendRegister($json['Data']));
		break;
	case 'GetPractice':
		echo json_encode($intentionalCustomer->getPractice($json['GroupId']));
		break;
}