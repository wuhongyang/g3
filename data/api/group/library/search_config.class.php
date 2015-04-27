<?php
 
class SearchConfig{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}

	public function searchConfigInfo($group_id){
		$sql = "SELECT `title`,`common_search`,`vip_search` FROM ".DB_NAME_GROUP.".search_config WHERE group_id={$group_id}";
		$row = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'搜索功能配置查看','Info'=>$row);
	}

	public function searchConfigSave($data){
		$status = array(0,1);
		if(!empty($data['title'])){
			$data['title'] = addslashes(htmlspecialchars($data['title']));
		}
		$data['common_search'] = intval($data['common_search']);
		if(!in_array($dat['common_search'], $status)){
			return array('Flag'=>101,'FlagString'=>'参数异常');
		}
		if(!in_array($data['vip_search'], $status)){
			return array('Flag'=>101,'FlagString'=>'参数异常');
		}
		$sql = "SELECT group_id FROM ".DB_NAME_GROUP.".search_config WHERE group_id={$data['group_id']}";
		if($this->db->get_var($sql) > 0){
			$sql = "UPDATE ".DB_NAME_GROUP.".search_config SET `title`='{$data['title']}',`common_search`={$data['common_search']},`vip_search`={$data['vip_search']} WHERE group_id={$data['group_id']}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_GROUP.".search_config(`group_id`,`title`,`common_search`,`vip_search`) VALUES({$data['group_id']},'{$data['title']}',{$data['common_search']},{$data['vip_search']})";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'搜索功能配置保存失败');
		}
		return array('Flag'=>100,'FlagString'=>'搜索功能配置保存成功');
	}

	public function getTitle($group_id,$type){
		$group_id = intval($group_id);
		$type = intval($type);
		if($group_id < 1 || $type < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT `title` FROM ".DB_NAME_GROUP.".custom_title WHERE group_id={$group_id} AND `type`={$type}";
		$title = $this->db->get_var($sql);
		return array('Flag'=>100,'FlagString'=>'模块标题','Title'=>$title);
	}

	public function save($data){
		if(empty($data['title'])){
			return array('Flag'=>101,'FlagString'=>'标题不能为空');
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
			$sql = "UPDATE ".DB_NAME_GROUP.".custom_title SET `title`='{$data['title']}' WHERE id={$id}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_GROUP.".custom_title(`group_id`,`title`,`type`) VALUES({$data['group_id']},'{$data['title']}',{$data['type']})";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'保存标题失败');
		}
		return array('Flag'=>100,'FlagString'=>'保存标题成功');
	}
}
