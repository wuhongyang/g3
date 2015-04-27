<?php
require_once '../library/global.fun.php';

$module = $_GET['module'];

switch($module){
	case 'uin_exists':
		$uin = intval($_GET['uin']);
		if($uin < 1){
			$info = array('Flag'=>101,'FlagString'=>'格式不正确');
		}else{
			$info = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'UinExist','Uin'=>$uin)));
		}
		exit(json_encode($info));
		break;
}