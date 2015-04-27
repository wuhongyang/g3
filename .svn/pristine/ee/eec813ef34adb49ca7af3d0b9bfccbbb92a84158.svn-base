<?php
require dirname(__FILE__).'/library/admin.class.php';
$admin = new admin();
$json = $_POST['extparam'];
$param = $_POST['param'];
switch($json['Tag']){
	case 'Cluster_create':
		if($json['Action'] == 'post'){
			$array = $admin->clusterCreate($json);
		}else{
			$cluster_name = $admin->clusterInfo($json);
			$array = array('module_name'=>'新建权限集群','js_name'=>'sysuser_create','cluster_name'=>$cluster_name);
		}
		echo json_encode($array);
		break;
	case 'Cluster_editor':
		if($json['Action'] == 'post'){
			$array = $admin->clusterCreate($json);
		}else{
			$cluster_name = $admin->clusterInfo($json);
			$array = array('module_name'=>'修改权限集群','js_name'=>'sysuser_create','cluster_name'=>$cluster_name);
		}
		echo json_encode($array);
		break;
	case 'Cluster_list':
		$array = $admin->clusterList($json);
		echo json_encode($array);
		break;
	case 'Group_create':
	case 'Group_editor':
		if($json['Action'] == 'post'){
			$array = $admin->groupCreate($json);
		}else{
			$array = $admin->groupConfig($json,$param);
		}
		echo json_encode($array);
		break;
	case 'Group_list':
		$array = $admin->groupList($json,$param);
		echo json_encode($array);
		break;
}