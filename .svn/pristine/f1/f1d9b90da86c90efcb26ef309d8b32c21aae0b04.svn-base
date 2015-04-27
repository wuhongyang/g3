<?php
require_once 'library/room_manage.class.php';
require_once dirname(dirname(dirname(__FILE__))).'/api/service/library/pass_manager.class.php';
$room_manage = new RoomManage();
$json = $_POST['extparam'];
$p = $_POST['param'];

switch($json['Tag']){
	case 'GetRoomUi':
		echo json_encode($room_manage->getRoomUi($json['RoomsUi']));
		break;
	case 'SetRoomUi':
		echo json_encode($room_manage->setRoomUi($json['RoomUi'],$json['TemplateUi'],$json['Roomid'],$p['GroupId']));
		break;
	case 'RoomInfo':
		echo json_encode($room_manage->roomInfo($json['RoomId'], $p['GroupId']));
		break;
	/*
	case 'GetUiPackage':
		echo json_encode($room_manage->getUiPackage($json['Data'],$json['Roomid'],$p['GroupId']));
		break;
	case 'SetUiPackage':
		echo json_encode($room_manage->setUiPackage($json['uiid'],$json['roomid'],$p['GroupId']));
		break;
	*/
	case 'GetRoomRanks':
		echo json_encode($room_manage->GetRoomRanks($p['ChannelId']));
		break;
	case 'SetRoomRanks':
		echo json_encode($room_manage->SetRoomRanks($p['ChannelId'],$json['Ranks']));
		break;
	case 'GetUserRooms':
		echo json_encode($room_manage->getUserRooms($json['Uin']));
		break;
	case 'getRoomInfo':
		echo json_encode($room_manage->getRoomInfo($json['Uin'],$json['RoomId']));
		break;
	case 'saveRoomInfo':
		$param = array();
		$param['name'] 		  = $json['name'];
		$param['description'] = $json['description'];
		$param['salutatory']  = $json['salutatory'];
		$param['room_id']	  = $json['room_id'];
		$param['bgalign']	  = $json['bgalign'];
		$param['type']		  = $json['type'];
		$param['robot_base_num']	  = $json['robot_base_num'];
		$param['robot_num']	  = $json['robot_num'];
		$param['Uin'] 		  = $json['Uin'];
		echo json_encode($room_manage->saveRoomInfo($param));
		break;
	case 'getEnterInfo':
		echo json_encode($room_manage->getEnterInfo($json['RoomId']));
		break;
	case 'saveEnterInfo':
		$param = array();
		$param['Uin'] 			= $json['Uin'];
		$param['status']		= $json['status'];
		$param['passwd']    	= $json['passwd'];
		$param['member_value']  = $json['member_value'];
		$param['deny_value'] 	= $json['deny_value'];
		$param['room_id']	    = $json['room_id'];
		echo json_encode($room_manage->saveEnterInfo($param));
		break;
	case 'getOrderInfo':
		echo json_encode($room_manage->getOrderInfo($json['RoomId']));
		break;
	case 'saveOrderInfo':
		$param = array();
		$param['Uin'] 				= $json['Uin'];
		$param['room_id'] 			= $json['room_id'];
		$param['mike_power']		= $json['mike_power'];
		$param['member'] 			= $json['member'];
		$param['main_video_time'] 	= $json['main_video_time'];
		echo json_encode($room_manage->saveOrderInfo($param));
		break;
	case 'getManagerInfo':
		echo json_encode($room_manage->getManagerInfo($json['RoomId']));
		break;
	case 'saveManagerInfo':
		$param = array();
		$param['Uin'] 		= $json['Uin'];
		$param['room_id'] 	= $json['room_id'];
		$param['member']	= $json['member'];
		$param['role_id']	= $json['role_id'];
		echo json_encode($room_manage->saveManagerInfo($param));
		break;
	case 'getReleaseIDInfo':
		echo json_encode($room_manage->getReleaseInfo($json['RoomId'],"id"));
		break;
	case 'getReleaseIPInfo':
		echo json_encode($room_manage->getReleaseInfo($json['RoomId'],"ip"));
		break;
	case 'releaseID':
		$param = array();
		$param['Uin'] 		= $json['Uin'];
		$param['id'] 	= $json['id'];
		echo json_encode($room_manage->release($param, "id"));
		break;
	case 'releaseIP':
		$param = array();
		$param['Uin'] 		= $json['Uin'];
		$param['id'] 	= $json['id'];
		echo json_encode($room_manage->release($param, "ip"));
		break;
	case 'addDenyID':
		$param = array();
		$param['Uin'] 			= $p['Uin'];
		$param['room_id'] 		= $json['room_id'];
		$param['kick_id']		= $json['kick_id'];
		$param['kick_nick'] 	= $json['kick_nick'];
		$param['operator_id']	= $json['operator_id'];
		$param['operator_nick'] = $json['operator_nick'];
		echo json_encode($room_manage->addDeny($param, "id"));
		break;
	case 'addDenyIP':
		$param = array();
		$param['Uin'] 			= $p['Uin'];
		$param['room_id'] 		= $json['room_id'];
		$param['kick_ip']		= $json['kick_ip'];
		$param['kick_id']		= $json['kick_id'];
		$param['kick_nick'] 	= $json['kick_nick'];
		$param['operator_id']	= $json['operator_id'];
		$param['operator_nick'] = $json['operator_nick'];
		echo json_encode($room_manage->addDeny($param, "ip"));
		break;
	case 'getRoomStatus':
		$param['ownuin'] = $json['ownuin'];
		echo json_encode($room_manage->getRoomStatus($param));
		break;
	case 'getRoomNotice':
		$param['Roomid'] = $json['Roomid'];
		echo json_encode($room_manage->getRoomNotice($param));
		break;
	case 'setRoomNotice':
		$param['Roomid'] = $json['Roomid'];
		$param['RoomNotice'] = $json['RoomNotice'];
		echo json_encode($room_manage->setRoomNotice($param));
		break;
	case 'GetRoomStatistics':
		echo json_encode($room_manage->getRoomStatistics($json['RoomId'],$json['Data']));
		break;	
}