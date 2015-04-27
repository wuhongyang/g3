<?php
require_once dirname(dirname(dirname(__FILE__))).'/library/global.fun.php';
$GroupData = domain::main()->GroupData();
$themes=($GroupData['Template']=='' || $GroupData['Template'] == -1 ) ? 'default1':$GroupData['Template'];$themes=$GroupData['Template']='aisvv';

$groupId=intval($GroupData['groupid']);
$groupInfo=$GroupData;
$groupExtInfo=json_decode($groupInfo['EXT'],true);
//底部
$footerInfo['icp_info']=$groupInfo['Icpinfo'];

if(!isset($_GET['self_module'])||empty($_GET['self_module'])){
	header('Location:/404.html');
	exit;
}
$selfModule=$_GET['self_module'];

//载入自定义内容
$file=$themes.'/'.$selfModule.'.php';
if(!file_exists($file)){
	header('Location:/404.html');
	exit;
}
include_once($file);
?>