<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module = !empty($_GET['module']) ? $_GET['module'] : 'about';

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template('aboutus/'.$module.".html",$tpl);