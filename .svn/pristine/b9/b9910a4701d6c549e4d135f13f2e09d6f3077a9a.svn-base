<?php
 
class Sort{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}
	
	//获取分类
	public function getSortList($groupId){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT `id`,`name`,`uptime`,`icon` FROM ".DB_NAME_GROUP.".`sort` WHERE group_id={$groupId} ORDER BY `order` ASC";
		$rst = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'站内分类列表','List'=>$rst);
	}
	
	//添加分类
	public function sortAdd($data){
		if($data['groupId']<1 || empty($data['name']) ){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".`sort` WHERE group_id={$data['group_id']} AND `name`='{$data['name']}'";
		$id = $this->db->get_var($sql);
		if($id > 0){
			return array('Flag'=>102,'FlagString'=>'分类名称不能相同');
		}
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_GROUP.".`sort` WHERE group_id={$data['groupId']}";
		$count = $this->db->get_var($sql);
		if($count >= 10){
			return array('Flag'=>103,'FlagString'=>'最多添加10个分类');
		}
		$time = time();
		$this->db->start_transaction();
		$sql = "INSERT INTO ".DB_NAME_GROUP.".`sort`(`group_id`,`name`,uptime,`icon`) VALUES({$data['groupId']},'{$data['name']}',{$time},'{$data['icon']}')";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'添加站内房间分类失败');
		}
		$order = $this->db->insert_id();
		$sql = "UPDATE ".DB_NAME_GROUP.".`sort` SET `order`={$order} WHERE id={$order}";

		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'添加站内房间分类失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'添加站内房间分类成功');
	}

	//编辑分类
	public function sortUpdate($id,$data){
		$id = intval($id);
		if($id<1 || empty($data['name'])){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".`sort` WHERE `id`<>{$id} AND group_id={$data['group_id']} AND `name`='{$data['name']}'";
		$count = $this->db->get_var($sql);
		if($count > 0){
			return array('Flag'=>102,'FlagString'=>'分类名称不能相同');
		}
		$sql = "UPDATE ".DB_NAME_GROUP.".`sort` SET `name`='{$data['name']}',`icon`='{$data['icon']}' WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'编辑站内房间分类失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑站内房间分类成功');
	}

	public function sortDel($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if ($id < 1 || $groupId < 1) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//分类是否存在
		$info = $this->sortInfo($id,$groupId);
		$g_id = $info['Info']['group_id'];
		if($g_id != $groupId){
			return array('Flag'=>102,'FlagString'=>'该分类不存在');
		}

		$sql = "DELETE FROM ".DB_NAME_GROUP.".`sort` WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'删除站内房间分类失败');
		}
		return array('Flag'=>100,'FlagString'=>'删除站内房间分类成功');
	}

	public function up($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if ($id < 1 || $groupId < 1) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//当前操作分类的排序值
		$info = $this->sortInfo($id,$groupId);
		$order = $info['Info']['order'];
		if($order < 1){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		//得到要交换的id,order排序值
		$sql = "SELECT `id`,`order` FROM ".DB_NAME_GROUP.".`sort` WHERE group_id={$groupId} AND `order`<{$order} ORDER BY `order` DESC";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'不能上移');
		}
		$source = array('id'=>$id,'order'=>$order);
		return $this->swap($source,$info);
	}

	public function down($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if ($id < 1 || $groupId < 1) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//当前操作分类的排序值
		$info = $this->sortInfo($id,$groupId);
		$order = $info['Info']['order'];
		if($order < 1){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		//得到要交换的id,order排序值
		$sql = "SELECT `id`,`order` FROM ".DB_NAME_GROUP.".`sort` WHERE group_id={$groupId} AND `order`>{$order} ORDER BY `order` ASC";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'不能下移');
		}
		$source = array('id'=>$id,'order'=>$order);
		return $this->swap($source,$info);	
	}

	public function sortInfo($id,$groupId){
		$sql = "SELECT `group_id`,`id`,`name`,`order`,`icon` FROM ".DB_NAME_GROUP.".`sort` WHERE id={$id} AND group_id={$groupId}";
		$info = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'分类详情','Info'=>$info);
	}

	//交换(上移，下移)
	private function swap($source,$dest){
		$this->db->start_transaction();
		//交换(下移)
		$sql = "UPDATE ".DB_NAME_GROUP.".`sort` SET `order`={$dest['order']} WHERE id={$source['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'移动失败');
		}

		$sql = "UPDATE ".DB_NAME_GROUP.".`sort` SET `order`={$source['order']} WHERE id={$dest['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'移动失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'移动成功');
	}

	public function getRoomList($sortId,$groupId){
		$sortId = intval($sortId);
		if($sortId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$sortInfo = $this->sortInfo($sortId,$groupId);
		$sortInfo = $sortInfo['Info'];
		if(empty($sortInfo)){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		$groupMysql = domain::main()->GroupDBConn();
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`rooms` WHERE sort_id={$sortId}";
		$list = $this->db->get_results($sql, ASSOC);
		foreach ((array)$list as $key => $val) {
			$sql = "SELECT name FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE id={$val['room_id']}";
			$list[$key]['name'] = $groupMysql->get_var($sql);
		}
		//$sql = "SELECT a.*,b.name FROM ".DB_NAME_GROUP.".`rooms` a LEFT JOIN ".DB_NAME_NEW_ROOMS.".`rooms` b ON a.room_id=b.id WHERE a.sort_id={$sortId}";
		//$list = $this->db->get_results($sql,ASSOC);

		//站点下房间
		$sql = "SELECT id,name FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE `group`={$groupId} AND `status`>0";
		$roomList = $groupMysql->get_results($sql,ASSOC);
		$rooms = array();
		if(is_array($roomList)){
			foreach ($roomList as $key => $val) {
				$rooms[$val['id']] = $val['name'];
			}
		}
		
		return array('Flag'=>100,'FlagString'=>'分类房间列表','List'=>$list,'SortName'=>$sortInfo['name'],'Rooms'=>$rooms);
	}

	public function roomAdd($roomId,$sortId){
		$roomId = intval($roomId);
		$sortId = intval($sortId);
		if($roomId < 1 || $sortId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$sql = "SELECT room_id FROM ".DB_NAME_GROUP.".`rooms` WHERE sort_id={$sortId} AND room_id={$roomId}";
		$r_id = $this->db->get_var($sql);
		if($r_id == $roomId){
			return array('Flag'=>102,'FlagString'=>'该分类下已存在这个房间');
		}

		$time = time();
		$sql = "INSERT INTO ".DB_NAME_GROUP.".`rooms`(`sort_id`,`room_id`,`uptime`) VALUES({$sortId},{$roomId},{$time})";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'添加失败');
		}
		return array('Flag'=>100,'FlagString'=>'添加成功');
	}

	public function roomDel($id){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$sql = "DELETE FROM ".DB_NAME_GROUP.".`rooms` WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'删除失败');
		}
		return array('Flag'=>100,'FlagString'=>'删除成功');
	}
}
