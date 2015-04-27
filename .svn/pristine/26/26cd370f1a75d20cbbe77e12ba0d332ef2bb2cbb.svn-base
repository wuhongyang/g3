<?php
include_once 'library/vip.class.php';
require_once __ROOT__.'/api/service/library/handle_matter.class.php';
$vip = vip::instance();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'VipList':
		echo json_encode($vip->vipList($json));//
		break;
	case 'AddVip':
		echo json_encode($vip->addVip($json));
		break;
	case 'VipInfo':
		echo json_encode($vip->vipInfo($json));
		break;
	case 'VipUpdate':
		echo json_encode($vip->vipUpdate($json));
		break;
	case 'VipDel':
		echo json_encode($vip->vipDel($json));
		break;
	case 'vipinfo_list':
		echo json_encode($vip->vipInfo($json));
		break;
	case 'VipSet':
		echo json_encode($vip->vipSet($json));
		break;
	case 'VipRank': //站内Vip数量排行
		echo json_encode($vip->vipRank());
		break;
	case 'SetState':
		echo json_encode($vip->setState($param, $json));
		break;
	case 'EditPass':
		echo json_encode($vip->editPass($json, $param['GroupId']));
		break;
	default:
		exit('{"Flag":101,"FlagString":"不存在的接口模块"}');
		break;
}