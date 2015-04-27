<?php
require_once '../library/global.fun.php';

$module = empty($_GET['module']) ? 'cluster_create' : trim($_GET['module']);

switch($module){
	case 'cluster_create':
		$Action = $_GET['action'];
		$Cluster = $_POST['cluster'];
		$Status = $_POST['status'];
		$Cid = $_GET['cid'];
		$ChildId = 102;
		$param = array(
			'extparam' => array('Tag'=>'Cluster_create','Action'=>$Action,'Cluster'=>$Cluster,'Status'=>$Status,'Cid'=>$Cid),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10010,'ChildId'=>$ChildId,'Desc'=>'新建权限组')
		);
		$admin_array = request($param);
		if($Action){
			exit(json_encode($admin_array));
		}
		$template = 'sys_usercreate.html';
		break;
	case 'cluster_editor':
		$Action = $_GET['action'];
		$Cluster = $_POST['cluster'];
		$Status = $_POST['status'];
		$Cid = $_GET['cid'];
		$ChildId = 103;
		$param = array(
			'extparam' => array('Tag'=>'Cluster_editor','Action'=>$Action,'Cluster'=>$Cluster,'Status'=>$Status,'Cid'=>$Cid),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10010,'ChildId'=>$ChildId,'Desc'=>'修改权限组')
		);
		$admin_array = request($param);
		if($Action){
			exit(json_encode($admin_array));
		}
		$template = 'sys_usercreate.html';
		break;
	case 'cluster_list':
		$Cluster = $_GET['cluster'];
		$Status = $_GET['status'];
		$param = array(
			'extparam' => array('Tag'=>'Cluster_list','Cluster'=>$Cluster,'Status'=>$Status),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10010,'ChildId'=>101,'Desc'=>'权限组分类列表')
		);
		$list_array = request($param);
		$link_array = getLevellink(10002,10006,10010,101);
		$page = $list_array['page'];
		unset($list_array['page']);
		$template = 'sys_userlist.html';
		break;
	case 'group_create':
		$Action = $_GET['action'];
		$Levels = $_POST['levels'];
		$Group_name = $_POST['group_name'];
		$CId = $_POST['CId'];
		$GId = $_POST['GId'];
		$param = array(
			'extparam' => array('Tag'=>'Group_create','Action'=>$Action,'Levels'=>$Levels,'Group_name'=>$Group_name,'CId'=>$CId,'Id'=>$Id,'GId'=>$GId),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10010,'ChildId'=>106,'Desc'=>'添加权限组分类')
		);
		$list_array = request($param);
		if($Action){
			exit(json_encode($list_array));
		}
		$cluster = $list_array['cluster'];
		$group_info = $list_array['group_info'];
		$levels = $list_array['login_info'];
		$template = 'sysuser_modify.html';
		break;
	case 'group_editor':
		$Action = $_GET['action'];
		$Levels = $_POST['levels'];
		$Group_name = $_POST['group_name'];
		$CId = $_POST['CId'];
		$GId = $_POST['GId'];
		$Id = $_GET['id'];
		$param = array(
			'extparam' => array('Tag'=>'Group_editor','Action'=>$Action,'Levels'=>$Levels,'Group_name'=>$Group_name,'CId'=>$CId,'Id'=>$Id,'GId'=>$GId),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10010,'ChildId'=>108,'Desc'=>'修改权限组分类')
		);
		$list_array = request($param);
		if($Action){
			exit(json_encode($list_array));
		}
		$cluster = $list_array['cluster'];
		$group_info = $list_array['group_info'];
		$levels = $list_array['login_info'];
		$template = 'sysuser_modify.html';
		break;
	case 'group_list':
		$Group = $_GET['group'];
		$Status = $_GET['status'];
		$Cid = $_GET['cid'];
		$param = array(
			'extparam' => array('Tag'=>'Group_list','Group'=>$Group,'Status'=>$Status,'Cid'=>$Cid),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10006,'ParentId'=>10010,'ChildId'=>107,'Desc'=>'权限组列表')
		);
		$list_array = request($param);
		$link_array = getLevellink(10002,10006,10010,107);
		$page = $list_array['page'];
		unset($list_array['page']);
		$template = 'group_list.html';
		break;
	default:
		exit('module wrong');
		
}
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('admin/'.$template,$tpl);
