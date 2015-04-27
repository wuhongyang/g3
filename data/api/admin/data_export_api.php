<?php
include 'library/data_export.class.php';

$data_export = new data_export();
$json = $_POST['extparam'];
switch($json['Tag']){
	case 'ExportTable':
		echo json_encode($data_export->export_table($json['Table'], $json['NewFile']));
		break;
	case 'ExportEnd':
		echo json_encode($data_export->export_end());
		break;
	case 'ForceEnd':
		echo json_encode($data_export->force_end());
		break;
}