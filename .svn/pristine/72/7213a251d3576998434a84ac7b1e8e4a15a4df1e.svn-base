<?php
require dirname(dirname(__FILE__)).'/library/global.fun.php';
require_once 'library/register.class.php';
define('THEMES_ROOT', $_SERVER['DOCUMENT_ROOT'].'/themes/tg');
session_start();
setcookie("FROMNAME",$_GET['from'],-1,'/',$_SERVER['HTTP_HOST']);
setcookie("FROMUID",$_GET['uid'],-1,'/',$_SERVER['HTTP_HOST']);

$template = 'roomreg.html';
$param = array(
	'extparam' => array('Tag'=>'GetRecommedRooms'),
	'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
);
$recommend_rooms = request($param);
$rooms = array_slice($recommend_rooms['HotRooms'],0,9);
/*加载模板*/
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','tg'));
include template($template,$tpl);
