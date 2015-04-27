<?php
require_once 'library/help.class.php';

$help = new help();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'ClassifyList':
		echo json_encode($help->classify_list($json['Data']));
		break;
	case 'SubstanceList':
		echo json_encode($help->substance_list($json['Data']));
		break;
	case 'SubstanceDetail':
		echo json_encode($help->substance_list($json['Data']));
		break;
	case 'GetLinkList':
		//{"Tag":"GetLinkList","Data":{"Limit":-1}}
		echo json_encode($help->get_link_list($json['Data']));
		break;
}