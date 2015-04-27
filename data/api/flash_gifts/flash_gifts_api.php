<?php
include_once __ROOT__.'/api/flash_func/library/absprize.class.php';
include_once 'library/gift.class.php';
$gift = new Gift();
$json = $_POST['extparam'];

switch ($json['Tag']){
	case 'SendGift':
		echo json_encode($gift->sendGift($_POST['param'],$json));
		break;
	default :
		exit('"Flag":101,"FlagString":"不存在的接口模块"');
		break;
}
