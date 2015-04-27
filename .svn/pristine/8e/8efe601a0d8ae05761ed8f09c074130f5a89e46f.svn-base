<?php
require_once '../library/global.fun.php';
$module = $_GET['module']?$_GET['module']:"list";

switch($module){
	case "list":
		$start_time = $_GET['StartTime']?strtotime($_GET['StartTime']):0;
		$end_time = $_GET['EndTime']?strtotime($_GET['EndTime']." 23:59:59"):0;
		$groupId = $_GET['groupId'];
		$param = array(
				'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10641,'ChildId'=>101,'Desc'=>'意向用户信息'),
				'extparam' => array('Tag'=>'UserIntention', 'StartTime'=>$start_time, 'EndTime'=>$end_time, 'GroupId'=>$groupId)
			);
		$result = request($param);
		$tpl = 'intentional_customer/list.html';
		break;
}

$template = template::getInstance();
$template->setOptions(get_config('template','admin'));

include template("{$tpl}",$template);