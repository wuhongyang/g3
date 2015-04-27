<?php
require_once '../library/global.fun.php';
require_once 'fund_config.php';

$module = empty($_GET['module']) ? 'user_balance' : trim($_GET['module']);
$_GET['startDate'] = $_GET['startDate'] ? $_GET['startDate'] : date('Y-m-01');
$database = $_GET['database'] ? $_GET['database'] : 'kkyoo_voucher';
if(empty($_GET['table'])){
	$table_keys = array_keys($config_array[$database]);
	$table = $table_keys[0];
}else{
	$table = $_GET['table'];
}

//当前使用站
$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

if(!$_GET['Group_id']){
	//只有非平台数据库才需要groupid
	if(strpos($database,'plat') === false){
		$_GET['Group_id'] = $__ADMIN_CURGROUP['groupid'];
	}
}

switch($module){
	case 'user_balance':
		$searchField=$config_array[$database][$table]['searchField'];
		$statistics=$config_array[$database][$table]['statistics']['name'];
		
		$param = array(
			'extparam' => array('Tag'=>'Fund_system','Database'=>$database,'Table'=>$table,'Data'=>$_GET),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>$link_array[$database],'ChildId'=>$config_array[$database][$table]['child_id'],'Desc'=>$config_array[$database][$table]['table_name'])
		);
		if(($database != 'kkyoo_kmoney' && $_GET['Group_id']>0) ||$database == 'kkyoo_kmoney'||$database == 'kkyoo_voucher_plat'||$database == 'kkyoo_tax_plat'){
			$list_array = request($param);
			if($list_array['Flag'] !== 100){
				echo json_encode($list_array);
				exit;
			}
		}
		$page = $list_array['page'];
		unset($list_array['page']);
		$template = 'fund_system.html';
	break;
	case 'get_statistics':
		$param=array(
			'extparam' => array('Tag'=>'Fund_system_statistics','Database'=>$database,'Table'=>$table,'Data'=>$_GET,'Statistics_data'=>$config_array[$database][$table]['statistics']['data']),
			'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>$link_array[$database],'ChildId'=>$config_array[$database][$table]['child_id'],'Desc'=>$config_array[$database][$table]['table_name'])
		);
		$resultList=request($param);
		if($resultList['Flag']!=100){
			echo json_encode($resultList);
			exit;
		}
		$resultList=$resultList['Result'];
		$result='';
		foreach($resultList as $val){
			if(isset($val['name'])){
				$result.=$val['name'].'：'.$val['total'].'，';
			}
			else{
				$result.=$val['total'].'，';
			}
		}
		$result=rtrim($result,'，');
		$result.='。';
		$array=array('Flag'=>100,'FlagString'=>'成功','Result'=>$result);
		echo json_encode($array);
		exit;
	break;
}
	
unset($list_array['Flag'],$list_array['FlagString']);
$link_array = getLevellink(10002,10007,$link_array[$database],101);
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('kmoney/'.$template,$tpl);
?>