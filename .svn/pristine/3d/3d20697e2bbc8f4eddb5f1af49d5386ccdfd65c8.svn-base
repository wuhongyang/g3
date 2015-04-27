<?php
require_once 'common.php';

$style = $_GET['style'];
$money = $_GET['recharge_money'];
$pay_id = intval($_GET['PayId']);
$groupId = intval($_GET['group_id']);
$recharge_style = array('index'=>'网银','tenpay'=>'财付通','mobilecard'=>'手机充值卡','gamecard'=>'游戏卡','vdouexchange'=>'金豆兑换', 'alipay'=>'支付宝');

switch ($style) {
	case 'tenpay':
		$rebate = 1;
		$type = 'tenpay';
		$recharge_name = '财付通';
		break;
	case 'chinabank':
		$rebate = 1;
		$type = 'index';
		$recharge_name = '网银在线';
		break;
	case 'alipay':
		$rebate = 1;
		$type = 'alipay';
		$recharge_name = '支付宝';
		break;
	default:
		if(in_array($pay_id, array(1,2,3))){
			$rebate = 0.9;
			$type = 'mobilecard';
			$recharge_name = '手机卡充值';
		}else{
			$rebate = 0.8;
			$type = 'gamecard';
			$recharge_name = '游戏卡充值';
		}
		break;
}
$vdian = $money * $rebate;
$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/shop/';
$tmp_config['cache_dir'].=$themes.'/tpl/shop/';
$tpl = template::getInstance();
$tpl->setOptions($tmp_config);
include template('index/success.html',$tpl);