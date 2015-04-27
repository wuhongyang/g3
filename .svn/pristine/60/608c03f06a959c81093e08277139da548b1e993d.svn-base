<?php
include_once 'library/absprize.class.php';
include_once 'library/chongbai.class.php';
include_once 'library/play_guess.class.php';
include_once 'library/windmill.class.php';
include_once 'library/zhipiao.class.php';
include_once 'library/baoshibox.class.php';
include_once 'library/GoldenEggs.class.php';
//include_once 'library/luckystar.class.php';
//include_once 'library/jubao.class.php';
$json = $_POST['extparam'];
$param = $_POST['param'];
switch ($json['Tag']){
	case 'ChongBai': //崇拜
	case 'MusicBaby': //点歌娃娃
		$chongbai = new Chongbai();
		echo json_encode($chongbai->deduct($_POST['param'],$json));
		break;
	
	/*我演你猜接口*/
	case 'GetConfig':
		$obj = new play_guess();
		echo json_encode($obj->getConfig($json['Cmd']));
		break;
	case 'StartGame':
		$obj = new play_guess();
		echo json_encode($obj->StartGame($param));
		break;
	case 'SelectTopic':
		$obj = new play_guess();
		echo json_encode($obj->SelectTopic($param,$json));
		break;
	case 'RefreshTopic':
		$obj = new play_guess();
		echo json_encode($obj->RefreshTopic($param,$json));
		break;
	case 'SubmitTopic':
		$obj = new play_guess();
		echo json_encode($obj->SubmitTopic());
		break;
	case 'WinUsers':
		$obj = new play_guess();
		echo json_encode($obj->WinUsers($param,$json));
		break;
		
	/*幸运风车接口*/
	case 'WindMill':
		$obj = new Windmill();
		echo json_encode($obj->deduct($param,$json));
		break;
		
	/*宝石盒接口*/
	case 'BaoShiBox':
	/*聚宝盆接口*/
	case 'JuBaoBowl':
	/*幸运星接口*/
	case 'LuckyStar':
		$obj = new Baoshi();
		echo json_encode($obj->deduct($param,$json));
		break;
		
	/*支票接口*/
	case 'ZhiPiao':
		$obj = new Zhipiao();
		echo json_encode($obj->SendCheck($param,$json));
		break;
		
	/*砸金蛋*/
	case 'StartEggs':
		$obj = new GoldenEggs();
		echo json_encode($obj->StartEggs($param,$json));
		break;
	case 'SmashEggs':
		$obj = new GoldenEggs();
		echo json_encode($obj->SmashEggs($param,$json));
		break;
	default :
		exit('"Flag":101,"FlagString":"不存在的接口模块"');
		break;
}
