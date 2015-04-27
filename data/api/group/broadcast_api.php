<?php
require_once 'library/broadcast.class.php';
$broadcast = new Broadcast();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'GetPrice':
		echo json_encode($broadcast->getPrice($json['GroupId']));
		break;
	case 'SavePrice':
		echo json_encode($broadcast->savePrice($json['GroupId'], $json['Data']));
		break;
}