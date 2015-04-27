<?php
require_once 'common.php';

$template = $GroupData['Template'];
if(!$user_fund_role || !in_array($template, array('aisvv', 'cc51','kaichang'))){
	ShowMsg("没用权限", -1);
}

$module=empty($_GET['module'])?'index':$_GET['module'];
$template = $template=='kaichang' ? 'aisvv' :$template;//开唱模板房间公积金和爱尚功能一样
switch($module){
	case 'index':
		$param = array(
			'param' => array(
					'BigCaseId'=>10004,
					'CaseId'=>10067,
					'ParentId'=>10630,
					'ChildId'=>101),
			'extparam'=>array(
				'Tag'=>'FundsList',
				'Uin'=>$user['Uin'],
				'GroupId'=>$group_id,
				'Template'=>$template
				)
		); 
		$res = request($param);
		$page_size = 3;
		$count = ceil(count($res['Data'])/$page_size);
		$file = "index.html";
		break;
	case 'get_funds':
		$room_id = intval($_GET['room_id']);
		$rule_id = intval($_GET['type']);
		if($room_id && $rule_id){
			$param = array(
				'param' => array(
						'BigCaseId'=>10004,
						'CaseId'=>10067,
						'ParentId'=>10630,
						'ChildId'=>102,
						'MoneyWeight'=>intval($_GET['w'])),
				'extparam'=>array(
					'Tag'=>'FundsExchange',
					'RoomId'=>$room_id,
					'RuleId'=>$rule_id,
					'GroupId'=>$group_id,
					'Uin'=>$user['Uin'],
					'Template'=>$template
					)
			); 
			$res = request($param);
			ShowMsg($res['FlagString'], "?module=index");
		}else{
			ShowMsg("系统错误", -1);
		}
		break;
	case 'details':
		$room_id = $_GET['room_id']?intval($_GET['room_id']):0;
		$start_date = $_GET['startDate']?strtotime($_GET['startDate']):0;
		$end_date = $_GET['endDate']?(strtotime($_GET['endDate'])+24*60*60):0;
		$param = array(
				'param' => array(
						'BigCaseId'=>10004,
						'CaseId'=>10067,
						'ParentId'=>10631,
						'ChildId'=>101),
				'extparam'=>array(
						'Tag'=>'ExchangeDetails',
						'Uin'=>$user['Uin'],
						'GroupId'=>$group_id,
						'RoomId'=>$room_id,
						'StartDate'=>$start_date,
						'EndDate'=>$end_date,
						'Template'=>$template,
						)
		);
		$res = request($param);
		$page = $res['Page'];
		$file = "details.html";
		break;
}

$serviceType = 'funds';

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/service/';
$tmp_config['cache_dir'].=$themes.'/tpl/service/';
$tpl = template::getInstance();
$tpl->setOptions($tmp_config);

include template('funds/'.$file,$tpl);