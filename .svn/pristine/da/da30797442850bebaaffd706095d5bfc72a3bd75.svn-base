<?php
// require dirname(dirname(dirname(__FILE__))).'/library/global.fun.php';
require_once dirname(__FILE__).'/library/user_game_info.class.php';
$userGameInfo = new UserGameInfo();
$json = $_POST['extparam'];

// $json['Tag'] = "GetList";
// $json['Data']['ParentId'] = "10114";
// $json['Data']['RoomId'] = "571111";

switch($json['Tag']){
	case 'GetList':
		echo json_encode($userGameInfo->getList($json['Data']));
		break;
	case 'GetLuckyRank'://房内幸运排行榜
		echo json_encode($userGameInfo->getLuckyRank(20));
		break;
}