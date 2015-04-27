<?php
require dirname(__FILE__).'/library/admin.class.php';
$admin = new admin();
$json = $_POST['extparam'];

switch($json['Tag']){
	case 'User_create':
		if($json['Action'] == 'post'){
			$array = $admin->userCreate($json);
		}
		elseif($json['Action'] == 'editor'){
			$array = $admin->userCreate($json);
		}else{
			$cluster_name = $admin->clusterInfo($json);
			$userinfo = $admin->userinfo($json);
			$array = array('module_name'=>'新建权限集群','js_name'=>'sysuser_create','cluster_name'=>$cluster_name,'userinfo'=>$userinfo);
		}
		echo json_encode($array);
		break;
	case 'User_list':
		$array = $admin->userList($json);
		echo json_encode($array);
		break;
	case 'Gruop_select':
		$array = $admin->gruopSelect($json);
		echo json_encode($array);
		break;
}
