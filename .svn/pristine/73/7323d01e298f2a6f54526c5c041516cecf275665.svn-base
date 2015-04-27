<?php
include_once '../library/global.fun.php';

$module = trim($_GET['module']);

if($module=='propsConfigList' || empty($module)){
	$status_arr = array("0"=>"不启用","1"=>"启用");
	$link_array = getLevellink(10002,10011,10022,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10011,
			"ParentId"   => 10022,
			"ChildId"	 => 101,
			"Desc"		 => "礼物配置列表"
		),
		'extparam'=>array(
			"Tag" 		 => "PropsConfigList",
			"SearchData" => $_POST
		)
	);
	
	$result = request($param);
	$page = $result['page'];
	if($result){
		unset($result['page']);
		foreach($result as $k=>$v){
			$result[$k]['uptime'] = date('Y-m-d H:i:s', $v['uptime']);
		}
	}

	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','admin'));
	include template('sysconfig/props_config_list.html',$tpl);
}elseif($module == 'propsConfigUpdate'){
	$link_array = getLevellink(10002,10011,10022,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10011,
			"ParentId"   => 10022,
			"ChildId"	 => 103,
			"Desc"		 => "修改礼物配置"
		),
		'extparam'=>array(
			"Tag" 		 => "PropsConfigUpdate",
			"Data"		 => $_POST
		)
	);
	$result = request($param);
	if($result['Flag'] == 100)
		ShowMsg($result['FlagString'],'?module=propsConfigList');
	else
		ShowMsg($result['FlagString'],"props_config_info.php?id=".$_POST['id']);
}elseif($module == 'propsConfigAdd'){
	$link_array = getLevellink(10002,10011,10022,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10011,
			"ParentId"   => 10022,
			"ChildId"	 => 102,
			"Desc"		 => "添加礼物配置"
		),
		'extparam'=>array(
			"Tag" 		 => "PropsConfigAdd",
			"Data"		 => $_POST
		)
	);
	$result = request($param);
	if($result['Flag'] == 100)
		ShowMsg($result['FlagString'],'?module=propsConfigList');
	else
		ShowMsg($result['FlagString'],"props_config_info.php");
}