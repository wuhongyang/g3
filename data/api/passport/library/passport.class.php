<?php

class Passport{

	protected $_base_url;
	protected $group_info = array();
		
	public function __construct(){
		$this->_base_url = 'http://'.$_SERVER['HTTP_HOST'].'/passport';
		$GroupData = domain::main()->GroupData();
		$g_id = (int)$GroupData['groupid'];
		$this->group_info = array(
			'group_id' 		=> $g_id,
			'group_name' 	=> $g_id > 0 ? $GroupData['name'] : 'VV酷'
		);
	}

	public function __call($name,$arguments){
		return array('Flag'=>101,'FlagString'=>'非法调用');
	}

	public final function userLogOut(){
		httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'UserLogOut')));
	}

	//忘记密码,用户检测,不能被继承
	public final function forget($data){
		$rst = $this->code_validate($data['username'],$data['checkcode']);
		if($rst['Flag'] != 100){
			return $rst;
		}
		$userinfo = $this->userExist($data['username']);
		if($userinfo['Flag'] != 100){
			return array('Flag'=>101,'FlagString'=>'用户不存在');
		}
		//获取是否绑定邮箱，手机
		$userinfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$userinfo['Uid'],'Status'=>1)));
		return array('Flag'=>100,'FlagString'=>'Ok','UserInfo'=>$userinfo);
	}

	public final function resetPassword($user,$data){
		if(empty($data['new_password'])){
			return array('Flag'=>101,'FlagString'=>'新密码不能为空');
		}
		if($data['new_password'] != $data['re_password']){
			return array('Flag'=>101,'FlagString'=>'两次密码输入不一致');
		}
		$param = array('extparam' => array('Tag'=>'ResetPassword','User'=>$user,'OldPass'=>md5($data['password']),'Pass'=>md5($data['new_password'])));
		$array = httpPOST(SSO_API_PATH, $param, true);
		return $array;
	}

	//注册
	public function register($data){
		//参数验证
		$rst = $this->checkParam($data);
		if($rst['Flag'] != 100){
			return $rst;
		}
		return $this->reg($data);
	}

	protected function reg($data){
		//用户是否存在
		$rst = $this->userExist($data['username']);
		if($rst['Flag'] == 100){
			return array('Flag'=>102,'FlagString'=>'用户已存在');
		}

		//注册
		$password = md5($data['password']);
		$res = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'RegPassport','User'=>$data['username'],'Pass'=>$password,'Platform'=>$data['type'])));
		if($res['Flag'] != 100){
			return $res;
		}
		//设置默认头像
		$bytes = file_get_contents(dirname(__ROOT__).'/pic/images/uin_man.jpg');
		if(!empty($bytes)){
			$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$res['Uin']);
			$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
		}

		//设置登录
		$loginInfo = array('username'=>$data['username'],'password'=>$data['password'],'group_id'=>$data['group_id'],'remember'=>0);
		$login = $this->login($loginInfo);
		//Array ( [Flag] => 100 [FlagString] => 登录成功 [Uid] => 20229369 [Login] => b@b.com [Created] => 1383803961 [Openid] => b@b.com [GroupId] => 5711238 [Nick] => 20229369 [Gender] => 0 [Uin] => 20229369 [Email] => b@b.com [Phone] => [UserName] => [QQCount] => [Token] => 563B0AE0E202D95C142E9098211231A3 [SessionKey] => 563B0AE0E202D95C142E9098211231A3 )
		return array('Flag'=>100,'FlagString'=>'注册成功','Uin'=>$res['Uin'],'uin'=>$res['Uin'],'nick'=>$login['Uin'],'group_id'=>$login['GroupId'],'username'=>$login['Login']);
	}

	//登录
	public function login($data){
		$password = md5($data['password']);
		return httpPOST(SSO_API_PATH,array('param'=>array("SessionKey"=>$password,"Uin"=>$data['username'],"GroupId"=>$data['group_id']),'extparam'=>array('Tag'=>'UserLogin','Remember'=>$data['remember'])));
	}

	//用户是否存在
	public function userExist($user){
		return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','UserName'=>$user,'Status'=>1)));
	}

	//参数是否正确
	public function checkParam($data){
		$rst = $this->user_validate($data['username']);
		if($rst['Flag'] != 100){
			return $rst;
		}
		if(!$this->pwd_validate($data['password'])){
			return array('Flag'=>101,'FlagString'=>'密码不正确');
		}
		if($data['password'] != $data['repassword']){
			return array('Flag'=>101,'FlagString'=>'两次密码输入不一致');
		}
		$rst = $this->code_validate($data['username'],$data['checkcode']);
		if($rst['Flag'] != 100){
			return $rst;
		}
		return array('Flag'=>100,'FlagString'=>'OK');
	}

	public function newpassIndex($data){
		$username = $this->_url_authdecode($data);
		if(empty($username)) {
			return array('Flag'=>101,'FlagString'=>'该链接无效或已过期');
		}
		return array('Flag'=>100,'FlagString'=>'OK','Username'=>$username);
	}

	public function getBackPassword($data){
		$info = $this->newpassIndex($data['Info']);
		if($info['Flag'] != 100){
			return $info;
		}

		$data['password'] = trim($data['password']);
		$data['repassword'] = trim($data['repassword']);
		if(empty($data['password']) || $data['password'] != $data['repassword']){
			return array('Flag'=>102,'FlagString'=>'密码错误');
		}
		$data['password'] = md5($data['password']);

		$userinfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','UserName'=>$info['Username'],'Status'=>1)));
		if($userinfo['Flag'] != 100){
			return array('Flag'=>103,'FlagString'=>'用户不存在或被冻结');
		}

		$rst = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'EditPassword','Uid'=>$userinfo['Uid'],'Pass'=>$data['password'])));
		if($rst['Flag'] != 100){
			return $rst;
		}
		return array('Flag'=>100,'FlagString'=>'找回密码成功，请登录');
	}

	protected function user_validate($user){}

	protected function pwd_validate($pwd){
		$len = strlen($pwd);
		if($len < 6 || $len > 20){
			return false;
		}
		return true;
	}

	protected function code_validate($key,$verify_code){
		if(empty($verify_code)){
			return array('Flag'=>101,'FlagString'=>'验证码不能为空');
		}
		if($this->get_code($key) != strtolower($verify_code)){
			return array('Flag'=>102,'FlagString'=>'验证码错误');
		}
		return array('Flag'=>100,'FlagString'=>'OK');
	}

	protected function get_code($key){
		if(empty($_SESSION)){
			session_start();
		}
		return strtolower($_SESSION['captcha']);
	}

	protected function _url_authencode($data){
		$data = json_encode($data);
		return rawurlencode(uc_authcode($data,'ENCODE',$this->authcode_link_key,$this->expire));
	}

	protected function _url_authdecode($code){
		$code = rawurldecode($code);
		return json_decode(uc_authcode($code,'DECODE',$this->authcode_link_key),true);
	}
}