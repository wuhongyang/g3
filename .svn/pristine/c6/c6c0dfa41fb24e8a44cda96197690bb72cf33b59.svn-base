<?php
 
class MicSetting{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= db::connect(config('database','default'));
	}

	public function micSettingInfo($group_id){
		$sql = "SELECT `title`,`status` FROM ".DB_NAME_GROUP.".mic_setting WHERE group_id={$group_id}";
		$row = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'上麦用户设置查看','Info'=>$row);
	}

	public function micSettingSave($data){
		if(!empty($data['title'])){
			$data['title'] = addslashes(htmlspecialchars($data['title']));
		}
		$data['status'] = intval($data['status']);
		if(!in_array($dat['status'], array(0,1))){
			return array('Flag'=>101,'FlagString'=>'参数异常');
		}
		$sql = "SELECT group_id FROM ".DB_NAME_GROUP.".mic_setting WHERE group_id={$data['group_id']}";
		if($this->db->get_var($sql) > 0){
			$sql = "UPDATE ".DB_NAME_GROUP.".mic_setting SET `title`='{$data['title']}',`status`={$data['status']} WHERE group_id={$data['group_id']}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_GROUP.".mic_setting(`group_id`,`title`,`status`) VALUES({$data['group_id']},'{$data['title']}',{$data['status']})";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'上麦用户设置失败');
		}
		return array('Flag'=>100,'FlagString'=>'上麦用户设置成功');
	}
}
