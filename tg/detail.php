<?php
require dirname(dirname(__FILE__)).'/library/global.fun.php';
require_once 'library/register.class.php';
define('THEMES_ROOT', $_SERVER['DOCUMENT_ROOT'].'/themes/tg');
$_GET['search']['stime'] = $_GET['search']['stime'] ? $_GET['search']['stime'] : date('Y-m-d');
$_GET['search']['etime'] = $_GET['search']['etime'] ? $_GET['search']['etime'] : date('Y-m-d');

if(!empty($_GET['from'])){
	$_GET['search']['Fromname'] = $_GET['from'];
	$obj = new register();
    $result = $obj->detailList($_GET['search']);
	$list = (array)$result['Result'];
    $template = 'detail.html';
	/*加载模板*/
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','tg'));
	include template($template,$tpl);
}

