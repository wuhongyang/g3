<?php
include_once 'library/custom.class.php';
include_once 'library/stamp.class.php';
include_once 'library/g3_ktv_api.class.php';
include_once 'config/g3_ktv_var_config.php';
include_once 'library/uipackage.class.php';

$json = $_POST['extparam'];
$param = $_POST['param'];

switch ($json['Tag']){
	case 'SetMikePlayTime':
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->setMikePlayTime($_POST['param'],$json));
		break;
	case 'RoomLogin': //用户登录
		$Pwd = strval($json['Pwd']);
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->roomlogin($param,$Pwd));
		break;
	case 'RoomLogOut': //用户退出
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->roomlogout($_POST['param'],$json));
		break;
	case 'ServerInit': //在线人数初始化
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->serverinit());
		break;
	case 'Stamp' : //盖章
		$StampId	= $json['StampId'];
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->Stamp($param,$StampId,$json));
		break;
	case 'UseBroadcast' : //广播
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->UseBroadcast($param,$json));
		break;
	// case 'RegionChat'://地区聊天 ***功能已废弃
		// $g3_ktv_api = new g3_ktv_api();
		// echo json_encode($g3_ktv_api->regionChat($param,$json));
		// break;
	case 'RobotLogin': //机器人登录
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->robotLogin($param));
		break;
	case 'RobotLogout': //机器人退出
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->roomlogout($param,$json));
		break;
	case 'RoomCuruserIpNum' : //房间在线人数，ip数
		$Roomid    = $json['Roomid'];
		$Curuser   = $json['Curuser'];
		$Ipnum     = $json['Ipnum'];
		$Reguser     = $json['MemberNum'];
		$rooms_ktv = new g3_ktv_api();
		echo json_encode($rooms_ktv->RoomCuruserIpNum($Roomid,$Curuser,$Ipnum,$Reguser));
		break;
	case 'SystemUserNumAndIp' : //系统在线人数，ip数
		$rooms_ktv = new g3_ktv_api();
		$result = array('Flag'=>100);
		$result['Result'][] = $rooms_ktv->userTotalNum(intval($json['Curuser']),intval($json['Ipnum']),intval($json['MemberNum']));
		foreach($json['Sites'] as $site){
			$Regionid  = $site['RegionId'];
			$Curuser   = $site['Num'];
			$Ipnum     = $site['Ip'];
			$Reguser     = $site['MemberNum'];
			$result['Result'][] = $rooms_ktv->systemUserNumAndIp($Regionid,$Curuser,$Ipnum,$Reguser);
		}
		echo json_encode($result);
		break;
	/* ==============================客户端调用=========================================== */
	case 'FreezeRoom':
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->freezeRoom($param));
		break;
	case 'GetRoomInfo':
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->getRoomInfo($param));
		break;
	case 'StampList' : //印章列表
		$stamp = new stamp();
		echo json_encode($stamp->StampList($param));
		break;
	case 'GetPhiz': // 表情
		$type = $json['Type'];
		if(empty($type))
			$type = 'swf';
		echo file_get_contents("http://{$_SERVER['HTTP_HOST']}/pic/phiz.php?type={$type}");
		break;
	case 'GetNewPhiz': //新获取表情
		$uipackage = new UiPackage();
		echo json_encode($uipackage->GetNewPhiz($param));
		break;
	case 'GetCaitiaoList': //彩条
		include dirname(__FILE__).'/config/caitiao.php';
		echo json_encode(array('Flag'=>100,'FlagString'=>'获取彩条成功','Result'=>$caitiao_config));
		break;
	case 'PropsList' : //礼物
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->PropsList($param,(array)$json));
		break;
	case 'InteractList' : //互动游戏 接口已废
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->InteractList($param));
		break;
	case 'GetRandNick':
		echo httpPOST(GUEST_API_PATH,array('extparam'=>array('Tag'=>'GetRandNick','Gender'=>$json['Gender'])),false);
		break;
	case 'SaveGuest':
		echo httpPOST(GUEST_API_PATH,array('extparam'=>array('Tag'=>'SaveGuest','GuestInfo'=>$json['GuestInfo'])),false);
		break;
	case 'EditNick':
		if($param['Uin'] >= GUEST_UIN_START && $param['Uin'] <= GUEST_UIN_END){
			echo httpPOST(GUEST_API_PATH,array('param'=>$param,'extparam'=>$json),false);
		}else{
			echo httpPOST(SSO_API_PATH,array('param'=>$param,'extparam'=>$json),false);
		}
		break;
	case 'SetMikePlaying': //上麦
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->setMikePlaying($param));
		break;
	case 'SetMikePlayEnd': //下麦
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->setMikePlayEnd($param,(array)$json));
		break;
	case 'queueMike': //排麦
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->queueMike($param));
		break;
	
	/*这里三个一样的接口需要优化*/
	case 'SetRoomInfo' : //上麦时长设置
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->setRoomInfo($param,(array)$json));
		break;
	case 'SetMikeTime' : //上麦时长设置
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->setRoomInfo($param,(array)$json));
		break;
	case 'SetDice': //骰子设置
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->setRoomInfo($param,(array)$json));
		break;
	/*--------------------------------*/
	
	case 'SendPubChat': //发送公聊
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->sendPubChat($param));
		break;
	case 'SendPrivChat': //发送私聊
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->sendPrivChat($param));
		break;
	case 'SendDice':
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->sendDice($param));
		break;
	case 'SendColourBar':
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->sendColourBar($param));
		break;
	
	/* ==============================MONGODB用户信息保存=========================================== */
	case 'SetUserInfo':
		$Uin = $param['Uin'];
		$UserInfo = $json['UserInfo'];
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->setUserInfo($Uin,$UserInfo));
		break;
	case 'GetUserInfo':
		$uin = $param['Uin'];
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->getUserInfo($uin));
		break;
	case 'SetRoomTheme':
		$roomid = $param['ChannelId'];
		$RoomTheme = $json['RoomTheme'];
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->setRoomTheme($roomid,$RoomTheme));
		break;
	case 'GetRoomTheme':
		$roomid = $param['ChannelId'];
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->getRoomTheme($roomid));
		break;
	case 'GetRoomConfig':
		$roomid = $param['ChannelId'];
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->getRoomConfig($roomid));
		break;
	case 'GetRoomConsume'://房内消费排行
		$roomid = (int)$param['ChannelId'];
		$type = $json['Type'];
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->getRoomConsume($roomid,10,$type,$json['Unit']));
		break;
	case 'GetRoomRank'://房内排行榜入口
		$json['ChannelUin'] = $json['ChannelUin'] >0? $json['ChannelUin'] : (int)$param['ChannelId'];
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->getRoomLucky($json['UinId'],$json['ChannelUin'],$json['ExtendUin'],$json['Ruleid'],$json['Period'],$json['Rows'],$json['Time']));
		break;
	case 'PopularVote'://人气票接口
		$g3_ktv_api = new g3_ktv_api();
		$param['DoingWeight'] =1;//判断收礼人是否是艺人
		echo json_encode($g3_ktv_api->PopularVote($param,$json));
		break;
	case 'GetLevelVaule'://获取等级值
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->getLevel($json['UinId'],$json['ChannelUin'],$json['ExtendUin'],$json['Ruleid'],$json['Period']));
		break;
	case 'GetPointVaule'://获取积分值
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->getWeight($json['UinId'],$json['ChannelUin'],$json['ExtendUin'],$json['Ruleid'],$json['Period']));
		break;
	case 'GetLevelResult'://获取等级结果集
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->getLevelResult($json['UinId'],$json['ChannelUin'],$json['ExtendUin'],$json['Ruleid'],$json['Period'],$json['Rows'],$json['Time']));
		break;
	case 'GetScoreDiff'://计算用户距离下一级别积分差
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->scoreDiff($json['UinId'],$json['ChannelUin'],$json['ExtendUin'],$json['Ruleid'],$json['Period']));
		break;
	case 'FunCardList'://功能牌列表
		$g3_ktv_api = new g3_ktv_api();
		$uin = $param['Uin'];
		$group_id = $param['GroupId'];
		echo json_encode($g3_ktv_api->FunCardList($group_id,$uin));
		break;
	case 'DaemonList':
		$g3_ktv_api = new g3_ktv_api();
		$group_id = $param['GroupId'];
		$room_id = $param['ChannelId'];
		$artist = $json['Artist'];
		echo json_encode($g3_ktv_api->daemonList($group_id,$room_id,$artist));
		break;
	case 'GetConfig':
		$g3_ktv_api = new g3_ktv_api();
		$group_id = $param['GroupId'];
		echo json_encode($g3_ktv_api->getConfig($group_id));
		break;
	case 'FrozenUser':
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->setState($param, $json));
		break;
	case 'GetUserRole':
		$g3_ktv_api = new g3_ktv_api();
		echo json_encode($g3_ktv_api->GetUserRole($param, $json));
		break;
	/* ==============================房间点歌=========================================== */
	case 'ListSong':
		include_once 'library/songs.class.php';
		$song = new RoomSongs($param);
		echo json_encode($song->listSong($json['Page'], $json['PageSize']));
		break;
}
