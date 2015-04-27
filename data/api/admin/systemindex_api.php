<?php
require_once 'library/systemindex.class.php';
$sys = new systemindex();
$json = $_POST['extparam'];
switch ($json['Tag']) {
	case 'GetMessage' :	//查找单个群组
		echo json_encode($sys->getMessage());
		break;

}

