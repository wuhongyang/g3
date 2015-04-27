<?php
include_once '../library/global.fun.php';

$module = $_GET['module'];
if($module == 'bigCaseList'){//一级科目列表
	$link_array = getLevelLink(10002,10009,10013,101);
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 101,
			"Desc"		 => "一级科目列表"
		),
		'extparam'=>array(
			"Tag" 		 => "GetBigCaseList",
			"Param" 	 => $_GET
		)
	);
	$result = request($param);
	$page = $result['Page'];
	$lists = $result['Result'];
	$temp = 'bigCaseList.html';
}elseif($module == 'bigCaseInfo'){//一级科目添加/修改页面
	$bigCaseId = intval($_GET['bigcase_id']);
	$link_array = getLevelLink(10002,10009,10013,101);
	if($bigCaseId > 0){
		$url = '?module=bigCaseUpdate';
		$param = array(
			'param'=>array(
				"BigCaseId"  => 10002,
				"CaseId"	 => 10009,
				"ParentId"   => 10013,
				"ChildId"	 => 101,
				"Desc"		 => "修改一级科目页面"
			),
			'extparam'=>array(
				"Tag" 		 => "GetBigCase",
				"BigCaseId"  => $bigCaseId
			)
		);
		$result = request($param);
		$edit = $result['Result'];
	}else{
		$url = '?module=bigCaseAdd';
	}
	$temp = 'addBigCase.html';
}elseif($module == 'bigCaseAdd'){//一级科目添加
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 102,
			"Desc"		 => "添加一级科目"
		),
		'extparam'=>array(
			"Tag" 		 => "SetBigCase",
			"Post" 	 	 => $_POST
		)
	);
	$result = request($param);
	$link_array = getLevelLink(10002,10009,10013,101);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[101]['url']);
	}else{
		ShowMsg($result['FlagString'],$link_array[105]['url']);
	}
}elseif($module == 'bigCaseUpdate'){//一级科目修改
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 103,
			"Desc"		 => "修改一级科目"
		),
		'extparam'=>array(
			"Tag" 		 => "SetBigCase",
			"Post" 	 	 => $_POST
		)
	);
	$result = request($param);
	$link_array = getLevelLink(10002,10009,10013,101);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[101]['url']);
	}else{
		ShowMsg($result['FlagString'],$link_array[105]['url'].'&bigcase_id='.$_POST['bigcase_id']);
	}
}elseif($module == 'caseList'){//二级科目列表
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 107,
			"Desc"		 => "二级科目列表"
		),
		'extparam'=>array(
			"Tag" 		 => "GetCaseList",
			"Param" 	 => $_GET
		)
	);
	$result = request($param);
	$page = $result['Page'];
	$lists = $result['Result'];
	$temp = 'caseList.html';
	$link_array = getLevelLink(10002,10009,10013,107);
}elseif($module == 'caseInfo'){//二级科目添加/修改页面
	$caseId = intval($_GET['edit']);
	$link_array = getLevelLink(10002,10009,10013,107);
	if($caseId){
		$url = '?module=caseUpdate';
		$param = array(
			'param'=>array(
				"BigCaseId"  => 10002,
				"CaseId"	 => 10009,
				"ParentId"   => 10013,
				"ChildId"	 => 109,
				"Desc"		 => "添加/修改二级科目显示"
			),
			'extparam'=>array(
				"Tag" 		 => "GetCase",
				"CaseId" 	 => $caseId
			)
		);
		$result = request($param);
		$edit = $result['Result'];
		//用于AJAX取得一级科目
		$_GET['bigcase_id'] = $edit['bigcase_id'];
	}else{
		$url = '?module=caseAdd';
	}
	
	$temp = 'addCase.html';
}elseif($module == 'caseAdd'){//二级科目添加
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 108,
			"Desc"		 => "添加二级科目"
		),
		'extparam'=>array(
			"Tag" 		 => "SetCase",
			"Post" 	 	 => $_POST
		)
	);
	$result = request($param);
	$link_array = getLevelLink(10002,10009,10013,107);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[107]['url'].'&bigcase_id='.$_POST['bigcase_id']);
	}else{
		ShowMsg($result['FlagString'],$link_array[110]['url'].'&bigcase_id='.$_POST['bigcase_id']);
	}
}elseif($module == 'caseUpdate'){//二级科目修改
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 109,
			"Desc"		 => "修改二级科目"
		),
		'extparam'=>array(
			"Tag" 		 => "SetCase",
			"Post" 	 	 => $_POST
		)
	);
	$result = request($param);
	$link_array = getLevelLink(10002,10009,10013,107);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[107]['url'].'&bigcase_id='.$_POST['bigcase_id']);
	}else{
		ShowMsg($result['FlagString']);
	}
}elseif($module == 'parentList'){//三级科目列表
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 112,
			"Desc"		 => "三级科目列表"
		),
		'extparam'=>array(
			"Tag" 		 => "GetParentList",
			"Param" 	 => $_GET
		)
	);
	$result = request($param);
	$page = $result['Page'];
	$lists = $result['Result'];
	$temp = 'parentList.html';
	$link_array = getLevelLink(10002,10009,10013,112);
}elseif($module == 'parentInfo'){//三级科目添加/修改页面
	$parentId = intval($_GET['edit']);
	$link_array = getLevelLink(10002,10009,10013,112);
	if($parentId){
		$url = '?module=parentUpdate';
		$param = array(
			'param'=>array(
				"BigCaseId"  => 10002,
				"CaseId"	 => 10009,
				"ParentId"   => 10013,
				"ChildId"	 => 114,
				"Desc"		 => "修改三级科目页面"
			),
			'extparam'=>array(
				"Tag" 		 => "GetParent",
				"ParentId" 	 => $parentId
			)
		);
		$result = request($param);
		$edit = $result['Result'];
		//用于AJAX取得一级科目
		$_GET['bigcase_id'] = $edit['bigcase_id'];
		$_GET['case_id'] = $edit['case_id'];
	}else{
		$url = '?module=parentAdd';
	}
	
	$temp = 'addParent.html';
}elseif($module == 'parentAdd'){//三级科目添加
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 113,
			"Desc"		 => "添加三级科目"
		),
		'extparam'=>array(
			"Tag" 		 => "SetParent",
			"Post" 	 	 => $_POST
		)
	);
	$result = request($param);
	$link_array = getLevelLink(10002,10009,10013,112);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[112]['url'].'&bigcase_id='.$_POST['bigcase_id'].'&case_id='.$_POST['case_id']);
	}else{
		ShowMsg($result['FlagString'],-2);
	}
}elseif($module == 'parentUpdate'){//三级科目修改
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 114,
			"Desc"		 => "修改三级科目"
		),
		'extparam'=>array(
			"Tag" 		 => "SetParent",
			"Post" 	 	 => $_POST
		)
	);
	$result = request($param);
	$link_array = getLevelLink(10002,10009,10013,112);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[112]['url'].'&bigcase_id='.$_POST['bigcase_id'].'&case_id='.$_POST['case_id']);
	}else{
		ShowMsg($result['FlagString'],-2);
	}
}elseif($module == 'childList'){//四级科目列表
	$trade_properties = array('3'=>'科目交易','4'=>'税金交易','5'=>'用户交易');
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 117,
			"Desc"		 => "四级科目列表"
		),
		'extparam'=>array(
			"Tag" 		 => "GetChildList",
			"Param" 	 => $_GET
		)
	);
	$result = request($param);
	$page = $result['Page'];
	$lists = $result['Result'];
	$temp = 'childList.html';
	$link_array = getLevelLink(10002,10009,10013,117);
}elseif($module == 'childInfo'){//四级科目添加/修改页面
	$childId = intval($_GET['edit']);
	$link_array = getLevelLink(10002,10009,10013,117);
	if($childId){
		$url = '?module=childUpdate';
		$param = array(
			'param'=>array(
				"BigCaseId"  => 10002,
				"CaseId"	 => 10009,
				"ParentId"   => 10013,
				"ChildId"	 => 117,
				"Desc"		 => "修改四级科目页面"
			),
			'extparam'=>array(
				"Tag" 		 => "GetChild",
				"ChildId" 	 => $childId
			)
		);
		$result = request($param);
		$edit = $result['Result'];
		//用于AJAX取得一级科目
		$_GET['bigcase_id'] = $edit['bigcase_id'];
		$_GET['case_id'] = $edit['case_id'];
		$_GET['parent_id'] = $edit['parent_id'];
	}else{
		$url = '?module=childAdd';
	}
	
	$temp = 'addChild.html';
}elseif($module == 'childAdd'){//四级科目添加
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 118,
			"Desc"		 => "添加四级科目"
		),
		'extparam'=>array(
			"Tag" 		 => "SetChild",
			"Post" 	 	 => $_POST
		)
	);
	$result = request($param);
	$link_array = getLevelLink(10002,10009,10013,117);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[117]['url'].'&bigcase_id='.$_POST['bigcase_id'].'&case_id='.$_POST['case_id'].'&parent_id='.$_POST['parent_id']);
	}else{
		ShowMsg($result['FlagString']);
	}
}elseif($module == 'childUpdate'){//四级科目修改
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 119,
			"Desc"		 => "修改四级科目"
		),
		'extparam'=>array(
			"Tag" 		 => "SetChild",
			"Post" 	 	 => $_POST
		)
	);
	$result = request($param);
	$link_array = getLevelLink(10002,10009,10013,117);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],$link_array[117]['url'].'&bigcase_id='.$_POST['bigcase_id'].'&case_id='.$_POST['case_id'].'&parent_id='.$_POST['parent_id']);
	}else{
		ShowMsg($result['FlagString']);
	}
}elseif($module == 'childSync'){//四级科目同步
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10009,
			"ParentId"   => 10013,
			"ChildId"	 => 122,
			"Desc"		 => "四级科目同步"
		),
		'extparam'=>array(
			"Tag" 		 => "ChildSync",
			"Ids" 	 	 => $_POST['id']
		)
	);
	$result = request($param);
	exit(json_encode($result));
}elseif($module == 'caseOrder'){
	$action = $_GET['action'];
	if($action == 'caseUp'){
		$param = array(
			'param'=>array(
				"BigCaseId"  => 10002,
				"CaseId"	 => 10009,
				"ParentId"   => 10013,
				"ChildId"	 => 123,
				"Desc"		 => "二级科目上移"
			),
			'extparam'=>array(
				"Tag" 		 => "CaseOrder",
				"Post" 	 	 => array(
					'up' => intval($_GET['up']),
					'bigcase_id' => intval($_GET['bigcase_id'])
				)
			)
		);
	}elseif($action == 'caseDown'){
		$param = array(
			'param'=>array(
				"BigCaseId"  => 10002,
				"CaseId"	 => 10009,
				"ParentId"   => 10013,
				"ChildId"	 => 123,
				"Desc"		 => "二级科目下移"
			),
			'extparam'=>array(
				"Tag" 		 => "CaseOrder",
				"Post" 	 	 => array(
					'down' => intval($_GET['down']),
					'bigcase_id' => intval($_GET['bigcase_id'])
				)
			)
		);
	}
	$result = request($param);
	if($result['Flag']){
		ShowMsg($result['FlagString'],-1);
	}else{
		ShowMsg($result['FlagString'],-1);
	}
}elseif($module == 'ajax'){
	if(isset($_GET['bigcase'])){
			$param = array(
				'param'=>array(
					"BigCaseId"  => 10002,
					"CaseId"	 => 10009,
					"ParentId"   => 10013,
					"ChildId"	 => 106,
					"Desc"		 => "使用的一级科目"
				),
				'extparam'=>array(
					"Tag" 		 => "GetUseBigCase"
				)
			);
			$result = request($param);
			exit(json_encode((array)$result));
		}elseif(isset($_GET['case'])){
			$param = array(
				'param'=>array(
					"BigCaseId"  => 10002,
					"CaseId"	 => 10009,
					"ParentId"   => 10013,
					"ChildId"	 => 111,
					"Desc"		 => "使用的二级科目"
				),
				'extparam'=>array(
					"Tag" 		 => "GetUseCase",
					"BigCaseId"  => $_GET['case']
				)
			);
			$result = request($param);
			exit(json_encode((array)$result));
		}elseif(isset($_GET['parent'])){
			$param = array(
				'param'=>array(
					"BigCaseId"  => 10002,
					"CaseId"	 => 10009,
					"ParentId"   => 10013,
					"ChildId"	 => 116,
					"Uin"		 => "",
					"Desc"		 => "使用的三级科目"
				),
				'extparam'=>array(
					"Tag" 		 => "GetUseParent",
					"CaseId"  	 => $_GET['parent']
				)
			);
			$result = request($param);
			exit(json_encode((array)$result));
		}elseif(isset($_GET['child'])){
			$param = array(
				'param'=>array(
					"BigCaseId"  => 10002,
					"CaseId"	 => 10009,
					"ParentId"   => 10013,
					"ChildId"	 => 121,
					"Uin"		 => "",
					"Desc"		 => "使用的四级科目"
				),
				'extparam'=>array(
					"Tag" 		 => "GetUseChild",
					"ParentId"   => $_GET['child']
				)
			);
			$result = request($param);
			exit(json_encode((array)$result));
		}elseif(isset($_GET['uin'])){
			$parame = array('parameter'=>'{"Tag":"GetUserInfo","Uin":"'.intval($_GET['uin']).'"}');
			$userinfo = json_decode(httpPOST(KBASIC_API_PATH,$parame),true);
			exit($userinfo['Nick']);
		}
}elseif($module == 'Childetail'){
	$param = array(
		'param'=>array(
			"BigCaseId"   => $_GET['BigCaseId'],
			"CaseId"   => $_GET['CaseId'],
			"ParentId"   => $_GET['ParentId'],
			"ChildId"   => $_GET['ChildId'],
		),
		'extparam'=>array(
			"Tag" 		 => "GetBusinessConfig",
		)
	);
	$result = httpPOST(CCS_API_PATH,$param);
	exit(json_encode((array)$result));
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('business/'.$temp,$tpl);
