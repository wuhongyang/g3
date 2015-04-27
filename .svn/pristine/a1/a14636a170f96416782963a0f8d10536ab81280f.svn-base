<?php
 
class Decoration{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db = domain::main()->GroupDBConn();
		$this->platform_db = db::connect(config('database','default'));
	}
	
	/**
	 *   修改站信息
	 *   @param	int $groupId 站ID
	 *   @param	array $data 更新信息
	 *   @return array $array 返回需要查找的站信息
	 */
	public function updateGroupInfo($groupId,$data){
		if($groupId <= 0 || ! is_array($data)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$set = '';
		foreach($data as $key=>$val){
			$set .= "`{$key}`='{$val}',";
		}
		$set = trim($set,',');
		
		$sql="UPDATE ".DB_NAME_GROUP.".tbl_groups SET {$set} WHERE groupid=".$groupId;
		if(!$this->platform_db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'更新失败');
		}
		return array('Flag'=>100,'FlagString'=>'更新成功');
	}

	public function updateGroupStyle($groupId,$data){
		if($groupId <= 0 || ! is_array($data)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$set = '';
		foreach($data as $key=>$val){
			$set .= "`{$key}`='{$val}',";
		}
		$set = trim($set,',');
		
		$sql="UPDATE ".DB_NAME_GROUP.".style SET {$set} WHERE group_id=".$groupId;
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'更新失败');
		}
		return array('Flag'=>100,'FlagString'=>'更新成功');
	}

	/**
	 *   添加站轮播图
	 *   @param	array $data 添加信息
	 *   @return array $result 是否添加成功
	 */
	public function addCarousel($data){
		$groupId = intval($data['groupId']);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if(empty($data['image'])){
			return array('Flag'=>101,'FlagString'=>'轮播图不能为空');
		}
		$data['explain'] = addslashes(htmlspecialchars($data['explain']));
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_GROUP.".`carousel` WHERE group_id={$groupId}";
		$count = $this->db->get_var($sql);
		if($count >= 5){
			return array('Flag'=>102,'FlagString'=>'最多添加5张轮播图');
		}
		$time = time();
		$this->db->start_transaction();
		$sql = "INSERT INTO ".DB_NAME_GROUP.".`carousel`(`group_id`,`image`,`explain`,`url`,`uptime`) VALUES({$groupId},'{$data['image']}','{$data['explain']}','{$data['url']}','{$time}')";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'添加轮播图失败');
		}
		$order = $this->db->insert_id();
		$sql = "UPDATE ".DB_NAME_GROUP.".`carousel` SET `order`={$order} WHERE id={$order}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'添加轮播图失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'添加轮播图成功');
	}

	public function carouselList($groupId){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`carousel` WHERE group_id={$groupId} ORDER BY `order`";
		$list = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'轮播图列表','List'=>$list);
	}

	//上移
	public function carouselUp($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if ($id < 1 || $groupId < 1) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//当前操作分类的排序值
		$info = $this->carouselInfo($id);
		$order = $info['order'];
		if($order < 1 || $groupId!=$info['group_id']){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		//得到要交换的id,order排序值
		$sql = "SELECT `id`,`order` FROM ".DB_NAME_GROUP.".`carousel` WHERE group_id={$groupId} AND `order`<{$order} ORDER BY `order` DESC";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'不能上移');
		}
		$source = array('id'=>$id,'order'=>$order);
		return $this->swap($source,$info);
	}

	//下移
	public function carouselDown($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if ($id < 1 || $groupId < 1) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//当前操作分类的排序值
		$info = $this->carouselInfo($id);
		$order = $info['order'];
		if($order < 1 || $groupId!=$info['group_id']){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}

		//得到要交换的id,order排序值
		$sql = "SELECT `id`,`order` FROM ".DB_NAME_GROUP.".`carousel` WHERE group_id={$groupId} AND `order`>{$order} ORDER BY `order` ASC";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'不能下移');
		}
		$source = array('id'=>$id,'order'=>$order);
		return $this->swap($source,$info);
	}

	//删除轮播图
	public function carouselDel($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if($id < 1 || $groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$info = $this->carouselInfo($id);
		if($info['group_id'] != $groupId){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		$sql = "DELETE FROM ".DB_NAME_GROUP.".`carousel` WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'删除轮播图失败');
		}
		return array('Flag'=>100,'FlagString'=>'删除轮播图成功');
	}

	//交换(上移，下移)
	private function swap($source,$dest){
		$this->db->start_transaction();
		//交换(下移)
		$sql = "UPDATE ".DB_NAME_GROUP.".`carousel` SET `order`={$dest['order']} WHERE id={$source['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'移动失败');
		}

		$sql = "UPDATE ".DB_NAME_GROUP.".`carousel` SET `order`={$source['order']} WHERE id={$dest['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'移动失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'移动成功');
	}

	//站点风格信息
	public function groupStyle($group_id){
		$group_id = intval($group_id);
		if($group_id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".style WHERE group_id={$group_id}";
		$row = $this->db->get_row($sql,ASSOC);
		$styleInfo = $this->styleInfo($row['style_id']);
		if($styleInfo['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		$styleInfo = $styleInfo['Info'];
		$color_style = $styleInfo['color_style'];
		unset($styleInfo['color_style']);
		$color_style = unserialize($color_style);
		$row = array_merge($row,$color_style);
		$row['style'] = $styleInfo;
		return array('Flag'=>100,'FlagString'=>'站点风格信息','StyleInfo'=>$row);
	}

	//风格列表
	public function styleList($cat_id=0){
		$where = '';
		if($cat_id > 0){
			$where = ' WHERE cat_id='.intval($cat_id);
		}
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_GROUP.".style_setting {$where}";
		$total = $this->platform_db->get_var($sql);
		if($total > 0){
			$page_arr = $this->showPage($total,6);
			$sql = "SELECT * FROM ".DB_NAME_GROUP.".style_setting {$where} LIMIT ".$page_arr['limit'];
			$list = $this->platform_db->get_results($sql,'ASSOC');
		}
		return array('Flag'=>100,'FlagString'=>'风格列表','StyleList'=>$list,'Page'=>$page_arr['page']);
	}

	public function styleCatList(){
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".style_category";
		$cats = $this->platform_db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'站点风格类别','Category'=>$cats);
	}

	//选择站点风格
	public function groupStyleSetting($group_id,$style_id){
		$group_id = intval($group_id);
		$style_id = intval($style_id);
		if($group_id < 1 || $style_id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//风格是否存在
		$style_info = $this->styleInfo($style_id);
		if($style_info['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'风格不存在');
		}
		$style_info = $style_info['Info'];
		$style_info['bg_tile'] = $style_info['bg_tile']==1 ? 1 : 2;
		//是否已设置风格
		$sql = "SELECT group_id FROM ".DB_NAME_GROUP.".style WHERE group_id={$group_id}";
		$g_id = $this->db->get_var($sql);
		$time = time();
		if($g_id != $group_id){
			$sql = "INSERT INTO ".DB_NAME_GROUP.".style(group_id,bg_status,bg_tile,bg_align,banner_status,style_id,uptime) VALUES({$group_id},{$style_info['bg_status']},{$style_info['bg_tile']},{$style_info['bg_align']},{$style_info['banner_status']},{$style_id},{$time})";
		}else{
			$sql = "UPDATE ".DB_NAME_GROUP.".style SET style_id={$style_id},bg_status={$style_info['bg_status']},bg_tile={$style_info['bg_tile']},bg_align={$style_info['bg_align']},banner_status={$style_info['banner_status']},uptime={$time} WHERE group_id={$group_id}";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'使用推荐风格失败');
		}
		return array('Flag'=>100,'FlagString'=>'使用推荐风格成功');
	}

	//风格详情
	public function styleInfo($style_id){
		$style_id = intval($style_id);
		if($style_id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".style_setting WHERE id={$style_id}";
		$row = $this->platform_db->get_row($sql,ASSOC);
		if($row){
			return array('Flag'=>100,'FlagString'=>'风格详情','Info'=>$row);
		}
		return array('Flag'=>102,'FlagString'=>'风格详情');
	}

	//初始化
	/*
	public function init($group_id){
		//得到初始化状态
		$sql = "SELECT init from ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$group_id}";
		$init = $this->platform_db->get_var($sql);
		if($init > 0){
			return array('Flag'=>101,'FlagString'=>'非法操作');
		}
		//上传图片     
		//1：游戏
		$bytes = file_get_contents(dirname(__ROOT__)."/pic/group/menu_icon1.png");
		$index = md5($bytes);
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
		$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
	
		$gameData = array('groupId'=>$group_id,'name'=>'游戏列表','icon'=>$index);
		//2：客服
		$bytes = file_get_contents(dirname(__ROOT__)."/pic/group/menu_icon2.png");
		$index = md5($bytes);
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
		$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
		$customerData = array('groupId'=>$group_id,'name'=>'在线客服','icon'=>$index);
		//3：活动
		$bytes = file_get_contents(dirname(__ROOT__)."/pic/group/menu_icon3.png");
		$index = md5($bytes);
		$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
		$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
		$activityData = array('groupId'=>$group_id,'name'=>'热门活动','icon'=>$index);

		$this->platform_db->start_transaction();
		//把字段置为1
		$sql = "UPDATE ".DB_NAME_GROUP.".tbl_groups SET `init`=1 WHERE groupid={$group_id}";
		if(!$this->platform_db->query($sql)){
			$this->platform_db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化失败');
		}
		//初始化推荐位
		require_once 'recommend.class.php';
		$recommend = new Recommend();
		$data = array('group_id'=>$group_id,'name'=>'房间推荐','type'=>1);
		$rst = $recommend->addRecommendCat($data);
		if($rst['Flag'] != 100){
			$this->platform_db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化推荐位失败');
		}
		$data = array('group_id'=>$group_id,'name'=>'火热房间','parent_id'=>$rst['Id'],'mode'=>2);
		$rst = $recommend->recommendSubCatAdd($data);
		if($rst['Flag'] != 100){
			$this->platform_db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化推荐位失败');
		}
		$data['mode'] = 1;
		$data['row'] = 2;
		$data['name'] = '全部房间';
		$rst = $recommend->recommendSubCatAdd($data);
		if($rst['Flag'] != 100){
			$this->platform_db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化推荐位失败');
		}
		$data = array('group_id'=>$group_id,'name'=>'会员推荐','type'=>2);
		$rst = $recommend->addRecommendCat($data);
		if($rst['Flag'] != 100){
			$this->platform_db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化推荐位失败');
		}

		//初始化左侧菜单
		require_once 'menu.class.php';
		$menu = new Menu($this->db);
		$rst = $menu->menuAdd($gameData);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化左侧菜单失败');
		}
		$rst = $menu->menuAdd($customerData);

		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化左侧菜单失败');
		}
		$rst = $menu->menuAdd($activityData);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化左侧菜单失败');
		}
		//初始化导航模块
		require_once 'navigate.class.php';
		$navigate = new Navigate($this->db);
		$data = array('group_id'=>$group_id,'name'=>'首页','module_name'=>'首页');
		$rst = $navigate->navigateAdd($data);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化导航模块失败');
		}
		$data = array('group_id'=>$group_id,'name'=>'排行榜','module_name'=>'排行榜');
		$rst = $navigate->navigateAdd($data);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化导航模块失败');
		}
		$data = array('group_id'=>$group_id,'name'=>'活动中心','module_name'=>'活动中心');
		$rst = $navigate->navigateAdd($data);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化导航模块失败');
		}
		$data = array('group_id'=>$group_id,'name'=>'加入我们','module_name'=>'加入我们');
		$rst = $navigate->navigateAdd($data);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>101,'FlagString'=>'初始化导航模块失败');
		}

		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'初始化成功');
	}*/

	private function carouselInfo($id){
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`carousel` WHERE id={$id}";
		return $this->platform_db->get_row($sql,ASSOC);
	}

	//分页
	private function showPage($total, $perpage = 15) {
		if ($total > 0) {
			$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
			));
			$page_arr['page'] = $page->show();
			$page_arr['limit'] = $page->limit();
			unset ($page);
		}
		return $page_arr;
	}
}
