<?php
include_once('../library/global.fun.php');
$module = isset($_GET['module']) ? trim($_GET['module']) : 'list';

if($module == 'list'){
	$link_array = getLevellink(10002,10003,10005,102);
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10005,"ChildId"=>101),
		'extparam'=>array("Tag"=>"StyleList")
	);
	$result = request($param);
	$lists = (array)$result['Result'];
	$page = $result['Page'];
	$temp = 'style_list.html';
}elseif($module == 'info'){
	$link_array = getLevellink(10002,10003,10005,101);
	if($_GET['group_id']>0){
		$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10005,"ChildId"=>101),
			'extparam'=>array("Tag"=>"StyleList","GroupId"=>(int)$_GET['group_id'])
		);
		$rst = request($param);
		$info = $rst['Result'][0];
	}
	
	//分类
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10005,"ChildId"=>103),
		'extparam'=>array("Tag"=>"StyleCategoryList")
	);
	$category = request($param);
	$category = $category['Result'];
	
	//风格列表
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10005,"ChildId"=>103),
		'extparam'=>array("Tag"=>"StyleSettingList")
	);
	$result = request($param);
	$style_lists = (array)$result['Result'];
	foreach($style_lists as $key=>$val){
		$param = array(
			'param'=>array('BigCaseId'=>'10002','CaseId'=>'10011','ParentId'=>'10019','ChildId'=>'104'),
			'extparam'=>array('Tag'=>'ShowOriPic','mypost'=>array('id'=>$val['thumb']))
		);
		$result = request($param);
		$style_lists[$key]['thumb']=$result['img_path'];
		if($info['style_id']==$val['id']){
			$info['style_cat_id']=$val['cat_id'];
		}
	}
	$style_lists = json_encode($style_lists);
	$temp = 'style_info.html';
}elseif($module == 'save'){
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10005,"ChildId"=>102),
		'extparam'=>array("Tag"=>"StyleSave","Data"=>$_POST)
	);
	$rst = request($param);
	if($rst['Flag'] != 100){
		alertMsg($rst['FlagString']);
	}
	alertMsg($rst['FlagString'],'?module=list');
}
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('group/'.$temp,$tpl);