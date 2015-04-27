<?php
/**
* 移动端使用
*/
require_once 'library/rooms.class.php';
$rooms = new rooms();

$param = $_POST['param']; 
$json = $_POST['extparam'];

switch($json['Tag']){
	/*
	case 'GetGroupFoot'://查询站点首页底部信息
		echo json_encode($rooms->getGroupFoot($json['GroupId'],$json['Domain']));
	break;
	*/
	case 'GetActivityRanks':
		echo json_encode($rooms->GetActivityRanks($json['Type'],$json['Ruleid'],$json['Uptime'],$json['Rows'],$json['Roomid']));
		break;
	case 'GetActivityRooms':
		echo json_encode($rooms->getActivityRooms($json));
		break;
	case 'GetUserInfo': //获取用户信息
		echo httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$_POST['param']['Uin'])),false);
		break;
	case 'UserLogin' : //用户登陆
		$array = array(
			'param' => array('Uin'=>$param['Uin'],'SessionKey'=>$param['SessionKey'],'GroupId'=>$param['GroupId']),
			'extparam' => array('Tag'=>'UserLogin','Remember'=>$json['Remember'])
		);
		echo httpPOST(SSO_API_PATH, $array, false);
		break;
	case 'GetHotGroups':
		echo json_encode($rooms->getHotGroups());
		break;
	case 'GetGroupInfo':
		echo json_encode($rooms->getGroupInfo($json['Id'],$param['Uin']));
		break;
	// case 'GetSiteData':
		// $param = array('extparam'=>array('Tag'=>'GetSite','Domain'=>$json['City']));
		// $site = httpPOST(REGION_API_PATH,$param,false);
		// echo $site;
		// break;
	// case 'PositionSite':
		// $citys = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'PositionSite','Ip'=>$json['Ip'])),false);
		// echo $citys;
		// break;
	case 'GetLoginUser':
		$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')),false);
		echo $user;
		break;
	// case 'GetAdImg':
		// $adimg = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'AdImg','RegionId'=>$json['RegionId'])),false);
		// echo $adimg;
		// break;
	// case 'GetRoomCase':
		// $rooms_case = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetRoomsCase','RegionId'=>$json['RegionId'])),false);
		// echo $rooms_case;
		// break;
	// case 'GetOpenSites':
		// $citys = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetOpenCity')),false);
		// echo $citys;
		// break;
	// case 'GetSiteList':
		// $sites = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetSiteList')),false);
		// echo $sites;
		// break;
	case 'GetHotRooms':
		echo json_encode($rooms->getHotRooms($json['RegionId'],$json['Groupid']));
		break;
	// case 'GetProgramRoom':
		// echo json_encode($rooms->getProgramRoom($json['RegionId']));
		// break;
	case 'GetRoomsList':
		echo json_encode($rooms->getRoomsList($json));
		break;
	case 'GetRoomsListByArtist':
		echo json_encode($rooms->getRoomsListByArtist($json));
		break;
	case 'GetRoomInfo':
		echo json_encode($rooms->getRoomInfo($json['Roomid']));
		break;
	// case 'GetNotice':
		// echo json_encode($rooms->getNotice($json['RegionId']));
		// break;
	case 'GetMoneyRank':
		$type = empty($json['Type']) ? 'month' : $json['Type'];
		$rows = empty($json['Rows']) ? 10 : $json['Rows'];
		echo json_encode($rooms->getMoneyRank($json['RegionId'],$json['Uptime'],$type,$rows));
		break;
	case 'ArtistPopularity':
		$type = empty($json['Type']) ? 'week' : $json['Type'];
		$rows = empty($json['Rows']) ? 10 : $json['Rows'];
		echo json_encode($rooms->artistPopularity($json['RegionId'],$json['Uptime'],$type,$rows));
		break;
	case 'RoomPopularity':
		$type = empty($json['Type']) ? 'week' : $json['Type'];
		$rows = empty($json['Rows']) ? 10 : $json['Rows'];
		echo json_encode($rooms->roomPopularity($json['RegionId'],$json['Uptime'],$type,$rows));
		break;
	case 'GetPhiz': // 表情
		$type = $json['Type'];
		if(empty($type)) $type = 'gif';
		echo socket_request('http://127.0.0.1/pic/phiz.php',array('type'=>$type));
		break;
	case 'HotArtist':
		echo json_encode($rooms->hotArtist($json));
		break;
	case 'GetRecommedRooms':
		echo json_encode($rooms->getRecommedRooms($json['RegionId']));
		break;
	case 'ConsumeRank'://总站消费排行
		$type = empty($json['Type']) ? 'week' : $json['Type'];
		$rows = empty($json['Rows']) ? 20 : $json['Rows'];
		echo json_encode($rooms->consumeRank($json['Uptime'],$type,$rows));
		break;
}