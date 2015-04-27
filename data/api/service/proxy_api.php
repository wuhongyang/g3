<?php
include_once 'library/proxy.class.php';
$proxy = proxy::instance();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'ProxyAccount':
		echo json_encode($proxy->proxyAccount($json['Data'],$param));//
		break;
}