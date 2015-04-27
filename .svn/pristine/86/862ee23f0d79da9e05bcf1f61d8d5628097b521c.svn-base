<?php
include_once('../library/global.fun.php');

$module = trim($_GET['module']);
if($module=='interact_list' || empty($module)){
	$link_array = getLevellink(10002,10002,10004,101);
	//取得表格数据
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10002,
			"ParentId"   => 10004,
			"ChildId"	 => 101,
			"Desc"		 => "分站游戏列表"
		),
		'extparam'=>array(
			"Tag" 	=> "InteractList",
			"Data"	=> array(
				'IsBack' => true,
				"Keyword"=> urlencode($_GET['wd'])
			)
		)
	);
	$arr = request($info);
	$area_arr = $arr['region'];
	$areaInfo = json_encode($area_arr);
	$perpage = 15;
	$pages = new extpage(array (
		'total' => $arr['total'],
		'perpage' => $perpage
	));
	
	$arr = (array)$arr['list'];
	for($i=$pages->offset; $i<$pages->offset + $perpage; $i++){
		if($i >= count($arr))  break;
		$interact_arr[] = $arr[$i];
	}
	$page = $pages->show();
	
	if(!empty($interact_arr)){
		//得到科目名称
		foreach((array)$interact_arr as $k=>$v){
			$res = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetBigCase','BigCaseId'=>$v['big_case_id'])));
			$interact_arr[$k]['bigcase_name'] = $res['Result']['bigcase_name'];
			$res = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetCase','CaseId'=>$v['case_id'])));
			$interact_arr[$k]['case_name'] = $res['Result']['case_name'];
			$res = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetParent','ParentId'=>$v['parent_id'])));
			$interact_arr[$k]['parent_name'] = $res['Result']['parent_name'];
		}
	}
	$template = 'interact_list.html';
	
}elseif($module == 'interact_add'){//添加
	$regionId = ($_POST['area'] == -1) ? $_POST['city'] : $_POST['area'];
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10002,
			"ParentId"   => 10004,
			"ChildId"	 => 102,
			"Desc"		 => "添加分站礼物"
		),
		'extparam'=>array(
			"Tag" 			=> "InteractAdd",
			"Cmd"			=> $_POST['cmd'],
			"CmdPath" 		=> $_POST['cmd_path'],
			"BigCaseId"     => $_POST['bigcase_id'],
			"CaseId"	    => $_POST['case_id'],
			"ParentId"      => $_POST['parent_id'],
			"ProvinceId"	=> $_POST['province'],
			"CityId"		=> $_POST['city'],
			"AreaId"		=> $_POST['area'],
			"RegionId"	    => $regionId,
			"InteractName"  => $_POST['interact_name'],
			"InteractStatus"=> $_POST['interact_status'],
			"RoomSpan"	    => $_POST['room_span'],
			"InteractCat"	=> $_POST['interact_cat'],
			"InteractPic"	=> $_POST['interact_pic'],
			"StatusCat"		=> $_POST['status_cat'],
			"StatusPic"		=> $_POST['status_pic']
		)
	);
	$result = request($info);
	if($result['Flag'] == '100'){
		ShowMsg($result['FlagString'],'?module=interact_list');
	}

}elseif($module == 'interact_update'){//更新
	$regionId = ($_POST['area'] == -1) ? $_POST['city'] : $_POST['area'];
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10002,
			"ParentId"   => 10004,
			"ChildId"	 => 103,
			"Desc"		 => "修改分站礼物"
		),
		'extparam'=>array(
			"Tag" 			=> "InteractUpdate",
			"Id"			=> $_POST['id'],
			"Cmd"			=> $_POST['cmd'],
			"CmdPath" 		=> $_POST['cmd_path'],
			"BigCaseId"     => $_POST['bigcase_id'],
			"CaseId"	    => $_POST['case_id'],
			"ParentId"      => $_POST['parent_id'],
			"ProvinceId"	=> $_POST['province'],
			"CityId"		=> $_POST['city'],
			"AreaId"		=> $_POST['area'],
			"RegionId"	    => $regionId,
			"InteractName"  => $_POST['interact_name'],
			"InteractStatus"=> $_POST['interact_status'],
			"Robot"         => $_POST['robot'],
			"RoomSpan"	    => $_POST['room_span'],
			"InteractCat"	=> $_POST['interact_cat'],
			"InteractPic"	=> $_POST['interact_pic'],
			"StatusCat"		=> $_POST['status_cat'],
			"StatusPic"		=> $_POST['status_pic'],
			"Category"		=> $_POST['category'],
			"Category_id"   => $_POST['category_id']
		)
	);
	$result = request($info);
	if($result['Flag'] == 100){
		ShowMsg($result['FlagString'],'?module=interact_list');
	}
}elseif($module == 'interact_del'){
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10002,
			"ParentId"   => 10004,
			"ChildId"	 => 104,
			"Desc"		 => "删除分站礼物"
		),
		'extparam'=>array(
			"Tag" 			=> "InteractDel",
			"Id"			=> $_GET['id']
		)
	);
	$result = request($info);
	if($result['Flag'] == '100'){
		ShowMsg($result['FlagString'],'?module=interact_list');
	}
}elseif($module == "interact_order"){
	$info = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10002,
			"ParentId"   => 10004,
			"ChildId"	 => 105,
			"Desc"		 => "分站礼物排序"
		),
		'extparam'=>array(
			"Tag" 	=> "InteractOrder",
			"Id"	=> intval($_GET['id']),
			"Type"  => trim($_GET['type'])
		)
	);
	$result = request($info);
	if($result['Flag'] == '100'){
		ShowMsg($result['FlagString'],'?module=interact_list');
	}
}elseif($module == 'ajax'){
	$bigcase_id = intval($_GET['bigcase_id']);
	$case_id = intval($_GET['case_id']);
	$parent_id = intval($_GET['parent_id']);
	exit(json_encode(httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetFlashCMD','BigCaseId'=>$bigcase_id,'CaseId'=>$case_id,'ParentId'=>$parent_id)))));
}elseif($module == 'interact_config'){//游戏配置列表
	$link_array = getLevellink(10002,10002,10004,101);
	$cmd = $_GET['cmd'];
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10004,"ChildId"=>106,"Desc"=>"游戏配置列表"),
		'extparam'=>array("Tag"=> "InteractConfigList","Cmd"=>$cmd)
	);
	$result = request($param);
	if($result['Flag'] != 100)
		exit($result['FlagString']);
	$list = (array)$result['Result'];
	$value = $list['value'];
	$descr  = $list['descr'];
	$template = 'interact_config.html';
}elseif($module == 'modifyConfig'){//游戏配置保存
	foreach($_POST['key'] as $key=>$value){
		if(!empty($_POST['key'][$key]) && is_string($_POST['key'][$key])) $data[$_POST['key'][$key]] = $_POST['value'][$key];
		$descr[$_POST['key'][$key]] = $_POST['descr'][$key];
	}
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10004,"ChildId"=>106,"Desc"=>"保存游戏配置"),
		'extparam'=>array("Tag"=> "InteractConfigSave","Key"=>$_POST['cmd'],"Value"=>$data,"Descr"=>$descr)
	);
	$result = request($param);
	if($result['Flag'] == 100)
		ShowMsg($result['FlagString'],'?module=interact_config&cmd='.$_POST['cmd']);
	else
		ShowMsg($result['FlagString'],-1);
}elseif($module == 'interactConfig'){
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10004,"ChildId"=>112,"Desc"=>"游戏配置列表"),
		'extparam'=>array("Tag"=> "interactConfig","Name"=>$_POST['name'],"Id"=>$_POST['id'])
	);
	$result = request($param);
	$area_arr = $result['region'];
	$areaInfo = json_encode($area_arr);
	$perpage = 15;
	$pages = new extpage(array (
		'total' => $result['total'],
		'perpage' => $perpage
	));
	
	$list = (array)$result['Result'];
	for($i=$pages->offset; $i<$pages->offset + $perpage; $i++){
		if($i >= count($list))  break;
	}
	$page = $pages->show();
	$game_array = array(1=>'扑克游戏',2=>'骨牌游戏',3=>'棋类游戏',4=>'休闲游戏');
	$category_array = array(
		1=>array(1=>'升级类',2=>'双扣类',3>'长牌类',4=>'博弈类',5=>'益智类',6=>'其它'),
		2=>array(1=>'麻将类',2=>'博弈类',3>'其它'),
		3=>array(1=>'象棋类',2=>'围棋类',3>'军棋类',4=>'其它'),
		4=>array(1=>'休闲类',2=>'运动类',3>'益智类',4=>'对战类',5=>'其它')
	);
	$link_array = getLevellink(10002,10002,10004,112);
	$template = 'interactconfig.html';

}elseif($module=='room_auth_config'){
	//tbl_interact_rooms
	$link_array = getLevellink(10002,10002,10004,112);
	if(!empty($_POST['rooms'])){
		$rooms = (array)explode("\r\n",trim($_POST['rooms']));
		$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10004,"ChildId"=>108,"Desc"=>"添加活动房间"),
			'extparam'=>array("Tag"=> "SaveActivityRooms",'Gameid'=>(int)$_GET['gameid'],"Rooms"=>$rooms)
		);
		$result = request($param);
		alertMsg($result['FlagString'],$_SERVER['REQUEST_URI']);
	}
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10004,"ChildId"=>108,"Desc"=>"活动房间"),
		'extparam'=>array("Tag"=> "GetActivityRooms",'Gameid'=>(int)$_GET['gameid'])
	);
	$result = request($param);
	$rooms = implode("\r\n",(array)$result['Result']);
	$template = 'room_auth_config.html';
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template("regions/{$template}",$tpl);