<?php
include_once('../library/global.fun.php');

$module = trim($_GET['module']);
if($module=='list'){
	$link_array = getLevellink(10002,10009,10014,101);
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10009,"ParentId"=>10014,"ChildId"=>101,"Desc"=>"常规四级科目列表"),
		'extparam'=>array("Tag"=>"CommonChildList","SearchData" => $_GET)
	);
	$result = request($param);
	$lists = $result['Result'];
	$page = $result['Page'];
	$temp = 'common_child_list.html';
}elseif($module == 'info'){
	$link_array = getLevellink(10002,10009,10014,101);
	$id = intval($_GET['id']);
	if($id > 0){
		$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10009,"ParentId"=>10014,"ChildId"=>101,"Desc"=>"常规四级科目详情"),
			'extparam'=>array("Tag"=>"CommonChildInfo","Id" => $id)
		);
		$result = request($param);
		$info = $result['Info'];
		$url = '?module=update';
	}else{
		$url = '?module=add';
	}
	$temp = 'common_child_info.html';
}elseif($module == 'add'){
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10009,"ParentId"=>10014,"ChildId"=>102,"Desc"=>"添加常规四级科目"),
		'extparam'=>array("Tag"=>"CommonChildAdd","Data"=>$_POST)
	);
	$result = request($param);
	if($result['Flag'] == 100)
		ShowMsg($result['FlagString'],'?module=list');
	else
		ShowMsg($result['FlagString'],-1);
}elseif($module == 'update'){
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10009,"ParentId"=>10014,"ChildId"=>103,"Desc"=>"修改常规四级科目"),
		'extparam'=>array("Tag"=>"CommonChildUpdate","Data"=>$_POST)
	);
	$result = request($param);
	if($result['Flag'] == 100)
		ShowMsg($result['FlagString'],'?module=list');
	else
		ShowMsg($result['FlagString'],-1);
}elseif($module == 'sync'){
	$ids = $_POST['id'];
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10009,"ParentId"=>10014,"ChildId"=>104,"Desc"=>"同步常规四级科目"),
		'extparam'=>array("Tag"=>"CommonChildSync","Ids"=>$ids)
	);
	$result = request($param);
	exit(json_encode($result));
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('business/'.$temp,$tpl);