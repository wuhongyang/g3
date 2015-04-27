<?php
 
class Navigate{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}

	public function navigateList($group_id){
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".navigate WHERE group_id={$group_id} ORDER BY `order`";
		$list = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'导航模块列表','List'=>$list);
	}

	//暂时没有，初始化的时候用
	public function navigateAdd($data){
		$data['group_id'] = intval($data['group_id']);
		if($data['group_id'] < 1 || empty($data['name']) || empty($data['module_name'])){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$data['name'] = addslashes(htmlspecialchars($data['name']));
		$data['module_name'] = addslashes(htmlspecialchars($data['module_name']));
		$sql = "SELECT group_id FROM ".DB_NAME_GROUP.".navigate WHERE group_id={$data['group_id']} AND name='{$data['module_name']}'";
		$g_id = $this->db->get_var($sql);
		if($g_id == $data['group_id']){
			return array('Flag'=>102,'FlagString'=>'已经存在该模块，不能重复添加');
		}
		$sql = "INSERT INTO ".DB_NAME_GROUP.".navigate(`group_id`,`name`,`module_name`) VALUES({$data['group_id']},'{$data['name']}','{$data['module_name']}')";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'添加导航模块失败');
		}
		$order = $this->db->insert_id();
		$sql = "UPDATE ".DB_NAME_GROUP.".navigate SET `order`={$order} WHERE id={$order}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'添加导航模块失败');
		}
		return array('Flag'=>100,'FlagString'=>'添加导航模块成功');
	}

	public function navigateEdit($id,$group_id,$data){
		$id = intval($id);
		if($id<1 || empty($data)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT group_id FROM ".DB_NAME_GROUP.".navigate WHERE id={$id}";
		$g_id = $this->db->get_var($sql);
		if($g_id != $group_id){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		$data['name'] = addslashes(htmlspecialchars($data['name']));
		$sql = "UPDATE ".DB_NAME_GROUP.".navigate SET `name`='{$data['name']}' WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'编辑导航模块失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑导航模块成功');
	}

	public function navigateInfo($id,$group_id){
		$id = intval($id);
		$group_id = intval($group_id);
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".navigate WHERE id={$id} AND group_id={$group_id}";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>101,'FlagString'=>'导航模块详情');
		}
		return array('Flag'=>100,'FlagString'=>'导航模块详情','Info'=>$info);
	}

	public function visible($id,$group_id){
		$id = intval($id);
		$group_id = intval($group_id);
		$info = $this->navigateInfo($id,$group_id);
		if($info['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		$status = $info['Info']['status'];
		$status = abs($status - 1);
		$sql = "UPDATE ".DB_NAME_GROUP.".navigate SET `status`={$status}  WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'设置失败');
		}
		return array('Flag'=>100,'FlagString'=>'设置成功');
	}

	public function leftMove($id,$group_id){
		$id = intval($id);
		$group_id = intval($group_id);
		if ($id < 1 || $group_id < 1) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//当前操作分类的排序值
		$info = $this->navigateInfo($id,$group_id);
		$order = $info['Info']['order'];
		if($order < 1){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		//得到要交换的id,order排序值
		$sql = "SELECT `id`,`order` FROM ".DB_NAME_GROUP.".`navigate` WHERE group_id={$group_id} AND `order`<{$order} ORDER BY `order` DESC";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'不能左移');
		}
		$source = array('id'=>$id,'order'=>$order);
		return $this->swap($source,$info);
	}

	public function rightMove($id,$group_id){
		$id = intval($id);
		$group_id = intval($group_id);
		if ($id < 1 || $group_id < 1) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//当前操作分类的排序值
		$info = $this->navigateInfo($id,$group_id);
		$order = $info['Info']['order'];
		if($order < 1){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		//得到要交换的id,order排序值
		$sql = "SELECT `id`,`order` FROM ".DB_NAME_GROUP.".`navigate` WHERE group_id={$group_id} AND `order`>{$order} ORDER BY `order` ASC";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'不能下移');
		}
		$source = array('id'=>$id,'order'=>$order);
		return $this->swap($source,$info);
	}

	//交换(上移，下移)
	private function swap($source,$dest){
		$this->db->start_transaction();
		//交换(下移)
		$sql = "UPDATE ".DB_NAME_GROUP.".`navigate` SET `order`={$dest['order']} WHERE id={$source['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'移动失败');
		}

		$sql = "UPDATE ".DB_NAME_GROUP.".`navigate` SET `order`={$source['order']} WHERE id={$dest['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'移动失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'移动成功');
	}
}
