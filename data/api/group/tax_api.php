<?php
require_once 'library/tax.class.php';
require_once dirname(dirname(__FILE__)).'/service/library/tax.class.php';
$group_tax = new GroupTax();
$tax = new Tax();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'GetBalance':
		echo json_encode($tax->getBalance($json['Uin'], $json['GroupId']));
		break;
	case 'TaxDetail':
		echo json_encode($tax->taxDetail($json['Uin'], $json['GroupId'], $json['CaseId'], $json['ParentId'], $json['ChildId'], $json['StartTime'], $json['EndTime'], $json['Ruleid']));
		break;
	case 'Exchange':
		echo json_encode($group_tax->exchange($param, $json['RMBWeight']));
		break;
	case 'KmoneyExchange':
		echo json_encode($group_tax->kmoneyExchange($param, $json['RMBWeight']));
		break;
}