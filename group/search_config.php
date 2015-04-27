<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'search_config_info':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
$Uin=$user['Uin'];
$Nick=$user['Nick'];
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
$groupId=(int)$permisssions['groupId'];
$permission=(array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];

if(!checkGroupPermission(10395,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

switch ($module) {
	case 'search_config_info':
		$param = array(
			'extparam'=>array('Tag'=>'SearchConfigInfo','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10395,'ChildId'=>101,'Desc'=>'搜索功能配置查看')
		);
		$info = request($param);
		$info = $info['Info'];
		if(empty($info)){
			$info = array('common_search'=>0,'vip_search'=>0);
		}
		$template = 'search_config';
		break;
	case 'search_config_save':
		$data = $_POST;
		$data['group_id'] = $groupId;
		$param = array(
			'extparam'=>array('Tag'=>'SearchConfigSave','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10395,'ChildId'=>102,'Desc'=>'搜索功能配置保存')
		);
		$rst = request($param);
		echo json_encode($rst);
		exit;
		break;
}
//$tool = 'activity';
//$serviceType = 'decoration';

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);