<?php
if(!class_exists(CommodityCategory))
	include 'library/commodity_category.class.php';
$cc = new CommodityCategory();
$json = $_POST['extparam'];

switch($json['Tag']){
	case 'List':
		echo json_encode($cc->lists($json['Data']));
		break;
	case 'Info':
		echo json_encode($cc->info($json['Id']));
		break;
	case 'Add':
		echo json_encode($cc->add($json['Data']));
		break;
	case 'Edit':
		echo json_encode($cc->edit($json['Id'],$json['Data']));
		break;
}