<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'rank_list':$_GET['module'];

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

$roles = array('roomer'=>'室主','artist'=>'艺人','agent'=>'代理');

if(!checkGroupPermission(10329,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}

//站详情
$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
);
$userGroupInfo=request($param);
$userGroupInfo=$userGroupInfo['Result'];
//绑定的积分规则表
$param=array(
	'extparam'=>array('Tag'=>'getBusinessRule'),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'GroupId'=>$groupId,'Desc'=>'')
);
$ruleList = request($param);
$ruleList = $ruleList['list'];

switch ($module) {
	case 'rank_list':
		$title='主页装修-排行榜设置';
		$template = 'rank_list';
		$param = array(
			'extparam'=>array('Tag'=>'RankList','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10329,'ChildId'=>101,'Desc'=>'首页排行榜列表读取')
		);
		$list = request($param);
		$rankList = $list['List'];
		
		$indexRank = $rankList['index_rank'];
		if(!empty($indexRank)){
			$indexRank = (array)unserialize($indexRank);
		}else{
			$indexRank = array();
		}

		$rank = $rankList['rank'];
		if(!empty($rank)){
			$rank = (array)unserialize($rank);
		}else{
			$rank = array();
		}
		
		foreach ($indexRank as $key1=>$val1){
			foreach ($ruleList as $key=>$val){
				if($val['id'] == $val1['rule']){
					$indexRank1[] = array(
									"rule"     => $val1['rule'],
									"name"     => $val1['name'],
									"Row"      => $val1['Row'],
									"rule_name"=> $val['name'],
									"desc"     => $val['desc'],
									);
				}
			}
		}
		foreach ($rank as $key1=>$val1){
			foreach ($ruleList as $key=>$val){
				if($val['id'] == $val1['rule']){
					$rank1[] = array(
								"rule"     => $val1['rule'],
								"name"     => $val1['name'],
								"Row"      => $val1['Row'],
								"rule_name"=> $val['name'],
								"desc"     => $val['desc'],
								);
				}
			}
		}
		break;
	case 'rank_index_save':
		//排重
		$_POST['index_rule'] = $_POST['index_rule']?$_POST['index_rule']:array();
		$rule = array();
		foreach ($_POST['index_rule'] as $key=>$val){
			if(!in_array($val, $rule)){
				$ranks[] = array(
								"rule" => $val,
								"name" => $_POST['index_name'][$key],
								"Row" => $_POST['index_show_num'][$key],
							);
			}
			$rule[] = $val;
		}
		$param = array(
			'extparam'=>array('Tag'=>'RankIndexSave','GroupId'=>$groupId,'Rank'=>$ranks),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10329,'ChildId'=>101,'Desc'=>'保存首页排行榜')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
	case 'rank_save':
		//排重
		$_POST['rule'] = $_POST['rule']?$_POST['rule']:array();
		$rule = array();
		foreach ($_POST['rule'] as $key=>$val){
			if(!in_array($val, $rule)){
				$ranks[] = array(
								"rule" => $val,
								"name" => $_POST['name'][$key],
								"Row" => $_POST['show_num'][$key]
							);
			}
			$rule[] = $val;
		}
		$param = array(
			'extparam'=>array('Tag'=>'RankSave','GroupId'=>$groupId,'Rank'=>$ranks),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10053,'ParentId'=>10329,'ChildId'=>104,'Desc'=>'保存排行榜')
		);
		$rst = request($param);
		exit(json_encode($rst));
		break;
}

$tool = 'rank';
$serviceType = 'decoration';

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('decoration/'.$template.'.html',$tpl);
