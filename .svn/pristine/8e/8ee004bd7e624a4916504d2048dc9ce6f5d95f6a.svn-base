<?php
include_once 'library/play_dice.class.php';
$json = $_POST['extparam'];
$param = $_POST['param'];

switch ($json['Tag']){
	case 'GetConfig':
		$obj = new play_dice();
		echo json_encode($obj->getConfig($json['Cmd']));
		break;
	case 'StartGame':
		$obj = new play_dice();
		echo json_encode($obj->startGame($param,$json));
		break;
	case 'UserReady':
		$obj = new play_dice();
		echo json_encode($obj->userReady($param,$json));
		break;
	case 'CancelGame':
		$obj = new play_dice();
		echo json_encode($obj->cancelGame($param,$json));
		break;
	case 'GameOver':
		$obj = new play_dice();
		echo json_encode($obj->gameOver($param,$json));
		break;
	case 'Auth':
		exit('{"Flag":100,"FlagString":"ok"}');
		break;
	default :
		exit('"Flag":101,"FlagString":"不存在的接口模块"');
		break;
}
