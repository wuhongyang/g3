<?php
include_once 'library/room.class.php';

$json = $_POST['extparam'];
$room = new room($json['GroupId']);

switch($json['Tag']){
	case 'RoomsList':
		$roomId = $json['Id'];		
		echo json_encode($room->roomsList($json['SearchData'],$roomId));
		break;
	case 'Freeze':
		$id = $json['Id'];
		echo json_encode($room->freeze($id));
		break;
	case 'RoomUpdate':
		echo json_encode($room->roomUpdate($json));
		break;
	case 'RoomAdd':
		echo json_encode($room->roomAdd($json['Data']));
		break;
	case 'GetAllAreaClassify':
		echo json_encode($room->getAllAreaClassify());
		break;
	case 'SetRoomRec':
		echo json_encode($room->setRoomRec($json));
		break;
	case 'GetRoomRec':
		echo json_encode($room->getRoomRec($json));
		break;
    case 'UpdateRoomUi':
		echo json_encode($room->updateRoomUi($json['Data']));
		break;
    case 'DeleteRoomUi':
		echo json_encode($room->deleteRoomUi($json['Data']));
		break;
    case 'GetRoomsUi':
		echo json_encode($room->getRoomsUi($json['Data'],$json['Getpic'],$json['IsPage']));
		break;
    case 'UpdateUiPackage':
		echo json_encode($room->updateUiPackage($json['Data']));
		break;
    case 'DeleteUiPackage':
		echo json_encode($room->DeleteUiPackage($json['Data']));
		break;
    case 'GetUiPackage':
		echo json_encode($room->GetUiPackage($json['Data'],$json['Getpic']));
		break;
    case 'CopyUiPackage':
    	echo json_encode($room->CopyUiPackage($json['Id']));
    	break;
}