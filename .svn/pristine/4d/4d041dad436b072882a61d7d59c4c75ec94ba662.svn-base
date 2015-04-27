<?php
@header("http/1.1 404 not found");
@header("status: 404 not found"); 
require_once dirname(__FILE__).'/data/library/global.fun.php';
$GroupData = domain::main()->GroupData();
$themes=$GroupData['Template']!=''?$GroupData['Template']:'default';
if($themes=='default'){
	include_once dirname(__FILE__).'/404.html';
}
else{
	include_once dirname(__FILE__).'/themes/g3/group_site/'.$themes.'/tpl/404.html';
}
exit;
?>