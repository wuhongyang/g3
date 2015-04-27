<?php
/**
* 手机通行证
* 手机帐号注册,绑定,找回
* @author dl
* @copyright (c) 杭州奥点科技有限公司
*/
class user_phone
{
	/**
	* 激活码有效时间
	* @parame floot $expire 秒
	*/
	protected $expire = 3600;
	protected $_type = 3;

	function __construct(){
		$GroupData = domain::main()->GroupData();
		$this->group_id = (int)$GroupData['groupid'];
		session_start();
		include_once 'models/username.php';
		$this->username = new username();
		$this->login_user = $this->username->getLogin();
	}
//---------------------------手机注册--------------------------------

	//注册页面
	function index(){
		//$group_id = intval($_GET['group_id']);
		include_once THEMES_ROOT.'/phone-reg.html';
	}

	//发送验证码
	function sendcode(){
		$group_id = $this->group_id;
		$group_name = 'VV酷';
		if($group_id > 0){
			//得到站名称
			$param=array(
				'extparam'=>array('Tag'=>'GetGroupInfo','Id'=>$group_id),
				'param'=>array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101,'Desc'=>'获取站信息')
			);
			$groupInfo=request($param);
			if($groupInfo['Flag'] == 100){
				$group_name = $groupInfo['Result']['name'];
			}else{
				alertMsg('站不存在');
			}
		}
		$type = array_keys($_POST);
		$type = $type[0];
		$phone = $_POST[$type];
		if(getUserType($phone) != 'phone') { //号码不正确
			exit(json_encode(array('Flag'=>101,'FlagString'=>'号码不正确')));
		}
		$user = $this->username->getAllUser($phone);
		if($type == 'user'){
			if($user['Flag'] != 100) { //用户不存在
				exit(json_encode(array('Flag'=>102,'FlagString'=>'用户不存在')));
			}
		}elseif($type == 'nouser'){
			if($user['Flag'] == 100) { //用户已存在
				exit(json_encode(array('Flag'=>103,'FlagString'=>'用户已存在')));
			}
		}
		$phone_key = 'phone_'.$_POST[$type];
		$code = $_SESSION[$phone_key];
		if(empty($code)){
			$code   = rand(100000,999999);
			$_SESSION[$phone_key] = $code;
		}
		$module = !empty($_POST['module']) ? $_POST['module'] : '手机注册';
		$msg = "您的校验码是：{$code}，有效时间".($this->expire/3600).'小时，如非本人操 作，请不要将此校验码告诉他人【'.$group_name.'通行证】';
		$rst = sendSMS($phone,$msg,$module);
		if($rst['Flag'] == 100){
			$_SESSION['uniqueId'] = $rst['UniqueId'];
		}
		exit(json_encode($rst));
		/*
		if($rst == 100){
			exit('1');
		}elseif($rst == 110){
			exit('3');
		}else{
			exit('0');
		}*/
	}

	//注册提交
	function regpost(){
		if(empty($_POST['username'])) alertMsg('手机号码不能为空');
		//if(empty($_POST['nick'])) alertMsg('昵称不能为空');
		if(strlen($_POST['password']) < 6) alertMsg('密码不能小于6位');
		if($_POST['read'] != 1) alertMsg('请勾选《用户协议》');
		$data = $_POST;
		$phone = 'phone_'.$data['username'];
		$code = $_SESSION[$phone];
		//设置下一步标识
		$uniqueId = !empty($data['uniqueid']) ? $data['uniqueid'] : $_SESSION['uniqueId'];
		unset($_SESSION['uniqueId']);
		updateNext($uniqueId);
		
		if(empty($code) || $data['msgcode'] != $code) alertMsg('短信验证码错误或已过期');
		$data['gender'] = isset($data['gender']) ? $data['gender'] : 1;
		$data['password'] = md5($data['password']);
		$rst = $this->username->reg(array('user'=>$data['username'],'pass'=>$data['password'],'type'=>$this->_type));
		if($rst['Flag'] != 100){
			showMsg($rst['FlagString']);
		}
		$this->username->login($data['username'],$data['password'],0); //设置登录
		//include_once THEMES_ROOT.'/phone-reg-finish.html';
		$info = array('username'=>$data['username'],'uin'=>$rst['Uin'],'nick'=>$rst['Nick'],'group_id'=>intval($rst['GroupId']));
		alertMsg("","/passport/?user_phone&success&info=".urlencode(json_encode($info)));
	}

	public function success(){
		$userInfo = json_decode(urldecode($_GET['info']), true);
		include_once THEMES_ROOT.'/phone-reg-success.html';
	}

//------------------------------获取密码------------------------------

	function getpwd(){
		if(!empty($_POST['msgcode'])){
			//设置下一步标识
			$uniqueId = !empty($_POST['uniqueid']) ? $_POST['uniqueid'] : $_SESSION['uniqueId'];
			unset($_SESSION['uniqueId']);
			updateNext($uniqueId);
			
			$phone = 'phone_'.$_POST['username'];
			if( ! $code = $_SESSION[$phone]) alertMsg('短信码无效或已过期');
			if($code != $_POST['msgcode']) alertMsg('短信码不正确');
			unset($_SESSION[$phone]);
			$wd = $this->username->url_authencode($_POST['username']);
			header("Location:?account&newpass&wd={$wd}");
		}
		if(empty($_POST['phone'])) alertMsg('您未绑定手机帐号');
		include_once THEMES_ROOT.'/phone-getpwd.html';
	}

//-------------------------------绑定手机----------------------------------

	function bind(){
		if($this->login_user['Flag'] !== 100) alertMsg('您还未登录，不能进行此操作','?account');
		if(!empty($_POST)){
			if($this->login_user['Flag'] !== 100) alertMsg('您还未登录，不能进行此操作','?account');
			$phone = 'phone_'.$_POST['phone'];
			if( ! $code = $_SESSION[$phone]) alertMsg('短信码无效或已过期');
			if($code != $_POST['msgcode']) alertMsg('短信码不正确');
			$rst = $this->username->bindUser($this->login_user['Email'],$_POST['phone']);
			if($rst['Flag'] != 100) alertMsg($rst['FlagString']);
			unset($_SESSION[$phone]);
			include_once THEMES_ROOT.'/phone-bind-finish.html';
			return true;
		}
		include_once THEMES_ROOT.'/phone-bind.html';
	}

}