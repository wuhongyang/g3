<?php
//include dirname(dirname(dirname(dirname(__FILE__)))).'/library/global.fun.php';
include_once 'library/group_foot.class.php';
$gf = new GroupFoot();
$json = $_POST['extparam'];
/*$json['Tag'] = 'Info';
$data = array('group_id'=>10000,'domain'=>'www.vvku.com','icp'=>'浙ICP备12027086号-1"','icp_info'=>'音乐殿堂娱乐中\心 电话：13605520252 浙ICP备12027086号-1');
$json['Id'] = 1;
$json['Data'] = $data;*/
switch($json['Tag']){
	case 'List':
		echo json_encode($gf->lists($json['GroupId'], $json['no_page']));
		break;
	case 'Info':
		echo json_encode($gf->info($json['Id']));
		break;
	case 'Insert':
		echo json_encode($gf->insert($json['Data']));
		break;
	case 'Update':
		echo json_encode($gf->update($json['Id'],$json['Data']));
		break;
	case 'SyncInfo':
		echo json_encode($gf->syncInfo($json['Id']));
		break;	
}