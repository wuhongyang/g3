<?php
include_once('../library/global.fun.php');
include 'config/data.php';

$module = trim($_GET['module']);

if($module=='list'){
	$link_array = getLevellink(10002,10016,10034,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10034,
			"ChildId"	 => 101,
			"Desc"		 => "渠道类别列表"
		),
		'extparam'=>array(
			"Tag" 		 => "List",
			"SearchData" => $_POST['cate']
		)
	);
	$result = request($param);
	$cates = $result['li'];
	$page = $result['page'];
	$temp = 'channel_category_list.html';
}elseif($module == 'info'){
	$link_array = getLevellink(10002,10016,10034,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10034,
			"ChildId"	 => 101,
			"Desc"		 => "渠道类别详情"
		),
		'extparam'=>array(
			"Tag" 		 => "Info",
			"Id"		 => $_GET['id']
		)
	);
	$info = request($param);
	$rules = (array)$info['Rules']['Result'];
	$info = (array)$info['Result'];
	$temp = 'channel_category_info.html';
}elseif($module == 'add'){
	$link_array = getLevellink(10002,10016,10034,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10034,
			"ChildId"	 => 103,
			"Desc"		 => "渠道类别添加"
		),
		'extparam'=>array(
			"Tag" 		 => "Add",
			"Data"		 => $_POST['cate']
		)
	);
	$result = request($param);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[101]['url']);
	}else{
		ShowMsg($result['FlagString'],$link_array[102]['url'].'&id='.$_POST['id']);
	}
	exit;
}elseif($module == 'update'){
	$link_array = getLevellink(10002,10016,10034,101);
	$id = intval($_POST['id']);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10034,
			"ChildId"	 => 104,
			"Desc"		 => "渠道类别添加"
		),
		'extparam'=>array(
			"Tag" 		 => "Update",
			"Data"		 => $_POST['cate'],
			"Id"		 => $id
		)
	);
	$result = request($param);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[101]['url']);
	}else{
		ShowMsg($result['FlagString'],$link_array[102]['url'].'&id='.$_POST['id']);
	}
	exit;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('channel_category/'.$temp,$tpl);