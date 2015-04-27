<?php
require_once 'common.php';
switch ($module) {
	case 'index':
	case 'login':
		if(isset($_POST) && !empty($_POST)){
			$param = array(
				'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10090,'ChildId'=>101,'Uin'=>$_POST['username'],'SessionKey'=>md5($_POST['password']),'DoingWeight'=>1,'GroupId'=>$_POST['group_id']),
				'extparam' => array('Tag'=>'UserLogin','Remember'=>$_POST['rememberme'])
			);
			$rst = request($param);
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
                echo json_encode($rst);exit;
            }
            if($rst['Flag'] == 100){
				$url = (empty($_REQUEST['url']) || ($_REQUEST['url']== '/') )? '/' : base64_decode($_REQUEST['url']);
				alertMsg('',$url);
			}else{
				$error = $rst['FlagString'];
			}
		}

		include THEMES_ROOT.'/account-login.html';
		break;

	case 'vclientlogin':
		if(empty($_GET['username']) || empty($_GET['password'])){
            exit('var data = {"Flag":101,"FlagString":"用户名密码不能为空"');
        }
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        $_GET['password'] = md5($_GET['password']);
        $param = array(
			'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10090,'ChildId'=>101,'Uin'=>trim($_GET['username']),'SessionKey'=>$_GET['password'],'DoingWeight'=>1,'GroupId'=>$_GET['group_id']),
			'extparam' => array('Tag'=>'UserLogin','Data'=>$_GET)
		);
        $userinfo = request($param);
        exit('var data = '.json_encode($userinfo));
		break;

	case 'forgot':
		if(isset($_POST) && !empty($_POST)){
			$rst = httpPOST('api/passport/passport_api.php',array('extparam'=>array('Tag'=>'Forget','Data'=>$_POST)));
			if($rst['Flag'] != 100){
				alertMsg($rst['FlagString']);
			}
			$userinfo = $rst['UserInfo'];
			include THEMES_ROOT.'/account-forgot2.html';
		}else{
			include THEMES_ROOT.'/account-forgot.html';
		}
		break;

	case 'newpass':
		if(isset($_POST) && !empty($_POST)){
			$_POST['Info'] = $_GET['wd'];
			$param = array(
				'param' => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10021,'ChildId'=>102),
				'extparam' => array('Tag'=>'GetBackPassword','Data'=>$_POST)
			);
			$rst = request($param);
			if($rst['Flag'] != 100){
				alertMsg($rst['FlagString']);
			}
			alertMsg($rst['FlagString'],'/');
		}else{
			$rst = httpPOST('api/passport/passport_api.php',array('extparam'=>array('Tag'=>'NewpassIndex','UserName'=>$_GET['wd'])));
			if($rst['Flag'] != 100){
				alertMsg($rst['FlagString']);
			}
			include_once THEMES_ROOT.'/account-forgot3.html';
		}
		break;
}