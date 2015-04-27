<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module = $_GET['module'];

//验证是否登陆
$user=checkDpLogin();

//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
$groupId=(int)$permisssions['groupId'];
$permission=(array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];

$classify = array(1=>'帮助',2=>'公告',3=>'关于我们');
$classify_desc = array(1=>'help',2=>'notice',3=>'about_us');

switch ($module) {
	case 'info':
		$info = array();
		$id = intval($_GET['id']);
		if($id > 0){
			$param = array(
				'extparam'=>array('Tag'=>'NoticeInfo','GroupId'=>$groupId,'Id'=>$id),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10572,'ChildId'=>101,'Desc'=>'公告详情')
			);
			$info = request($param);
			$info = (array)$info['Info'];
		}
		
		$template = 'notice_info';
		break;

	case 'update':
		$id = $_POST['id'];
		unset($_POST['id']);
		$_POST['group_id'] = $groupId;
		$param = array(
			'extparam'=>array('Tag'=>'NoticeUpdate','Id'=>$id,'Data'=>$_POST),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10572,'ChildId'=>102,'Desc'=>'公告更新')
		);
		$rst = request($param);
		exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		break;

	case 'add':
		$_POST['is_default'] = 0;
		$_POST['group_id'] = $groupId;
		$param = array(
			'extparam'=>array('Tag'=>'NoticeAdd','Data'=>$_POST),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10572,'ChildId'=>102,'Desc'=>'公告添加')
		);
		$rst = request($param);
		exit('<script>parent.callback('.$rst['Flag'].',"'.$rst['FlagString'].'");</script>');
		break;

	case 'del':
		$param = array(
			'extparam'=>array('Tag'=>'NoticeDel','Id'=>$_POST['id'],'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10572,'ChildId'=>103,'Desc'=>'公告删除')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	
	default:
		$param = array(
			'extparam'=>array('Tag'=>'NoticeList','GroupId'=>$groupId,'Data'=>$_GET),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10572,'ChildId'=>101,'Desc'=>'公告帮助列表')
		);
		$noticeList = request($param);
		$page = $noticeList['Page'];
		$noticeList = (array)$noticeList['List'];
		foreach ($noticeList as $key => $val) {
			$noticeList[$key]['url'] = 'http://'.$_SERVER['SERVER_NAME'].'/'.$classify_desc[$val['category']];
			if($val['is_default'] != 1){
				$noticeList[$key]['url'] .= '_'.$val['id'];
			}
			$noticeList[$key]['url'] .= '.html';
		}
		$template = 'notice_list';
		break;
}

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);