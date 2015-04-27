<?php

/**
 *   用户安全中心管理
 *   文件: usersafe.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class usersafe
{
	private $authcode_link_key = 'VvKu4aTtBc0De';
	private $expire = '3600';
	//构造函数
	public function __construct() 
	{
		$GroupData = domain::main()->GroupData();
		$this->vv = $GroupData['groupid'] > 0 ? $GroupData['name'] : 'VV酷';
		$this->group_id = $GroupData['groupid'];
		$this->db = domain::main()->GroupDBConn('mysql');
		$this->mem = cache::connect(config('cache','memcache'));
	}
	
	/*
	 *   验证用户邮箱是否存在
	 *   @param	array	$message	邮箱
	 *   @return	array	$return		判读结果
	 */
	 /*
	public function yzEmail($message)
	{
		$email = $message['email'];
		$table = DB_NAME_SSO.'.username';
		$sql = "SELECT * FROM ".$table." WHERE `email` = '$email'";
		$return = $this->db->get_row($sql);
		if( !empty($return) )
		{
			$return = array(
				'Flag'	=> '100',
				'FlagString' => '邮箱已存在',
			);
		}
		else
		{
			$return = array(
				'Flag'	=> '101',
				'FlagString' => '邮箱不存在',
			);
		}
		return $return;
	}*/
	
	/*
	 *   判读手机号码是否存在
	 *   @param	array	$message	用户手机号码
	 *   @return	array	$return		判读结果
	 */
	 /*
	public function yzPhone($message)
	{
		$phone = $message['phone'];
		$table = DB_NAME_IM.'.username';
		$sql = "SELECT * FROM ".$table." WHERE `phone` = '$phone'";
		$return = $this->db->get_row($sql);
		if( !empty($return) )
		{
			$return = array(
				'Flag'	=> '100',
				'FlagString' => '手机号码已存在',
			);
		}
		else
		{
			$return = array(
				'Flag'	=> '101',
				'FlagString' => '手机号码不存在',
			);
		}
		return $return;
	}*/

	/*
	 *   修改绑定邮箱
	 *   @param	array	$message	用户邮箱地址
	 *   @return	array	$return		发送结果
	 */
	public function changeEmail($message)
	{
		$data = array('user'=> $message['user']['Email']);
		$codes = $this->url_authencode($data);
		$url = BASE_URL_EMAIL.'?module=email&bindcode='.$codes;
		$msg = '点击此链接进行邮箱绑定 <a href="'.$url.'">'.$url.'</a>';
		$return = $this->sendMail($message['user']['Email'],$this->vv.'通行证-绑定邮箱邮件',$msg);
		return $return;
	}

	/*
	 *   判断用户是否可以更改手机号码
	 *   @param	array	$message	手机验证码
	 *   @return	array	$return		判断结果
	 */
	public function changePhone($message)
	{
	
		$phone = $message['phone'];
		$code = $this->get($phone);
		$email = $message['email'];
		if( empty($message['msgcode']) )
		{
			$return = array(
				'Flag'	=> '102',
				'FlagString' => '请输入验证码',
			);
			return $return;
		}
		if( $code['val'] == $message['msgcode'] )
		{
			$data = array('user'=> $email);
			$codes = $this->url_authencode($data);
			$this->del($phone);
			$return = array(
				'Flag'	=> '100',
				'FlagString' => 'success',
				'code' => $codes,
			);
		}
		else
		{
			$return = array(
				'Flag'	=> '101',
				'FlagString' => '手机验证码错误',
			);
		}
		return $return;
	}
	
	/*
	 *   发送短信
	 *   @param	array	$message	
	 *   @return 	array	$return		发送结果
	 */
	public function sendPhoneCode($message)
	{
		$phone = $message['phone'];
		$group_id = $this->group_id;
		if($this->getUserType($phone) != 'phone') { //号码不正确
			return array('Flag'=>101,'FlagString'=>'号码不正确');
		}
		if($this->isBinded($phone,$message['user']['Uin']) > 0){
			return array('Flag'=>'102','FlagString'=>'手机已被他人注册，请更换手机号');
		}

		$code = $this->get($phone,$this->expire);
		$msg = "您的校验码是：{$code['val']}，有效时间".($this->expire/3600).'小时，如非本人操 作，请不要将此校验码告诉他人【'.$this->vv.'通行证】';
		if(empty($code)){
			$code   = rand(100000,999999);
			$this->set($phone,$code);
			$msg = "您的校验码是：{$code}，有效时间".($this->expire/3600).'小时，如非本人操 作，请不要将此校验码告诉他人【'.$this->vv.'通行证】';
		}
		$rst = sendSMS($phone,$msg,$message['module']);
		return $rst;
	}

	private function isBinded($login,$uin){
		$sql = "SELECT id FROM ".DB_NAME_IM.".new_username WHERE group_id={$this->group_id} AND login='{$login}' AND uin<>{$uin}";
		return $this->db->get_var($sql);
	}
	/*
	 *   绑定用户手机号码
	 *   @param	array	$message	
	 *   @return	array	$result		$result
	 */
	public function bindPhone($message)
	{
		/*
		$user = $message['user'];
		$data = $this->url_authdecode($_GET['bindcode']);
		$con = $user['Email']?$user['Email']:$message['user']['Phone'];
		$one = $this->getUser($con);
		$exist_phone = $one['phone']?true:false;
	
		if(empty($data) && $exist_phone && $one['email']){
			return array('Flag'=> 101,'FlagString' => '此链接无效！');
		}
		$phone = $message['phone'];
		if( ! $code = $this->get($phone,$this->expire))
		{
			$return = array(
				'Flag'	=> '101',
				'FlagString' => '短信已过期',
			);
			return $return;
		} 
		if($code['val'] != $message['msgcode'])
		{
			$return = array(
				'Flag'	=> '102',
				'FlagString' => '验证码不正确',
			);
			return $return;
		}*/
		$param = array(
			'extparam' => array(
					'Tag'	=>  'BindPhone',
					'Phone'	=>  $message['phone'],
					'Uid'	=>  $message['user']['Uin'],
					'BindCode'=>  $message['msgcode']
				)
		);
		$rst = httpPOST(SSO_API_PATH, $param, true);
		if($rst['Flag'] != 100)
		{
			$return = array(
				'Flag' => '103',
				'FlagString' => $rst['FlagString'],
			);
			return $return;
		}
		/*
		$result = $this->del($phone);
		//用户退出
		$param = array(
			'extparam' => array(
					'Tag'	=>  'UserLogOut',
				)
		);
		httpPOST(SSO_API_PATH, $param, true);*/
		$return = array(
			'Flag'	=> '100',
			'FlagString' => 'success',
		);
		return $return;
	}

	function set($key,$val){
		$table = DB_NAME_IM.'.user_tmp';
		if( ! is_string($val) && ! is_numeric($val)) $val = serialize($val);
		$uptime = time() + $this->expire;
		$sql = "SELECT count(*) FROM {$table} WHERE `get`='{$key}' AND group_id='{$this->group_id}' LIMIT 1";
		$rst = intval($this->db->get_var($sql));
		if($rst){
			$sql = "UPDATE {$table} SET `val`='{$val}',uptime={$uptime} WHERE `get`='{$key}' AND `group_id`={$this->group_id}";
		}else{
			$sql = "INSERT INTO {$table} (`get`,`val`,`uptime`,`group_id`)VALUES('{$key}','{$val}',{$uptime},{$this->group_id})";
		}
		return $this->db->query($sql);
	}

	function get($key,$expire=0){
		$table = DB_NAME_IM.'.user_tmp';
		$rst = $this->db->get_results("SELECT `val`,`uptime` FROM {$table} WHERE `get`='{$key}' AND `group_id`='{$this->group_id}' ORDER BY `uptime`",'ASSOC');
		foreach($rst as $one){
			if($expire > 0 && (time()-$one['uptime']) > $expire){
				$this->del($key);
			}else{
				return $one;
			}
		}
		return false;
		
		/*
		if(empty($rst)) return false;
		
		$val = unserialize($rst['val']);
		if($val) return $val;
		return $rst['val'];
		*/
	}

	function del($key){
		$table = DB_NAME_IM.'.user_tmp';
		return $this->db->query("DELETE FROM {$table} WHERE `get`='{$key}' AND `group_id`={$this->group_id}");
	}
	/*
	 *   用户密码修改
	 *   @param	array	$message	新密码和老密码
	 *   @return	array	$return		修改结果
	 */
	public function editPwd($uin,$message)
	{
		$oldpass = $message['OldPass'];
		$newpass = $message['Pass'];
		$user = $message['User'];
		if($uin > 0 && !empty($oldpass) && !empty($newpass) && !empty($user)){
			$param = array(
				'extparam' => array(
					'Tag'	=>  'ResetPassword',
					'User'	=>  $user,
					'OldPass'	=>  $oldpass,
					'Pass'	=>  $newpass,
				)
			);
			$array = httpPOST(SSO_API_PATH, $param, true);
			if($array['Flag'] == 100){
				//退出登录
				httpPOST(SSO_API_PATH, array('extparam' => array('Tag'=> 'UserLogOut')), true);
			}
		}else{
			$array = array(
				'Flag'	=> '101',
				'FlagString' => '参数有误',
			);
		}
		return $array;
	}

	/*
	 *   用户认证
	 *   @param	array	$message	用户的邮箱或身份证号
	 *   @return	array	$return		认证结果
	 */
	public function userRenzheng($uin,$data){
		$uin = intval($uin);
		if($uin < 1 || empty($data)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

/*		$info = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$uin,'Status'=>1)));
		if(!empty($data['idcard']) && $data['idcard'] == $info['IdCard']){
			return array('Flag'=>102,'FlagString'=>'身份证已被其他人绑定');
		}

		if(!empty($info['Name'])){
			if(!empty($info['idcard'])){
				return array('Flag'=>103,'FlagString'=>'已经通过实名认证');
			}
			$data['name'] = $info['Name'];
		}*/
		
		require_once 'pass_manager.class.php';
		$pm = new PassManager();
		return $pm->savePassInfo($uin,$data);
	}

	/*
	 *   绑定邮箱
	 *   @param	string	$message	url中的code值
	 *   @return	array	$return		绑定结果
	 */
	public function bindEmail($message){
		$data = $this->url_authdecode($message['bindcode']);
		if(empty($data)) alertMsg('链接无效或已过期,请重新绑定!');
		$param = array(
			'extparam' => array(
					'Tag'	=>  'BindUser',
					'Bind'	=>  $data['email'],
					'Uid'	=>  $message['user']['Uin'],
					'Platform' => 1
				)
		);
		$return = httpPOST(SSO_API_PATH, $param, true);
		
		if($return['Flag'] != 100){
			return $return;
		}
		/*
		$return['email'] = $data['email'];
		$param = array(
			'extparam' => array(
				'Tag'	=>  'UserLogOut',
			)
		);
		httpPOST(SSO_API_PATH, $param, true);*/
		return $return;
	}

	//用户是否存在
	/*
	function getUser($user){
		$table = DB_NAME_IM.'.username';
		$sql = "SELECT * FROM {$table} WHERE `email`='{$user}' OR `phone`='{$user}'";
		return $this->db->get_row($sql);
	}*/

	/*
	 *   发送邮件  
	 *   @param	array	$message	用户id和用户邮箱
	 *   @return	array	$return		验证结果
	 */
	public function emailValida($message)
	{
		$data =  $this->url_authdecode($_GET['bindcode']);
		if($data['user'] != $message['user']['Email']){
			return array('Flag'=>101,'FlagString'=>'请按步骤修改!');
		}
		$email = $message['email'];
		$uin = $message['user']['Uin'];
		//对邮箱格式进行验证一次
		$info = $this->getUserType($email);
		if( $info != 'email' )
		{
			$return = array(
				'Flag'	=> '100',
				'FlagString' => '邮箱格式不正确',
			);
		}
		//是否被绑定
		if($this->isBinded($email,$uin) > 0){
			return array('Flag'=>'102','FlagString'=>'邮箱已被他人注册，请更换邮箱号');
		}
		//$user = $message['user'];
		//$user = isset($user['Phone'])? $user['Phone'] : $user['Email'];
		$data = array('email' => $email, 'uin'=> $uin);
		return $this->sendBindMail($data);
	}

	//通过邮箱修改手机号码
	function emailChangePhone($message){
		$user = $message['Email'];
		$data = array('user'=> $user);
		return $this->sendBindMailToPhone($data);
	}
	
	function phoneChangeEmail($message){
		return $this->changePhone($message);
	}
	
	private function sendBindMail(array $data){
		$code = $this->url_authencode($data);
		$url  = BASE_URL_EMAIL.'?module=bindemail&bindcode='.$code;
		$msg .= '<p>您申请的绑定邮箱链接为：</p><p><a href="'.$url.'">'.$url.'</a></p>';
		$msg .= '<p>请尽快完成操作，该链接'.($this->expire/3600).'小时内有效</p>';
		return $this->sendMail($_REQUEST['email'],$this->vv.'通行证-绑定邮箱邮件',$msg);
	}

	private function sendBindMailToPhone(array $data){
		$code = $this->url_authencode($data);
		$url  = BASE_URL_EMAIL.'?module=phone&bindcode='.$code;
		$msg .= '<p>您申请的绑定邮箱链接为：</p><p><a href="'.$url.'">'.$url.'</a></p>';
		$msg .= '<p>请尽快完成操作，该链接'.($this->expire/3600).'小时内有效</p>';
		return $this->sendMail($data['user'],$this->vv.'通行证-绑定手机邮件',$msg);
	}
	
	private function sendMail($to,$subject,$message, $type=''){
		$result = sendMail($to,$subject,$message);
		if( $result > 0 )
		{
			$return = array(
				'Flag'	=>  '100',
				'FlagString' => '发送成功',
			);
		}
		else
		{
			$return = array(
				'Flag'	=>  '101',
				'FlagString' => '发送失败',
			);
		}
		return $return;
	}

	private function url_authencode($data){
		$data = json_encode($data);
		return rawurlencode(uc_authcode($data,'ENCODE',$this->authcode_link_key,$this->expire));
	}

	private function url_authdecode($code){
		$code = rawurldecode($code);
		return json_decode(uc_authcode($code,'DECODE',$this->authcode_link_key),true);
	}

	/**
	 *   查看用户是否登录
	 *   @return	array	$return		用户登录信息	
	 */
	private function isLogin()
	{
		$param = array(
			'extparam' => array(
					'Tag'	=>  'GetLogin',
				)
		);
		$return = httpPOST(SSO_API_PATH, $param, true);
		return $return;
	}

	//用户密码加密
	private function password($char){
		return md5('88'.$char.'(+_+)');
	}

	//验证用户名密码
	/*
	private function verifyUserPassword($user,$pass){
		if( ! $usertype = $this->getUserType($user)) return array('Flag'=>101,'FlagString'=>'用户名类型错误');
		$pass = $this->password($pass);
		$table = DB_NAME_IM.".username";
		$sql = "SELECT * FROM {$table} WHERE `{$usertype}`='{$user}' AND `pass`='{$pass}' LIMIT 1";
		$info = $this->db->get_row($sql,'ASSOC');
		if(empty($info)) return array('Flag'=>102,'FlagString'=>'用户名或密码错误');
		return array('Flag'=>100,'Uid'=>$info['uid'],'Openid'=>$user,'Email'=>$info['email'],'Phone'=>$info['phone'],'Nick'=>$info['nick'],'Gender'=>$info['gender'],'Created'=>$info['uptime']);
	}*/

	//获取用户名类型
	function getUserType($user){
		if(preg_match('/^\w+@(\w+([._-][a-zA-Z]+))+$/',$user)) return 'email';
		if(preg_match('/^(13|15|18)\d{9}$/',$user)) return 'phone';
		return false;
	}
	
}
