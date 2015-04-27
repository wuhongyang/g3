<?php
require_once 'library/search_config.class.php';
$s = new SearchConfig();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'SearchConfigInfo':
		echo json_encode($s->searchConfigInfo($json['GroupId']));
		break;
	case 'SearchConfigSave':
		echo json_encode($s->searchConfigSave($json['Data']));
		break;
}