<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'list':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
$permisssions=getDpUserPermission($user['Uin']);
$groupId=(int)$permisssions['groupId'];

//站详情
$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId,'IsDetails'=>true),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
);
$userGroupInfo=request($param);
if($userGroupInfo['Flag']!=100){
	alertMsg($userGroupInfo['FlagString'],'/');
}
$userGroupInfo=$userGroupInfo['Result'];

switch ($module){
	case "add":
		$data = array(
			"area_id" => $groupId,
			"area_name" => $userGroupInfo['name'],
			"response_user" => $user['Uin'],
			"response_nick" => $user['Nick'],
			"response_email" => $user['Email'],
			"response_phone" => $user['Phone']
		);
		$html = socket_request("http://faq.kkyoo.com/Aodian_Issue_Tracking/index.php?a=ticketGroup&m=group_add", $data);
		echo $html;
		exit;
		break;
	case "list":
		$template = "list";
		$data = array(
			"area_id" => $groupId,
		);
		if($_GET['id']) $data['id'] = $_GET['id'];
		if($_GET['initiate_type_id']) $data['initiate_type_id'] = $_GET['initiate_type_id'];
		if($_GET['status']) $data['status'] = $_GET['status'];
		if($_GET['bg_date']) $data['bg_date'] = $_GET['bg_date'];
		if($_GET['ed_date']) $data['ed_date'] = $_GET['ed_date'];
		$html = socket_request("http://faq.kkyoo.com/Aodian_Issue_Tracking/index.php?a=ticketGroup&m=group_index", $data);
		echo $html;
		exit;
		break;
}

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('issue_tracking/'.$module.'.html',$tpl);