<?php
require_once 'library/funds.class.php';
$funds = new funds();

$json = $_POST['extparam'];
$param = $_POST['param'];

switch ($json['Tag']) {
	case 'FundsList':
		echo json_encode($funds->findsList($json['Uin'], $json['GroupId'], $json['Template']));
		break;
	case 'ExchangeDetails':
		echo json_encode($funds->exchangeDetails($json['Uin'], $json['GroupId'], $json['RoomId'], $json['StartDate'], $json['EndDate'], $json['Template']));
		break;
	case 'GroupFunds':
		echo json_encode($funds->groupFunds($json['GroupId'], $json['RoomId'], $json['Template']));
		break;
	case 'FundsExchange':
		echo json_encode($funds->fundsExchange($json['Uin'], $json['GroupId'], $json['RoomId'], $json['RuleId'], $param['MoneyWeight'], $json['Template']));
		break;
}