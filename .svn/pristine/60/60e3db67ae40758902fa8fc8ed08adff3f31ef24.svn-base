<?php
 
class Artist{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->groupMysql = domain::main()->GroupDBConn('mysql');
	}

	public function getArtistSalary($uin,$roomId,$groupId){
		$uin = intval($uin);
		$roomId = intval($roomId);
		$groupId = intval($groupId);
		if($uin < 1 || $roomId < 1 || $groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT artist_salary FROM ".DB_NAME_PARTNER.".`channel_user` WHERE uid=$uin AND room_id=$roomId AND up_uid=$groupId AND type=15";
		$salary = $this->groupMysql->get_var($sql);
		if($salary===''){
			return array('Flag'=>102,'FlagString'=>'没有艺人数据');
		}
		return array('Flag'=>100,'FlagString'=>'约定底薪','Salary'=>$salary);
	}
	
	public function editArtistSalary($salary,$uin,$roomId,$groupId){
		$salary = intval($salary);
		$uin = intval($uin);
		$roomId = intval($roomId);
		$groupId = intval($groupId);
		if($salary < 0 || $uin < 1 || $roomId < 1 || $groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "UPDATE ".DB_NAME_PARTNER.".`channel_user` SET artist_salary=$salary WHERE uid=$uin AND room_id=$roomId AND up_uid=$groupId AND type=15 LIMIT 1";
		if(!$this->groupMysql->query($sql)){
			return array('Flag'=>102,'FlagString'=>'修改失败');
		}
		return array('Flag'=>100,'FlagString'=>'修改成功');
	}

}
