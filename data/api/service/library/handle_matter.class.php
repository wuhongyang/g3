<?php

class HandleMatter{
	private $db;
	public function __construct(){
		$this->db = domain::main()->GroupDBConn('mysql');
	}

	public function getCount($uin){
		$uin = intval($uin);
		if($uin < 0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_IM.".handle_matter WHERE uin={$uin} AND `status`=0";
		$count = $this->db->get_var($sql);
		$count = $count>0 ? $count : 0;
		return array('Flag'=>100,'FlagString'=>'成功','Count'=>$count);
	}

	public function add($data){
		$time = time();
		$data['content'] = addslashes($data['content']);
		$sql = "INSERT INTO ".DB_NAME_IM.".handle_matter(uin,content,link,link_name,uptime) VALUES({$data['uin']},'{$data['content']}','{$data['link']}','{$data['link_name']}',{$time})";
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'添加待办事项成功');
		}
		return array('Flag'=>102,'FlagString'=>'添加待办事项失败');
	}

	public function unread($uin){
		if($uin < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_IM.".handle_matter WHERE uin={$uin} AND `status`=0";
		$result = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'成功','List'=>$result);
	}

	public function setRead($id){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "UPDATE ".DB_NAME_IM.".handle_matter SET `status`=1 WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'失败');
		}
		return array('Flag'=>100,'FlagString'=>'成功');
	}
}