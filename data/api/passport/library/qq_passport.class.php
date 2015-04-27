<?php

require_once 'passport.class.php';

class QqPassport extends Passport{
	public function register($data){
		if(empty($data['openid'])){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$res = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'OpenidReg','Data'=>array('openid'=>$data['openid'],'nick'=>$data['nick']))));
		if($res['Flag'] != 100){
			return $res;
		}

		//设置默认头像
		$bytes = socket_request(urldecode($data['picurl']));
		if(!empty($bytes)){
			$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$res['Uin']);
			$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
		}

		//设置登录
		$loginInfo = array('openid'=>$data['openid'],'remember'=>0);
		$login = $this->login($loginInfo);
		return array('Flag'=>100,'FlagString'=>'注册成功','Uin'=>$res['Uin'],'uin'=>$res['Uin'],'nick'=>$data['nick'],'group_id'=>$login['GroupId']);
	}

	public function login($data){
		return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'OpenidLogin','Userinfo'=>$data)));
	}
}