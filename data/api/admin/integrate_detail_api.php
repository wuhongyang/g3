<?php
include 'library/integrate_detail.class.php';
include_once dirname(__FILE__).'/library/rule_define.class.php';

$json = $_POST['extparam'];
$ruleDefine = new RuleDefine();
$integrate_detail = new IntegrateDetail($ruleDefine,$json['DataGroupId']);

//没有办法
switch($json['Tag']){
	case 'DetailList':
		echo json_encode($integrate_detail->detailList($json['SearchData']));
		break;
	case 'SummaryList':
		echo json_encode($integrate_detail->summaryList($json['SearchData']));
		break;
}