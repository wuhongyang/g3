<?php
include 'library/proxy.class.php';
$proxy = proxy::instance();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'LowestWeightSet':
		echo json_encode($proxy->lowestWeightSet($json['Data']));//
		break;
}