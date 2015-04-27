<?php
require_once dirname(dirname(dirname(__FILE__))).'/library/global.fun.php';
$GroupData = domain::main()->GroupData();
$themes=($GroupData['Template']=='' || $GroupData['Template'] == -1 ) ? 'default1':$GroupData['Template'];$themes=$GroupData['Template']='aisvv';

$groupId=intval($GroupData['groupid']);
$groupInfo=$GroupData;
$groupExtInfo=json_decode($groupInfo['EXT'],true);
//底部
$footerInfo['icp_info']=$groupInfo['Icpinfo'];

include_once('library/'.$themes.'/common.php');
?>