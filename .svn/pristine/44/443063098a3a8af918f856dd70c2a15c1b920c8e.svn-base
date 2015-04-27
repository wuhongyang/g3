<?php
require_once dirname(__FILE__).'/library/permission.class.php';

$p = new Permission();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'DpUserLogin':
		echo json_encode($p->dpUserLogin($json['Data']));
		break;
	case 'RoleList':
		echo json_encode($p->roleList($json['GroupId']));
		break;
	case 'RoleInfo':
		echo json_encode($p->roleInfo($json['Id']));
		break;
	case 'VerifyUin':
		echo json_encode($p->verifyUin($json['Uin']));
		break;
	case 'UpdateRole':
		echo json_encode($p->updateRole($json['Data']));
		break;
	case 'InsertRole':
		echo json_encode($p->insertRole($json['Data']));
		break;
	case 'PermissionInfo':
		echo json_encode($p->permissionInfo($json['Id'],$json['GroupId']));
		break;
	case 'SetPermission':
		echo json_encode($p->setPermission($json['Data']));
		break;
	case 'OwnPermissions':
		echo json_encode($p->ownPermissions($json['Uin']));
		break;
	default:
		echo json_encode(array('Flag'=>101,'FlagString'=>'非法请求'));
		break;
}