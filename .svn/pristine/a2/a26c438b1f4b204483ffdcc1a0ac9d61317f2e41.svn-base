<?php
require_once '../library/global.fun.php';
$param = array(
	'extparam' => array(
		'Tag'    => 'GetMessage', 
		'mypost' => $mypost 
	),
	'param' => array(
		'BigCaseId'		=> '10002',
		'CaseId'    	=> '10014',
		'ParentId'  	=> '10023', 
		'ChildId'   	=> '101',
		'Uin' 	    	=> '',
		'SessionKey'	=> '',
		'ChannelId' 	=> 0,
		'TargetUin' 	=> '',
		'Client'    	=> 'WEB ADMIN',
		'DoingWeight'   => 1,
		'MoneyWeight'	=> 1,
		'Desc'			=> '数据摘要查看'
	)
);
$result = request($param);
$users = $result['user'];
$count = $result['count'];
$ip = $result['ip'];
$address = $result['address'];
$date = date('Y-m-d:H:i:s',time());
$template = "system/systemindex.html";

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template($template, $tpl);
