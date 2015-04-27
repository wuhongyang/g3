<?php
/**
* 邮箱通行证
* 邮箱帐号注册,绑定,找回
* @author dl
* @copyright (c) 杭州奥点科技有限公司
*/
class user_email
{

	/**
	* 激活链接有效时间
	* @parame floot $expire 秒
	*/
	private $expire = 3600;
	protected $_type = 1;

	function __construct(){
		$this->g_data = domain::main()->GroupData();
		$this->group_id = (int)$this->g_data['groupid'];
		include_once 'models/username.php';
		$this->username = new username();
		$this->login_user = $this->username->getLogin();
	}

	//用户是否存在
	function countmail(){
		$user = $this->username->getUser($_GET['countmail']);
		if($user['Flag'] != 100) exit('0');exit('1');
	}

	//注册页面
	function index(){
		if(!empty($_POST)){
			if(empty($_POST['username'])) alertMsg('邮箱地址不能为空');
			if(strlen($_POST['password']) < 6) alertMsg('密码不能小于6位');
			if($_POST['read'] != 1) alertMsg('请勾选《用户协议》');
			if(empty($_POST['checkcode']) || strtolower($_POST['checkcode']) != $this->getSession('captcha')) alertMsg('验证码不正确');
			$gender = isset($_POST['gender']) ? intval($_POST['gender']) : 1;
			$group_id = $this->group_id;
			$data = array('username'=>$_POST['username'],'password'=>$_POST['password'],'nick'=>$_POST['nick'],'gender'=>$gender,'group_id'=>$group_id);
			if($group_id > 0){
				//得到站名称
				$param=array(
					'extparam'=>array('Tag'=>'GetGroupInfo','Id'=>$group_id),
					'param'=>array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101,'Desc'=>'获取站信息')
				);
				$groupInfo=request($param);
				if($groupInfo['Flag'] == 100){
					$data['group_name'] = $groupInfo['Result']['name'];
				}else{
					alertMsg('站不存在');
				}
			}
			$sendrst = $this->sendRegMail($data);
			if($_POST['resend']==1){
				echo $sendrst? '1' : '0';exit;
			}
			if($sendrst){
				alertMsg('','/passport/?user_email&active='.urlencode(json_encode($_POST)));
			}else{
				alertMsg('发送失败');
			}
		}
		include_once THEMES_ROOT.'/email-reg.html';
	}
	
	function checkcode(){
		if(empty($_POST['checkcode']) || strtolower($_POST['checkcode']) != $this->getSession('captcha')){
			echo '0';
		}else{
			echo '1';
		}
	}

	public function active(){
		$data = json_decode(urldecode($_GET['active']),true);
		include_once THEMES_ROOT.'/email-active.html';
	}

	//注册激活
	function rc(){
		$data = $this->username->url_authdecode($_GET['rc']);
		if(empty($data)) alertMsg('链接无效或已过期,请重新注册!',BASE_URL);
		$pass = md5($data['password']);
		$rst = $this->username->reg(array('user'=>$data['username'],'pass'=>$pass,'type'=>$this->_type));
		if($rst['Flag'] != 100) alertMsg($rst['FlagString'],'/passport/?account&login');
		$this->username->login($data['username'],$pass,0); //设置登录
		$info = array('username'=>$data['username'],'uin'=>$rst['Uin'],'nick'=>$rst['Nick'],'group_id'=>$rst['GroupId']);
		alertMsg("","/passport/?user_email&success&info=".urlencode(json_encode($info)));
	}

	public function success(){
		$userInfo = json_decode(urldecode($_GET['info']), true);
		include_once THEMES_ROOT.'/email-reg-finish.html';
	}

//-------------------------------------邮箱绑定---------------------------------------

	//绑定页面
	function bind(){
		if($this->login_user['Flag'] !== 100) alertMsg('您还未登录，不能进行此操作','?account');
		if(!empty($_POST)){
			if(getUserType($_POST['email']) !== 'email') alertMsg('邮箱帐号格式不正确');
			$user = empty($this->login_user['Phone'])? $this->login_user['Email'] : $this->login_user['Phone'];
			$data = array('email'=>$_POST['email'],'username'=>$user);
			$sendrst = $this->sendBindMail($data);
			if( ! $sendrst) alertMsg('邮件发送失败');
			include_once THEMES_ROOT.'/email-bind2.html';
			return true;
		}
		include_once THEMES_ROOT.'/email-bind.html';
	}

	//绑定激活
	function bindcode(){
		$data = $this->username->url_authdecode($_GET['bindcode']);
		if(empty($data)) alertMsg('链接无效或已过期,请重新绑定!');
		$rst = $this->username->bindUser($data['username'],$data['email']);
		if($rst['Flag'] != 100) alertMsg($rst['FlagString'],'?account');
		include_once THEMES_ROOT.'/email-bind-finish.html';
	}

//----------------------------找回密码-------------------------------

	//忘记密码
	function getpwd(){
		if(!empty($_POST)){
			if( ! $this->username->getUser($_POST['email'])) alertMsg('用户不存在');
			$sendRset = $this->sendResetMail($_POST['email']);
			if( ! $sendRset) alertMsg('邮件发送失败！');
			include_once THEMES_ROOT.'/email-getpwd2.html';
			return true;
		}
		if(empty($_POST['email'])) alertMsg('您未绑定邮箱帐号');
		include_once THEMES_ROOT.'/email-getpwd.html';
	}

	//验证链接
	function setcode(){
		$email = $this->username->url_authdecode($_GET['setcode']);
		if(empty($email)) alertMsg('该链接无效或已过期',BASE_URL);
		if(!empty($_POST)){
			if( ! $this->username->getUser($email)) alertMsg('用户不存在');
			$rst = $this->username->resetPassword($email,$_POST['password']);
			if($rst['Flag'] != 100) alertMsg($rst['FlagString']);
			include_once THEMES_ROOT.'/getpwd-finish.html';
			return true;
		}
		include_once THEMES_ROOT.'/email-getpwd3.html';
	}

//------------------------------私有方法----------------------------------

	private function sendRegMail(array $data){
		$code = $this->username->url_authencode($data);
		$url  = BASE_URL.'?user_email&rc='.$code;
		$vv = $data['group_id'] <= 0 ? 'VV酷' : $data['group_name'];
		$msg  = '<h1 style="font-weight:bold;font-size:16px;">亲爱的'.$vv.'用户,请点击下方链接，完成邮箱激活。</h1>';
		$msg .= '<p>请点击 <a href="'.$url.'">确认注册</a> 链接完成注册</p>';
		$msg .= '<p>如果“确认注册”链接无法打开，请复制以下地址到您的浏览器打开</p><p>'.$url.'</p>';
		$msg .= '<p>请尽快确认注册，'.($this->expire/3600).'小时内有效。</p>';
		$msg .= '<p>该邮件由系统发出，请勿回复，感谢您注册'.$vv.'通行证！</p>';
		return sendMail($data['username'],$vv.'通行证-注册确认邮件',$msg);
	}
	
	private function sendBindMail(array $data){
		$code = $this->username->url_authencode($data);
		$vv = ($this->group_id > 0) ? $this->g_data['name'] : 'VV酷';
		$url  = BASE_URL.'?user_email&bindcode='.$code;
		$msg .= '<p>您申请的绑定邮箱链接为：</p><p><a href="'.$url.'">'.$url.'</a></p>';
		$msg .= '<p>请尽快完成操作，该链接'.($this->expire/3600).'小时内有效</p>';
		return sendMail($_POST['email'],$vv.'通行证-绑定邮箱邮件',$msg);
	}

	private function sendResetMail($data){
		$code = $this->username->url_authencode($data);
		$vv = ($this->group_id > 0) ? $this->g_data['name'] : 'VV酷';
		$url  = BASE_URL."?account&newpass&wd={$code}";
		$msg .= '<p>您申请的找回密码链接为：</p><p><a href="'.$url.'">'.$url.'</a></p>';
		$msg .= '<p>请尽快完成操作，该链接'.($this->expire/3600).'小时内有效</p>';
		return sendMail($_POST['email'],$vv.'通行证-找回密码邮件',$msg);
	}
	
	private function getSession($key){
		if(empty($_SESSION)){
			session_start();
		}
		return $_SESSION[$key];
	}
}