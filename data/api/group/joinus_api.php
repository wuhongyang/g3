<?php
require_once 'library/joinus.class.php';
$j = new Joinus();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'JoinList':
		echo json_encode($j->joinList($json['GroupId']));
		break;
	case 'JoinInfo':
		echo json_encode($j->joinInfo($json['GroupId'],$json['Role']));
		break;
	case 'JoinEdit':
		echo json_encode($j->joinEdit($json['Data']));
		break;
	case 'JoinDel':
		echo json_encode($j->joinDel($json['GroupId'],$json['id']));
		break;
}