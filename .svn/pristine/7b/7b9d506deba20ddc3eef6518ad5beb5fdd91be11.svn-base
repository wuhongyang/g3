<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'mic_setting_info':$_GET['module'];

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

if(!checkGroupPermission(10397,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

switch ($module) {
	case 'mic_setting_info':
		$param = array(
			'extparam'=>array('Tag'=>'MicSettingInfo','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10397,'ChildId'=>101,'Desc'=>'上麦用户设置查看')
		);
		$info = request($param);
		$info = (array)$info['Info'];
		$template = 'mic_setting';
		break;
	case 'mic_setting_save':
		$data = $_POST;
		$data['group_id'] = $groupId;
		$param = array(
			'extparam'=>array('Tag'=>'MicSettingSave','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10397,'ChildId'=>102,'Desc'=>'搜索功能配置保存')
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