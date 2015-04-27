<?php
include_once('../library/global.fun.php');

$module = trim($_GET['module']);

if($module=='list'){
	$link_array = getLevellink(10002,10069,10648,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 139,
			"Desc"		 => "业务参数配置列表查看"
		),
		'extparam'=>array(
			"Tag" 		 => "ParamConfigList",
			"SearchData" => $_GET['search']
		)
	);
	$result = request($param);
	$paramConfigList = (array)$result['Result'];
	$page = $result['Page'];
	$temp = 'param_config_list.html';
	
}elseif($module == 'info'){
	$id = intval($_GET['id']);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 139,
			"Desc"		 => "创建/查看/修改业务参数配置"
		),
		'extparam'=>array(
			"Tag" 		 => "ParamConfigInfo",
			"Id" 		 => $id,
            "TplId"      => $_GET['tpl_id']
		)
	);
	$result = request($param);
	$info = (array)$result['Info'];
// 	$info['integration'] = json_decode(urldecode($info['integration']), true);
// 	print_r($info['integration']);exit;
	$rules = (array)$result['Rule']['Result'];
	$readInterfaces = (array)$result['ReadInterface']['Result'];
	$writeInterfaces = (array)$result['WriteInterface']['Result'];
	//读取角色分组
	$param = array(
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>150,"Desc"=>"角色组列表"),
			'extparam'=>array("Tag"=>"RoleCate","IsNotPage" =>true,'SearchData'=>array('status'=>1,'tpl_id'=>$_GET['tpl_id']))
	);
	$result = request($param);
	$cateList = $result['CateList'];
	//读取角色
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理列表"),
		'extparam'=>array("Tag"=>"RoleList","SearchData" => array('rule'=>2,'status'=>1,'tpl_id'=>$_GET['tpl_id']),'IsNotPage'=>true)
	);
	$roleList = request($param);
	$roleList = (array)$roleList['RoleList'];
	$roleListArr = array();
	foreach($roleList as $one){
		$roleListArr[$one['cate_id']][] = array($one['id'], $one['name']);
	}
	$link_array = getLevellink(10002,10069,10648,101);
	$temp = 'param_config_info.html';
}elseif($module == 'add'){
	$info = $_POST['info'];
	if($info['style'] == 1){
		$info['integration'] = (array)$_POST['fixed'];
	}elseif($info['style'] == 2){
		foreach ($_POST['interval'] as $key => $val) {
			if($val['role'] > 0){
				$param = array(
					'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理详情"),
					'extparam'=>array("Tag"=>"RoleInfo","Id"=>$val['role'])
				);
				$result = request($param);
				$roleInfo = $result['RoleInfo'];
				$_POST['interval'][$key]['scope'] = (int)$roleInfo['scope'];
			}
			if($val['min_role'] > 0){
				$param = array(
						'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理详情"),
						'extparam'=>array("Tag"=>"RoleInfo","Id"=>$val['role'])
				);
				$result = request($param);
				$roleInfo = $result['RoleInfo'];
				$_POST['interval'][$key]['min_scope'] = (int)$roleInfo['scope'];
			}
		}
		$info['integration'] = (array)$_POST['interval'];
	}else{
		$info['integration'] = array();
	}
	$info['integration'] = urlencode(json_encode($info['integration']));
    $info['tpl_id'] = intval($_POST['tpl_id']);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 144,
			"Desc"		 => "创建业务参数配置"
		),
		'extparam'=>array(
			"Tag" 		 => "ParamConfigAdd",
			"Info" 		 => $info
		)
	);
	$result = request($param);
	$link_array = getLevellink(10002,10069,10648,101);
	if($result['Flag'] == 100){
		showMsg($result['FlagString'],'?module=list&search[tpl_id]='.$_POST['tpl_id']);
	}else{
		showMsg($result['FlagString'],'?module=info&tpl_id='.$_POST['tpl_id']);
	}
}elseif($module == 'update'){
	$id = intval($_POST['id']);
	$info = $_POST['info'];
	if($info['style'] == 1){
		$info['integration'] = (array)$_POST['fixed'];
	}elseif($info['style'] == 2){
		foreach ($_POST['interval'] as $key => $val) {
			if($val['role'] > 0){
				$param = array(
					'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理详情"),
					'extparam'=>array("Tag"=>"RoleInfo","Id"=>$val['role'])
				);
				$result = request($param);
				$roleInfo = $result['RoleInfo'];
				$_POST['interval'][$key]['scope'] = (int)$roleInfo['scope'];
			}
			if($val['min_role'] > 0){
				$param = array(
						'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理详情"),
						'extparam'=>array("Tag"=>"RoleInfo","Id"=>$val['min_role'])
				);
				$result = request($param);
				$roleInfo = $result['RoleInfo'];
				$_POST['interval'][$key]['min_scope'] = (int)$roleInfo['scope'];
			}
		}
		$info['integration'] = (array)$_POST['interval'];
	}else{
		$info['integration'] = array();
	}
	$info['integration'] = urlencode(json_encode($info['integration']));
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10069,
			"ParentId"   => 10648,
			"ChildId"	 => 148,
			"Desc"		 => "修改业务参数配置"
		),
		'extparam'=>array(
			"Tag" 		 => "ParamConfigUpdate",
			"Info" 		 => $info,
			"Id"		 => $id
		)
	);
	$result = request($param);
	//$link_array = getLevellink(10002,10008,10070,104);
	if($result['Flag'] == 100){
		showMsg($result['FlagString'],'?module=list&search[tpl_id]='.$_POST['tpl_id']);
	}else{
		showMsg($result['FlagString'],'?module=info&id='.$id."&tpl_id=".$_POST['tpl_id']);
	}
}
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('behavior/'.$temp,$tpl);