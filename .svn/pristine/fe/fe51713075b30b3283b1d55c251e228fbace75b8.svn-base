<?php
include_once 'library/flash_games.class.php';
// require dirname(dirname(dirname(__FILE__))).'/library/global.fun.php';

$flashGame = new FlashGame();

$param = $_POST['param'];
$json = $_POST['extparam'];

// $json['Tag'] = "GameLevelImfomation";
// $json['Uin'] = array(10000, 168888);
// $param = array("BigCaseId"=>10001,"CaseId"=>10032,"ParentId"=>10114,"ChildId"=>110);

switch ($json['Tag']){
	case 'AuthRequest': //进入场次
		echo json_encode($flashGame->authRequest($param,$json));
		break;
	case 'CarryMoney': //游戏开始
		echo json_encode($flashGame->carryMoney($param,$json['Gameid'],$json));
		break;
	case 'BackMoney': //离开游戏
		echo json_encode($flashGame->backMoney($param,$json['Gameid'],$json));
		break;
	case 'GameResult': //游戏结算
		echo json_encode($flashGame->gameResult($param,$json['Gameid'],$json['Users'],$json));
		break;
	case 'GameLevelImfomation':
		echo json_encode($flashGame->gameLevelImfomation($param, $json['Uin']));
		break;
}
