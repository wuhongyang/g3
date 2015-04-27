<?php
include_once('library/common.php');

if($themes != 'vvai'){
	header("HTTP/1.1 404 NOT FOUND");
	header("status:404 NOT FOUND");
	header("Content-Type:text/html;charset=utf-8");
	exit('<h1>文件不存在！</h1>');
}

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('agreement.html',$tpl);
?>