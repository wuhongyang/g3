<?php
require_once 'library/passport.class.php';
$json = $_POST['extparam'];
$p	  = $_POST['param'];
$group = new passport($json['GroupId']);

switch ($json['Tag']) {
	case 'getPassDetail':
		echo json_encode($group->getPassDetail($json['Id']));
		break;
	case 'listPass' :  	//显示通行证列表
		echo json_encode($group->listPass( $json['mypost'] ));
		break;
	case 'setPass'  :	//设置账号状态，是否冻结
		echo json_encode($group->setPass( $json['mypost'] ));
		break;
	case  'getOnePass'   :	//获得单条通行证信息
		echo json_encode($group->getOnePass( $json['mypost'] ));
		break;
	case 'editPass'   :	//密码重置
		echo json_encode($group->editPass( $json['mypost'] ));
		break;
	case 'SavePassInfo':
		echo json_encode($group->savePassInfo($json['Data']));
		break;
}


