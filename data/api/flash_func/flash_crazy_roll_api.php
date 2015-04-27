<?php
include_once 'library/crazy_roll.class.php';
$json = $_POST['extparam'];
$param = $_POST['param'];

$obj = new crazy_roll();

switch ($json['Tag']){
	case 'GetConfig':
		echo json_encode($obj->getConfig($json['Cmd']));
		break;
	case 'StartGame':
		echo json_encode($obj->startGame($param,$json));
		break;
	case 'GrapSeat':
		echo json_encode($obj->grapSeat($param,$json));
		break;
	case 'DownSeat':
		echo json_encode($obj->downSeat($param,$json['Gameid']));
		break;
	case 'GameResult':
		echo json_encode($obj->gameResult($param,$json));
		break;
	case 'GameOver':
		echo json_encode($obj->gameOver($json['Gameid']));
		break;
	case 'Auth':
		exit('{"Flag":100,"FlagString":"ok"}');
		break;
	default :
		exit('{"Flag":101,"FlagString":"不存在的接口模块"}');
		break;
}
