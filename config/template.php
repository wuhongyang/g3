<?php
$config = array (
	'admin' => array(
		'template_dir'	=> __BASE__.'/admin/template/', //指定模板文件存放目录
		'cache_dir'		=> __BASE__.'/admin/template/compile/', //指定缓存文件存放目录
		'cache_lifetime'=> 0,     //缓存生命周期(秒)，为 0 表示永久
		'debug'			=> false, //每次自动删除缓存文件,true |　false
		'show_exectime' => true, //显示页面的执行时间
	),
	'rooms' => array(
		'template_dir'	=> __BASE__.'/themes/g3/rooms/', //指定模板文件存放目录
		'cache_dir'		=> __BASE__.'/themes/compile/rooms/', //指定缓存文件存放目录
		'cache_lifetime'=> 0,     //缓存生命周期(秒)，为 0 表示永久
		'debug'			=> false, //每次自动删除缓存文件,true |　false
	),
	//个人中心
	'service' => array(
		'template_dir'  => __BASE__.'/themes/g3/service/',
		'cache_dir'	=>__BASE__.'/themes/compile/service/',
		'cache_lifetime'=> 0,
		'debug'		=> false
	),
	//微博
	'weibo' => array(
		'template_dir'  => __BASE__.'/themes/g3/weibo/',
		'cache_dir'	=>__BASE__.'/themes/compile/weibo/',
		'cache_lifetime'=> 0,
		'debug'		=> false
	),
	//商城
	'shop' => array(
		'template_dir'  => __BASE__.'/themes/g3/shop/',
		'cache_dir'	=>__BASE__.'/themes/compile/shop/',
		'cache_lifetime'=> 0,
		'debug'		=> false
	),
	//推广
	'tg' => array(
		'template_dir'  => __BASE__.'/themes/g3/tg/',
		'cache_dir'	=>__BASE__.'/themes/compile/tg/',
		'cache_lifetime'=> 0,
		'debug'		=> false
	),
	'passport' => array(
		'template_dir'  => __BASE__.'/themes/g3/passport/',
		'cache_dir'	=>__BASE__.'/themes/compile/passport/',
		'cache_lifetime'=> 0,
		'debug'		=> false
	),
	//站管理
	'group' => array(
		'template_dir'  => __BASE__.'/themes/g3/group/',
		'cache_dir'	=>__BASE__.'/themes/compile/group/',
		'cache_lifetime'=> 0,
		'debug'		=> true
	),
	//站前台
	'group_site' => array(
		'template_dir'  => __BASE__.'/themes/g3/group_site/',
		'cache_dir'	=>__BASE__.'/themes/compile/group_site/',
		'cache_lifetime'=> 0,
		'debug'		=> true
	)
);

define('THEMES_URL','http://'.$_SERVER['HTTP_HOST'].'/themes/g3/');
define('ADMIN_THEMES_URL','http://'.$_SERVER['HTTP_HOST'].'/admin/template/');