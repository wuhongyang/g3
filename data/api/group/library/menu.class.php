<?php
 
class Menu{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}
	
	//左侧菜单列表
	public function getMenuList($groupId){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$superId = 0;
		$menuList = $this->menuList($groupId,$superId);
		return array('Flag'=>100,'FlagString'=>'左侧菜单列表','MenuList'=>$menuList);
	}

	public function subMenuList($groupId,$superId){
		$groupId = intval($groupId);
		$superId = intval($superId);
		if($groupId < 1 || $superId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$menuInfo = $this->menuInfo($superId,$groupId);

		$submenuList = $this->menuList($groupId,$superId);

		require_once 'group_manage.class.php';
		$gm = new GroupManage();
		$rooms = $gm->getGroupRooms($groupId);
		$rooms = $rooms['roomList'];

		return array('Flag'=>100,'FlagString'=>'左侧菜单列表','MenuList'=>$submenuList,'MenuName'=>$menuInfo['MenuInfo']['name'],'Rooms'=>$rooms);
	}

	//添加左侧菜单
	public function menuAdd($data){
		if(empty($data['name']) || empty($data['icon']) || $data['groupId'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$data['superId'] = 0;
		$data['url'] = '';
		$data['other'] = array();
		return $this->addMenu($data);
	}

	public function subMenuAdd($data){
		if(empty($data['name']) || $data['groupId'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		return $this->addMenu($data);
	}

	//编辑左侧菜单
	public function menuEdit($data){
		if(empty($data['name']) || empty($data['icon']) || $data['groupId'] < 1 || $data['id'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".`menu` WHERE group_id={$data['groupId']} AND super_id=0 AND name='{$data['name']}' AND id!={$data['id']}";
		$id = $this->db->get_var($sql);
		if($id > 0){
			return array('Flag'=>102,'FlagString'=>'已存在相同的菜单名称');
		}
		$info = array('name'=>$data['name'],'icon'=>$data['icon']);
		$where = array('id'=>$data['id'],'groupId'=>$data['groupId']);
		$success = $this->editMenu($where,$info);
		if($success != 1){
			return array('Flag'=>103,'FlagString'=>'编辑左侧菜单失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑左侧菜单成功');
	}

	public function subMenuEdit($data){
		if(empty($data['name']) || $data['groupId'] < 1 || $data['id'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".`menu` WHERE group_id={$data['groupId']} AND super_id={$data['superId']} AND name='{$data['name']}' AND id!={$data['id']}";
		$id = $this->db->get_var($sql);
		if($id > 0){
			return array('Flag'=>102,'FlagString'=>'已存在相同的菜单名称');
		}
		$info = array('name'=>$data['name'],'icon'=>$data['icon'],'url'=>$data['url'],'other'=>json_encode((array)$data['other']));
		$where = array('id'=>$data['id'],'groupId'=>$data['groupId']);
		$success = $this->editMenu($where,$info);
		if($success != 1){
			return array('Flag'=>103,'FlagString'=>'编辑二级菜单失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑二级菜单成功');
	}

	//一级菜单显示/不显示
	public function menuVisible($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if($id< 1 || $groupId<1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$menuInfo = $this->menuInfo($id,$groupId);
		$menuInfo = $menuInfo['MenuInfo'];
		if(empty($menuInfo)){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		$words = array('0'=>'不','1'=>'');
		$status = $menuInfo['status']==1 ? '0' : '1';

		$this->db->start_transaction();
		//如果一级菜单设为不显示，则把下属二级菜单设为不显示
		if($status != 1){
			$sql = "UPDATE ".DB_NAME_GROUP.".`menu` SET `status`='{$status}' WHERE group_id={$groupId} AND super_id={$id}";
			if(!$this->db->query($sql)){
				$this->db->rollback();
				return array('Flag'=>103,'FlagString'=>"菜单{$words[$status]}显示设置失败");
			}
		}
		
		$sql = "UPDATE ".DB_NAME_GROUP.".`menu` SET `status`='{$status}' WHERE id={$id}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>"一级菜单{$words[$status]}显示设置失败");
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>"一级菜单{$words[$status]}显示设置成功");
	}

	//二级菜单显示/不显示
	public function subMenuVisible($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if($id< 1 || $groupId<1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$menuInfo = $this->menuInfo($id,$groupId);
		$menuInfo = $menuInfo['MenuInfo'];
		if(empty($menuInfo)){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		$words = array('0'=>'不','1'=>'');
		$status = $menuInfo['status']==1 ? '0' : '1';

		//二级菜单显示，先检查一级菜单是否为显示
		if($status == 1){
			$sql = "SELECT `status` FROM ".DB_NAME_GROUP.".`menu` WHERE id={$menuInfo['super_id']}";
			$menu_status = $this->db->get_var($sql);
			if($menu_status != '1'){
				return array('Flag'=>103,'FlagString'=>'请先设置其一级菜单为显示状态');
			}
		}

		$sql = "UPDATE ".DB_NAME_GROUP.".`menu` SET `status`='{$status}' WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>"二级菜单{$words[$status]}显示设置失败");
		}
		return array('Flag'=>100,'FlagString'=>"二级菜单{$words[$status]}显示设置成功");
	}

	//显示/不显示
	/*public function setVisible($data){
		if($data['groupId'] < 1 || $data['id'] < 1 || !in_array($data['status'], array(0,1))){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$words = array('0'=>'不','1'=>'');
		$info = array('status'=>$data['status']);
		$where = array('id'=>$data['id'],'groupId'=>$data['groupId']);
		$success = $this->editMenu($where,$info);
		if($success != 1){
			return array('Flag'=>102,'FlagString'=>"菜单{$words[$data['status']]}显示设置失败");
		}
		return array('Flag'=>100,'FlagString'=>"菜单{$words[$data['status']]}显示设置成功");
	}*/

	//菜单详情
	public function menuInfo($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if($id < 1 || $groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`menu` WHERE id={$id} AND group_id={$groupId}";
		$info = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'菜单详情','MenuInfo'=>$info);
	}

	//下移
	public function down($id,$groupId){
		$menuInfo = $this->menuInfo($id,$groupId);
		if($menuInfo['Flag'] != 100){
			return $menuInfo;
		}
		$menuInfo = $menuInfo['MenuInfo'];
		if(empty($menuInfo)){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		$sql = "SELECT `id`,`order` FROM ".DB_NAME_GROUP.".`menu` WHERE `group_id`={$groupId} AND `super_id`={$menuInfo['super_id']} AND `order`>{$menuInfo['order']} ORDER BY `order` ASC LIMIT 1";
		$dest = $this->db->get_row($sql);
		$source = array('id'=>$id,'order'=>$menuInfo['order']);
		return $this->swap($source,$dest);
	}

	//上移
	public function up($id,$groupId){
		$menuInfo = $this->menuInfo($id,$groupId);
		if($menuInfo['Flag'] != 100){
			return $menuInfo;
		}
		$menuInfo = $menuInfo['MenuInfo'];
		if(empty($menuInfo)){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		$sql = "SELECT `id`,`order` FROM ".DB_NAME_GROUP.".`menu` WHERE `group_id`={$groupId} AND `super_id`={$menuInfo['super_id']} AND `order`<{$menuInfo['order']} ORDER BY `order` DESC LIMIT 1";
		$dest = $this->db->get_row($sql);
		$source = array('id'=>$id,'order'=>$menuInfo['order']);
		return $this->swap($source,$dest);
	}

	private function swap($source,$dest){
		$this->db->start_transaction();
		$sql = "UPDATE ".DB_NAME_GROUP.".`menu` SET `order`={$dest['order']} WHERE id={$source['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'排序失败');
		}
		$sql = "UPDATE ".DB_NAME_GROUP.".`menu` SET `order`={$source['order']} WHERE id={$dest['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'排序失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'排序成功');
	}

	private function editMenu($where,$data){
		$condition = '';
		foreach ($data as $key => $val) {
			$condition .= "`{$key}`='{$val}',";
		}
		if(empty($condition)){
			return 1;
		}
		$condition = rtrim($condition,',');
		$sql = "UPDATE ".DB_NAME_GROUP.".`menu` SET {$condition} WHERE id={$where['id']} AND group_id={$where['groupId']}";
		return $this->db->query($sql) ? 1 : 0;
	}

	private function addMenu($data){
		$other = json_encode((array)$data['other']);
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".`menu` WHERE group_id={$data['groupId']} AND super_id={$data['superId']} AND name='{$data['name']}'";
		$id = $this->db->get_var($sql);
		if($id > 0){
			return array('Flag'=>102,'FlagString'=>'已存在相同的菜单名称');
		}
		$time = time();
		//$this->db->start_transaction();
		$sql = "INSERT INTO ".DB_NAME_GROUP.".`menu`(`group_id`,`name`,`icon`,`super_id`,`url`,`uptime`,`other`) VALUES({$data['groupId']},'{$data['name']}','{$data['icon']}',{$data['superId']},'{$data['url']}',{$time},'{$other}')";
		if(!$this->db->query($sql)){
			//$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'添加菜单失败');
		}
		$order = $this->db->insert_id();
		$sql = "UPDATE ".DB_NAME_GROUP.".`menu` SET `order`={$order} WHERE id={$order}";
		if(!$this->db->query($sql)){
			//$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'添加菜单失败');
		}
		//$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'添加菜单成功');
	}

	//获得菜单列表
	private function menuList($groupId,$superId){
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`menu` WHERE group_id={$groupId}";
		if($superId >= 0){
			$sql .= " AND super_id={$superId}";
		}
		$sql .= " ORDER BY `order` ASC";
		return $this->db->get_results($sql,ASSOC);
	}

}
