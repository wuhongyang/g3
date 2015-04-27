<?php
include_once '../library/global.fun.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : '';

$link_array = getLevellink(10002,10011,10022,101);
if(!empty($id) && $id!=0){
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10011,
			"ParentId"   => 10022,
			"ChildId"	 => 101,
			"Desc"		 => "礼物配置详情"
		),
		'extparam'=>array(
			"Tag" => "PropsConfigList",
			"Id"  => $id
		)
	);
	
	$info = request($param);
	$url = 'props_config.php?module=propsConfigUpdate';
}else{
	$url = 'props_config.php?module=propsConfigAdd';
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('sysconfig/props_config_info.html',$tpl);