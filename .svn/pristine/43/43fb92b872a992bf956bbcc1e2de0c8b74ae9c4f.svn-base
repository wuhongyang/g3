<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/data/library/global.fun.php';
setcookie('back',$_GET['back'],0,'/',$_SERVER['HTTP_HOST']);
if(isset($_GET['redirect'])){
	setcookie('redirect',$_GET['redirect'],0,'/',$_SERVER['HTTP_HOST']);
}
if(isset($_GET['domain'])){
	setcookie('domain',$_GET['domain'],0,'/',$_SERVER['HTTP_HOST']);
}
if(isset($_GET['dplogin'])){
	setcookie('dplogin',$_GET['dplogin'],0,'/',$_SERVER['HTTP_HOST']);
}
require_once("API/qqConnectAPI.php");
$qc = new QC();
$qc->qq_login();
