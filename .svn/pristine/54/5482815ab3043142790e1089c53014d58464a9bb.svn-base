<?php
require_once '../library/global.fun.php';
$db_config = config('database','default');
$db = db::connect($db_config);

if(isset($_GET['bigcase'])){
	echo httpPOST('core/ccs/ccs_api.php',array('extparam'=>array("Tag"=>"GetUseBigCase")),false);
}elseif(isset($_GET['case'])){
	echo httpPOST('core/ccs/ccs_api.php',array('extparam'=>array("Tag"=>"GetUseCase","BigCaseId"=>$_GET['case'])),false);
}elseif(isset($_GET['parent'])){
	echo httpPOST('core/ccs/ccs_api.php',array('extparam'=>array("Tag"=>"GetUseParent","CaseId"=>$_GET['parent'])),false);
}elseif(isset($_GET['child'])){
	echo httpPOST('core/ccs/ccs_api.php',array('extparam'=>array("Tag"=>"GetUseChild","ParentId"=>$_GET['child'])),false);
}elseif(isset($_GET['uin'])){
	$userinfo = httpPOST('core/kbasic/kbasic_api.php',array('extparam'=>array("Tag"=>"GetUserInfo","Uin"=>intval($_GET['uin']))));
	echo $userinfo['Nick'];
}else{
	$sql = 'SELECT parent_id,child_id,extparam,descr FROM g3_test.test_api WHERE `status` = 1';
	$result = $db->get_results($sql);
	foreach((array)$result as $row){
		$extparam['param_'.$row['parent_id'].'_'.$row['child_id']]['extparam'] = $row['extparam'];
		$extparam['param_'.$row['parent_id'].'_'.$row['child_id']]['descr'] = $row['descr'];
	}
	echo 'var extparams = '.json_encode($extparam);
}