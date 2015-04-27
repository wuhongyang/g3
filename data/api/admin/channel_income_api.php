<?php
include 'library/RMB.class.php';

$RMB = new RMB();
$param = $_POST['param'];
$json = $_POST['extparam'];
switch($json['Tag']){
	case 'RmbAdd':
		echo json_encode($RMB->rmbAdd($param['ParentId'],$param['ChildId'],$json['Data']));
		break;
}