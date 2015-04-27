<?php
require_once 'common.php';
define('USERNAME_TYPE', 2);
switch ($module) {
	case 'index':
		include_once THEMES_ROOT.'/username-reg.html';
		break;

	case 'register':
		$_POST['type'] = USERNAME_TYPE;
		$param = array(
			'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10128,'ChildId'=>103,'DoingWeight'=>1,'Desc'=>'用户名注册','GroupId'=>$GroupData['groupid']),
			'extparam' => array('Tag'=>'Register','Type'=>USERNAME_TYPE,'Data'=>$_POST)
		);
		$rst = request($param);
		if($rst['Flag'] == 100){
			alertMsg("","/passport/?user_name&success");
		}else{
			alertMsg($rst['FlagString']);
		}
		break;

	case 'success':
		$userInfo=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
		include_once THEMES_ROOT.'/user-reg-finish.html';
		break;

	case 'username_exist':
		$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','UserName'=>$_GET['username'])));
		echo intval($rst['Flag'] == 100);
		exit;
		break;

	default:
		# code...
		break;
}