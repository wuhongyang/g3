<?php
require_once 'library/tax.class.php';
$tax = new Tax();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'TaxDetail':
		echo json_encode($tax->taxDetail($json['Uin'], $json['GroupId'], $json['CaseId'], $json['ParentId'], $json['ChildId'], $json['StartTime'], $json['EndTime']));
		break;
	case 'GetBalance':
		echo json_encode($tax->getBalance($json['Uin'], $json['GroupId']));
		break;
}