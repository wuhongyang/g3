<?php
 
class Setting{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}

	public function settingSave($group_id,$data){
		$groupid = intval($group_id);
		if($group_id < 1 || empty($data['key'])){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$data['value'] = serialize((array)$data['value']);
		$row = $this->getByKey($group_id,$data['key']);
		if($row['id'] > 0){
			$sql = "UPDATE ".DB_NAME_GROUP.".group_setting SET `value`='{$data['value']}' WHERE id={$row['id']}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_GROUP.".group_setting(`group_id`,`key`,`value`) VALUES({$group_id},'{$data['key']}','{$data['value']}')";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'保存失败');
		}
		return array('Flag'=>100,'FlagString'=>'保存成功');
	}

	public function settingValue($group_id,$key){
		$groupid = intval($group_id);
		if($group_id < 1 || empty($key)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$row = $this->getByKey($group_id,$key);
		if(empty($row)){
			return array('Flag'=>101,'FlagString'=>'失败','Value'=>array());
		}
		return array('Flag'=>100,'FlagString'=>'成功','Value'=>$row['value']);
	}

	private function getByKey($group_id,$key){
		$sql = "SELECT id,`value` FROM ".DB_NAME_GROUP.".group_setting WHERE group_id={$group_id} AND `key`='{$key}'";
		return $this->db->get_row($sql,ASSOC);
	}
}
