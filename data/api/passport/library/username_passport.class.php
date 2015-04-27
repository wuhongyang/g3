<?php

require_once 'passport.class.php';

class UsernamePassport extends Passport{

	protected function user_validate($username){
		$len = mb_strlen($username,'UTF-8');
		if($len < 2 || $len > 15){
			return array('Flag'=>101,'FlagString'=>'用户名必须在2-15个字符之间');
		}
		if(preg_match('/^\d+$/', $username) === 1){
			return array('Flag'=>101,'FlagString'=>'用户名不能为纯数字');
		}
		return array('Flag'=>100,'FlagString'=>'OK');
	}
}