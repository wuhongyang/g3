<?php
require_once 'library/ccs.class.php';
require_once 'library/common_child_config.class.php';

$json = $_POST['extparam'];
$param = $_POST['param'];
$ccs = new ccs();
$childConfig = new CommonChildConfig();
switch($json['Tag']){
	case 'CheckUserPower':
		echo json_encode($ccs->CheckUserPower($param));
		break;
	case 'GetBusinessConfig':
		echo json_encode($ccs->getBusinessConfig($param['BigCaseId'],$param['CaseId'],$param['ParentId'],$param['ChildId'],$json['CheckBind']));
		break;
	case 'GetBusinessInfo':
		echo json_encode($ccs->getBusinessInfo($json['BigCaseId'],$json['CaseId'],$json['ParentId'],$json['ChildId']));
		break;
	case 'SetBigCase':
		echo json_encode($ccs->setBigCase($json['Post']));
		break;
	case 'SetCase':
		echo json_encode($ccs->setCase($json['Post']));
		break;
	case 'SetParent':
		echo json_encode($ccs->setParent($json['Post']));
		break;
	case 'SetChild':
		echo json_encode($ccs->setChild($json['Post']));
		break;
	case 'GetBigCase':
		echo json_encode($ccs->getBigCase($json['BigCaseId']));
		break;
	case 'GetCase':
		echo json_encode($ccs->getCase($json['CaseId']));
		break;
	case 'GetParent':
		echo json_encode($ccs->getParent($json['ParentId']));
		break;
	case 'GetChild':
		echo json_encode($ccs->getChild($json['ChildId']));
		break;
	case 'GetBigCaseList':
		echo json_encode($ccs->getBigCaseList($json['Param']));
		break;
	case 'GetCaseList':
		echo json_encode($ccs->getCaseList($json['Param']));
		break;
	case 'GetParentList':
		echo json_encode($ccs->getParentList($json['Param']));
		break;
	case 'GetChildList':
		echo json_encode($ccs->getChildList($json['Param']));
		break;
	case 'GetAdminLeftMenu':
		echo json_encode($ccs->getAdminLeftMenu($json['Levels'],$json['Gid'],$json['Menu']));
		break;
	case 'ChildSync':
		echo json_encode($ccs->childSync($json['Ids']));
		break;
	case 'GetUseBigCase':
		echo json_encode($ccs->getUseBigCase());
		break;
	case 'GetUseCase':
		echo json_encode($ccs->getUseCase($json['BigCaseId']));
		break;
	case 'GetUseParent':
		echo json_encode($ccs->getUseParent($json['CaseId']));
		break;
	case 'GetUseChild':
		echo json_encode($ccs->getUseChild($json['ParentId']));
		break;
	case 'CaseOrder':
		echo json_encode($ccs->setCaseOrder($json['Post']));
		break;
	case 'GetFlashCMD':
		echo json_encode($ccs->getFlashCMD($json['BigCaseId'],$json['CaseId'],$json['ParentId']));
		break;
	case 'GetPowerInfo'://取得有权限控制的科目详情
		echo json_encode($ccs->getPowerInfo());
		break;
	case 'GetInfoUnderBig':
		echo json_encode($ccs->getInfoUnderBig($json['BigCaseId']));
		break;
	case 'CommonChildList':
		echo json_encode($childConfig->commonChildList($json['SearchData']));
		break;
	case 'CommonChildInfo':
		echo json_encode($childConfig->commonChildInfo($json['Id']));
		break;
	case 'CommonChildAdd':
		echo json_encode($childConfig->commonChildAdd($json['Data']));
		break;
	case 'CommonChildUpdate':
		echo json_encode($childConfig->commonChildUpdate($json['Data']));
		break;
	case 'CommonChildSync':
		echo json_encode($childConfig->commonChildSync($json['Ids']));
		break;
	case 'SCSave':
		echo json_encode($ccs->sc_save($json['Data']));
		break;
	case 'SCList':
		echo json_encode($ccs->sc_get_list());
		break;
	case 'SCList2':
		echo json_encode($ccs->sc_get_list2($json['GroupId']));
		break;
	case 'SCSave2':
		echo json_encode($ccs->sc_save2($json['Data']));
		break;
	case 'SCGet':
		echo json_encode($ccs->sc_get($json['Data'])); 
		break;
	case 'Charge':
		echo json_encode($ccs->Charge($json));
		break;
	case 'Close':
		echo json_encode($ccs->Close($json));
		break;
	case 'SCBalanceAdd':
		echo json_encode($ccs->sc_balance_add($json['Data']));
		break;
	case 'SCBalanceGet':
		echo json_encode($ccs->sc_balance_get($json['Data']));
		break;
}