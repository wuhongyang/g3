<?php
include_once 'common.php';

$template = $GroupData['Template'];
if(!in_array($template, array('aisvv', 'cc51'))){
	exit('您的站还没有该功能');
}

$module=empty($_GET['module'])?'funds_list':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$groupId=(int)$permisssions['groupId'];
$permission=(array)$permisssions['permission'];

if(!checkGroupPermission(10632,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

switch($module){
	case 'funds_list':
		$room_id = $_GET['room_id'];
		$param = array(
				'param' => array(
						'BigCaseId'=>10006,
						'CaseId'=>10047,
						'ParentId'=>10632,
						'ChildId'=>104),
				'extparam'=>array(
						'Tag'=>'GroupFunds',
						'GroupId'=>$groupId,
						'RoomId'=>$room_id,
						'Template'=>$template
						)
		);
		$res = request($param);
		break;
	case 'exchange_details':
		$room_id = $_GET['room_id']?intval($_GET['room_id']):0;
		$start_date = $_GET['startDate']?strtotime($_GET['startDate']):0;
		$end_date = $_GET['endDate']?(strtotime($_GET['endDate'])+24*60*60):0;
		
		$param = array(
				'param' => array(
						'BigCaseId'=>10006,
						'CaseId'=>10047,
						'ParentId'=>10632,
						'ChildId'=>101),
				'extparam'=>array(
						'Tag'=>'ExchangeDetails',
						'GroupId'=>$groupId,
						'RoomId'=>$room_id,
						'StartDate'=>$start_date,
						'EndDate'=>$end_date,
						'Template'=>$template
						)
		);
		$res = request($param);
		$page = $res['Page'];
		break;
}

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('funds/'.$module.'.html',$tpl);