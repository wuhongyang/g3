<?php
include_once('../library/global.fun.php');
include 'config/data.php';

$module = trim($_GET['module']);

//当前使用站
$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

//获得url
$link_array = getLevellink(10002,10016,10027,101);

if($module=='infoList'){
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 101,
			"Desc"		 => "合作商详情"
		),
		'extparam'=>array(
			"Tag" 	 => "PartnerInfoList",
			"Id"	 => intval($_GET['id']),
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	$info = request($param);
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','admin'));
	include template('partner/partner_info.html',$tpl);
}elseif($module == 'partnerAdd'){
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 103,
			"Desc"		 => "合作商添加"
		),
		'extparam'=>array(
			"Tag" 	 => "PartnerAdd",
			"Data"	 => $_POST['info'],
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	
	$result = request($param);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[101]['url']);
	}else{
		ShowMsg($result['FlagString'],$link_array[103]['url']);
	}
}elseif($module == 'partnerUpdate'){
	$id = intval($_POST['id']);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10016,
			"ParentId"   => 10027,
			"ChildId"	 => 110,
			"Desc"		 => "合作商修改"
		),
		'extparam'=>array(
			"Tag" 	 => "PartnerUpdate",
			"Data"	 => $_POST['info'],
			"Id"	 => $id,
			"GroupId"=> $__ADMIN_CURGROUP['groupid']
		)
	);
	
	$result = request($param);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[101]['url']);
	}else{
		ShowMsg($result['FlagString'],$link_array[110]['url'].'&id='.$id);
	}
}