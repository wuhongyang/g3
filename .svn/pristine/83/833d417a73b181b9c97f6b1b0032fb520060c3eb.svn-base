<?php
include_once( '../library/global.fun.php' );
if($_POST){
	foreach($_POST['min'] as $key=>$value){
		$_POST['min'][$key] = trim($value);
	}
	foreach($_POST['need'] as $key=>$value){
		$_POST['need'][$key] = trim($value);
	}
	$param = array(
			'param'=>array(
					"BigCaseId"  => 10002,
					"CaseId"	 => 10009,
					"ParentId"   => 10313,
					"ChildId"	 => 102,
					"Desc"		 => "更新站内科目启动配置"
			),
			'extparam'=>array(
					"Tag" 	 => "SCSave",
					"Data"	 => $_POST
			)
	);
	$result = request($param);
	alertMsg($result['FlagString'], "start_config.php");
}
$param = array(
		'param'=>array(
				"BigCaseId"  => 10002,
				"CaseId"	 => 10009,
				"ParentId"   => 10313,
				"ChildId"	 => 101,
				"Desc"		 => "查看站内科目启动配置"
		),
		'extparam'=>array(
				"Tag" 	 => "SCList"
		)
);
$result = request($param);

$page = $result['Data']['Page'];
$list = $result['Data']['Result'];
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('behavior/start_config.html',$tpl);