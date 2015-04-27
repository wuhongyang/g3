<?php
/**
* 邮箱通行证
* 邮箱帐号注册,绑定,找回
* @author dl
* @copyright (c) 杭州奥点科技有限公司
*/
include_once 'abstract_reg.php';
class qq extends Reg
{

	protected $_type = 4;

	//注册页面
	public function index(){
		$info = json_decode(urldecode(base64_decode($_GET['info'])),true);
		include_once THEMES_ROOT.'/qq-reg.html';
	}

	public function success(){
		$userInfo = json_decode(urldecode($_GET['info']), true);
		include_once THEMES_ROOT.'/qq-reg-finish.html';
	}

	public function qqsuccess(){
		$userInfo = json_decode(urldecode($_GET['info']), true);
		include_once THEMES_ROOT.'/qq-bind-finish.html';
	}

	public function bind(){
		//获取类型
		$userinfo = $this->username->getUser($_POST['user']);
		if($userinfo['Flag'] != 100){
			alertMsg('账号错误');
		}
		$platform = $userinfo['Platform'];

		//绑定
		$data = array('username' => $_POST['user'], 'password' => $_POST['pass'], 'type' => $platform, 'openid' => $_POST['openid']);
		$res = $this->reg($data);
		if($res['Flag'] != 100){
			alertMsg($res['FlagString']);
		}

		//登录
		$this->login($data);

		//跳转成功页面
		$data['uin'] = $res['Uin'];
		//$this->redirect($data);
		alertMsg("",'/passport/?'.get_class($this).'&qqsuccess&info='.urlencode(json_encode($data)));
	}

	protected function redirect($data){
		alertMsg("",'/passport/?'.get_class($this).'&success&info='.urlencode(json_encode($data)));
	}

	protected function reg($data){
		$platform = $data['type'] > 0 ? $data['type'] : 2;
		return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'OpenidReg','User'=>$data['username'],'Pass'=>md5($data['password']),'Platform'=>$platform,'OpenId'=>$data['openid'],'PicUrl'=>$data['picurl'])));
	}

	protected function login($data){
		httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'OpenidLogin','Userinfo'=>array('openid'=>$data['openid'],'picurl'=>$data['picurl']))));
	}
}