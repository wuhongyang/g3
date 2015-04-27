<?php
//require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/api/passport/library/phone_passport.class.php');
class IntentionalCustomer {
	protected $group_info = array();
		
	public function __construct(){
		$this->db = domain::main()->GroupDBConn();
		$GroupData = domain::main()->GroupData();
		$g_id = (int)$GroupData['groupid'];
		$this->group_info = array(
			'group_id' 		=> $g_id,
			'group_name' 	=> $g_id > 0 ? $GroupData['name'] : 'VV酷'
		);
	}
	
	public function sendCode4Reg($phone){
		$rst = $this->user_validate($phone);
		if($rst['Flag'] != 100){
			return $rst;
		}
		return $this->sendCode($phone);
	}

	protected function user_validate($phone){
		if(preg_match('/^1[3|4|5|8]\d{9}$/', $phone) == 1){
			return array('Flag'=>100,'FlagString'=>'OK');
		}
		return array('Flag'=>101,'FlagString'=>'手机格式不正确');
	}
	
	private function sendCode($phone){
		$codeInfo = $this->getCodeInfo($phone);
		$uptime = time()+3600;
		$code   = rand(100000,999999);
		//写入验证码
		if(empty($codeInfo)){
			$sql = "INSERT INTO ".DB_NAME_IM.".user_tmp VALUES('{$phone}','{$code}',{$uptime},'{$this->group_info['group_id']}')";
		}else{
			$sql = "UPDATE ".DB_NAME_IM.".user_tmp SET `val`='{$code}',uptime={$uptime} WHERE `get`='{$phone}' AND group_id='{$this->group_info['group_id']}'";
		}
        $groupMysql = domain::main()->GroupDBConn("mysql", $this->group_info['group_id']);
		$rst = $groupMysql->query($sql);
		if(!$rst){
			return array('Flag'=>103,'FlagString'=>'服务器异常');
		}
		$msg = "您的校验码是：{$code}，有效时间1小时，如非本人操作，请不要将此校验码告诉他人【{$this->group_info['group_name']} 通行证】";
		//发送短信
		$rst = sendSMS($phone,$msg,$this->_code_desc);
		return $rst;
	}
	
	private function getCodeInfo($phone){
		$sql = "SELECT `val`,`uptime` FROM ".DB_NAME_IM.".user_tmp WHERE `get`='{$phone}' AND group_id='{$this->group_info['group_id']}'";
        $groupMysql = domain::main()->GroupDBConn("mysql", $this->group_info['group_id']);
		return $groupMysql->get_row($sql,ASSOC);
	}
	
	private function delCode($phone){
		$sql = "DELETE FROM ".DB_NAME_IM.".user_tmp WHERE `get`='{$phone}' AND group_id='{$this->group_info['group_id']}'";
		$groupMysql = domain::main()->GroupDBConn("mysql", $this->group_info['group_id']);
        $groupMysql->query($sql);
	}
	
	//参数是否正确
	public function checkParam($data){
		preg_match_all("/./us", $data['nick'], $match);
		$len = count($match[0]);
		if(empty($data['nick'])){
			return array('Flag'=>101,'FlagString'=>'昵称不能为空');
		}
		if($len > 10){
			return array('Flag'=>101,'FlagString'=>'昵称不能超过10个字');
		}
		
		$rst = $this->user_validate($data['phone']);
		if($rst['Flag'] != 100){
			return $rst;
		}
		$rst = $this->code_validate($data['phone'],$data['msgcode']);
		if($rst['Flag'] != 100){
			return $rst;
		}
		return array('Flag'=>100,'FlagString'=>'OK');
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
		$codeInfo = $this->getCodeInfo($key);
		if(time() > $codeInfo['uptime']){
			return '';
		}
		return $codeInfo['val'];
	}
	//意向用户注册
	public function IntendRegister($data){
		//参数验证
		$rst = $this->checkParam($data);
		if($rst['Flag'] != 100){
			return $rst;
		}
		return $this->reg1($data);
	}
	
	protected function reg1($data){
		$user_name = $data['nick'];
		$telephone = $data['phone'];
		$group_id = $this->group_info['group_id'];
		if($group_id < 0 ){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql  = "INSERT INTO ".DB_NAME_GROUP.".`user_intention` (`user_name`, `telephone`, `group_id`, `uptime`) VALUES ('".$user_name."', ".$telephone.", '".$group_id."', '".time()."');";
		$done = $this->db->query($sql);
		if(!$done){
			return array('Flag'=>101,'FlagString'=>'数据库错误，请重新登陆');
		}
		//删除验证码记录
		$this->delCode($data['phone']);
		return array("Flag"=>100, "FlagString"=>"创建成功");	
	}
	
	function getPractice($group_id){
		$sql 		  = "SELECT * FROM ".DB_NAME_GROUP.".`practice_account` WHERE group_id = ".$group_id." ORDER BY `id` ASC";
		
		$account_list = $this->db->get_results($sql, "ASSOC");
		
		$account_list_with_uin = array();
		foreach($account_list as $row){
			$row['account_details']  = json_decode($row['account_details'], true);
			$uin_str				 = "";
			foreach($row['account_details'] as $account){
				$uin_str .= $account['login'].",";
			}
			$row['uin_str'] 		 = substr($uin_str, 0, -1);
			
			$account_list_with_uin[] = $row; 
		}
		
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$account_list_with_uin);
	}
}