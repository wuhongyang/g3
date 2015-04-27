<?php
/*
require_once 'library/param_config.class.php';
$paramConfig = new ParamConfig();
$json = $_POST['extparam'];

switch($json['Tag']){
	case 'ParamConfigList':
		echo json_encode($paramConfig->lists($json['SearchData']));
		break;
	case 'ParamConfigInfo':
		echo json_encode($paramConfig->getInfo($json['Id']));
		break;
	case 'ParamConfigAdd':
		echo json_encode($paramConfig->add($json['Info']));
		break;
	case 'ParamConfigUpdate':
		echo json_encode($paramConfig->update($json['Info'],$json['Id']));
		break;
}
*/