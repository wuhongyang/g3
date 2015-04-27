<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$user=checkDpLogin();
$uin=$user['Uin'];

$module = isset($_GET['module']) ? $_GET['module'] : '';

//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
$groupId=(int)$permisssions['groupId'];
$permission=(array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];
if(!checkGroupPermission(10466,$permission)){
	alertMsg('无权访问','/group/mgr.html');
}

switch($module){
	case 'role_order_config':
		if(isset($_POST) && !empty($_POST)){
			$param=array(
				'extparam'=>array('Tag'=>'SaveRoleOrderConfig','GroupId'=>$groupId,'RoleOrderType'=>$_POST['role_order_type']),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10466,'ChildId'=>103,'Uin'=>$uin,'Desc'=>'角色排序设置')
			);
			$rst = request($param);
			//$rst = array('Flag'=>100,'FlagString'=>'保存成功');
			exit(json_encode($rst));
		}else{
			//站详情
			$param=array(
				'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId,'IsDetails'=>true),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$uin,'Desc'=>'获取站信息')
			);
			$userGroupInfo=request($param);
			$role_order_type = $userGroupInfo['Result']['role_order_type'];
			$template = 'role_order_config';
		}
		break;

	default:
		break;
}

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('group_manage/'.$template.'.html',$tpl);