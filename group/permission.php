<?php
include '../library/global.fun.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'permissionInfo';

$user = checkDpLogin();

$permisssions = getDpUserPermission($user['Uin']);
$isDz = (int)$permisssions['isDz'];
$groupId = (int)$permisssions['groupId'];
$permission = (array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];

//权限判断
if(!checkGroupPermission(10281,$permission)){
	alertMsg('没有权限','group.php?module=group_info');
}

//判断是否有角色组的权限
if(checkGroupPermission(10280,$permission)){
	$roleList=true;
}

if($module == 'permissionInfo'){
	$id = intval($_GET['id']);
	$list = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetInfoUnderBig','BigCaseId'=>10006)));
	$list = (array)$list['Result'];
	$param = array(
		'extparam' => array('Tag'=>'PermissionInfo','Id'=>$id,'GroupId'=>$groupId),
		'param'    => array('BigCaseId'=>10006,'CaseId'=>10051,'ParentId'=>10281,'ChildId'=>101,'Desc'=>'添加角色用户组')
	);
	$rst = request($param);
	$info = (array)$rst['Info'];

	$temp = 'permission_info';
}elseif($module == 'setPermission'){
	$_POST['group_id'] = $groupId;
	$_POST['uin'] = $user['Uin'];
	$_POST['isDz'] = $isDz;
	$param = array(
		'extparam' => array('Tag'=>'SetPermission','Data'=>$_POST),
		'param'    => array('BigCaseId'=>10006,'CaseId'=>10051,'ParentId'=>10281,'ChildId'=>102,'Desc'=>'保存角色组权限')
	);
	$rst = request($param);
	exit(json_encode($rst));
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template("permission/{$temp}.html",$tpl);