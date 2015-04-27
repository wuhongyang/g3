<?php
require_once 'library/recharge_order.class.php';
$recharge_order = new recharge_order();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'GetList':
		echo json_encode($recharge_order->get_list($json['Data']));
		break;
	case 'Remedy':
		echo json_encode($recharge_order->remedy($json['TradeId']));
		break;
}