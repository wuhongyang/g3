<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'message_list':$_GET['module'];

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

if(!checkGroupPermission(10396,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

switch ($module) {
	case 'message_list':
		$param = array(
			'extparam'=>array('Tag'=>'MessageList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10396,'ChildId'=>102,'Desc'=>'滚动消息列表读取')
		);
		$list = request($param);
		$messageInfo = $list['MessageInfo'];
		$messageList = $list['List'];
		$template = 'message_list';
		break;
	case 'message_info':
		$param = array(
			'extparam'=>array('Tag'=>'MessageInfo','GroupId'=>$groupId,'Id'=>$_GET['id']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10396,'ChildId'=>102,'Desc'=>'滚动消息详情')
		);
		$info = request($param);
		exit(json_encode((array)$info['Info']));
		break;
	case 'message_add':
		$data = $_POST;
		$data['group_id'] = $groupId;
		$param = array(
			'extparam'=>array('Tag'=>'MessageAdd','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10396,'ChildId'=>103,'Desc'=>'滚动消息添加')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'message_edit':
		$data = $_POST;
		$id = $data['id'];
		$data['group_id'] = $groupId;
		$param = array(
			'extparam'=>array('Tag'=>'MessageEdit','Id'=>$id,'Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10396,'ChildId'=>103,'Desc'=>'滚动消息编辑')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'order':
		$param = array(
			'extparam'=>array('Tag'=>'Order','Id'=>$_POST['id'],'GroupId'=>$groupId,'Type'=>$_POST['type']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10396,'ChildId'=>104,'Desc'=>'滚动消息排序')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'message_del':
		$param = array(
			'extparam'=>array('Tag'=>'MessageDel','Id'=>$_POST['id'],'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10396,'ChildId'=>105,'Desc'=>'滚动消息删除')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'title_save':
		$data = $_POST;
		$data['group_id'] = $groupId;
		$param = array(
			'extparam'=>array('Tag'=>'TitleSave','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10396,'ChildId'=>101,'Desc'=>'滚动消息标题保存')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
}

//$tool = 'activity';
//$serviceType = 'decoration';

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);