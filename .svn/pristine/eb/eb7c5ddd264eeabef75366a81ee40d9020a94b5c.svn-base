<?php
require_once 'library/join.class.php';
error_reporting(0);
$group_join=new GroupJoin();
$json=$_POST['extparam'];
$param=$_POST['param'];

switch($json['Tag']){
	case 'GetArticleInfo'://查询文章详情
		echo json_encode($group_join->getArticleInfo($json['GroupId'],$json['id']));
	break;
	case 'GetJoinList'://加入我们列表
		echo json_encode($group_join->getJoinList($json['GroupId']));
	break;
}