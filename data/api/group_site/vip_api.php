<?php
include_once dirname(__FILE__).'/library/vip.class.php';
$vip = new Vip();

$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	// case 'BuyVip':
		// $array = $vip->buyVip($json['Data']);
		// echo json_encode($array);
		// break;
	// case 'GetVipPrice':
		// $array = $vip->getVipPrice($json['GroupId'],$param['Uin']);
		// echo json_encode($array);
		// break;
	// case 'GetVipInfo':
		// $array = $vip->getVipInfo($json['GroupId'],$param['Uin']);
		// echo json_encode($array);
		// break;
	// case 'IsVipCanBuy':
		// $array = $vip->isVipCanBuy($json['GroupId'],$param['Uin'],$json['Price']);
		// echo json_encode($array);
		// break;
}