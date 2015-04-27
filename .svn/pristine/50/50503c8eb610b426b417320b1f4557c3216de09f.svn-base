<?php
include_once('../library/global.fun.php');
include 'config/data.php';

$module = trim($_GET['module']);

if($module=='list'){
    $link_array = getLevellink(10002,10069,10648,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 141,
			"Desc"		 => "业务规则列表查看"
		),
		'extparam'=>array(
			"Tag" 		 => "RuleDefineListPage",
			"SearchData" => $_GET['search']
		)
	);
	$result = request($param);
	$ruleDefineList = (array)$result['Result'];
	$page = $result['Page'];
	$temp = 'rule_define_list.html';
	
}elseif($module == 'info'){
	$link_array = getLevellink(10002,10008,10069,101);
	$id = intval($_GET['id']);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 141,
			"Desc"		 => "创建/查看/修改业务规则"
		),
		'extparam'=>array(
			"Tag" 		 => "RuleDefineInfo",
			"Id" 		 => $id
		)
	);
	$result = request($param);
	$info = $result['Info'];
	$extend = json_decode(urldecode($info['extend']),true);
	$extend['rankInfo'] = (array)$extend['rankInfo'];
	$rule = urldecode($info['rule']);
	$interfaces = $result['Interface']['Result'];
	
	//读取角色分组
	$param = array(
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>150,"Desc"=>"角色组列表"),
			'extparam'=>array("Tag"=>"RoleCate","IsNotPage" =>true,"SearchData"=>array("status"=>1,"tpl_id"=>$_GET['tpl_id']))
	);
	$result = request($param);
	$cateList = $result['CateList'];
	//读取角色
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理列表"),
		'extparam'=>array("Tag"=>"RoleList","SearchData" => array('rule'=>2,'status'=>1,"tpl_id"=>$_GET['tpl_id']),'IsNotPage'=>true)
	);
	$roleList = request($param);
	$roleList = $roleList['RoleList'];
	$roleListArr = array();
	$id_to_cate = array();
	foreach((array)$roleList as $one){
		$roleListArr[$one['cate_id']][] = array($one['id'], $one['name']);
		$id_to_cate[$one['cate_id']][] = $one['id'];
	}
	foreach($extend['rankInfo'] as $k=>$v){
		foreach($id_to_cate as $cate_id=>$id_arr){
			if(in_array($extend['rankInfo'][$k]['role'], $id_arr)){
				$extend['rankInfo'][$k]['cate_id'] = $cate_id;
				break;
			}	
		}
	}
	if($info['business_id_type']){
		$key_select = json_decode($info['business_id_type'], true);
	}
	//主键组
	$param = array(
			'extparam' => array('Tag'=>'ComposeKeyList'),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10008,'ParentId'=>10575,'ChildId'=>101,'Desc'=>'主键列表')
	);
	$result = request($param);
	$key_list = $result['List'];
	//主键组
	$param = array(
			'extparam' => array('Tag'=>'ComposeKeyList'),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10008,'ParentId'=>10575,'ChildId'=>101,'Desc'=>'主键列表')
	);
	$result = request($param);
	$key_list = $result['List'];
	$compose_list = $result['ComposeList'];
	$cb_subject = json_decode($info['cb_subject'],true);
	$temp = 'rule_define_info.html';
}elseif($module == 'add'){
	$info = $_POST['info'];
	$rules = array();
	foreach((array)$_POST['rule'] as $rule){
		$arr = json_decode(stripslashes($rule),true);
		if($arr['s_time']){
			$arr['start_time'] = str_replace(':','',$arr['s_time']);
			$arr['start_time'] = intval($arr['start_time']);
		}
		if($arr['e_time']){
			$arr['end_time'] = str_replace(':','',$arr['e_time']);
			$arr['end_time'] = intval($arr['end_time']);
		}
		//去除重复四级科目的记录
		foreach($rules as $k=>$v){
			if($v['bigcaseId']==$arr['bigcaseId'] && $v['caseId']==$arr['caseId'] && $v['parentId']==$arr['parentId'] && $v['childId']==$arr['childId']){
				continue 2;
			}
		}
		$rules[] = $arr;
	}
	if($info['rule_type'] == 2){
		$scope = array();
		$total = 0;
		foreach((array)$_POST['rule_type']['role'] as $key => $val){
			if($val > 0){
				$param = array(
					'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理详情"),
					'extparam'=>array("Tag"=>"RoleInfo","Id"=>$val)
				);
				$result = request($param);
				$roleInfo = $result['RoleInfo'];
				$scope[] = array('role'=>$val,'scope'=>$roleInfo['scope'],'start'=>(int)$_POST['rule_type']['start'][$key],'end'=>(int)$_POST['rule_type']['end'][$key],'val'=>(int)$_POST['rule_type']['range_rule'][$key]);
				++$total;
			}
		}
		$extend = array('total'=>$total,'rankInfo'=>$scope);
		$info['extend'] = urlencode(json_encode($extend));
	}else{
		$info['extend'] = urldecode(json_encode(array()));
		unset($_POST['rule_type']);
	}
	$info['rule'] = urlencode(json_encode($rules));
    $info['tpl_id'] = intval($_POST['tpl_id']);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 146,
			"Desc"		 => "创建业务规则"
		),
		'extparam'=>array(
			"Tag" 		 => "RuleDefineAdd",
			"Info" 		 => $info
		)
	);
	$result = request($param);
	//$link_array = getLevellink(10002,10008,10069,103);
	if($result['Flag'] == 100){
		showMsg($result['FlagString'],'?module=list&search[tpl_id]='.$_POST['tpl_id']);
	}else{
		showMsg($result['FlagString'],'?module=info&tpl_id='.$_POST['tpl_id']);
	}
}elseif($module == 'update'){
	$id = intval($_POST['id']);
	$info = $_POST['info'];
	$rules = array();
	foreach((array)$_POST['rule'] as $rule){
		$arr = json_decode(stripslashes($rule),true);
		if($arr['s_time']){
			//$s_time = explode(':',$arr['s_time']);
			$arr['start_time'] = str_replace(':','',$arr['s_time']);
			$arr['start_time'] = intval($arr['start_time']);
		}
		if($arr['e_time']){
			$arr['end_time'] = str_replace(':','',$arr['e_time']);
			$arr['end_time'] = intval($arr['end_time']);
		}
		//去除重复四级科目的记录
		foreach($rules as $k=>$v){
			if($v['bigcaseId']==$arr['bigcaseId'] && $v['caseId']==$arr['caseId'] && $v['parentId']==$arr['parentId'] && $v['childId']==$arr['childId']){
				continue 2;
			}
		}
		$rules[] = $arr;
	}
	if($info['rule_type'] == 2){
		$scope = array();
		$total = 0;
		foreach((array)$_POST['rule_type']['role'] as $key => $val){
			if($val > 0){
				$param = array(
					'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理详情"),
					'extparam'=>array("Tag"=>"RoleInfo","Id"=>$val)
				);
				$result = request($param);
				$roleInfo = $result['RoleInfo'];
				//array_push($scope, $roleInfo['scope']);
				$scope[] = array('role'=>$val,'scope'=>$roleInfo['scope'],'start'=>(int)$_POST['rule_type']['start'][$key],'end'=>(int)$_POST['rule_type']['end'][$key],'val'=>(int)$_POST['rule_type']['range_rule'][$key]);
				++$total;
			}
		}
		$extend = array('total'=>$total,'rankInfo'=>$scope);
		$info['extend'] = urlencode(json_encode($extend));
	}else{
		$info['extend'] = urldecode(json_encode(array()));
		unset($_POST['rule_type']);
	}

	$info['rule'] = urlencode(json_encode($rules));
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 149,
			"Desc"		 => "修改业务规则"
		),
		'extparam'=>array(
			"Tag" 		 => "RuleDefineUpdate",
			"Info" 		 => $info,
			"Id"		 => $id
		)
	);
	$result = request($param);
	//$link_array = getLevellink(10002,10008,10069,104);
	if($result['Flag'] == 100){
		showMsg($result['FlagString'],'?module=list&search[tpl_id]='.$_POST['tpl_id']);
	}else{
		showMsg($result['FlagString'],'?module=info&id='.$id.'&tpl_id='.$_POST['tpl_id']);
	}
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('behavior/'.$temp,$tpl);