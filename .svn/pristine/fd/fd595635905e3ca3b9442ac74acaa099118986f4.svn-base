<?php
if(is_numeric($_GET['roomid'])){
	$url = "http://{$_SERVER['HTTP_HOST']}/v/{$_GET['roomid']}";
	$filename = empty($_GET['title'])? $_GET['roomid'].'.url' : $_GET['title'].'.url';
}
elseif(is_numeric($_GET['group_id'])){
	$url = "http://{$_SERVER['HTTP_HOST']}/";
	$filename = empty($_GET['title'])? $_GET['group_id'].'.url' : $_GET['title'].'.url';
}
else{
	$url = "http://{$_SERVER['HTTP_HOST']}/";
	$filename = str_replace("+", "%20",urlencode('VV酷娱乐社区.url'));
}
$ua = $_SERVER["HTTP_USER_AGENT"]; 
header('Content-Type: application/octet-stream');
if(preg_match("/Firefox/", $ua)){
	header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
}else{
	$filename = iconv('UTF-8','GB2312',$filename);
	header('Content-Disposition: attachment; filename="'.$filename.'"');
}

echo "
[InternetShortcut]
URL={$url}
title={$filename}
IDList=
IconFile=http://{$_SERVER['HTTP_HOST']}/favicon.ico
IconIndex=0
[{000214A0-0000-0000-C000-000000000046}]
Prop3=19,2
";
