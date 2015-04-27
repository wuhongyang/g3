<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'price':$_GET['module'];

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

if(!checkGroupPermission(10579,$permission)){
	ShowMsg('无权访问',-1);
}

switch ($module) {
	case 'price':
		$param = array(
			'extparam'=>array('Tag'=>'GetPrice','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10579,'ChildId'=>101,'Desc'=>'广播价格查看')
		);
		$info = request($param);
		$data = $info['Data'];
		$data['room_bc_price'] = $data['room_bc_price']?$data['room_bc_price']:5000;
		$data['site_bc_price'] = $data['site_bc_price']?$data['site_bc_price']:20000;
		break;
	case 'signet_times':
		$param = array(
			'extparam'=>array('Tag'=>'GetPrice','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10579,'ChildId'=>101)
		);
		$info = request($param);
		$data = $info['Data'];
		$data['signet_times'] = $data['signet_times']?$data['signet_times']:30;
		break;
	case 'runway_price':
		$param = array(
			'extparam'=>array('Tag'=>'GetPrice','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10579,'ChildId'=>101)
		);
		$info = request($param);
		$data = $info['Data'];
		$data['runway_price'] = $data['runway_price']?$data['runway_price']:20000;
		break;	
	case 'save_price':
		$room_bc_price = $_POST['room_bc_price'];
		$site_bc_price = $_POST['site_bc_price'];
		$param = array(
				'extparam'=>array('Tag'=>'SavePrice','GroupId'=>$groupId,'Data'=>$_POST),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10579,'ChildId'=>101,'Desc'=>'广播价格保存')
		);
		$info = request($param);
		ShowMsg($info['FlagString'], "?module=".$_POST['back_module']);
}

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('broadcast/'.$module.'.html',$tpl);