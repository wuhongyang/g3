<?php
/**
* 通告证欢迎页面
* @author dl
* @copyright (c) 杭州奥点科技有限公司
*/
class account
{
	//构造函数
	function __construct(){
		include_once 'models/username.php';
		$this->username = new username();
		$this->login_user = $this->username->getLogin();
	}

	//用户登录
	function index(){
		$this->login();
	}
	
	//忘记密码
	function forgot(){
		if(!empty($_POST)){
			session_start();
			//if( ! getUserType($_POST['username'])) alertMsg('用户名错误');
			if(strtolower($_POST['checkcode']) != $_SESSION['captcha']) alertMsg('验证码错误');
			$userinfo = $this->username->getUser($_POST['username']);
			$userinfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$userinfo['Uid'],'Status'=>1)));
			if(empty($userinfo)) alertMsg('用户不存在');
			include THEMES_ROOT.'/account-forgot2.html';
			return;
		}
		include THEMES_ROOT.'/account-forgot.html';
	}
	
	function newpass(){
		$username = $this->username->url_authdecode($_GET['wd']);
		if(empty($username)) alertMsg('该链接无效或已过期','?account&forgot');
		if(!empty($_POST)){
			$rst = $this->username->editPassword($username,$_POST['password']);
			if($rst['Flag'] != 100) alertMsg($rst['FlagString']);
			include_once THEMES_ROOT.'/account-forgot4.html';
			return;
		}
		include_once THEMES_ROOT.'/account-forgot3.html';
	}
	
	function editpwd(){
		if($this->login_user['Flag'] !== 100) alertMsg('您还未登录','?account');
		if(!empty($_POST)){
			$user = isset($this->login_user['Phone'])? $this->login_user['Phone'] : $this->login_user['Email'];
			$rst = $this->username->resetPassword($user,$_POST['oldpwd'],$_POST['password']);
			if($rst['Flag'] !== 100) alertMsg($rst['FlagString']);
			alertMsg('修改成功','?account&userinfo');
		}
		include THEMES_ROOT.'/account-editpwd.html';
	}

	function login(){
		if(!empty($_POST)){
			if(empty($_POST['username']) || empty($_POST['password'])){
				$error = '用户名密码不能为空';
				//alertMsg('用户名密码不能为空');
			}
			$_POST['password'] = md5($_POST['password']);
			$userinfo = $this->username->login(trim($_POST['username']),$_POST['password'],$_POST['rememberme'],$_POST['group_id']);
            //判断是否ajax登录
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
                echo json_encode($userinfo);exit;
            }
			if($userinfo['Flag'] == 100){
				$url = (empty($_REQUEST['url']) || ($_REQUEST['url']== '/') )? '/' : base64_decode($_REQUEST['url']);
				alertMsg('',$url);
				//header("Location:{$url}");
			}else{
				//alertMsg($userinfo['FlagString']);
				$error = $userinfo['FlagString'];
			}
		}
        
		// $group_id = GROUP_ID;
		include THEMES_ROOT.'/account-login.html';
	}
    
    public function vclientlogin(){
        if(empty($_GET['username']) || empty($_GET['password'])){
            exit('var data = {"Flag":101,"FlagString":"用户名密码不能为空"}');
        }
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        $_GET['password'] = md5($_GET['password']);
        $userinfo = $this->username->login(trim($_GET['username']),$_GET['password'],$_GET['rememberme'],$_GET['group_id']);
        exit('var data = '.json_encode($userinfo));
    }

	function logout(){
		$this->username->logout();
		header('Location:/');
	}
	
}