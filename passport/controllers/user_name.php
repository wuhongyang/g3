<?php
/**
* 邮箱通行证
* 邮箱帐号注册,绑定,找回
* @author dl
* @copyright (c) 杭州奥点科技有限公司
*/
include_once 'abstract_reg.php';
class user_name extends Reg
{

	protected $_type = 2;

	//注册页面
	public function index(){
		include_once THEMES_ROOT.'/username-reg.html';
	}

	public function success(){
		$userInfo = json_decode(urldecode($_GET['info']), true);
		include_once THEMES_ROOT.'/user-reg-finish.html';
	}
}