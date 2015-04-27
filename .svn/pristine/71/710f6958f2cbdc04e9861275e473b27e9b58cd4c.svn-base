<?php
require_once 'library/mic_setting.class.php';
$m = new MicSetting();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'MicSettingInfo':
		echo json_encode($m->micSettingInfo($json['GroupId']));
		break;
	case 'MicSettingSave':
		echo json_encode($m->micSettingSave($json['Data']));
		break;
}