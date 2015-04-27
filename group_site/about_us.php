<?php
include_once('library/common.php');

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/';
$tmp_config['cache_dir'].=$themes.'/tpl/';

$tpl=template::getInstance();
$tpl->setOptions($tmp_config);
include template('about_us.html',$tpl);
?>