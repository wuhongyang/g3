<?php
 
class Joinus{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}

	public function joinList($groupId){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`joinus` WHERE `group_id`={$groupId}";
		//$sql .= " ORDER BY uptime DESC";
		$list = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'获取加入我们列表','List'=>$list);
	}

	public function joinInfo($groupId,$role){
		$groupId = intval($groupId);
		if($groupId < 1 || !$role){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`joinus` WHERE `group_id`={$groupId} AND `id`='{$role}'";
		$info = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'加入我们详情','Info'=>$info);
	}

	public function joinEdit($data){
		$groupId = intval($data['groupId']);
		$id = intval($data['id']);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$time = time();
		foreach($data['phone'] as $key=>$value){
			$contact[$key]['phone'] = $value;
			$contact[$key]['qq'] = $data['qq'][$key];
		}
		$data['content'] = addslashes($data['content']);
		if($id > 0){
			$sql = "UPDATE ".DB_NAME_GROUP.".`joinus` SET role='{$data['role']}',content='{$data['content']}',contact='".json_encode($contact)."',uptime={$time},`status`='1' WHERE id={$id}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_GROUP.".`joinus`(`group_id`,`role`,`content`,`contact`,`uptime`,`status`) VALUES({$groupId},'{$data['role']}','{$data['content']}','".json_encode($contact)."',{$time},'1')";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'编辑失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑成功');
	}

	public function joinDel($groupId,$id){
		$groupId = intval($groupId);
		$id = intval($id);
		if($groupId < 1 || $id < 0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		/*$info = $this->joinInfo($groupId,$role);
		$id = $info['Info']['id'];
		if($id < 1){
			return array('Flag'=>102,'FlagString'=>'删除成功');
		}
		echo $sql = "UPDATE ".DB_NAME_GROUP.".`joinus` SET `status`='0',`content`='',contact='',uptime=0 WHERE id={$id}";*/
		$sql = "DELETE FROM ".DB_NAME_GROUP.".`joinus` WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'删除失败');
		}
		return array('Flag'=>100,'FlagString'=>'删除成功');
	}
}
