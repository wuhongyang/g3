<?php
require_once 'library/activity.class.php';
$a = new Activity();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'ActivityList':
		echo json_encode($a->activityList($json['GroupId']));
		break;
	case 'ActivityListNoPage':
		echo json_encode($a->activityListNoPage($json['GroupId'],$json['Where']));
		break;
	case 'ActivityInfo':
		echo json_encode($a->activityInfo($json['Id']));
		break;
	case 'ActivityAdd':
		echo json_encode($a->activityAdd($json['Data']));
		break;
	case 'ActivityUpdate':
		echo json_encode($a->activityUpdate($json['Id'],$json['Data']));
		break;
	case 'ActivityRecommend':
		echo json_encode($a->activityRecommend($json['Id'],$json['GroupId']));
		break;
	case 'ActivityOrder':
		echo json_encode($a->activityOrder($json['Data']));
		break;
	case 'ActivityDel':
		echo json_encode($a->activityDel($json['Data']));
		break;
}