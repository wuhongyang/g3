<?php
require_once 'library/navigate.class.php';
$n = new Navigate();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'NavigateList':
		echo json_encode($n->navigateList($json['GroupId']));
		break;
	case 'LeftMove':
		echo json_encode($n->leftMove($json['Id'],$json['GroupId']));
		break;
	case 'RightMove':
		echo json_encode($n->rightMove($json['Id'],$json['GroupId']));
		break;
	case 'NavigateInfo':
		echo json_encode($n->navigateInfo($json['Id'],$json['GroupId']));
		break;
	case 'NavigateAdd':
		echo json_encode($n->navigateAdd($json['Data']));
		break;
	case 'NavigateEdit':
		echo json_encode($n->navigateEdit($json['Id'],$json['GroupId'],$json['Data']));
		break;
	case 'Visible':
		echo json_encode($n->visible($json['Id'],$json['GroupId']));
		break;
}