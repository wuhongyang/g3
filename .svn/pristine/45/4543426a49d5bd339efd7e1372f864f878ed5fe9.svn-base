<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';
require_once 'image_to_oss.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'sort_list';

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

if(!checkGroupPermission(10319,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

switch($module){
	case 'sort_list':
		$param=array(
			'extparam'=>array('Tag'=>'GetSortList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>102,'Desc'=>'获取站内房间分类')
		);
		$list = request($param);
		$title='主页装修-房间分类管理';
		$sortList = (array)$list['List'];
		$template = 'sort_list';
		break;
	case 'sort_info':
		$param=array(
			'extparam'=>array('Tag'=>'SortInfo','GroupId'=>$groupId,'Id'=>$_GET['id']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>102,'Desc'=>'获取站内房间分类详情')
		);
		$info = request($param);
		exit(json_encode((array)$info['Info']));
		break;
	case 'sort_add':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$iconMd5 = '';
			if($_POST['style'] > 0){
				$rst = check_upload($_FILES['custom_icon']);
				if($rst['Flag'] != 100){
					exit('<script>parent.callback(101,"'.$rst['FlagString'].'");</script>');
				}
				$rst = send_to_oss($_FILES['custom_icon']['tmp_name']);
				if($rst['Flag'] != 100){
					exit('<script>parent.callback(101,"上传图标失败");</script>');
				}
				$iconMd5 = $rst['File'];
			}
			
			$data = array('groupId'=>$groupId,'name'=>$_POST['name'],'icon'=>$iconMd5);
			$param=array(
				'extparam'=>array('Tag'=>'SortAdd','Data'=>$data),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>101,'Desc'=>'添加站内房间分类')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}else{
			exit('<script>parent.callback(101,"非法调用");</script>');
		}
		break;
	case 'sort_edit':
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$iconMd5 = '';
			if($_POST['style'] > 0){
				if(empty($_FILES['custom_icon']['name'])){
					$iconMd5 = $_POST['icon'];
				}else{
					$rst = check_upload($_FILES['custom_icon']);
					if($rst['Flag'] != 100){
						exit('<script>parent.callback(101,"'.$rst['FlagString'].'");</script>');
					}
					$rst = send_to_oss($_FILES['custom_icon']['tmp_name']);
					if($rst['Flag'] != 100){
						exit('<script>parent.callback(101,"上传图标失败");</script>');
					}
					$iconMd5 = $rst['File'];
				}
			}
			$data = array('name'=>$_POST['name'],'icon'=>$iconMd5);
			$param=array(
				'extparam'=>array('Tag'=>'SortUpdate','Data'=>$data,'Id'=>$_POST['id']),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>101,'Desc'=>'编辑站内房间分类')
			);
			$rst = request($param);
			exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		}else{
			exit('<script>parent.callback(101,"非法调用");</script>');
		}	
		break;
	case 'sort_del':
		$id = intval($_POST['id']);
		$param=array(
			'extparam'=>array('Tag'=>'SortDel','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>101,'Desc'=>'删除站内房间分类')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'up':
		$id = intval($_POST['id']);
		$param=array(
			'extparam'=>array('Tag'=>'Up','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>101,'Desc'=>'上移站内房间分类')
		);
		$rst = request($param);
		exit(json_encode($rst));
	case 'down':
		$id = intval($_POST['id']);
		$param=array(
			'extparam'=>array('Tag'=>'Down','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>101,'Desc'=>'下移站内房间分类')
		);
		$rst = request($param);
		exit(json_encode($rst));
	case 'room_list':
		$sortId = intval($_GET['sort_id']);
		$param=array(
			'extparam'=>array('Tag'=>'GetRoomList','SortId'=>$sortId,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>102,'Desc'=>'获取站内房间列表')
		);
		$list = request($param);
		$roomList = (array)$list['List'];
		$sortName = $list['SortName'];
		$rooms = (array)$list['Rooms'];
		unset($list);
		$template = 'room_list';
		break;
	case 'room_add':
		$roomId = intval($_POST['room_id']);
		$sortId = intval($_POST['sort_id']);
		$param=array(
			'extparam'=>array('Tag'=>'RoomAdd','RoomId'=>$roomId,'SortId'=>$sortId,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>101,'Desc'=>'添加分类下房间')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'room_del':
		$id = intval($_POST['id']);
		$param=array(
			'extparam'=>array('Tag'=>'RoomDel','Id'=>$id,'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10319,'ChildId'=>101,'Desc'=>'添加分类下房间')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
}

$tool = 'room_sort';
$serviceType='decoration';

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);