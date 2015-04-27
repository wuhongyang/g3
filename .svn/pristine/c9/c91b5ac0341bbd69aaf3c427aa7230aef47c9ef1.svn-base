<?php

require_once 'passport.class.php';

class EmailPassport extends Passport{

	private $_expire = 3600;

	//邮箱的注册就是发一封邮件，，等待激活
	public function register($data){
		//重发时不需要参数验证
		if(!isset($data['resend'])){
			//参数验证
			$rst = $this->checkParam($data);
			if($rst['Flag'] != 100){
				return $rst;
			}
		}
		
		//用户是否存在
		$rst = $this->userExist($data['username']);
		if($rst['Flag'] == 100){
			return $rst;
		}
		$sendRst = $this->_sendRegMail($data);
		if(!$sendRst){
			return array('Flag'=>102,'FlagString'=>'发送失败');
		}
		return array('Flag'=>100,'FlagString'=>'发送成功');
	}

	public function active($data){
		$data = $this->_url_authdecode($data);
		if(empty($data)){
			return array('Flag'=>101,'FlagString'=>'链接无效或已过期,请重新注册!');
		}
		$rst = $this->reg($data);
		if($rst['Flag'] != 100) {
			return $rst;
		}
		
		//$rst['username'] = $data['username'];
		return $rst;
	}

	public function getPasswdByEmail($username){
		$rst = $this->userExist($username);
		if($rst['Flag'] != 100){
			return $rst;
		}
		$userinfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$rst['Uid'],'Status'=>1)));
		$sendRset = $this->sendResetMail(array('email'=>$userinfo['Email']));
		if( ! $sendRset) {
			return array('Flag'=>102,'FlagString'=>'邮件发送失败','Email'=>$userinfo['Email']);
		}
		return array('Flag'=>100,'FlagString'=>'OK','Email'=>$userinfo['Email']);
	}

	protected function user_validate($email){
		if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
			return array('Flag'=>101,'FlagString'=>'邮箱格式有误');
		}
		return array('Flag'=>100,'FlagString'=>'OK');
	}

	private function _sendRegMail(array $data){
		$code = $this->_url_authencode($data);
		$url  = $this->_base_url.'?user_email&rc='.$code;
		$msg  = '<h1 style="font-weight:bold;font-size:16px;">亲爱的'.$this->group_info['group_name'].'用户,请点击下方链接，完成邮箱激活。</h1>';
		$msg .= '<p>请点击 <a href="'.$url.'">确认注册</a> 链接完成注册</p>';
		$msg .= '<p>如果“确认注册”链接无法打开，请复制以下地址到您的浏览器打开</p><p>'.$url.'</p>';
		$msg .= '<p>请尽快确认注册，'.($this->_expire/3600).'小时内有效。</p>';
		$msg .= '<p>该邮件由系统发出，请勿回复，感谢您注册'.$this->group_info['group_name'].'通行证！</p>';
		return sendMail($data['username'],$this->group_info['group_name'].'通行证-注册确认邮件',$msg);
	}

	private function sendResetMail($data){
		$code = $this->_url_authencode($data['email']);
		$url  = $this->_base_url."?account&newpass&wd={$code}";
		$msg .= '<p>您申请的找回密码链接为：</p><p><a href="'.$url.'">'.$url.'</a></p>';
		$msg .= '<p>请尽快完成操作，该链接'.($this->_expire/3600).'小时内有效</p>';
		return sendMail($data['email'],$this->group_info['group_name'].'通行证-找回密码邮件',$msg);
	}
}