<?php
include '../library/global.fun.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'roleList';

$user = checkDpLogin();

$permisssions = getDpUserPermission($user['Uin']);
$isDz = (int)$permisssions['isDz'];
$groupId = (int)$permisssions['groupId'];
$permission = (array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];
$title = '权限管理';

//权限判断
if(!checkGroupPermission(10280,$permission)){
	alertMsg('没有权限','group.php?module=group_info');
}

//判断是否有赋予权限的权限
if(checkGroupPermission(10281,$permission)){
	$permissionInfo=true;
}

if($module == 'roleList'){
	$title .= ' - 角色组列表';
	$param = array(
		'extparam' => array('Tag'=>'RoleList','GroupId'=>$groupId),
		'param'    => array('BigCaseId'=>10006,'CaseId'=>10051,'ParentId'=>10280,'ChildId'=>101,'Desc'=>'角色列表')
	);
	$roleList = request($param);
	$roleList = (array)$roleList['List'];
	$temp = 'role_list';
}elseif($module == 'info'){
	$title .= ' - 角色组列表';
	$id = intval($_GET['id']);
	if($id > 0){
		$param = array(
			'extparam' => array('Tag'=>'RoleInfo','Id'=>$id),
			'param'    => array('BigCaseId'=>10006,'CaseId'=>10051,'ParentId'=>10280,'ChildId'=>101,'Desc'=>'角色详情')
		);
		$role = request($param);
		$roleInfo = (array)$role['RoleInfo'];
		$uins = (array)$role['UinInfo'];
	}
	
	$temp = 'role_info';
}elseif($module == 'verifyUin'){
	$uin = intval($_GET['uin']);
	$param = array(
		'extparam' => array('Tag'=>'VerifyUin','Uin'=>$uin),
		'param'    => array('BigCaseId'=>10006,'CaseId'=>10051,'ParentId'=>10280,'ChildId'=>101,'Desc'=>'uin是否存在')
	);
	$userInfo = request($param);
	exit(json_encode($userInfo));
}elseif($module == 'update'){
	$param = array(
		'extparam' => array('Tag'=>'UpdateRole','Data'=>$_POST),
		'param'    => array('BigCaseId'=>10006,'CaseId'=>10051,'ParentId'=>10280,'ChildId'=>102,'Desc'=>'修改角色用户组')
	);
	$rst = request($param);
	exit(json_encode($rst));
}elseif($module == 'insert'){
	$_POST['group_id'] = $groupId;
	$param = array(
		'extparam' => array('Tag'=>'InsertRole','Data'=>$_POST),
		'param'    => array('BigCaseId'=>10006,'CaseId'=>10051,'ParentId'=>10280,'ChildId'=>102,'Desc'=>'添加角色用户组')
	);
	$rst = request($param);
	exit(json_encode($rst));
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template("permission/{$temp}.html",$tpl);