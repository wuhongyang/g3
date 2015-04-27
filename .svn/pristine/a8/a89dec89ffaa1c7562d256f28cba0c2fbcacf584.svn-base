<?php
require_once 'library/link.class.php';
error_reporting(0);
$GroupLink=new GroupLink();
$json=$_POST['extparam'];
$param=$_POST['param'];

switch($json['Tag']){
	case 'GetLinkCateList'://查询文章详情
		echo json_encode($GroupLink->getLinkCateList($json['GroupId']));
	break;
	case 'GetLinkList'://查询文章详情
		echo json_encode($GroupLink->getLinkList($json['GroupId'],$json['CateId']));
	break;
}