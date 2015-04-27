<?php
 
class Title{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}

	public function getTitle($group_id,$type){
		$group_id = intval($group_id);
		$type = intval($type);
		if($group_id < 1 || $type < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT `title`,`status` FROM ".DB_NAME_GROUP.".custom_title WHERE group_id={$group_id} AND `type`={$type}";
		$info = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'模块标题','Info'=>$info);
	}

	public function save($data){
		$data['status'] = intval($data['status']);
		if($data['status'] ==1){
			if(empty($data['title'])){
				return array('Flag'=>101,'FlagString'=>'标题不能为空');
			}
		}
		if(!in_array($data['status'], array(0,1))){
			return array('Flag'=>101,'FlagString'=>'状态错误');
		}
		$data['group_id'] = intval($data['group_id']);
		if($data['group_id'] < 1){
			return array('Flag'=>101,'FlagString'=>'数据异常');
		}
		$data['type'] = intval($data['type']);
		if($data['type'] < 1){
			return array('Flag'=>101,'FlagString'=>'标题类型错误');
		}
		$data['title'] = addslashes(htmlspecialchars($data['title']));
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".custom_title WHERE group_id={$data['group_id']} AND `type`={$data['type']}";
		$id = $this->db->get_var($sql);
		if($id > 0){
			$sql = "UPDATE ".DB_NAME_GROUP.".custom_title SET `title`='{$data['title']}',`status`={$data['status']} WHERE id={$id}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_GROUP.".custom_title(`group_id`,`title`,`status`,`type`) VALUES({$data['group_id']},'{$data['title']}',{$data['status']},{$data['type']})";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'保存标题失败');
		}
		return array('Flag'=>100,'FlagString'=>'保存标题成功');
	}
}
