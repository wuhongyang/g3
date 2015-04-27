<?php
/*
include_once 'library/props.class.php';
$json = $_POST['extparam'];
$props = new props();

switch($json['Tag']){
	case 'GetInteractInfoForUpdate':
		echo json_encode($props->getInteractInfoForUpdate($json['Id']));
		break;
	case 'GetInteractInfoForAdd':
		echo json_encode($props->getInteractInfoForAdd());
		break;
	case 'GetPicMD5';
		$result = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1,'id'=>$json['mypost']['id']))));
		echo json_encode(array_merge(array('Flag'=>100,'FlagString'=>'成功'),(array)$result));
		break;
	default :
		echo httpPOST(REGION_API_PATH,$_POST,false);
		break;
}
*/
echo httpPOST(REGION_API_PATH,$_POST,false);