<?php
require_once 'common.php';
define('QQ_TYPE', 4);
switch ($module) {
	case 'bind_index':
		$info = json_decode(base64_decode($_GET['info']),true);
		if(isset($_POST) && !empty($_POST)){
			$_POST['openid'] = $info['openid'];
			$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'QqBind','Data'=>$_POST)));
			if($rst['Flag'] == 100){
				$info = array('uin'=>$rst['Uin'],'username'=>$_POST['username']);
				alertMsg('','/passport/?qq&bind_success&info='.urlencode(json_encode($info)));
			}
		}else{
			$info['nick'] = urldecode($info['nick']);
			$info['picurl'] = urldecode($info['picurl']);
			include_once THEMES_ROOT.'/qq-bind.html';
		}
		break;

	case 'bind_success':
		$userInfo = json_decode(urldecode($_GET['info']),true);
		include_once THEMES_ROOT.'/qq-bind-finish.html';
		break;
}