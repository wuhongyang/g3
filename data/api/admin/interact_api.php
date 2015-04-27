<?php
include_once 'library/interact_api.class.php';
$json = $_POST['extparam'];
$interact = new interact_api();

switch($json['Tag']){
	case 'GetFrozenList':
		echo json_encode($interact->getFrozenList($json['cmd'],$json['start'],$json['end']));
		break;
	case 'FreeFrozen':
		echo json_encode($interact->freeFrozen($json['cmd'],$json['start'],$json['end']));
		break;
	case 'GetActivityRooms':
		echo json_encode($interact->getActivityRooms($json));
		break;
	case 'SaveActivityRooms':
		echo json_encode($interact->saveActivityRooms($json));
		break;
	default :
		echo httpPOST(REGION_API_PATH,$_POST,false);
		break;
}
