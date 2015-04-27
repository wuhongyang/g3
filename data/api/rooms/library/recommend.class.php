<?php
class Recommends
{
	private $db;
	
	function __construct(){
		$this->db = domain::main()->GroupDBConn();
	}
	
	public function GetRoomInfo($roomid){
		$roomid = intval($roomid);
		if($roomid < 1){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		//$sql = "SELECT a.roomid,a.status,a.desc,a.worktime,b.id,b.name,b.region_id,b.description FROM ".DB_NAME_NEW_ROOMS.".rooms AS b LEFT JOIN ".DB_NAME_NEW_ROOMS.".recommend AS a ON b.id=a.roomid WHERE b.id={$roomid} AND b.status>0 AND a.type=2";
		$sql = "SELECT `id`,`name`,`region_id`,`description` FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid}";
		$rst = $this->db->get_row($sql,'ASSOC');
		if(empty($rst)){
			return array('Flag'=>102,'FlagString'=>'房间信息获取失败');
		}
		$sql = "SELECT `roomid`,`status`,`desc`,`worktime` FROM ".DB_NAME_NEW_ROOMS.".recommend WHERE roomid={$roomid} AND `type`=2";
		$rst1 = $this->db->get_row($sql,'ASSOC');
		if($rst1){
			$rst = array_merge($rst,$rst1);
		}
		return array('Flag'=>100,'FlagString'=>'房间信息获取成功','Info'=>$rst);
	}
	
	public function postInfo($data){
		if($data['roomid']<1 || $data['region_id']<0 || empty($data['start']) || empty($data['end'])){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		$worktime = array();
		foreach($data['start'] as $key => $val){
			$worktime[] = array('start'=>$val,'end'=>$data['end'][$key]);
		}
		$worktime = json_encode($worktime);
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_NEW_ROOMS.".recommend WHERE roomid={$data['roomid']} AND `type`=2";
		if($this->db->get_var($sql) > 0){
			$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".recommend SET worktime='{$worktime}',`desc`='',`status`=0,uptime=".time()." WHERE roomid={$data['roomid']} AND `type`=2";
		}else{
			$sql = "INSERT INTO ".DB_NAME_NEW_ROOMS.".recommend(`roomid`,`type`,`region_id`,`worktime`,`uptime`) VALUES({$data['roomid']},2,{$data['region_id']},'{$worktime}',".time().")";
		}
		$rst = $this->db->query($sql);
		if(!$rst){
			return array('Flag'=>102,'FlagString'=>'提交推荐位申请失败');
		}
		return array('Flag'=>100,'FlagString'=>'提交推荐位申请成功');
	}
}