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
	$data['key'] = 'my_attention';
	$data['value'] = $_POST;
	$param = array(
		'extparam'=>array('Tag'=>'SettingSave','GroupId'=>$groupId,'Data'=>$data),
		'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10573,'ChildId'=>102,'Desc'=>'保存我的关注设置')
	);
	$rst = request($param);
	exit(json_encode($rst));
}else{
	$param = array(
		'extparam'=>array('Tag'=>'SettingValue','GroupId'=>$groupId,'Key'=>'my_attention'),
		'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10573,'ChildId'=>101,'Desc'=>'我的关注设置查看')
	);
	$info = request($param);
	$info = unserialize($info['Value']);
	$tpl=template::getInstance();
	$tpl->setOptions(get_config('template','group'));
	include template('decoration/my_attention.html',$tpl);
}