<?php
require_once 'common.php';
define('PHONE_TYPE', 3);
switch ($module) {
	case 'index':
		include_once THEMES_ROOT.'/phone-reg.html';
		break;

	case 'regpost':
		$_POST['type'] = PHONE_TYPE;
		$_POST['checkcode'] = $_POST['msgcode'];
		$param = array(
			'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10128,'ChildId'=>103,'DoingWeight'=>1,'Desc'=>'手机注册','GroupId'=>$GroupData['groupid']),
			'extparam' => array('Tag'=>'Register','Type'=>PHONE_TYPE,'Data'=>$_POST)
		);
		$rst = request($param);
		if($rst['Flag'] == 100){
			alertMsg("","/passport/?user_phone&success&info=".urlencode(json_encode($rst)));
		}else{
			alertMsg($rst['FlagString']);
		}
		break;

	case 'sendcode':
		if($_POST['nouser']){
			$rst = httpPOST('api/passport/passport_api.php',array('extparam'=>array('Tag'=>'SendCode4Reg','Type'=>PHONE_TYPE,'Phone'=>$_POST['nouser'])));
		}elseif($_POST['user']){
			$rst = httpPOST('api/passport/passport_api.php',array('extparam'=>array('Tag'=>'SendCode4GetPwd','Type'=>PHONE_TYPE,'Phone'=>$_POST['user'])));
		}
		exit(json_encode($rst));
		break;

	case 'success':
		$userInfo = json_decode(urldecode($_GET['info']), true);
		include_once THEMES_ROOT.'/phone-reg-success.html';
		break;

	case 'getpwd':
		if(!empty($_POST['msgcode'])){
			$rst = httpPOST('api/passport/passport_api.php',array('extparam'=>array('Tag'=>'GetPasswdByPhone','Type'=>PHONE_TYPE,'Data'=>$_POST)));
			if($rst['Flag'] != 100){
				alertMsg($rst['FlagString']);
			}
			alertMsg('',"?account&newpass&wd={$rst['Wd']}");
		}else{
			$userinfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','UserName'=>$_POST['username'],'Status'=>1)));
			$userinfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$userinfo['Uid'],'Status'=>1)));
			include_once THEMES_ROOT.'/phone-getpwd.html';
		}
		break;

	default:
		# code...
		break;
}