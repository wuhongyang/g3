<?php
require_once 'library/channel.class.php';
$channel = new channel();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch ($json['Tag']) {
	case 'Account':
		echo json_encode($channel->account($json['Uin']));
		break;
	case 'SaveAccount':
		echo json_encode($channel->saveAccount($param['Uin'],$json['Data']));
		break;
}