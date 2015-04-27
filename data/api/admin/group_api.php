<?php
require_once dirname(__FILE__).'/library/group.class.php';
$group = new group();
$json = $_POST['extparam'];
switch ($json['Tag']) {
	case 'AddGroup' :  	//增加或更新群组,如果是插入 不需要将id写入
		echo json_encode($group->addGroup( $json['Data'] ));
		break;
	case 'EditGroup':
		echo json_encode($group->editGroup( $json['Data'] ));
		break;
	case 'listGroup' :	//查找所有群组
		echo json_encode($group->listGroup( $json['Data'] ));
		break;
    case 'listGroupUsed' :	//组列表
		echo json_encode($group->listGroupUsed());
		break;
	case 'GroupInfo':
		echo json_encode($group->GroupInfo( $json['Info'] ));
		break;
	case 'existGroup' :	//查看群号是否存在
		echo json_encode($group->existGroup( $json['mypost'] ));
		break;
	case 'Recommend' :	//查看群号是否存在
		echo json_encode($group->recommend( $json['Id'] ));
		break;
	case 'openNum' :	//修改房间额度
		echo json_encode($group->opennum( $json['Data'] ));
		break;
	case 'EditGame':
		echo json_encode($group->editGame( $json['Data'] ));
		break;
	case 'GameInterfaceList':
		echo json_encode($group->gameInterfaceList( $json['GroupId'] ));
		break;
	case 'GameInterfaceSave':
		echo json_encode($group->gameInterfaceSave( $json['Data'] ));
		break;
	case 'ExtInfo':
		echo json_encode($group->extInfo($json['GroupId']));
		break;
	case 'SyncInfo':
		echo json_encode($group->syncInfo($json['GroupId']));
		break;
/*	case 'addUser'	:	//群组添加成员
		echo json_encode($group->addUser( $json['mypost'] ));
		break;
	case 'delGroup' :	//删除群组
		echo json_encode($group->delGroup( $json['mypost'] ));
		break;
	case 'listOneGroup' :	//查找单个群组
		echo json_encode($group->listOneGroup( $json['mypost'] ));
		break;
	case 'upUser'   :	//组成员编辑
		echo json_encode($group->upUser( $json['mypost'] ));
		break;
	case 'delUser'  :	//组成员删除
		echo json_encode($group->delUser( $json['mypost'] ));
		break;	
	case 'listUser' :	//显示组成员
		echo json_encode($group->listUser($json['mypost']));
		break;
	case 'passUser' :	//通过群组成员
		echo json_encode($group->passUser( $json['mypost'] ));
		break;*/
	//================体验账号==============
	case 'PracticeAccountList':
		echo json_encode($group->practiceAccountList($json['GroupId'], $json['is_page']));
		break;
	case 'SavePracticeAccount':
		echo json_encode($group->savePracticeAccount($json['Id'], $json['GroupId'], $json['RoleName'], $json['Accounts'], $json['RoomId']));
		break;
	case 'DelPracticeAccount':
		echo json_encode($group->delPracticeAccount($json['Id']));
		break;
	case 'PracticeAccountDetail':
		echo json_encode($group->practiceAccountDetail($json['Id']));
		break;
	case 'UserIntention':
		echo json_encode($group->userIntention($json['StartTime'], $json['EndTime'], $json['GroupId']));
		break;
}

