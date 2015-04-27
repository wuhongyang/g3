<?php
//require_once dirname(dirname(dirname(__FILE__))).'/library/global.fun.php';
require_once 'library/notice.class.php';
$a = new Notice();
$json = $_POST['extparam'];
$param = $_POST['param'];

//$json = array('Tag'=>'NoticeUpdate','Id'=>1,'Data'=>array('group_id'=>5711238,'title'=>'aaa','content'=>'bbb','category'=>3));

switch($json['Tag']){
	case 'NoticeList':
		echo json_encode($a->noticeList($json['GroupId'],$json['Data']));
		break;
	case 'NoticeInfo':
		echo json_encode($a->noticeInfo($json['Id'],$json['GroupId']));
		break;
	case 'NoticeAdd':
		echo json_encode($a->noticeAdd($json['Data']));
		break;
	case 'NoticeUpdate':
		echo json_encode($a->noticeUpdate($json['Id'],$json['Data']));
		break;
	case 'NoticeDel':
		echo json_encode($a->noticeDel($json['Id'],$json['GroupId']));
		break;
}