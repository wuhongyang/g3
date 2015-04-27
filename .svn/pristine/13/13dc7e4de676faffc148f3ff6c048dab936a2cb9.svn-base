<?php
 
class Recommend{
	//数据库指针
	protected $db = null;
	protected $table;
	protected $where;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
		$this->table = DB_NAME_GROUP.'.recommend';
	}

	public function recommendCatList($group_id){
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".recommend_cat WHERE group_id={$group_id} ORDER BY `order`";
		$list = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'推荐位类别','List'=>$list);
	}

	public function addRecommendCat($data){
		if(empty($data['name'])){
			return array('Flag'=>101,'FlagString'=>'推荐名称不能为空');
		}
		if(!in_array($data['type'],array(1,2,3,4))){
			return array('Flag'=>101,'FlagString'=>'请选择推荐类别');
		}
		$this->table = DB_NAME_GROUP.".recommend_cat";
		$data['name'] = addslashes(htmlspecialchars($data['name']));
		$data['uptime'] = time();
		return $this->add($data);
	}

	public function recommendSubCatAdd($data){
		$data['name'] = addslashes(htmlspecialchars($data['name']));
		$data['parent_id'] = intval($data['parent_id']);
		if(empty($data['name']) || $data['parent_id']<1 || !in_array($data['mode'], array('1','2')) || $data['group_id']<1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$this->table = DB_NAME_GROUP.".recommend_sub_cat";
		$sql = "SELECT COUNT(*) FROM {$this->table} WHERE group_id={$data['group_id']} AND parent_id={$data['parent_id']}";
		//$count = $this->db->get_var($sql);
		//if($count >= 5){
		//	return array('Flag'=>102,'FlagString'=>'只能添加5个分类');
		//}
		$data['uptime'] = time();
		return $this->add($data);
	}

	public function recommendSubCatArtistAdd($data){
		if($data['is_live'] > 0){
			$sql = "SELECT id FROM ".DB_NAME_GROUP.".recommend_sub_cat WHERE group_id={$data['group_id']} AND parent_id={$data['parent_id']} AND is_live=1";
			if($this->db->get_var($sql) > 0){
				return array('Flag'=>101,'FlagString'=>'只能有一个艺人推荐位直播墙显示');
			}
			$data['is_live'] = 1;
		}else{
			$data['is_live'] = 0;
		}
		$data['is_recommend'] = ($data['is_recommend'] > 0) ? 1 : 0;
		return $this->recommendSubCatAdd($data);
	}

	public function recommendSubCatArtistEdit($id,$group_id,$data){
		if($data['is_live'] > 0){
			$sql = "SELECT id FROM ".DB_NAME_GROUP.".recommend_sub_cat WHERE group_id={$group_id} AND parent_id={$data['parent_id']} AND is_live=1 AND id!={$id}";
			if($this->db->get_var($sql) > 0){
				return array('Flag'=>101,'FlagString'=>'只能有一个艺人推荐位直播墙显示');
			}
			$data['is_live'] = 1;
		}else{
			$data['is_live'] = 0;
		}
		$data['is_recommend'] = ($data['is_recommend'] > 0) ? 1 : 0;
		return $this->recommendSubCatEdit($id,$group_id,$data);
	}

	public function recommendCatEdit($id,$group_id,$data){
		$id = intval($id);
		$this->table = DB_NAME_GROUP.".recommend_cat";
		return $this->edit($id,$group_id,$data);
	}

	public function recommendSubCatEdit($id,$group_id,$data){
		$id = intval($id);
		$this->table = DB_NAME_GROUP.".recommend_sub_cat";
		return $this->edit($id,$group_id,$data);
	}

	public function recommendCommonEdit($id,$group_id,$data){
		$id = intval($id);
		$this->table = DB_NAME_GROUP.".recommend_common";
		return $this->edit($id,$group_id,$data);
	}

	protected function edit($id,$group_id,$data){
		if(empty($data)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$info = $this->recommendInfo($id,$group_id);
		if(empty($info['Info'])){
			return array('Flag'=>102,'FlagString'=>'非法参数');
		}
		$set = '';
		foreach ($data as $key => $val) {
			$set .= '`'.$key.'`="'.$val.'",';
		}
		$set = rtrim($set,',');
		$sql = "UPDATE {$this->table} SET {$set} WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'编辑失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑成功');
	}

	protected function add($data){
		if(empty($data)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$cols = '';
		$values = '';
		foreach ($data as $key => $val) {
			$cols .= '`'.$key.'`,';
			$values .= '"'.$val.'",';
		}
		$cols = rtrim($cols,',');
		$values = rtrim($values,',');
		$sql = "INSERT INTO {$this->table}({$cols}) VALUES({$values})";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'添加失败');
		}
		$order = $this->db->insert_id();
		$sql = "UPDATE {$this->table} SET `order`={$order} WHERE id={$order}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'添加失败');
		}
		return array('Flag'=>100,'FlagString'=>'添加成功','Id'=>$order);
	}

	//一级分类排序
	public function recommendCatOrder($id,$group_id,$type=1){
		$this->table = DB_NAME_GROUP.'.recommend_cat';
		$this->where = '';
		if(intval($type) == 1){
			return $this->up($id,$group_id);
		}else{
			return $this->down($id,$group_id);
		}
	}

	//二级分类排序
	public function recommendSubCatOrder($id,$group_id,$parent_id,$type=1){
		$id = intval($id);
		$parent_id = intval($parent_id);
		$this->table = DB_NAME_GROUP.'.recommend_sub_cat';
		$this->where = " AND parent_id={$parent_id}";
		if(intval($type) == 1){
			return $this->up($id,$group_id);
		}else{
			return $this->down($id,$group_id);
		}
	}

	public function recommendCommonOrder($id,$group_id,$type){
		$this->table = DB_NAME_GROUP.".recommend_common";
		$info = $this->recommendInfo($id,$group_id);
		$parent_id = $info['Info']['parent_id'];
		$this->where = " AND parent_id={$parent_id}";
		if(intval($type) == 1){
			return $this->up($id,$group_id);
		}else{
			return $this->down($id,$group_id);
		}
	}

	public function recommendOrder($id,$group_id,$type){
		$info = $this->recommendInfo($id,$group_id);
		$parent_id = $info['Info']['parent_id'];
		$this->where = " AND parent_id={$parent_id}";
		if(intval($type) == 1){
			return $this->up($id,$group_id);
		}else{
			return $this->down($id,$group_id);
		}
	}

	public function recommendCatInfo($id,$group_id){
		$id = intval($id);
		$this->table = DB_NAME_GROUP.".recommend_cat";
		return $this->recommendInfo($id,$group_id);
	}

	//二级分类详情
	public function recommendSubCatInfo($id,$group_id){
		$id = intval($id);
		$this->table = DB_NAME_GROUP.".recommend_sub_cat";
		return $this->recommendInfo($id,$group_id);
	}

	public function recommendCommonInfo($id,$group_id){
		$id = intval($id);
		$this->table = DB_NAME_GROUP.".recommend_common";
		return $this->recommendInfo($id,$group_id);
	}

	//类别显示不显示
	public function recommendCatVisible($id,$group_id){
		$id = intval($id);
		$this->table = DB_NAME_GROUP.".recommend_cat";
		return $this->visible($id,$group_id);
	}

	//子类别显示不显示
	public function recommendSubCatVisible($id,$group_id){
		$id = intval($id);
		$this->table = DB_NAME_GROUP.".recommend_sub_cat";
		return $this->visible($id,$group_id);
	}

	//二级  推荐类别子列表
	public function recommendSubCatShow($group_id,$parent_id){
		$parent_id = intval($parent_id);
		$this->table = DB_NAME_GROUP.".recommend_sub_cat";
		$list = $this->show($group_id,$parent_id);

		$this->table = DB_NAME_GROUP.".recommend_cat";
		$info = $this->recommendInfo($parent_id,$group_id);
		$info = $info['Info'];
		return array('Flag'=>100,'FlagString'=>'推荐分类列表','List'=>$list,'ParentInfo'=>$info);
	}

	//三级  推荐列表
	public function recommendShow($group_id,$parent_id){
		$parent_id = intval($parent_id);

		$this->table = DB_NAME_GROUP.".recommend_sub_cat";
		$info = $this->recommendInfo($parent_id,$group_id);
		
		//得到类别详情
		$cat_id = $info['Info']['parent_id'];
		$this->table = DB_NAME_GROUP.".recommend_cat";
		$catInfo = $this->recommendInfo($cat_id,$group_id);
		if($catInfo['Info']['type'] == 1){
			$this->table = DB_NAME_GROUP.".recommend";
			$list = $this->show($group_id,$parent_id);
			//房间
			require_once 'group_manage.class.php';
			$gm = new GroupManage();
			$roomList = $gm->getGroupRooms($group_id);
			$roomList = (array)$roomList['roomList'];
			$rooms = array();
			foreach ($roomList as $key => $val) {
				$name = empty($val['name']) ? $val['id'] : $val['name'];
				$rooms[$val['id']] = array('name'=>$name,'status'=>$val['status']);
			}
		}elseif($catInfo['Info']['type'] == 2 || $catInfo['Info']['type'] == 4){
			$this->table = DB_NAME_GROUP.".recommend";
			$list = $this->show($group_id,$parent_id);
			foreach ((array)$list as $k => $v) {
				$userInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$v['code'])));
				$list[$k]['nick'] = !empty($userInfo['baseInfo']['nick']) ? $userInfo['baseInfo']['nick'] : $v['code'];
			}
		}else{
			$this->table = DB_NAME_GROUP.".recommend_common";
			$list = $this->show($group_id,$parent_id);
			//通用推荐
		}

		return array('Flag'=>100,'FlagString'=>'推荐分类列表','List'=>$list,'ParentInfo'=>$info['Info'],'Type'=>$catInfo['Info']['type'],'Rooms'=>$rooms);
	}

	//二级  推荐子类别删除
	public function recommendSubCatDel($id,$group_id){
		$id = intval($id);
		$this->table = DB_NAME_GROUP.".recommend_sub_cat";
		$this->db->start_transaction();
		$rst = $this->del($id,$group_id);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return $rst;
		}
		$sql = "DELETE FROM ".DB_NAME_GROUP.".`recommend` WHERE group_id={$group_id} AND parent_id={$id}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'删除子类失败');
		}
		$sql = "DELETE FROM ".DB_NAME_GROUP.".`recommend_common` WHERE group_id={$group_id} AND parent_id={$id}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'删除子类失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'删除成功');
	}

	//三级  推荐删除
	public function recommendDel($id,$group_id){
		$id = intval($id);
		return $this->del($id,$group_id);
	}

	public function recommendCommonDel($id,$group_id){
		$id = intval($id);
		$this->table = DB_NAME_GROUP.".recommend_common";
		return $this->del($id,$group_id);
	}

	//删除
	protected function del($id,$group_id){
		$info = $this->recommendInfo($id,$group_id);
		$info = $info['Info'];
		if(empty($info)){
			return array('Flag'=>101,'FlagString'=>'数据异常');
		}
		$sql = "DELETE FROM {$this->table} WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'删除失败');
		}
		return array('Flag'=>100,'FlagString'=>'删除成功');
	}

	protected function show($group_id,$parent_id){
		$sql = "SELECT * FROM {$this->table} WHERE parent_id={$parent_id} AND group_id={$group_id} ORDER BY `order`";
		$list = $this->db->get_results($sql,ASSOC);
		return $list;
	}

	//设置显示不显示
	protected function visible($id,$group_id){
		$info = $this->recommendInfo($id,$group_id);
		$info = $info['Info'];
		$status = abs($info['status'] - 1);
		$sql = "UPDATE {$this->table} SET `status`={$status} WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'设置失败');
		}
		return array('Flag'=>100,'FlagString'=>'设置成功');
	}

	//添加房间推荐位
	public function addRoomRecommend($data){
		$data['parent_id'] = intval($data['parent_id']);
		$data['code'] = intval($data['code']);
		if($data['parent_id']<1 || $data['code']<1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		return $this->addRecommend($data);
	}

	//添加艺人推荐位
	public function addVipRecommend($data){
		if($data['parent_id']<1 || $data['code']<1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//验证VIP是否是本站的
		$vipInfo = getGroupVip($data['code'],$data['group_id']);
		if($vipInfo['uin'] != $data['code']){
			return array('Flag'=>103,'FlagString'=>'不是本站会员');
		}
		return $this->addRecommend($data);
	}

	public function addCommonRecommend($data){
		if($data['parent_id']<1 || empty($data['title'])){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$data['uptime'] = time();
		$this->table = DB_NAME_GROUP.".recommend_common";
		return $this->add($data);
	}

	//上移
	protected function up($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if ($id < 1 || $groupId < 1) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//当前操作分类的排序值
		$info = $this->recommendInfo($id,$groupId);
		$order = $info['Info']['order'];
		if($order < 1){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		//得到要交换的id,order排序值
		$sql = "SELECT `id`,`order` FROM {$this->table} WHERE group_id={$groupId} AND `order`<{$order} {$this->where} ORDER BY `order` DESC";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'不能移动');
		}
		$source = array('id'=>$id,'order'=>$order);
		return $this->swap($source,$info);
	}

	//下移
	protected function down($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if ($id < 1 || $groupId < 1) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//当前操作分类的排序值
		$info = $this->recommendInfo($id,$groupId);
		$order = $info['Info']['order'];
		if($order < 1 || $groupId!=$info['Info']['group_id']){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		//得到要交换的id,order排序值
		$sql = "SELECT `id`,`order` FROM {$this->table} WHERE group_id={$groupId} AND `order`>{$order} {$this->where} ORDER BY `order` ASC";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'不能移动');
		}
		$source = array('id'=>$id,'order'=>$order);
		return $this->swap($source,$info);
	}

	//交换(上移，下移)
	protected function swap($source,$dest){
		$this->db->start_transaction();
		//交换(下移)
		$sql = "UPDATE {$this->table} SET `order`={$dest['order']} WHERE id={$source['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'移动失败');
		}

		$sql = "UPDATE {$this->table} SET `order`={$source['order']} WHERE id={$dest['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'移动失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'移动成功');
	}

	//推荐位详情
	protected function recommendInfo($id,$group_id){
		$sql = "SELECT * FROM {$this->table} WHERE id={$id} AND group_id={$group_id}";
		$info = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'分类详情','Info'=>$info);
	}

	public function addRecommend($data){
		$time = time();
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".`recommend` WHERE group_id={$data['group_id']} AND code={$data['code']} AND `parent_id`='{$data['parent_id']}'";
		$id = $this->db->get_var($sql);
		if($id > 0){
			return array('Flag'=>103,'FlagString'=>'不能重复添加');
		}
		$this->db->start_transaction();
		$sql = "INSERT INTO ".DB_NAME_GROUP.".`recommend`(`group_id`,`code`,`uptime`,`parent_id`) VALUES({$data['group_id']},{$data['code']},{$time},{$data['parent_id']})";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'添加推荐位失败');
		}
		$order = $this->db->insert_id();
		$sql = "UPDATE ".DB_NAME_GROUP.".`recommend` SET `order`={$order} WHERE id={$order}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'添加推荐位失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'添加推荐位成功','Id'=>$order);
	}

}
