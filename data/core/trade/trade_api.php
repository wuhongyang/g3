<?php
require_once 'library/trade.class.php';
$trade = new trade();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch ($json['Tag']){
	case 'GetTradeInfo' :	//查询订单信息
		$TradeId	= $json['TradeId'];
		$TradeFee	= $param['MoneyWeight'];
		echo json_encode($trade->getTradeInfo($TradeId,$TradeFee));
		break;
	case 'UpdateTrade' :	//更新订单信息
		$TradeId	= $json['TradeId'];
		$Status	= $json['Status']?$json['Status']:1;
		$TradeFee	= $param['MoneyWeight'];
		echo json_encode($trade->updateTrade($TradeId,$TradeFee,$Status));
		break;
// 	case 'GetTradeExist' :	//查看订单是否存在
// 		$TradeId	= $json['TradeId'];
// 		$TradeFee	= $param['MoneyWeight'];
// 		echo json_encode($trade->getTradeExist($TradeId,$TradeFee));
// 		break;
	case 'SubmitTrade' :	//提交订单
		$PayUin		= $param['Uin'];//付款id
		$Uin		= $param['TargetUin'];//接收id
		$ParentId	= $param['ParentId'];
		$ChildId	= $param['ChildId'];	
		$TradeFee	= $param['MoneyWeight'];
		$Rebate		= $json['Rebate'];
		$GroupId		= $json['GroupId'];
		$PayId		= $json['PayId'];//充值方式
		$ChannelId		= $json['ChannelId'];//房间id
		$Callback		= $json['Callback'];//业务回调接口地址
		$Element		= $json['Element'];//业务回调接口参数
		echo json_encode($trade->submitTrade($PayUin,$Uin, $ParentId, $ChildId, $TradeFee, $Rebate,$GroupId,$PayId,$ChannelId,$Callback,$Element));
		break;
	default :
		exit('{"Flag":101,"FlagString":"不存在的接口模块"}');
		break;
}

