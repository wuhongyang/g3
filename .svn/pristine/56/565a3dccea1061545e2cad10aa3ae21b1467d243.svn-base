<?php
abstract class Reg
{
	protected $group_id;

	public function __construct(){
		$GroupData = domain::main()->GroupData();
		$this->group_id = (int)$GroupData['groupid'];
		include_once 'models/username.php';
		$this->username = new username();
		$this->login_user = $this->username->getLogin();
	}

	/**
	 * 用户名是否存在
	 * @param string
	 * @return null  
	 **/
	public function isUsernameExists(){
		$username = $_GET['username'];
		$user = $this->username->getUser($username);
		if($user['Flag'] != 100) {
			exit('0');
		}
		exit('1');
	}

	//注册页面
	public abstract function index();

	//成功页面
	public abstract function success();

	//注册，绑定
	public function register(){
		$data = $this->validate($_POST);

		//检测站是否存在
		$group_id = $this->group_id;
		$this->groupInfo($group_id);

		//注册
		$res = $this->reg($data);
		if($res['Flag'] != 100){
			alertMsg($res['FlagString']);
		}

		//新注册账号自动登录
		$this->login($data);

		//跳转成功页面
		$data['uin'] = $res['Uin'];
		$data['nick'] = $res['Nick'];
		$data['group_id'] = intval($res['GroupId']);
		unset($data['password'],$data['repassword'],$data['checkcode']);
		$this->redirect($data);
	}

//------------------------------私有方法----------------------------------
	protected function redirect($data){
		alertMsg("",'/passport/?'.get_class($this).'&success&info='.urlencode(json_encode($data)));
	}
	
	protected function reg($data){
		$data = array('user'=>$data['username'],'pass'=>md5($data['password']),'type'=>$this->_type);
		return $this->username->reg($data);
	}
	protected function login($data){
		$this->username->login($data['username'],md5($data['password']),$data['remember']);
		//httpPOST('core/sso/new_sso_api.php',array('param'=>array("SessionKey"=>md5($data['password']),"Uin"=>$data['username']),'extparam'=>array('Tag'=>'UserLogin','Remember'=>$data['remember'])));
	}

	protected function groupInfo($group_id){
		if($group_id > 0){
			//得到站名称
			$param=array(
				'extparam'=>array('Tag'=>'GetGroupInfo','Id'=>$group_id),
				'param'=>array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101,'Desc'=>'获取站信息')
			);
			$groupInfo=request($param);
			if($groupInfo['Flag'] != 100){
				alertMsg('站不存在');
			}
		}
		return $groupInfo;
	}

	protected function validate(array $array){
		$array['username'] = trim($array['username']);
		$array['password'] = trim($array['password']);

		$this->validateUsername($array['username']);
		$this->validatePassword($array['password']);
		$this->validateRepassword($array['password'],$array['repassword']);
		$this->validateCode($array['checkcode']);
		$this->validateAgreement($array['read']);

		return $array;
	}

	protected function validateUsername($username){
		$username_pattern = '/^[a-zA-Z0-9_]{2,15}$/';
		if(preg_match($username_pattern, $username) === 0){
			alertMsg('请填写合法的用户名');
		}
		if(is_numeric($username)){
			alertMsg('用户名不能是纯数字');
		}
	}

	protected function validatePassword($password){
		$password_len = strlen($password);
		if($password_len < 6 || $password_len > 20){
			alertMsg('请填写合法的密码');
		}
	}

	protected function validateRepassword($password,$repassword){
		if($password != $repassword){
			alertMsg('两次输入的密码不一致');
		}
	}

	protected function validateAgreement($read){
		if($read != 1){
			alertMsg('请勾选《用户协议》');
		}
	}

	protected function validateCode($checkcode){
		$checkcode = strtolower($checkcode);
		if(empty($checkcode) || $checkcode != $this->getSession('captcha')) {
			alertMsg('验证码不正确');
		}
	}
	
	protected function getSession($key){
		if(empty($_SESSION)){
			session_start();
		}
		return $_SESSION[$key];
	}
}