<?php
include_once 'library/key.class.php';

$key = new key();
$json = $_POST['extparam'];

switch($json['Tag']){
	case 'KeyList':
		echo json_encode($key->key_list($json['SearchData'], $json['IsNotPage']));
		break;
	case 'KeySave':
		echo json_encode($key->key_save($json['Data']));
		break;
	case 'KeyDetail':
		echo json_encode($key->key_detail($json['KeyId']));
		break;
	case 'ComposeList':
		echo json_encode($key->compose_list());
		break;
	case 'ComposeSave':
		echo json_encode($key->compose_save($json['Data']));
		break;
	case 'ComposeDetail':
		echo json_encode($key->compose_detail($json['ComposeId']));
		break;
	case 'ComposeKeyList':
		echo json_encode($key->compose_key_list());
		break;
}