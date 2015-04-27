<?php
require_once 'library/group_manage.class.php';
require_once __ROOT__.'/api/service/library/pass_manager.class.php';
$gm = new GroupManage();
$json = $_POST['extparam'];
$param = $_POST['param'];
switch($json['Tag']){
	case 'GetGroupInfo':  	//查询站信息
		echo json_encode($gm->getGroupInfo($json['Uin'],$json['GroupId'],$json['IsDetails']));
		break;
	case 'GetGroupRooms':   //获取站下所有房间信息
		echo json_encode($gm->getGroupRooms($json['GroupId']));
		break;
	case 'GetGroupRoomsList':   //获取站下房间列表信息，带分页
		echo json_encode($gm->getGroupRoomsList($json, $json['no_page']));
		break;	
	case 'OpenRoom':   //站长开设房间
		echo json_encode($gm->openRoom($json['Data'], $param['GroupId']));
		break;
	case 'GetGroupRole':	//站内角色
		echo json_encode($gm->getGroupRole($param['GroupId'],$json['RoleShowOne'],$json['RoleShowTwo']));
		break;
	case 'GroupFlow':
		echo json_encode($gm->groupFlow($json['Data']));
		break;
	case 'GroupIncome':
		echo json_encode($gm->groupImcome($json['Data']));
		break; 
	case 'TaxDetials':
		echo json_encode($gm->taxDetails($json['Data']));
		break;
	case 'GetBalance':
		echo json_encode($gm->getBalance($json['Uin']?$json['Uin']:$param['Uin']));
		break;
	case "ExchangeDetails":
		echo json_encode($gm->exchangeDetails($json['Data']));
		break;
	case 'SignatoryDetails':
		echo json_encode($gm->signatoryDetails($json['Data']));
		break;
	case 'getBusinessRule'://获取积分规则
		echo json_encode($gm->getBusinessRule($param['GroupId']));
		break;
	case 'SaveRoleInfo':	//签约管理-赋予角色，添加修改角色
		echo json_encode($gm->saveRoleInfo($json['Data']));
		break;
	case 'GetRoleList':	//签约管理-赋予角色，列表
		echo json_encode($gm->getRoleList($json['GroupId'], $json['RoleIds'], $json['Data']));
		break;	
	case 'GetRoleInfo':	//查看站内代理
		echo json_encode($gm->getRoleInfo($json['Id'],$json['GroupId']));
		break;
	case 'RemoveRoleInfo':	//删除站内代理
		echo json_encode($gm->removeRoleInfo($json['Id'],$json['GroupId'],$json['PackageId'],$json['RoleId']));
		break;
	case 'IntegralSearch':
		echo json_encode($gm->integralSearch($json['Type'],$json['RuleId'],$json['Search'],$json['GroupId']));
		break;
	case 'SendMsg':
		echo json_encode($gm->sendMsg($json['Title'],$json['Content'],$json['GroupId']));
		break;
	case 'GetMsg':
		echo json_encode($gm->getMsg($json['LastId'],$json['GroupId']));
		break;
}