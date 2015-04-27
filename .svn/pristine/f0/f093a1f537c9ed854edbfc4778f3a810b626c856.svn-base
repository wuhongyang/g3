<?php
require_once '../library/global.fun.php';

$module = $_GET['module'];

switch($module){
	case 'ccs':
		if(isset($_GET['bigcase_id'])){
			$caseInfo = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetUseCase','BigCaseId'=>$_GET['bigcase_id'])));
			exit(json_encode((array)$caseInfo));
		}
		elseif(isset($_GET['case_id'])){
			$parentInfo = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetUseParent','CaseId'=>$_GET['case_id'])));
			exit(json_encode((array)$parentInfo));
		}
		elseif(isset($_GET['parent_id'])){
			$childInfo = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetUseChild','ParentId'=>$_GET['parent_id'])));
			exit(json_encode((array)$childInfo));
		}else{
			$bigcaseInfo = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetUseBigCase')));
			exit(json_encode((array)$bigcaseInfo));
		}
		break;
	case 'uin_type':
		$group_id = intval($_GET['group_id']);
		$type = intval($_GET['type']);
		$uin = intval($_GET['uin']);
		$info = getGroupChannelUser($group_id,$type,$uin);
		if(!empty($info)){
			$uininfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$uin)));
			$info['nick'] = $uininfo['baseInfo']['nick'];
		}
		exit(json_encode((array)$info));
		break;
}