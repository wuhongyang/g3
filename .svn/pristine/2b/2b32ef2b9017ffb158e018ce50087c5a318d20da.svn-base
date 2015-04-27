<?php
class username
{
	protected $table = 'username';
	private $authcode_link_key = 'VvKu4aTtBc0De';
	protected $expiry;

	function __construct(){
		$this->expiry = 86400*7;
	}

	//用户登录
	function login($user,$password,$remember=0,$groupid=0){
		$rst = httpPOST(SSO_API_PATH,array('param'=>array("SessionKey"=>$password,"Uin"=>$user,"GroupId"=>$groupid),'extparam'=>array('Tag'=>'UserLogin','Remember'=>$remember)));
		/*
		$param = array(
				'param'=>array("BigCaseId"=>10001,"CaseId"=>10001,"ParentId"=>10001,"ChildId"=>101,'GroupId'=>$groupid,"SessionKey"=>$password,"Uin"=>$user,"Desc"=>$desc),
				'extparam'=>array('Tag'=>'UserLogin','Remember'=>$remember)
		);
		$rst = request($param);*/
		return $rst;
	}

	//获取登录
	function getLogin(){
		$user = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
		return $user;
	}

	//设置登录
	function setLogin($openid,$userinfo){
		$userinfo = array_merge($this->getLogin(),$userinfo);
		if($userinfo['Flag'] == 100){
			return httpPOST(SSO_API_PATH,array('extparam'=>'SetStorage','Usrinfo'=>$userinfo));
		}else{
			return $userinfo;
		}
	}

	//退出登录
	function logout(){
		return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'UserLogOut')));
	}

	//绑定帐号
	function bindUser($user,$bind){
		if( ! $userType = getUserType($user)) return array('Flag'=>101,'FlagString'=>'用户名类型错误');
		if( ! $bindType = getUserType($bind)) return array('Flag'=>102,'FlagString'=>'绑定的用户名类型错误');
		return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'BindUser','User'=>$user,'Bind'=>$bind)));
	}

	//注册帐号
	function reg($data/*$user,$pass,$nick='',$gender=1,$type*/){
		/*
		if( ! $usertype = getUserType($user)) return array('Flag'=>101,'FlagString'=>'用户名类型错误');
		if($type=="phone"){
			$desc 	 = "手机注册"; 
			$childid = 101;
		}elseif($type=="email"){
			$desc    = "邮箱注册";
			$childid = 102;
		}else{
			exit("need type");
		}
		
		$param = array(
			'param'=>array("BigCaseId"=>10004,"CaseId"=>10013,"ParentId"=>10128,"ChildId"=>$childid,"DoingWeight"=>1,"Desc"=>$desc,"SessionKey"=>$pass,"Uin"=>$user),
			'extparam'=>array('Tag'=>'RegPassport','User'=>$user,'Pass'=>$pass,'Nick'=>$nick,'Gender'=>$gender)
		);*/
		$gender = ($data['gender'] == 2) ? 2 : 1;
		$res = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'RegPassport','User'=>$data['user'],'Pass'=>$data['pass'],'Platform'=>$data['type'],'OpenId'=>$data['openid'])));
		//$res = request($param);
		if($res['Flag'] == 100){ //保存默认头像
			$face = array(1=>'../pic/images/uin_man.jpg',2=>'../pic/images/uin_woman.jpg');
			$bytes = file_get_contents($face[$gender]);
			if(!empty($bytes)){
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$res['Uin']);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
			}
		}
		return $res;
	}

	//重置密码
	function resetPassword($user,$oldpass,$pass){
		if( ! $usertype = getUserType($user)) return array('Flag'=>101,'FlagString'=>'用户名类型错误');
		return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'ResetPassword','User'=>$user,'OldPass'=>$oldpass,'Pass'=>$pass)));
	}

	//修改密码
	function editPassword($user,$pass){
		if( ! $usertype = getUserType($user)) return array('Flag'=>101,'FlagString'=>'用户名类型错误');
		$userinfo = $this->getUser($user);
		if($userinfo['Flag'] == 100){
			return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'EditPassword','Uid'=>$userinfo['Uid'],'Pass'=>$pass)));
		}else{
			return $userinfo;
		}
	}

	//用户是否存在
	function getUser($user){
		return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','UserName'=>$user,'Status'=>1)));
	}
	
	function getAllUser($user){
		return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','UserName'=>$user)));
	}
	
	function url_authencode($data){
		$data = json_encode($data);
		return rawurlencode(uc_authcode($data,'ENCODE',$this->authcode_link_key,$this->expire));
	}
	
	function url_authdecode($code){
		$code = rawurldecode($code);
		return json_decode(uc_authcode($code,'DECODE',$this->authcode_link_key),true);
	}
}
