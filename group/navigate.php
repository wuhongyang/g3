<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module = empty($_GET['module'])?'navigate_list':$_GET['module'];

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

if(!checkGroupPermission(10338,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

//站详情
$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
);
$userGroupInfo=request($param);
if($userGroupInfo['Flag']!=100){
	alertMsg($userGroupInfo['FlagString'],'/');
}
/*
if($userGroupInfo['Result']['init'] < 1){
	alertMsg('请先初始化','/group/decoration.php?module=init&url='.urlencode($_SERVER['REQUEST_URI']));
}*/

$modules = array('1' => '排行榜', '2' => '活动中心', '3' => '加入我们', '4' => '首页', '5' => '会员搜索', '6'=>'商城');

switch ($module) {
	case 'navigate_list':
		$modules_JSON = json_encode($modules);
		$param=array(
			'extparam'=>array('Tag'=>'NavigateList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10338,'ChildId'=>101,'Desc'=>'导航模块列表')
		);
		$navigateList = request($param);
		$navigateList = $navigateList['List'];

		$template = 'navigate_list';
		break;
	case 'navigate_add':
		$module_name = $modules[intval($_POST['m'])];
		if(empty($module_name)){
			exit(json_encode(array('Flag'=>101,'FlagString'=>'非法的模块名')));
		}
		$data = array('group_id' => $groupId, 'name' => $_POST['name'], 'module_name' => $module_name);
		$param = array(
			'extparam'=>array('Tag'=>'NavigateAdd','GroupId'=>$groupId,'Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10338,'ChildId'=>102,'Desc'=>'编辑导航模块')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'navigate_info':
		$id = $_GET['id'];
		$param=array(
			'extparam'=>array('Tag'=>'NavigateInfo','GroupId'=>$groupId,'Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10338,'ChildId'=>101,'Desc'=>'导航模块详情')
		);
		$info = request($param);
		exit(json_encode($info['Info']));
		break;
	case 'navigate_edit':
		$id = $_POST['id'];
		$name = $_POST['name'];
		$param = array(
			'extparam'=>array('Tag'=>'NavigateEdit','GroupId'=>$groupId,'Id'=>$id,'Data'=>array('name'=>$name)),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10338,'ChildId'=>102,'Desc'=>'编辑导航模块')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'left_move':
		$id = $_POST['id'];
		$param=array(
			'extparam'=>array('Tag'=>'LeftMove','GroupId'=>$groupId,'Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10338,'ChildId'=>103,'Desc'=>'导航模块左移')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'right_move':
		$id = $_POST['id'];
		$param=array(
			'extparam'=>array('Tag'=>'RightMove','GroupId'=>$groupId,'Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10338,'ChildId'=>103,'Desc'=>'导航模块右移')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'visible':
		$id = $_POST['id'];
		$param=array(
			'extparam'=>array('Tag'=>'Visible','GroupId'=>$groupId,'Id'=>$id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10338,'ChildId'=>104,'Desc'=>'设置导航模块')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
}

$tool = 'recommend';
$serviceType = 'decoration';

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);