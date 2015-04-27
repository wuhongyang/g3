<?php
require_once 'library/setting.class.php';
$s = new Setting();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'SettingSave':
		echo json_encode($s->settingSave($json['GroupId'],$json['Data']));
		break;
	case 'SettingValue':
		echo json_encode($s->settingValue($json['GroupId'],$json['Key']));
		break;
}