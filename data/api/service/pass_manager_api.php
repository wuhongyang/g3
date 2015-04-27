<?php
require_once 'library/pass_manager.class.php';

$pm = new PassManager();

$json = $_POST['extparam'];
$param = $_POST['param'];

switch ($json['Tag']) {
	case 'Info':
		echo json_encode($pm->info($json['Uid']));
		break;
	case 'SsoInfo':
		echo json_encode($pm->ssoInfo($json['Uid']));
		break;	
	case 'InfoForUin':
		echo json_encode($pm->infoForUin($json['Uin']));
		break;
	case 'SavePassInfo':
		echo json_encode($pm->savePassInfo($json['Uid'],$json['Data']));
		break;
	case 'SaveAccount':
		echo json_encode($pm->saveAccount($json['Uid'],$json['Data']));
		break;
	case 'SaveSpecialty':
		echo json_encode($pm->saveSpecialty($json['Uid'],$json['Data']));
		break;
	case 'GetSpecialtyInfo':
		echo json_encode($pm->getSpecialtyInfo($json['Uid']));
		break;
	case 'AllUins':
		echo json_encode($pm->uins($json['Uid']));
		break;
	case 'SetDefaultUin':
		echo json_encode($pm->setDefaultUin($param['Uid'],$json['Uin']));
		break;
}