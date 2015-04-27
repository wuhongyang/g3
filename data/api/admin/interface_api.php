<?php
include 'library/interfaces.class.php';
$interface = new Interfaces();
$json = $_POST['extparam'];

switch($json['Tag']){
	case 'GetBusinessInterface':
		echo json_encode($interface->getBusinessInterface());
		break;
	case 'GetReadInterface':
		echo json_encode($interface->getReadInterface());
		break;
	case 'GetWriteInterface':
		echo json_encode($interface->getWriteInterface());
		break;
}