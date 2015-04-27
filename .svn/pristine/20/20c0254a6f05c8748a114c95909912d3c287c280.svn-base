<?php
require_once 'library/notice.class.php';
error_reporting(0);
$GroupNotice=new GroupNotice();
$json=$_POST['extparam'];
$param=$_POST['param'];

switch($json['Tag']){
	case 'GetNoticeInfo'://查询文章详情
		echo json_encode($GroupNotice->getNoticeInfo($json['GroupId'],$json['Type'],$json['Id']));
	break;
}