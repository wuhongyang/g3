<?php
require_once '../library/global.fun.php';
$request = json_decode($_GET['param'],true);

if($request['Gameid'] > 0){
	//房间列表
	$param = array(
		'extparam' => array('Tag'=>'GetActivityRooms','Gameid'=>(int)$request['Gameid']),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>102)
	);
	$rooms_list = request($param);
	$roomsListbyRoom = $rooms_list['Result'];
	
	/*游戏活动分类列表*/
	$interact_arr = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'InteractList','Data'=>array('region_id'=>$site['region_id'],'interact_status'=>1))));
	$games = array();
	foreach($interact_arr['list'] as $row){
		$games[$row['parent_id']] = $row;
	}
	
	$temp = 'activity_game_rooms_ajax.html';
}else{

	$page = $request['page'] > 0 ? $request['page'] : 1;

	$type = isset($request['type']) ? $request['type'] : 1;
	//$request['sortID'] = intval($request['sortID']);

	//房间列表
	$param = array(
		'extparam' => array('Tag'=>'GetRoomsList','region_id'=>$request['region_id'],/*'sortID'=>$request['sortID'],*/'keyword'=>$request['keyword'],'page'=>$page),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>102)
	);
	$rooms_list = request($param);
	foreach((array)$rooms_list['RoomsList'] as $key => $val){
		$r = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetNameByRegion','RegionId'=>$val['region_id'])));
		$rooms_list['RoomsList'][$key]['region_name'] = rtrim($r['SiteName'],'市');
	}
	$roomsListbyRoom = (array)$rooms_list['RoomsList'];

	$roomsListbyArt = array();
	//if($request['sortID'] < 1){
	//房间列表
	$param = array(
		'extparam' => array('Tag'=>'GetRoomsListByArtist','region_id'=>$request['region_id'],'keyword'=>$request['keyword'],'page'=>$page),
		'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>102)
	);
	$rooms_list_by_art = request($param);
	foreach((array)$rooms_list_by_art['RoomsList'] as $key => $val){
		$r = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetNameByRegion','RegionId'=>$val['region_id'])));
		$rooms_list_by_art['RoomsList'][$key]['region_name'] = rtrim($r['SiteName'],'市');
	}
	$roomsListbyArt = (array)$rooms_list_by_art['RoomsList'];
	//}
	if($request['region_id']>0){
		$temp = 'rooms_list_ajax.html';
	}else{
		$temp = 'navsite_rooms_list_ajax.html';
	}

}


$tpl = template::getInstance();
$tpl->setOptions(get_config('template','rooms'));
include template($temp,$tpl);