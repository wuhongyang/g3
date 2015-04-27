<?php
require_once 'library/group_site.class.php';
require_once __ROOT__.'/api/rooms/library/g3_ktv_api.class.php';
error_reporting(0);
$group_site=new GroupSite();
$json=$_POST['extparam'];
$param=$_POST['param'];

switch($json['Tag']){
	case 'GetGroupNavigate'://查询站点顶部导航
		echo json_encode($group_site->getGroupNavigate($json['GroupId']));
		break;
	case 'GetGroupStyle'://查询站点风格信息
		echo json_encode($group_site->getGroupStyle($json['GroupId']));
		break;
	case 'GetGroupImg'://查询站点图片
		echo json_encode($group_site->getGroupImg($json['GroupId']));
	break;
	case 'GetGroupSearchConfig'://查询站点搜索配置
		echo json_encode($group_site->getGroupSearchConfig($json['GroupId']));
	break;	
	case 'GetGroupCarousel'://查询站点首页轮播图
		echo json_encode($group_site->getGroupCarousel($json['GroupId']));
	break;
	case 'GetGroupArtistList'://查询站点下艺人
		echo json_encode($group_site->getGroupArtistList($json['Data'], $json['RuleId'], $json['IsArtistDetail']));
	break;
	case 'GetGroupRoomList'://查询站点下房间
		echo json_encode($group_site->getGroupRoomList($json['Data']));
	break;
	case 'GetGroupRecommend'://查询站点推荐位
		echo json_encode($group_site->getGroupRecommend($json['GroupId'], $json['RuleId'], $json['IsArtistDetail']));
	break;
	case 'GetRecommendSub'://查询推荐位
		echo json_encode($group_site->getRecommendSub($json['SubId'], $json['RuleId'], $json['IsArtistDetail']));
	break;
	case 'GetGroupRank'://查询站点首页排行榜设置
		echo json_encode($group_site->getGroupRank($json['GroupId'],$json['Type'],$json['Row'],$json['RoleImg']));
	break;
	case 'GetGroupMenu'://查询站点首页左部导航
		echo json_encode($group_site->getGroupMenu($json['GroupId']));
	break;
	case 'GetGroupMessage'://查询站点动态信息
		echo json_encode($group_site->getGroupMessage($json['GroupId']));
	break;	
	case 'GetGroupVipOnline'://查询站内在麦会员
		echo json_encode($group_site->getGroupVipOnline($json['GroupId']));
	break;
	case 'GetGroupOnlineNum'://查询站点在线人数
		echo json_encode($group_site->getGroupOnlineNum($json['GroupId']));
	break;
	case 'GetGroupSetting'://查询站点配置
		echo json_encode($group_site->getGroupSetting($json['GroupId']));
	break;
	case 'GetGroupLivePhoto'://查询站点艺人直播墙
		echo json_encode($group_site->getGroupLivePhoto($json['GroupId']));
	break;
	case 'PracticeUserLogin':
		echo json_encode($group_site->practiceUserLogin($json['UserName'], $json['Telephone'], $json['GroupId']));
		break;
	case 'GetPractice':
		echo json_encode($group_site->getPractice($json['GroupId']));
		break;
    case 'ArtistDetail':
		echo json_encode($group_site->artistDetail($json['GroupId'], $json['RuleId'], $json['UinArr']));
        break;
}