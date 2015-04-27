<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'image_list':$_GET['module'];

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

if(!checkGroupPermission(10394,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

switch ($module) {
	case 'image_list':
		$param = array(
			'extparam'=>array('Tag'=>'ImageList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10394,'ChildId'=>101,'Desc'=>'图片模块列表读取')
		);
		$list = request($param);
		$imageList = $list['List'];
		$template = 'image_list';
		break;
	case 'image_info':
		$param = array(
			'extparam'=>array('Tag'=>'ImageInfo','Id'=>$_GET['id'],'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10394,'ChildId'=>101,'Desc'=>'图片模块详情')
		);
		$info = request($param);
		exit(json_encode((array)$info['Info']));
		break;
	case 'image_add':
		$data = $_POST;
		$data['group_id'] = $groupId;
		$param = array(
			'extparam'=>array('Tag'=>'ImageAdd','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10394,'ChildId'=>102,'Desc'=>'图片模块添加')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'image_edit':
		$data = $_POST;
		$id = $data['id'];
		$data['group_id'] = $groupId;
		$param = array(
			'extparam'=>array('Tag'=>'ImageEdit','Id'=>$id,'Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10394,'ChildId'=>102,'Desc'=>'图片模块编辑')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'order':
		$param = array(
			'extparam'=>array('Tag'=>'Order','Id'=>$_POST['id'],'GroupId'=>$groupId,'Type'=>$_POST['type']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10394,'ChildId'=>103,'Desc'=>'图片模块排序')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'visible':
		$param = array(
			'extparam'=>array('Tag'=>'Visible','Id'=>$_POST['id'],'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10394,'ChildId'=>105,'Desc'=>'图片模块显示')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'image_del':
		$param = array(
			'extparam'=>array('Tag'=>'ImageDel','Id'=>$_POST['id'],'GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10394,'ChildId'=>104,'Desc'=>'图片模块删除')
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