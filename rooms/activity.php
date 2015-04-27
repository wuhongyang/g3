<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
$user = checkLogin();
//是否为站长
$isManager = getChannelType($user['Uin'],0,8);

//是否为代理
$isAgency = getChannelType($user['Uin'],0,16);

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template("active/activity.html",$tpl);