<?php
if(!class_exists('CommodityScheme'))
	include 'library/commodity_scheme.class.php';
$cs = new CommodityScheme();
$json = $_POST['extparam'];

switch($json['Tag']){
	case 'List':
		echo json_encode($cs->lists($json['Data']));
		break;
	case 'Info':
		echo json_encode($cs->info($json['Id']));
		break;
	case 'Add':
		echo json_encode($cs->add($json['Data']));
		break;
	case 'Edit':
		echo json_encode($cs->edit($json['Id'],$json['Data']));
		break;
}