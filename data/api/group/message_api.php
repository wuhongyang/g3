<?php
require_once 'library/message.class.php';
$m = new message();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'MessageList':
		echo json_encode($m->messageList($json['GroupId']));
		break;
	case 'MessageInfo':
		echo json_encode($m->messageInfo($json['Id'],$json['GroupId']));
		break;
	case 'MessageAdd':
		echo json_encode($m->messageAdd($json['Data']));
		break;
	case 'MessageEdit':
		echo json_encode($m->messageEdit($json['Id'],$json['Data']));
		break;
	case 'MessageDel':
		echo json_encode($m->messageDel($json['Id'],$json['GroupId']));
		break;
	case 'Order':
		echo json_encode($m->order($json['Id'],$json['GroupId'],$json['Type']));
		break;
	case 'TitleSave':
		echo json_encode($m->titleSave($json['Data']));
		break;
}