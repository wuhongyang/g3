<?php
include 'library/create_account.class.php';

$create_account = new createAccount();
$json  = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'create_single':
		echo json_encode($create_account -> create_single($json['uin']));
		break;
	case 'create_range':
		echo json_encode($create_account -> create_group($json['range']));
		break;
		break;
}
