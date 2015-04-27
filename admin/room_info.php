<?php
include_once('../library/global.fun.php');

$id = isset($_GET['id']) ? (int)$_GET['id'] : '';

if(!empty($id) && $id!=0){
	$link_array = getLevellink(10002,10003,10020,101);
	$param = array(
		'param'=>array("BigCaseId"=>10002,"CaseId"=> 10003,"ParentId"=> 10020,"ChildId"=> 101,"Desc"=>"房间详情"),
		'extparam'=>array("Tag"=>"RoomsList","Id"=>$_GET['id'],"GroupId"=>$_GET['group'])
	);
	$info = request($param);
	if(empty($info['li'])){
		ShowMsg('没有这个房间','rooms.php?module=roomList');
	}
	//$area_arr = $info['region'];
	//$areaInfo = json_encode($area_arr);
	//$roomCase = $info['roomCase'];
	//$roomCase = json_encode($roomCase);
	//$parentId = $info['parentId'];
	$info = $info['li'];
	
	if($info['group']>0){
		$param = array(
			'extparam'=>array('Tag'=>'listGroup','Data'=>array('Groupid'=>$info['group'])),
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10003,'ParentId'=>10002,'ChildId'=>101,'Desc'=>'群列表')
		);
		$case = request($param);
		$info['group_id']=$case['lists'][0]['id'];
		$room_ui = json_decode(domain::main()->GroupKeyVal($info['group'],'room_ui'));
		$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10069,"ParentId"=>10337,"ChildId"=>101,"Desc"=>"查看UI列表"),
			'extparam'=>array("Tag"=>"GetRoomsUi",'Data'=>array('id'=>$room_ui), 'Getpic'=>true)
		);
		$result = request($param);
		$uiList = (array)$result['Result'];
		$rooms_ui = array();
		foreach ($uiList as $val) {
			$rooms_ui[$val['id']] = $val;
		}
	}
	
	$operate = "room_update";
}/*else{
	//开房间时，得到地区和分类信息
	$param = array(
		'param'=>array(
			"BigCaseId"  => 10002,
			"CaseId"	 => 10002,
			"ParentId"   => 10020,
			"ChildId"	 => 101,
			"Desc"		 => ""
		),
		'extparam'=>array(
			"Tag" 	=> "GetAllAreaClassify"
		)
	);
	$result = request($param);
	$area_arr = $result['open_citys'];
	$roomCase = json_encode($result['classifys']);
	$areaInfo = json_encode($area_arr);
	$operate = "room_add";
	//规模数组
	$maxuser_arr = array(
		"50" => "50人弦音房间",
		"100" => "100人弦音房间",
		"300" => "300人弦音房间",
		"500" => "500人弦音房间"
	);
	$expire = array(
		"365" => "365天"
	);
    $param = array(
        'param'=>array("BigCaseId"=>10002,"CaseId"=>10002,"ParentId"=>10399,"ChildId"=>101,"Desc"=>"查看UI方案"),
        'extparam'=>array("Tag"=>"GetUiPackage",'Getpic'=>true)
    );
    $result = request($param);
    $rooms_ui = $result['Result'];
}*/
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('rooms/room_info.html',$tpl);
