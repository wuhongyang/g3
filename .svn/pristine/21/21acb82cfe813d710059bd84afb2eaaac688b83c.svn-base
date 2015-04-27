<?php
require_once 'library/get_level.class.php';
$channel = new get_level();

$json = $_POST['extparam'];
$param = $_POST['param'];

switch ($json['Tag']) {
	case 'GetLevel':
		echo json_encode($channel->get_level($json['Data']));
		break;
	/*
	case 'GetVipLevel':
		echo json_encode($channel->get_vip_level($json['Data']));
		break;
	*/
}