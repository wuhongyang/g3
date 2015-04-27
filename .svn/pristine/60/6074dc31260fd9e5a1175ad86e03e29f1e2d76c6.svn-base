<?php
include_once('../library/global.fun.php');

if($_GET['module']=='getfrozenlist'){
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10036,'ParentId'=>10138,'ChildId'=>101,'Desc'=>'查看游戏冻结'),
		'extparam'=>array('Tag'=>'InteractList'),
	);
	$flash_games = request($param);
	$lists = array();
	$page = '';
	if(!empty($_GET['cmd']) && !empty($_GET['start']) && !empty($_GET['end']) && $_GET['start'] < $_GET['end']){
		$start = strtotime($_GET['start']);
		$end  = strtotime($_GET['end']);
		$param = array(
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10036,'ParentId'=>10138,'ChildId'=>101,'Desc'=>'查看游戏冻结'),
			'extparam'=>array('Tag'=>'GetFrozenList','cmd'=>$_GET['cmd'],'start'=>$start,'end'=>$end),
		);
		$frozenlist = request($param);
		$lists = $frozenlist['Result'];
		$page = $frozenlist['Page'];
	}
	$link_array = getLevellink(10002,10036,10138,101);
	$template = 'flash_games/frozenlist.html';
}elseif($_GET['module']=='freefrozen'){
	if(empty($_POST['cmd']) || empty($_POST['start']) || empty($_POST['end'])){
		alertMsg('查询条件不正确');
	}
	$start = strtotime($_POST['start']);
	$end  = strtotime($_POST['end']);
	$param = array(
		'param'=>array('BigCaseId'=>10002,'CaseId'=>10036,'ParentId'=>10138,'ChildId'=>102,'Desc'=>'返还冻结余额'),
		'extparam'=>array('Tag'=>'FreeFrozen','cmd'=>$_POST['cmd'],'start'=>$start,'end'=>$end),
	);
	$freefrozen = request($param);
	if(!empty($freefrozen['Error'])){
		alertMsg(implode(',',$freefrozen['Error']).' 返还失败');
	}else{
		alertMsg('操作成功',$_SERVER['HTTP_REFERER']);
	}
}elseif($_GET['module']=='ipCount'){
	$start = isset($_GET['starttime'])? strtotime($_GET['starttime']) : 0;
	$end = isset($_GET['endtime'])? strtotime($_GET['endtime']) : 0;
	$page = (int)$_GET['page'];
	$param = array(
		'param' => array("BigCaseId"=>10002,"CaseId"=>10040,"ParentId"=>10236,"ChildId"=>101,"Desc"=>"游戏IP局数日统计"),
		'extparam' => array("Tag"=>"GetList","Data"=>$id,'Data'=>array('Start'=>$start,'End'=>$end,'Page'=>$page))
	);
	$result = request($param);
	if($result['Flag'] != 100) alertMsg($result['FlagString']);
	$lists = $result['Data'];
	$page = $result['Page'];
	$template = 'regions/interact_ipcount.html';
}else{
	$id = isset($_GET['id']) ? (int)$_GET['id'] : '';
	$status_arr = array("不使用","使用");
	if(!empty($id) && $id!=0){
		$link_array = getLevellink(10002,10002,10004,101);
		$url = $link_array[101]['url'].'?module=interact_update';
		$param = array(
			'param' => array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10004,"ChildId"=>101,"Desc"=>"分站游戏信息"),
			'extparam' => array("Tag"=>"InteractList","Id"=>$id,'Data'=>array('IsBack' => true))
		);
	}else{
		$link_array = getLevellink(10002,10002,10004,101);
		$url = $link_array[101]['url'].'?module=interact_add';
		$param = array(
			'param' => array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10004,"ChildId"=>101,"Desc"=>"分站游戏信息"),
			'extparam' => array("Tag"=>"InteractList",'Data'=>array('IsBack' => true))
		);
	}
	$result = request($param);
	$info = $result['list'];
	//地域信息
	$areaInfo = $result['region'];
	$areaInfo = json_encode($areaInfo);
	//得到图片分类
	$cat = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'CatList','mypost'=>array('state'=>1))));
	$cat = json_encode($cat['lists']);
	//得到图片
	$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1))));
	$pic = json_encode($pic['lists']);
	
	//复制添加
	if($id > 0 && isset($_GET['copyto'])){
		$id = '';
		$url = $link_array[101]['url'].'?module=interact_add';
		unset($info['city_id'],$info['area_id']);
	}
	
	$template = 'regions/interact_info.html';
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template($template,$tpl);
