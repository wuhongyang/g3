<?php
require_once '../library/global.fun.php';

$module = $_GET['module']?$_GET['module']:"finance_margin";

//当前使用站
$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

if(!$_GET['groupid']){
	$_GET['groupid'] = $__ADMIN_CURGROUP['groupid'];
}

switch($module){
	case "finance_margin":
		require_once 'finance_margin_config.php';
		$link_array = getLevellink(10002,10007,10426,101);
		$template = "finance_margin.html";
		//$date = $_GET['Date']?$_GET['Date']:date("Y-m-d");
		$param=array(
			'extparam' => array('Tag'=>'Finance_margin','Date'=>$_GET['Date']),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10426,'ChildId'=>101,'Desc'=>'财务对账-总账平衡查看')
		);
		$finance_margin = request($param);
		if($finance_margin['Flag']==100){
			foreach($finance_margin['map'] as $key=>$map){
				$finance_margin['map'][$key]['uptime']=date('Y-m-d',$map['uptime']);
			}
		}
		$finance_margin=$finance_margin['map'];
		break;
	case "finance_group_margin":
		require_once 'finance_group_margin_config.php';
		$link_array = getLevellink(10002,10007,10426,102);
		$template = "finance_group_margin.html";
		//$date = $_GET['Date']?$_GET['Date']:date("Y-m-d");
		$param=array(
			'extparam' => array('Tag'=>'Finance_group_margin','Date'=>$_GET['Date'], 'GroupId'=>trim($_GET['groupid'])),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10426,'ChildId'=>102)
		);
		$finance_group_margin = request($param);
		if($finance_group_margin['Flag']==100){
			foreach($finance_group_margin['map'] as $key=>$map){
				$finance_group_margin['map'][$key]['uptime']=date('Y-m-d',$map['uptime']);
			}
		}
		$finance_group_margin = $finance_group_margin['map'];
		break;
	case "finance_abnormal_running":
		$link_array = getLevellink(10002,10007,10426,103);
		$template = "finance_abnormal_running.html";
		$param=array(
			'extparam' => array('Tag'=>'Finance_abnormal_running','Date'=>$_GET['Date'], 'GroupId'=>trim($_GET['groupid'])),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10426,'ChildId'=>103)
		);
		$finance_abnormal_running = request($param);
		if($finance_abnormal_running['Flag'] == 100){
			$page = $finance_abnormal_running['page'];
			foreach($finance_abnormal_running['map'] as $key=>$val){
				$finance_abnormal_running['map'][$key]['trade_desc'] = rawurldecode($val['trade_desc']);
			}
		}
		$finance_abnormal_running = $finance_abnormal_running['map'];
		break;
	case "finance_manage":
		$_GET['startDate'] = $_GET['startDate'] ? $_GET['startDate'] : date('Y-m').'-1';
		$_GET['endDate'] = $_GET['endDate'] ? $_GET['endDate'] : date('Y-m-d');
		$StartDate = $_GET['startDate'];
		$EndDate = $_GET['endDate'];
		$link_array = getLevellink(10002,10007,10426,104);
		$template = "finance_manage.html";
		$param=array(
			'extparam' => array('Tag'=>'Finance_manage','StartDate'=>$StartDate,'EndDate'=>$EndDate,'GroupId'=>trim($_GET['groupid'])),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10426,'ChildId'=>104)
		);
		$finance_manage = request($param);
		$page = $finance_manage['page'];
		$finance_manage = $finance_manage['map'];
		break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('kmoney/'.$template,$tpl);
?>