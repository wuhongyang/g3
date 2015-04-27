<?php
require_once 'library/kwealth_sys.class.php';
$kwealth_sys = kwealth_sys::instance();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch ($json['Tag']){
	case 'Commit':
	//	$kwealth_sys->commit();
		break;
	case 'Rollback':
	//	$kwealth_sys->rollback();
		break;
	case 'Kwealth':
		$info = array();
		$info['consume_uin']  = (int)$param['Uin'];
		$info['receive_uin']  = (int)$param['TargetUin'];
		$info['channel_id']   = (int)$param['ChannelId'];
		$info['trade_money']  = (float)$param['MoneyWeight'];
		$info['bigcase_id']   = (int)$param['BigCaseId'];
		$info['case_id']	  = (int)$param['CaseId'];
		$info['parent_id']    = (int)$param['ParentId'];
		$info['child_id']     = (int)$param['ChildId'];
		$info['client']       = $param['Client'];
		$info['desc']         = $param['Desc'];
		$info['operator']     = $json['Operator'];
		$result = $kwealth_sys->trade($info);
		echo json_encode($result);
		break;
	case 'GetKwealthBalance': //获取V宝余额
		$Uin = $json['Uin'];
		echo json_encode($kwealth_sys->getLastBalance($Uin));
		break;
	case 'GetBusinessBalance': //获取三级科目余额
		$BigCaseId  = $param['BigCaseId'];
		$CaseId     = $param['CaseId'];
		$ParentId   = $param['ParentId'];
		echo json_encode($kwealth_sys->getBusinessBalance($BigCaseId,$CaseId,$ParentId));
		break;
	default :
		exit('{"Flag":101,"FlagString":"不存在的接口模块"}');
		break;
}
