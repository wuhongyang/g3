<?php
//初始化
require_once '../library/global.fun.php';
require_once 'library/register.class.php';
$obj = new register();
$route = array_keys($_GET);
$c = $route[0];
$a = empty($route[1])? 'index' : $route[1]; unset($route);
$obj->$a();
