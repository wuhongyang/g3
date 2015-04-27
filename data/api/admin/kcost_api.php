<?php
if (!class_exists('kcost')){
	require dirname(__FILE__).'/library/kcost.class.php';
}
require_once dirname(__FILE__).'/library/group.class.php';

$kcost = new kcost();
$json  = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'Charge':
		echo json_encode($kcost -> Charge($json));
		break;
	case 'PCharge':
		echo json_encode($kcost -> PCharge($json));
		break;
	case 'UCharge':
		echo json_encode($kcost -> UCharge($json));
		break;
	case 'MCharge':
		echo json_encode($kcost -> MCharge($json));
		break;
}
