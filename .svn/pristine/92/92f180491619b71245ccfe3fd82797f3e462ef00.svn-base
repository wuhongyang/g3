<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'list':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$permission=(array)$permisssions['permission'];
$group_id=(int)$permisssions['groupId'];

if(!checkGroupPermission(10429,$permission)){
	alertMsg('无权访问','/group/mgr.html');
}

$title = "商城管理-商城充值订单管理";

switch($module){
	case 'list':
		$search = $_GET;
		unset($search['module']);
		if($search['start_time']){
			$search['start_time'] = strtotime($search['start_time']);
		}
		if($search['end_time']){
			$search['end_time'] = strtotime($search['end_time']);
		}
		foreach($search as $k=>$v){
			$search[$k] = htmlspecialchars(addslashes($v));
		}
		$search['group_id'] = $group_id;
		$param = array(
				'extparam' => array('Tag'=>'GetList', 'Data'=>$search),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10474,'ChildId'=>101,'Desc'=>'订单列表读取')
		);
		$get = http_build_query($search);
		$result = request($param);
		$balance = get_parent_money(10006, 10049, 10269, $group_id);
		$list = (array)$result['List'];
		foreach($list as $key=>$value){
			$result = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$value['uin'])));
			$list[$key]['nick'] = empty($result['Nick']) ? $value['uin'] : $result['Nick'];
		}
		$page = $result['Page'];
		$start_time = date("Y-m-d")." 0:00:00";
		$end_time = date("Y-m-d", time()+24*60*60)." 0:00:00";
		break;
	case 'remedy':
		$param = array(
				'extparam' => array('Tag'=>'Remedy', 'TradeId'=>$_GET['trade_id']),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10474,'ChildId'=>102,'Desc'=>'商家:'.$group_id.' 补单')
		);
		$result = request($param);
		$get = $_GET;
		unset($get['module']);
		unset($get['trade_id']);
		$get = htmlspecialchars_decode(http_build_query($get));
		ShowMsg($result['FlagString'], "?module=list&".$get);
		break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('recharge_order/'.$module.".html",$tpl);