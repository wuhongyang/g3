<?php
class guest_api
{
	//构造函数
	protected $expire;

	public function __construct() {
		$this->db  = domain::main()->GroupDBConn('mysql');
		$this->expire = time()+86400*30;
	}
	
	//游客注册(生成cookie)
	public function guestRegister(array $guest){
		$guest['SessionKey'] = md5($this->expire.uniqid());
		$guest = $this->save($guest);
		if($guest['Flag'] == 100){
			$cookie = json_encode(array('Uin'=>$guest['Uin'],'SessionKey'=>$guest['SessionKey']));
			setcookie('GUEST_LOGIN_TOKEN',$cookie,time()+86400*30,'/',$_SERVER['HTTP_HOST']);
		}
		return $guest;
	}
	
	//机器人注册(不生成cookie)
	public function RobotRegister($gender){
		$guest = Guest_api::getNick($gender);
		$guest['SessionKey'] = md5($this->expire.uniqid());
		return $this->save($guest);
	}
	
	//获取随机昵称
	public static function getNick($gender=1){
		include dirname(dirname(__FILE__)).'/config/guest_config.php';
		$nick = $nameArr[array_rand($nameArr)];
		// if($gender==1){
			// $nick .= $man_name[array_rand($man_name)];
		// }else{
			// $nick .= $woman_name[array_rand($woman_name)];
		// }
		return array('Flag'=>100,'FlagString'=>'ok','Nick'=>$nick,'Gender'=>$gender);
	}
	
	//游客登录
	public function guestLogin($uin,$session){
		if(empty($uin) || empty($session)) return false;
		$time = time();
		$sql = "SELECT * FROM ".DB_NAME_IM.".guest_user WHERE Uin={$uin} AND SessionKey='{$session}' AND expire>{$time} LIMIT 1";
		$guest_info = $this->db->get_row($sql,'ASSOC');
		if(empty($guest_info)){
			return array('Flag'=>101,'FlagString'=>'登录失败');
		}
		$this->db->query("UPDATE ".DB_NAME_IM.".guest_user SET expire={$this->expire} WHERE Uin={$uin}");
		return array_merge(array('Flag'=>100,'FlagString'=>'登录成功'),$guest_info);
	}
	
	//修改游客昵称
	public function editNick($param,$nick){
		if(empty($nick)) return array('Flag'=>101,'FlagString'=>'昵称不能为空');
		$sql = "UPDATE ".DB_NAME_IM.".guest_user SET Nick='{$nick}' WHERE Uin={$param['Uin']} AND SessionKey='{$param['SessionKey']}'";
		$rst = $this->db->query($sql);
		if( ! $rst){
			return array('Flag'=>102,'Uin'=>$param['Uin'],'FlagString'=>'修改失败');
		}
		return array('Flag'=>100,'Uin'=>$param['Uin'],'FlagString'=>'修改成功');
	}
	
	//保存游客信息
	protected function save(array $guest){
		$this->db->start_transaction();
		$this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
		$time = time();
		$sql = "SELECT Uin FROM ".DB_NAME_IM.".guest_user WHERE expire<{$time} LIMIT 1 FOR UPDATE";
		$guest['Uin'] = $this->db->get_var($sql);
		if(empty($guest['Uin'])){
			$sql = "SELECT Uin FROM ".DB_NAME_IM.".guest_user ORDER BY Uin DESC LIMIT 1 FOR UPDATE";
			$guest['Uin'] = $this->db->get_var($sql) + 1;
			if($guest['Uin'] < GUEST_UIN_START){
				$guest['Uin'] = GUEST_UIN_START;
			}elseif($guest['Uin'] > GUEST_UIN_END){
				return array('Flag'=>101,'FlagString'=>'保存失败，请稍候重试！');
			}
		}
		//更新游客信息
		$guest['Nick'] = urldecode($guest['Nick']);
		$sql = "REPLACE INTO ".DB_NAME_IM.".guest_user(Uin,SessionKey,Nick,Gender,expire)VALUES({$guest['Uin']},'{$guest['SessionKey']}','{$guest['Nick']}',{$guest['Gender']},{$this->expire})";
		//返回结果
		if( ! $this->db->query($sql)){
			$guest['Flag'] = 103;
			$guest['FlagString'] = '失败';
			$this->db->rollback();
		}else{
			$guest['Flag'] = 100;
			$guest['FlagString'] = '成功';

			$this->db->commit();
		}
		return $guest;
	}
}
