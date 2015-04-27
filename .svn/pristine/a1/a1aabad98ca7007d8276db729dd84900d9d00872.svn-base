<?php

require_once 'passport.class.php';

class PhonePassport extends Passport{

	private $_code_desc = '申请通行证';

	public function sendCode4Reg($phone){
		$rst = $this->user_validate($phone);
		if($rst['Flag'] != 100){
			return $rst;
		}
		$rst = $this->userExist($phone);
		if($rst['Flag'] == 100){
			return array('Flag'=>102,'FlagString'=>'该手机号已被其他人注册');
		}
		return $this->sendCode($phone);
	}

	public function sendCode4GetPwd($phone){
		$rst = $this->user_validate($phone);
		if($rst['Flag'] != 100){
			return $rst;
		}
		$rst = $this->userExist($phone);
		if($rst['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'用户不存在');
		}
		return $this->sendCode($phone);
	}

	public function getPasswdByPhone($data){
		$rst = $this->code_validate($data['username'],$data['msgcode']);
		if($rst['Flag'] != 100){
			return $rst;
		}
		$rst = $this->userExist($data['username']);
		if($rst['Flag'] != 100){
			return $rst;
		}
		$this->delCode($data['username']);
		$wd = $this->_url_authencode($data['username']);
		return array('Flag'=>100,'FlagString'=>'OK','Wd'=>$wd);
	}

	protected function user_validate($phone){
		if(preg_match('/^1[3|4|5|8]\d{9}$/', $phone) == 1){
			return array('Flag'=>100,'FlagString'=>'OK');
		}
		return array('Flag'=>101,'FlagString'=>'手机格式不正确');
	}

	protected function get_code($key){
		$codeInfo = $this->getCodeInfo($key);
		if(time() > $codeInfo['uptime']){
			return '';
		}
		return $codeInfo['val'];
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
        $groupMysql = domain::main()->GroupDBConn("mysql");
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
		$this->db = db::connect(config('database','default'));
		$sql = "SELECT `val`,`uptime` FROM ".DB_NAME_IM.".user_tmp WHERE `get`='{$phone}' AND group_id='{$this->group_info['group_id']}'";
        $groupMysql = domain::main()->GroupDBConn("mysql");
		return $groupMysql->get_row($sql,ASSOC);
	}

	private function delCode($phone){
		$sql = "DELETE FROM ".DB_NAME_IM.".user_tmp WHERE `get`='{$phone}' AND group_id='{$this->group_info['group_id']}'";
        $groupMysql = domain::main()->GroupDBConn("mysql");
		$groupMysql->query($sql);
	}
}