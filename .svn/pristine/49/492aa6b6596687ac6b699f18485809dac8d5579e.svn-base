<?php
require_once dirname(__FILE__).'/library/statistics.class.php';

$json = $_POST['extparam'];
$count = new Statistics($json['DataGroupId']);

switch($json['Tag']){
	case 'AllUserTotal':
		echo json_encode($count->allUserTotal());
		break;
	case 'AllUserHistory':
		echo json_encode($count->allUserHistory($json['Data']));
		break;
	case 'RoomsUserTotal'://分站在线人数汇总
		echo json_encode($count->roomsUserTotal($json['RegionId']));
		break;
	case 'RoomsUserHistory'://分站在线人数明细
		echo json_encode($count->roomsUserHistory($json['Data']));
		break;
	case 'RoomsCountDay':
		echo json_encode($count->roomsCountDay($json['Data']));
		break;
	case 'RoomsUserInfo':
		echo json_encode($count->roomsUserInfo($json['Data']));
		break;
}