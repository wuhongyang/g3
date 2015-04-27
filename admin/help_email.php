<?php
require_once '../library/global.fun.php';
$module = $_GET['module'];
$link_array = getLevellink(10002,10040,10274,101);

switch($module){
	default:
	case 'list':
		$start_time = $_GET['StartTime']?strtotime($_GET['StartTime']):-1;
		$end_time = $_GET['EndTime']?strtotime($_GET['EndTime']):-1;
		$param = array(
				'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10274,'ChildId'=>101,'Desc'=>'发送邮件列表查询'),
				'extparam' => array('Tag'=>'EmailList', 'StartTime'=>$start_time, 'EndTime'=>$end_time)
			);
		$result = request($param);
		$tpl = "email_list.html";
		break;
	case 'detail':
		$param = array(
				'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10274,'ChildId'=>101,'Desc'=>'发送邮件内容查询'),
				'extparam' => array('Tag'=>'EmailDetail', 'Id'=>$_GET['id'])
			);
		$result = request($param);
		$title = $result['Data']['title'];
		$content = $result['Data']['content'];
		$users = json_decode($result['Data']['users'], true);
		$users_count = count($users);
		$tpl = "email_detail.html";
		break;
	case 'add':
		if($_POST){
			$param = array(
				'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10274,'ChildId'=>102,'Desc'=>'邮件添加发送'),
				'extparam' => array('Tag'=>'AddEmail','Data'=>array('Title'=>$_POST['title'], 'Content'=>$_POST['content']))
			);
			$result = request($param);
			alertMsg($result['FlagString'], "?module=list");
		}
		$tpl = "email_add.html";
		break;
}

$template = template::getInstance();
$template->setOptions(get_config('template','admin'));
include template("help/".$tpl,$template);