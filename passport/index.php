<?php
require_once 'common.php';
define('THEMES_URL','http://'.$_SERVER['HTTP_HOST'].'/themes/g3/');
define('BASE_URL','http://'.$_SERVER['HTTP_HOST'].'/passport');
if($GroupData['Template']=='default' || $GroupData['Template'] == ''){
	define('THEMES_ROOT',realpath('../themes/g3/passport'));
}
else{
	define('THEMES_COMMON_ROOT',realpath('../themes/g3/group_site/'.$GroupData['Template'].'/tpl'));
	define('THEMES_ROOT',realpath('../themes/g3/group_site/'.$GroupData['Template'].'/tpl/passport'));
}
$ext = json_decode($GroupData['EXT'], true);
$callback = $ext['callback']['value'];
if(empty($callback)) $callback = "openlogin.vvku.com";
define('CALLBACK', $callback);
//加载模块
$route = array_keys($_GET);
if(empty($route[0])){
	$c = 'user_email';
	$_GET[$c] = '';
}else{
	$c = $route[0];
}
$module = empty($route[1])? 'index' : $route[1]; 
unset($route);

//$types = array('user_email'=>1, 'user_phone'=>2, 'user_name'=>3);
$file = $c.'.php';
if(!file_exists($file)){
	alertMsg('非法调用','/');
}
require_once $file;