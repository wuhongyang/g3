<?php
require_once '../library/global.fun.php';

if($_POST['Tag'] == 'GetLoginUser'){
	$user=checkDpLogin();
	if($user['Flag']==100){
		$permisssions=getDpUserPermission($user['Uin']);
		$groupId=(int)$permisssions['groupId'];
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId,'IsDetails'=>true),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$user['Uin'],'Desc'=>'获取站信息')
		);
		$userGroupInfo=request($param);
		$GroupInfo = $userGroupInfo['Result']['serviceInfo'];
		$GroupInfo['Nick'] = $user['Nick'];
		$GroupInfo['Flag'] = $userGroupInfo['Flag'];
		$GroupInfo['FlagString'] = $userGroupInfo['FlagString'];
		echo json_encode($GroupInfo);
	}
}elseif($_GET['Tag'] == 'userinfo'){
	$userinfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$_GET['uin'])));
	if($userinfo['Flag'] == 100){
		$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$_GET['uin'])));
		$userinfo['Nick'] = empty($user['baseInfo']['nick']) ? $userinfo['Uid'] : $user['baseInfo']['nick'];
	}
	echo json_encode($userinfo);exit;
}