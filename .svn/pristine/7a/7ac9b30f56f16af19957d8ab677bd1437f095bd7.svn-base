<?php
require dirname(__FILE__).'/library/recommend.class.php';

$obj = new Recommends();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'GetRoomInfo':
		echo json_encode($obj->getRoomInfo($json['Roomid']));
		break;
	case 'PostInfo':
		echo json_encode($obj->postInfo($json['Data']));
		break;
	default :
		exit('{"Flag":"101","FlagString":"接口不存在"}');
		break;
}