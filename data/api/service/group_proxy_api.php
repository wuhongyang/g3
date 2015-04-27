<?php
include_once 'library/group_proxy.class.php';
$groupProxy = new groupProxy();
$json = $_POST['extparam'];
$param = $_POST['param'];
switch ($json['Tag']) {
	case 'GetRemitAccountList': //划账流水列表
		echo json_encode($groupProxy->getRemitAccountList($json['Data']));
		break;
	case 'GetRemitAccountCount': //划账流水总计
		echo json_encode($groupProxy->getRemitAccountCount($json['Data']));
		break;	
	case 'ProxyRecharge': //划账
		echo json_encode($groupProxy->proxyRecharge($json['GroupId'],$param['Uin'],$param['TargetUin'],$param['MoneyWeight']));
		break;		
}