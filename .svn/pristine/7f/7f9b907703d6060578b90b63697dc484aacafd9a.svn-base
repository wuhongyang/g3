<?php
include('cache.class.php');
include(dirname(dirname(__FILE__)).'/config/cache.php');

$storage = cache::connect($config['memcache']);
$overload = $storage->get('PHP_OVERLOAD');
$serverload = $_SERVER['PHP_OVERLOAD'];

if($overload > $serverload && $serverload > 0){
	if($_POST['parameter'] || $_GET['parameter']){
		echo '{"Flag":1203,"FlagString":"数据库繁忙，请稍后 #'.$serverload.'"}';
	}else{
		header($_SERVER["SERVER_PROTOCOL"]." 503 Service Temporarily Unavailable");
		header('Status: 503 Service Temporarily Unavailable');
		header('Retry-After: 5');
	}
	$log = $_POST['parameter'] ? $_POST['parameter'] : $_GET['parameter'];
	$log = json_encode($log);
	$log .= "\r\n";
	$log .= date('Y-m-d H:i:s');
	error_log($log,3,$_SERVER['DOCUMENT_ROOT'].'/error_log/overload_'.date('Ymd').'.txt');
	exit;
}else{
	$storage->set('PHP_OVERLOAD',$overload+1,1);
}