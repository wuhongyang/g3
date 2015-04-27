<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$user=checkDpLogin();
$uin=$user['Uin'];

$module = isset($_GET['module']) ? $_GET['module'] : 'price';

//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
$groupId=(int)$permisssions['groupId'];
$permission=(array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];
if(!checkGroupPermission(10640,$permission)){
	alertMsg('无权访问','/group/mgr.html');
}

switch($module){
	case 'price':
		$param = array(
				'extparam'=>array('Tag'=>'GetPrice','GroupId'=>$groupId),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10640,'ChildId'=>102,'Desc'=>'点歌设置读取')
		);
		$rst = request($param);
		$data = $rst['Data'];
		break;
	case 'save_price':
		$pick_price = intval($_POST['pick_price']);
		$act_percentage = abs(intval($_POST['act_percentage']));
		$tax_percentage = abs(100 - $act_percentage);
		if($pick_price < 1000 || $pick_price%100 != 0){
			$arr = array('Flag'=>101, 'FlagString'=>'点播歌曲价格不能小于1000,并且要为100的整数倍');
			echo json_encode($arr);
			exit;
		}
		if($act_percentage + $tax_percentage != 100){
			$arr = array('Flag'=>101, 'FlagString'=>'百分比之和必须为100');
			echo json_encode($arr);
			exit;
		}
		$param = array(
				'extparam'=>array('Tag'=>'SavePrice','GroupId'=>$groupId,'PickPrice'=>$pick_price,'ActPercentage'=>$act_percentage,'TaxPercentage'=>$tax_percentage),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10640,'ChildId'=>101,'Desc'=>'点歌设置')
		);
		$rst = request($param);
		echo json_encode($rst);
		exit;
}

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('songs/'.$module.'.html',$tpl);