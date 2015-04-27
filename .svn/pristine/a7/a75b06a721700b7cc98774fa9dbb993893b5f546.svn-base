<?php
require_once 'library/search.class.php';
error_reporting(0);
$search_api=new VipSearch();
$json=$_POST['extparam'];
$param=$_POST['param'];

switch($json['Tag']){
	case 'search'://根据条件查询站内会员
		echo json_encode($search_api->search($json['GroupId'],$json['Data']));
	break;
}