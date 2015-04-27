<?php
include_once 'library/props.class.php';
$props = new Props();
$json = $_POST['extparam'];

switch ($json['Tag']){
	case 'propApply': 
		echo json_encode($props->propApply($_POST['param']));
		break;
	case 'storeMoney': 
		echo json_encode($props->storeMoney($_POST['param'],$json));
		break;
	case 'StoreGameScore':
		$cmd  = $json['Cmd'];
		$uin  = $json['Uin'];
		$level = $json['Level'];
		$score = $json['Score'];
		$cleartime = $json['ClearTime'];
		echo json_encode($props->storeGameScore($cmd,$uin,$level,$score,$cleartime));
		break;
	case 'GetGameScore':
		$cmd  = $json['Cmd'];
		$uin  = $json['Uin'];
		$level = $json['Level'];
		echo json_encode($props->getGameScore($cmd,$uin,$level));
		break;
	case 'GetGameScoreRank':
		$cmd  = $json['Cmd'];
		$uin  = $json['Uin'];
		$level = $json['Level'];
		$num   = $json['Num'];
		echo json_encode($props->getGameScoreRank($cmd,$uin,$level,$num));
		break;
	default :
		exit('"Flag":101,"FlagString":"不存在的接口模块"');
		break;
}
