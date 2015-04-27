<?php
require_once 'library/sort.class.php';
$s = new Sort();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'GetSortList':
		echo json_encode($s->getSortList($json['GroupId']));
		break;
	case 'SortInfo':
		echo json_encode($s->sortInfo($json['Id'],$json['GroupId']));
		break;
	case 'SortAdd':
		echo json_encode($s->sortAdd($json['Data']));
		break;
	case 'SortUpdate':
		echo json_encode($s->sortUpdate($json['Id'],$json['Data']));
		break;
	case 'SortDel':
		echo json_encode($s->sortDel($json['Id'],$json['GroupId']));
		break;
	case 'Up':
		echo json_encode($s->up($json['Id'],$json['GroupId']));
		break;
	case 'Down':
		echo json_encode($s->down($json['Id'],$json['GroupId']));
		break;
	case 'GetRoomList':
		echo json_encode($s->getRoomList($json['SortId'],$json['GroupId']));
		break;
	case 'RoomAdd':
		echo json_encode($s->roomAdd($json['RoomId'],$json['SortId']));
		break;
	case 'RoomDel':
		echo json_encode($s->roomDel($json['Id']));
		break;
}