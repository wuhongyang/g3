<?php
require_once 'library/config.class.php';
$c = new Config();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'SaveRoleOrderConfig':
		echo json_encode($c->saveRoleOrderConfig($json['GroupId'],$json['RoleOrderType']));
		break;
	case 'SearchConfigSave':
		echo json_encode($s->searchConfigSave($json['Data']));
		break;
}