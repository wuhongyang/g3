<?php
 
class Config{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= db::connect(config('database','default'));
	}

	public function saveRoleOrderConfig($group_id,$role_order_type){
		$role_order_type = intval($role_order_type);
		if(!in_array($role_order_type, array(1,2,3))){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "UPDATE ".DB_NAME_GROUP.".tbl_groups SET role_order_type={$role_order_type} WHERE groupid={$group_id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'保存失败');
		}
		return array('Flag'=>100,'FlagString'=>'保存成功');
	}
}
