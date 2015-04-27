<?php
include_once 'library/voucher.class.php';
$voucher = voucher::instance();
$json = $_POST['extparam'];
$param = $_POST['param'];
switch($json['Tag']){
	case 'VoucherBalance':
		echo json_encode($voucher->VoucherBalance($json));//
		break;
	case 'VoucherRunning':
		echo json_encode($voucher->VoucherRunning($json));
		break;
	case 'VoucherParent':
		echo json_encode($voucher->VoucherParent($json));
		break;
	case 'TaxRecharge':
		echo json_encode($voucher->TaxRecharge($param,$json));
		break;
	case 'VipRecharge':
		echo json_encode($voucher->VipRecharge($param,$json));
		break;
	case 'VipDeduct':
		echo json_encode($voucher->VipDeduct($param,$json));
		break;
	case 'GetChannelTax':
		echo json_encode($voucher->GetChannelTax($json));
		break;
	case 'Account_balacne':
		echo json_encode($voucher->Account_balacne($json));
		break;
	default:
		exit('{"Flag":101,"FlagString":"不存在的接口模块"}');
		break;
}