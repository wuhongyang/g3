<?php
require_once '../library/global.fun.php';

$module = empty($_GET['module']) ? 'user_list' : trim($_GET['module']);

switch($module){
	case 'user_create':
		$Action = $_GET['action'];
		$Passid = $_POST['passid'];
		$Gid = $_POST['group_id'];
		$Cid = $_POST['cluster_id'];
		$Status = $_POST['status'];
		$Passname = $_POST['passname'];
		$param = array(
			'extparam' => array('Tag'=>'User_create','Action'=>$Action,'Passid'=>$Passid,'Passname'=>$Passname,'Gid'=>$Gid,'Cid'=>$Cid,'Status'=>$Status),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10012,'ChildId'=>101,'Desc'=>'新建权限用户')
		);
		$list_array = request($param);
		if($Action){
			exit(json_encode($list_array));
		}
		$template = 'user_create.html';
		break;
	case 'user_editor':
		$Action = $_GET['action'];
		$Pid = $_GET['pid'];
		$Passid = $_POST['passid'];
		$Gid = $_POST['group_id'];
		$Cid = $_POST['cluster_id'];
		$Status = $_POST['status'];
		$Passname = $_POST['passname'];
		$param = array(
			'extparam' => array('Tag'=>'User_create','Action'=>$Action,'Passid'=>$Passid,'Passname'=>$Passname,'Gid'=>$Gid,'Cid'=>$Cid,'Status'=>$Status,'Pid'=>$Pid),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10012,'ChildId'=>103,'Desc'=>'修改权限用户')
		);
		$list_array = request($param);
		if($Action){
			exit(json_encode($list_array));
		}
		$template = 'user_create.html';
		break;
	case 'gruop_select':
		$Cid = $_GET['cid'];
		$param = array(
			'extparam' => array('Tag'=>'Gruop_select','Cid'=>$Cid),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10012,'ChildId'=>101,'Desc'=>'新建权限用户')
		);
		exit(json_encode(request($param)));
		break;
	case 'user_list':
		$Group = $_GET['Group'];
		$Status = $_GET['status'];
		$param = array(
			'extparam' => array('Tag'=>'User_list','Group'=>$Group,'Status'=>$Status),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10012,'ChildId'=>102,'Desc'=>'权限用户列表')
		);
		$list_array = request($param);
		$link_array = getLevellink(10002,10006,10012,102);
		$page = $list_array['page'];
		unset($list_array['page']);
		$template = 'user_list.html';
		break;
		
}
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('level/'.$template,$tpl);
