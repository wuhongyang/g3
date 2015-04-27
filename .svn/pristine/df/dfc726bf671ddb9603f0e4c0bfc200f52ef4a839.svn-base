<?php
require_once 'library/role.class.php';
$json = $_POST['extparam'];
$param = $_POST['param'];
$role = new Role();

switch($json['Tag']){
// 	case 'UnbindRole':
// 		echo json_encode($role->unbindRole($json['Uin'],$json['RoleId']));
// 		break;
// 	case 'SetRole2User':
// 		echo json_encode($role->setRole2User($json['Uin'],$json['Role']));//$json['Roles'] 多个用逗号分隔
// 		break;
// 	case 'AddRole':
// 		echo json_encode($role->addRole($json['Data']));
// 		break;
// 	case 'UpdateRole':
// 		echo json_encode($role->updateRole($json['Uin'],$json['Roles']));
// 		break;
	case 'UinRole':
		echo json_encode($role->uinRole($json['GroupId']));
		break;	
	case 'UserRole':
		echo json_encode($role->userRole($json['Uin'],$json['GroupId'],$json['ChannelId']));
		break;
	case 'AddGroupRole':
		echo json_encode($role->addGroupRole($json['GroupId'],$json['Uin'],$json['RoleId'],$json['RoomId'],$json['NewGroup'],$json['Ruleid'],$json['ModuleId']));
		break;
	case 'DeleteGroupRole':
		echo json_encode($role->deleteGroupRole($json['GroupId'],$json['Uin'],$json['RoleId'],$json['RoomId']));
		break;
	case 'GetRole':
		echo json_encode($role->getRole((int)$json['GroupId'],(int)$json['ChannelId'],$json['Uin'],$json['RoleId'],$json['Ruleid'],$json['Category'],$json['Artist']));
		break;
}