<?php
include 'library/ad.class.php';

$Ad = new Ad();
$json = $_POST['extparam'];
//没有办法
switch($json['Tag']){
	case 'DetailList':
		echo json_encode($Ad->detailList($json['SearchData']));
		break;
}