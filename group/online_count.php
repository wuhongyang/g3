<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

//验证是否登陆
$user=checkDpLogin();

//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
$groupId=(int)$permisssions['groupId'];
$permission=(array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];

if(isset($_POST) && !empty($_POST)){
	$data['key'] = 'online_count';
	$data['value'] = $_POST;
	unset($_POST['online_count']);
	$param = array(
		'extparam'=>array('Tag'=>'SettingSave','GroupId'=>$groupId,'Data'=>$data),
		'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10571,'ChildId'=>102,'Desc'=>'在线人数统计配置保存')
	);
	$rst = request($param);
	exit(json_encode($rst));
}else{
	$param = array(
		'extparam'=>array('Tag'=>'SettingValue','GroupId'=>$groupId,'Key'=>'online_count'),
		'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10571,'ChildId'=>101,'Desc'=>'在线人数统计配置查看')
	);
	$info = request($param);
	$info = unserialize($info['Value']);
	$tpl=template::getInstance();
	$tpl->setOptions(get_config('template','group'));
	include template('decoration/online_count.html',$tpl);
}