<?php
require dirname(__FILE__).'/library/kmoney.class.php';
$json = $_POST['extparam'];
$kmoney = new kmoney($json['Data']['Group_id']);

switch($json['Tag']){
	case 'Fund_system':
		$array = $kmoney->FundSystem($json);
		echo json_encode($array);
		break;
	case 'Fund_system_statistics':
		$array = $kmoney->FundSystemStatistics($json);
		echo json_encode($array);
		break;	
	case 'Finance_margin':
		$array = $kmoney->FinanceMargin($json);
		echo json_encode($array);
		break;
	case 'Finance_group_margin':
		$array = $kmoney->FinanceGroupMargin($json);
		echo json_encode($array);
		break;
	case 'Finance_abnormal_running':
		$array = $kmoney->FinanceAbnormalRunning($json);
		echo json_encode($array);
		break;
	case 'Finance_manage':
		$array = $kmoney->FinanceManage($json);
		echo json_encode($array);
		break;
}
