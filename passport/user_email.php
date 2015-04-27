<?php
require_once 'common.php';
define('EMAIL_TYPE', 1);
switch ($module) {
	case 'index':
		if(isset($_POST) && !empty($_POST)){
			//发邮件，真正注册在rc
			$_POST['type'] = EMAIL_TYPE;
			$rst = httpPOST('api/passport/passport_api.php',array('extparam'=>array('Tag'=>'Register','Type'=>EMAIL_TYPE,'Data'=>$_POST)));
			if(isset($_POST['resend'])){
				echo intval($rst['Flag'] == 100);exit;
			}
			if($rst['Flag'] != 100){
				alertMsg($rst['FlagString']);
			}
			alertMsg('','/passport/?user_email&active='.urlencode(json_encode($_POST)));
		}else{
			include THEMES_ROOT.'/email-reg.html';
		}
		break;

	case 'active':
		$data = json_decode(urldecode($_GET['active']),true);
		include_once THEMES_ROOT.'/email-active.html';
		break;

	case 'rc':
		//注册
		$param = array(
			'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10128,'ChildId'=>103,'DoingWeight'=>1,'Desc'=>'邮箱注册','GroupId'=>$GroupData['groupid']),
			'extparam' => array('Tag'=>'Active','Type'=>EMAIL_TYPE,'Data'=>$_GET['rc'])
		);
		$rst = request($param);
		if($rst['Flag'] == 100){
			alertMsg("","/passport/?user_email&success&info=".urlencode(json_encode($rst)));
		}else{
			alertMsg($rst['FlagString'],BASE_URL);
		}
		break;

	case 'success':
		$userInfo = json_decode(urldecode($_GET['info']), true);
		include_once THEMES_ROOT.'/email-reg-finish.html';
		break;

	case 'countmail':
		$rst = httpPOST('api/passport/passport_api.php',array('extparam'=>array('Tag'=>'UserExist','Type'=>EMAIL_TYPE,'User'=>$_GET['countmail'])));
		echo $rst['Flag'] == 100 ? 1 : 0;
		exit;
		break;

	case 'getpwd':
		$rst = httpPOST('api/passport/passport_api.php',array('extparam'=>array('Tag'=>'GetPasswdByEmail','Type'=>EMAIL_TYPE,'UserName'=>$_POST['username'])));
		if($rst['Flag'] != 100){
			alertMsg($rst['FlagString']);
		}
		include_once THEMES_ROOT.'/email-getpwd2.html';
		break;

	case 'newpass':
		if(isset($_POST) && !empty($_POST)){
			$_POST['Info'] = $_GET['wd'];
			$param = array(
				'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10021,'ChildId'=>102),
				'extparam' => array('Tag'=>'GetBackPassword','Type'=>EMAIL_TYPE,'Data'=>$_POST)
			);
			$rst = request($param);
			if($rst['Flag'] != 100){
				alertMsg($rst['FlagString']);
			}
			alertMsg($rst['FlagString'],'/');
		}else{
			$rst = httpPOST('api/passport/passport_api.php',array('extparam'=>array('Tag'=>'NewpassIndex','Type'=>EMAIL_TYPE,'UserName'=>$_GET['wd'])));
			if($rst['Flag'] != 100){
				alertMsg($rst['FlagString']);
			}
			include_once THEMES_ROOT.'/account-forgot3.html';
		}
		break;
}