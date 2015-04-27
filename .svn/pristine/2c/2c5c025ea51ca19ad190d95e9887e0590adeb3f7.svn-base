<?php
require_once 'library/kbasic.class.php';
$kbasic = new kbasic();
$json = $_POST['extparam'];

switch ($json['Tag']) {
// 	case 'GetLuckStarBalance':
// 		echo json_encode($kbasic->getLuckStarBalance($json['Uin']));
// 		break;
	// case 'AddLuckStarBalance':
	// 	echo json_encode($kbasic->addLuckStarBalance($json['Uin'],$json['Num']));
	// 	break;
// 	case 'CutLuckStarBalance':
// 		echo json_encode($kbasic->cutLuckStarBalance($json['Uin'],$json['Num']));
// 		break;
	case 'GetUserInfo' : // 获取用户信息[{"Tag":"GetUserInfo","Uin":"123456"}]
		echo json_encode($kbasic->getUserInfo($json['Uin']));
		break;
	// case 'SetUserInfo' : // 更新用户信息[{"Tag":"SetUserInfo","Uin":"123456","Vip":"1","Level":"1","Votes":"1","UseVotes":"1","Prefix":"his"}]
	// 	echo json_encode($kbasic->set_uininfo($json));
	// 	break;
	// case 'UpdateGrade' : // 更新等级信息[{"Tag":"UpdateGrade","Uin":"123456","GradeDate":"1","BeginDate":"1","EndDate":"1"}]
	// 	$Uin = $json['Uin'];
	// 	$GradeDate = $json['GradeDate'];
	// 	$BeginDate = $json['BeginDate'];
	// 	$EndDate = $json['EndDate'];
	// 	echo json_encode($kbasic->update_grade($Uin,$GradeDate,$BeginDate,$EndDate));
	// 	break;
	// case 'SetUserVip' : // 设置用户VIP
	// 	$Uin		= $json['Uin'];
	// 	$VipUin		= $json['VipUin'];
	// 	$VipGrade	= $json['VipGrade'];
	// 	$UseExpire	= $json['UseExpire'];
	// 	$BuyExpense	= $json['BuyExpense'];
	// 	$PayExpense	= $json['PayExpense'];
	// 	echo json_encode($kbasic->set_uinvip($Uin,$VipUin,$VipGrade,$UseExpire,$BuyExpense,$PayExpense));
	// 	break;
	// case 'CheckVip':
	// 	echo json_encode($kbasic->checkVip($json['VipUin'],$json['VipGrade']));
	// 	break;
	case 'GetRolesIcon':
		echo json_encode($kbasic->getRolesIcon($json['Uin']));
		break;
}
