<?php
require_once 'library/handle_matter.class.php';

$hm = new handleMatter();

$json = $_POST['extparam'];
$param = $_POST['param'];

switch ($json['Tag']) {
	case 'UnRead':
		echo json_encode($hm->unread($param['Uin']));
		break;
	case 'SetRead':
		echo json_encode($hm->setRead($json['Id']));
		break;
	case 'Count':
		echo json_encode($hm->getCount($param['Uin']));
		break;	
}