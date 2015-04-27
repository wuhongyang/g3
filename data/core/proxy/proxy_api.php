<?php
require_once 'library/proxy_sys.class.php';
$proxy_sys = proxy_sys::instance();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'ProxyAccount':
		echo json_encode($proxy_sys->proxyAccount($param));//
		break;
	case 'ProxyBalance':
		echo json_encode($proxy_sys->proxyBalance($json['Data']));//
		break;
	default :
		exit('{"Flag":101,"FlagString":"不存在的接口模块"}');
		break;
}