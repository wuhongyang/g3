<?php
require_once 'library/active.class.php';
error_reporting(0);
$group_active=new GroupActive();
$json=$_POST['extparam'];
$param=$_POST['param'];

switch($json['Tag']){
	case 'GetActiveAd'://查询站点活动轮播图
		echo json_encode($group_active->getActiveAd($json['GroupId']));
	break;
	case 'GetActiveList'://查询站点活动列表
		echo json_encode($group_active->getActiveList($json['GroupId'],$json['Limit']));
	break;
	case 'GetActiveInfo'://查询站点活动详情
		echo json_encode($group_active->getActiveInfo($json['ActiveId']));
	break;
	case 'JoinActive'://参与站点活动
		echo json_encode($group_active->joinActive($json['ActiveId'],$param['Uin']));
	break;
	case 'GetActivePlayers'://查询活动参与者
		echo json_encode($group_active->getActivePlayers($json['ActiveId']));
	break;
	case 'GetActiveListJson'://查询站点活动列表
		echo json_encode($group_active->getActiveListJson($json['ActiveIds']));
	break;
}