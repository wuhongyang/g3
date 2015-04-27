<?php
require_once 'library/transit_sys.class.php';
require_once 'library/fund_factory.class.php';
require_once 'library/kmoney_sys.class.php';
require_once 'library/voucher_sys.class.php';
require_once 'library/kwealth_sys.class.php';
require_once 'library/kincome_sys.class.php';
$json = $_POST['extparam'];
$param = $_POST['param'];
$transit_sys = new transit_sys();

switch ($json['Tag']){
	case 'Commit':
		break;
	case 'Rollback':
		break;
	case 'Kmoney':
		$info = array();
		$info['consume_uin']  = (int)$param['Uin'];
		$info['receive_uin']  = (int)$param['TargetUin'];
		$info['channel_id']   = (int)$param['ChannelId'];
		$info['trade_money']  = (int)$param['MoneyWeight'];
		$info['bigcase_id']   = (int)$param['BigCaseId'];
		$info['case_id']      = (int)$param['CaseId'];
		$info['parent_id']    = (int)$param['ParentId'];
		$info['child_id']     = (int)$param['ChildId'];
		$info['tax_type']     = (int)$param['TaxType'];
		$info['group_id']     = (int)$json['GroupId'];
		$info['client']       = $param['Client'];
		$info['desc']         = $param['Desc'];
		$info['operator']     = $json['Operator'];
		$info['method']     = 'trade';
		$result = $transit_sys->trade($info);
		echo json_encode($result);
		break;
	case 'Kwealth':
	case 'Kincome':
	case 'Kownincome':
		$info = array();
		$info['consume_uin']  = (int)$param['Uin'];
		$info['receive_uin']  = (int)$param['TargetUin'];
		$info['channel_id']   = (int)$param['ChannelId'];
		$info['trade_money']  = (int)$param['MoneyWeight'];
		$info['bigcase_id']   = (int)$param['BigCaseId'];
		$info['case_id']      = (int)$param['CaseId'];
		$info['parent_id']    = (int)$param['ParentId'];
		$info['child_id']     = (int)$param['ChildId'];
		$info['tax_type']     = (int)$param['TaxType'];
		$info['group_id']     = (int)$json['GroupId'];
		$info['client']       = $param['Client'];
		$info['desc']         = $param['Desc'];
		$info['operator']     = $json['Operator'];
		$info['method']     = 'trade';
		$info['tag']     = $json['Tag'];
		$result = $transit_sys->trade($info);
		echo json_encode($result);
		break;
	case 'TaxTrade':
		$info = array();
		$info['consume_uin']  = (int)$param['Uin'];
		$info['receive_uin']  = (int)$param['TargetUin'];
		$info['channel_id']   = (int)$param['ChannelId'];
		$info['trade_money']  = (int)$param['MoneyWeight'];
		$info['bigcase_id']   = (int)$param['BigCaseId'];
		$info['case_id']      = (int)$param['CaseId'];
		$info['parent_id']    = (int)$param['ParentId'];
		$info['child_id']     = (int)$param['ChildId'];
		$info['tax_type']     = (int)$param['TaxType'];
		$info['group_id']     = (int)$json['GroupId'];
		$info['client']       = $param['Client'];
		$info['desc']         = $param['Desc'];
		$info['operator']     = $json['Operator'];
		$info['method']     = 'TaxTrade';
		$result = $transit_sys->trade($info);
		echo json_encode($result);
		break;
	case 'GetKmoneyBalance': //获取V豆余额
		$info['uin'] = (int)$json['Uin'];
		$info['group_id']     = (int)$json['GroupId'];
		$info['method']     = 'getLastBalance';
		echo json_encode($transit_sys->trade($info));
		break;
	case 'GetTaxBalance': //获取站税收余额
		$info['group_id']     = (int)$json['GroupId'];
		$info['method']     = 'getTaxBalance';
		echo json_encode($transit_sys->trade($info));
		break;
	case 'GetBusinessBalance': //获取三级科目余额
		$info['bigcaseid']  = (int)$param['BigCaseId'];
		$info['caseid']     = (int)$param['CaseId'];
		$info['parentid']   = (int)$param['ParentId'];
		$info['group_id']	= (int)$json['GroupId'];
		$info['method']     = 'getBusinessBalance';
		echo json_encode($transit_sys->trade($info));
		break;
	default :
		exit('{"Flag":101,"FlagString":"不存在的接口模块!!!!!!"}');
		break;
}
