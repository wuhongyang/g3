<?php
require_once 'library/songs.class.php';
$songs = new Songs();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'SavePrice':
		echo json_encode($songs->save_price($json['GroupId'], $json['PickPrice'], $json['ActPercentage'], $json['TaxPercentage']));
		break;
	case 'GetPrice':
		echo json_encode($songs->get_price($json['GroupId']));
		break;
}