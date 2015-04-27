<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'vip_list':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
$Uid=$user['Uid'];
$Uin=$user['Uin'];
$Nick=$user['Nick'];
$serviceType='vip';
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
$group_id=(int)$permisssions['groupId'];
$groupId= $group_id;
$permission=(array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];

$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$group_id),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101)
);
$groupinfo=request($param);
$Ginfo=$groupinfo['Result'];
if($groupinfo['Flag']!=100){
	ShowMsg('站不存在','/');
}

//是否有会员管理的权限
if(checkGroupPermission(10267,$permission)){
	$vipList=true;
}
//是否有会员申请管理的权限
if(checkGroupPermission(10268,$permission)){
	$vipInfoList=true;
}

switch($module){
	default:
	case 'vip_list':
		//权限判断
		if(!$vipList){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$param=array(
			'extparam'=>array('Tag'=>'VipList','Uin'=>$_POST['uin'],'GroupId'=>$group_id,'sex'=>$_POST['sex'],'province'=>$_POST['province'],'city'=>$_POST['city'],'status'=>1),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10048,'ParentId'=>10267,'ChildId'=>101,'Desc'=>'会员列表')
		);
		$result = request($param);
		$lists = $result['List'];
		$page = $result['Page'];
		$p = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetAllProvince')));
		$p = (array)$p['Result'];
		unset($p[0]);
		foreach($p as $province){
			$provinces[$province['province_id']] = $province['province_name'];
		}
		$title = '会员管理-会员管理列表';
		$file = "vip_list.html";
		break;
	case 'vip_info' :
		//权限判断
		if(!$vipList){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$param=array(
			'extparam'=>array('Tag'=>'VipInfo','Uin'=>$_GET['Uin'],'GroupId'=>$group_id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10048,'ParentId'=>10267,'ChildId'=>102,'Desc'=>'查看会员信息')
		);
		$result = request($param);
		$info = $result['baseInfo'];
		$areaName = '';
		if($info['province'] > 0){
			$p = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetProvinceName','ProvinceId'=>$info['province'])));
			$areaName .= $p['provinceName'];
		}
		if($info['city'] > 0){
			$c = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$info['city'])));
			$areaName .= $c['cityName'];
		}
		$title = '会员管理-会员信息';
		$file = "vip_info.html";
		break;
	case 'vip_state':
		$param=array(
				'extparam'=>array('Tag'=>'SetState'),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10048,'ParentId'=>10267,'ChildId'=>106,'TargetUin'=>$_GET['Uin'],'GroupId'=>$group_id)
		);
		$result = request($param);
		ShowMsg($result['FlagString'], "?module=vip_list");
		break;
	case 'edit_pass':
		if($_POST){
			if(strlen($_POST['pass']) < 6){
				echo json_encode(array("Flag"=>101,"FlagString"=>"密码长度不能小于6位"));
				exit;
			}
			$param=array(
					'extparam'=>array('Tag'=>'EditPass', "uin"=>intval($_POST['uin']), "pass"=>md5(intval($_POST['pass']))),
					'param'=>array('BigCaseId'=>10006,'CaseId'=>10048,'ParentId'=>10267,'ChildId'=>107,'GroupId'=>$group_id)
			);
			$result = request($param);
		}
		echo json_encode($result);
		break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('vip_manage/'.$file,$tpl);