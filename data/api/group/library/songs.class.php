<?php
class Songs{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}
	
	function get_price($group_id){
		$sql = "SELECT `pick_price`, `act_percentage`, `tax_percentage` FROM ".DB_NAME_NEW_ROOMS.".`tbl_roomsbroadcast_config` WHERE group_id = ".$group_id;
		$row = $this->db->get_row($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$row);
	}
	
	function save_price($group_id, $pick_price, $act_percentage, $tax_percentage){
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".`tbl_roomsbroadcast_config` WHERE group_id = ".$group_id;
		$exist = $this->db->get_var($sql);
		if($exist){
			$done = $this->update_price($group_id, $pick_price, $act_percentage, $tax_percentage);
		}else{
			$done = $this->add_price($group_id, $pick_price, $act_percentage, $tax_percentage);
		}
		if($done){
			return array("Flag"=>100, "FlagString"=>"更新成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"更新失败");
		}
	}
	
	private function update_price($group_id, $pick_price, $act_percentage, $tax_percentage){
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".`tbl_roomsbroadcast_config` SET `pick_price` = '".$pick_price."' ,`act_percentage` = '".$act_percentage."' ,`tax_percentage` = '".$tax_percentage."' WHERE `group_id` = '".$group_id."'; ";
		return $this->db->query($sql);
	}
	
	private function add_price($group_id, $pick_price, $act_percentage, $tax_percentage){
		$sql = "INSERT INTO ".DB_NAME_NEW_ROOMS.".`tbl_roomsbroadcast_config` (`group_id`, `pick_price`, `act_percentage`, `tax_percentage`) VALUES ('".$group_id."', '".$pick_price."', '".$act_percentage."', '".$tax_percentage."'); ";
		return $this->db->query($sql);
	}
}