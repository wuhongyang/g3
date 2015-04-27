<?php
include_once('../library/global.fun.php');

$module = trim($_GET['module']);

if(isset($_POST) && !empty($_POST)){
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>153,"Desc"=>"保存角色权限"),
		'extparam'=>array("Tag"=>"SaveRolePermission","RoleId"=>$_POST['role_id'],'Data'=>$_POST['rules'])
	);
	$rst = request($param);
	if($rst['Flag'] == 100){
		$url = 'role.php?module=list&tpl_id='.$_GET['tpl_id'];
	}else{
		$url = -1;
	}
	alertMsg($rst['FlagString'],$url);
}else{
	$role_id = isset($_GET['id']) ? intval($_GET['id']) : 10000;
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>152,"Desc"=>"角色权限"),
		'extparam'=>array("Tag"=>"PermissionList","RoleId"=>$role_id)
	);
	$permissionList = request($param);
	$roleName = $permissionList['RoleName'];
	$permissionList = (array)$permissionList['PermissionList'];
	$temp = 'permission_list.html';
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('permission/'.$temp,$tpl);