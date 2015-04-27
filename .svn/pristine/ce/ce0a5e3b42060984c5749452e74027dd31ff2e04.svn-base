<?php
class Broadcast{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}
	
	function getPrice($group_id){
		$sql = "SELECT * FROM ".DB_NAME_NEW_ROOMS.".`tbl_roomsbroadcast_config` WHERE group_id = ".$group_id;
		$res = $this->db->get_row($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$res);
	}
	
	function savePrice($group_id, $data){
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".`tbl_roomsbroadcast_config` WHERE `group_id` = ".$group_id;
		$exist = $this->db->get_var($sql);
		if($exist){
			$set='';
			if($data['room_bc_price']>0){
				$set.='room_bc_price='.$data['room_bc_price'].',';
			}
			if($data['site_bc_price']>0){
				$set.='site_bc_price='.$data['site_bc_price'].',';
			}
			if($data['signet_times']>0){
				$set.='signet_times='.$data['signet_times'].',';
			}
			if($data['runway_price']>0){
				$set.='runway_price='.$data['runway_price'].',';
			}
			$set=rtrim($set,',');
			$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".`tbl_roomsbroadcast_config` SET $set WHERE group_id = ".$group_id;
		}else{
			$sql = "INSERT INTO ".DB_NAME_NEW_ROOMS.".`tbl_roomsbroadcast_config` (`group_id`, `room_bc_price`, `site_bc_price`, `signet_times`, `runway_price`) VALUES ('".$group_id."', '".$data['room_bc_price']."', '".$data['site_bc_price']."', '".$data['signet_times']."', '".$data['runway_price']."');";
		}
		$done = $this->db->query($sql);
		if($done){
			return array("Flag"=>100, "FlagString"=>"保存成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"保存失败");
		}
		
	}
}