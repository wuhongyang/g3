<?php
require dirname(dirname(__FILE__)).'/library/global.fun.php';
//require_once 'library/register.class.php';
define('THEMES_ROOT', $_SERVER['DOCUMENT_ROOT'].'/themes/tg');
session_start();

$template = 'register.html';

/*加载模板*/
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','tg'));
include template($template,$tpl);
