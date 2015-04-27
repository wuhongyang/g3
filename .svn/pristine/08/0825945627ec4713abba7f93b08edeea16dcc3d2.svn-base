<?php
require_once 'library/weibo.class.php';
require_once 'library/fans.class.php';
$fans = new fans();
$weibo = new weibo($fans);
$json = $_POST['extparam'];
$param = $_POST['param'];
switch ($json['Tag']) {
	case 'GetWeiboByFriends':
		echo json_encode($weibo->getWeiboByFriends($json['Uin']));
		break;
	case 'ListWeibo':	//查看微博
		echo json_encode($weibo->getWeiboNews());
		break;
	case 'AddWeibo' :	//微博添加
		echo json_encode($weibo->addWeibo($json['Uin'],$json['Nick'],$json['Post']));
		break;
	case 'DelWeibo' :	//删除微博
		echo json_encode($weibo->deleteWeibo($json['Uin'],$json['Tid']));
		break;
	case 'getRelayMyWeibo' :	//@提到我的微博
		echo json_encode($weibo->getRelayMyWeibo($json['Uin']));
		break;
	case 'WeiboHome'  :	//查看个人所有微博
		echo json_encode($weibo->getWeiboByUser($json['Uin']));
		break;
	case 'AllWithWeibo'  :	//全部转播
		echo json_encode($weibo->getWeiboComments( $json['Uin'],$json));
		break;
	case 'CountWeibo'  :	//全部转播
		echo json_encode($weibo->countWeibo($json['Uin']));
		break;
	case 'RecommendFans'  :	//粉丝推荐
		echo json_encode($fans->recommendFans($json['Uin']));
		break;
	case 'GetFansNum'   :	//获得粉丝数量
		$other_uin = !empty($json['OtherUin']) ? intval($json['OtherUin']) : 0;
		echo json_encode($fans->getFansNum($json['Uin'],$other_uin));
		break;
	case 'GetFollowNum' :	//获得关注数量
		echo json_encode($fans->getFollowNum($json['Uin'],$json['OtherUin']));
		break;
	case 'GetFollow' :	//获得关注用户
		echo json_encode($fans->getFollow($param['Uin'],$param['TargetUin']));
		break;
	case 'ListFans' :	//获取粉丝信息
		echo json_encode($fans->listFans($json['Uin']));
		break;
	case 'MoveFans' :	//移除粉丝
		echo json_encode($fans->moveFans( $json['Data'] ));
		break;
	case 'ListFollow' :	//获取关注信息
		echo json_encode($fans->listFollow($json['Uin']));
		break;
	case 'MoveFollow'  :	//移除关注
		echo json_encode($fans->moveFollow($param['Uin'],$param['TargetUin']));
		break;
	case 'AddFollow'  :	//添加关注
		echo json_encode($fans->addFollow($param['Uin'],$param['TargetUin']));
		break;
}
