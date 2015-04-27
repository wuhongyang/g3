<?php
include_once dirname(__FILE__).'/library/shop.class.php';
$shop = new shop();

$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'WebPay'	:	//Web支付V宝充值
		$post = $json['mypost'];
		$array = $shop->webPay($post, $param);
		echo json_encode($array);
		break;
	case 'AlipayNotify' :	//支付宝异步请求
		$array = $shop->alipayNotify($param,$json);
		echo json_encode($array);
		break;
	case 'AlipayReturn' :	//支付宝同步请求
		$array = $shop->alipayReturn($param,$json);
		echo json_encode($array);
		break;
	case 'ChinabankAuto' :	//网银在线auto自动接收请求
		$array = $shop->chinabankAuto($param,$json);
		echo json_encode($array);
		break;
	case 'ChinabankReceive' ://网银在线receive请求
		$array = $shop->chinabankReceive($param,$json);
		echo json_encode($array);
		break;
	case 'Tenpay'	: //财付通回调请求
		$array = $shop->Tenpay($param,$json);
		echo json_encode($array);
		break;
	case 'BaofooReceive'	: //财付通回调请求
		$array = $shop->BaofooReceive($param,$json);
		echo json_encode($array);
		break;
	case 'SubmitTrade' :	//生成订单
		$array = $shop->submitTrade($json, $param);
		echo json_encode($array);
		break;
}