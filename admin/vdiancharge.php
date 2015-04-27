<?php
require_once '../library/global.fun.php';

$module = empty($_GET['module']) ? 'charge' : trim($_GET['module']);
$link_array = getLevellink(10002,10007,10275,105);
$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);
$__ADMIN_CURGROUP_ID = $__ADMIN_CURGROUP['groupid'];
switch($module){
	case 'charge'://站V点充值
		$uin = intval($_POST['GroupId']);
		$MoneyWeight = intval($_POST['MoneyWeight']);
		if($uin && $MoneyWeight){
			$param = array(
				'extparam' => array('Tag'=>"Charge","Group_id"=>$uin,'Desc'=>$_POST['Desc'],'MoneyWeight'=>$MoneyWeight,'Child_id'=>$_POST['child_id']),
				'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10275,'ChildId'=>105)
			);
			$result = request($param);
			alertMsg($result['FlagString'],'vdiancharge.php?module=charge');
		}
		break;
	case 'pcharge'://站点科目划账
		$uin = intval($_POST['GroupId']);
		$MoneyWeight = intval($_POST['MoneyWeight']);
		$bigcase_id = intval($_POST['bigcase_id']);
		$case_id = intval($_POST['case_id']);
		$parent_id = intval($_POST['parent_id']);
		$child_id = intval($_POST['child_id']);
		if($uin && $MoneyWeight){
			$param = array(
				'extparam' => array('Tag'=>"PCharge","Group_id"=>$uin,'Desc'=>$_POST['Desc'],'MoneyWeight'=>$MoneyWeight,'BigCaseId'=>$bigcase_id,'CaseId'=>$case_id,'ParentId'=>$parent_id,'ChildId'=>$child_id),
				'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10275,'ChildId'=>103)
			);
			$result = request($param);
			alertMsg($result['FlagString'],'vdiancharge.php?module=pcharge');
		}
		break;
	case 'ucharge'://站点用户划账
		$uin = intval($_POST['uin']);
		$group_id = intval($_POST['GroupId']);
		$MoneyWeight = intval($_POST['MoneyWeight']);
		$child_id = intval($_POST['child_id']);
		if($uin && $MoneyWeight){
			$param = array(
				'extparam' => array('Tag'=>"UCharge","Group_id"=>$group_id,"Uin"=>$uin,'Desc'=>$_POST['Desc'],'MoneyWeight'=>$MoneyWeight,'ChildId'=>$child_id),
				'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10275,'ChildId'=>101)
			);
			$result = request($param);
			alertMsg($result['FlagString'],'vdiancharge.php?module=ucharge');
		}
		break;
	case 'mcharge'://错账调账
		$uin = intval($_POST['GroupId']);
		$MoneyWeight = intval($_POST['MoneyWeight']);
		$child_id = intval($_POST['child_id']);
		if($uin && $MoneyWeight){
			$param = array(
				'extparam' => array('Tag'=>"MCharge","Group_id"=>$uin,'Desc'=>$_POST['Desc'],'MoneyWeight'=>$MoneyWeight,'ChildId'=>$child_id),
				'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10275,'ChildId'=>102)
			);
			$result = request($param);
			alertMsg($result['FlagString'],'vdiancharge.php?module=pcharge');
		}
		break;
	default:
		exit('module wrong');
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('kcost/'.$module.".html",$tpl);