<?php
require_once 'library/room_collect.class.php';
$roomCollect=new roomCollect($fans);
$json = $_POST['extparam'];
$param = $_POST['param'];
switch ($json['Tag']) {
	case 'CollectRoom'://收藏房间
		echo json_encode($roomCollect->collectRoom($param['Uin'],$param['ChannelId']));
		break;
	case 'CancelRoom'://取消收藏房间
		echo json_encode($roomCollect->cancelRoom($param['Uin'],$param['ChannelId']));
		break;
	case 'GetCollect'://收藏房间列表
		echo json_encode($roomCollect->getCollectList($param['Uin'],$param['ChannelId']));
		break;		
	case 'GetCollectNum'://收藏房间数量
		echo json_encode($roomCollect->getCollectNum($json['Uin']));
		break;	
}
