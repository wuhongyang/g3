<?php
 
class Link{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}

	//友情链接分类列表
	public function linkCateList($groupId){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`link_cate` WHERE `group_id`={$groupId}";
		$list = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'获取友情链接分类列表','List'=>$list);
	}
	
	public function LinkCateSave($data){
		$groupId = intval($data['group_id']);
		if($groupId < 1 || !in_array($data['type'], array('txt', 'img'))){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		preg_match_all("/./us", $data['title'], $match);
		$title_len = count($match[0]);
		if(empty($data['title'])){
			return array('Flag'=>101,'FlagString'=>'标题不能为空');
		}
		if($title_len > 8){
			return array('Flag'=>101,'FlagString'=>'标题不能超过8个字');
		}
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".`link_cate` WHERE group_id={$groupId} AND type='".$data['type']."'";
		$id = $this->db->get_var($sql);
		$time = time();
		if($id > 0){
			$sql = "UPDATE ".DB_NAME_GROUP.".`link_cate` SET title='{$data['title']}',uptime={$time} WHERE id={$id}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_GROUP.".`link_cate`(`group_id`,`title`,`uptime`,`type`) VALUES({$groupId},'{$data['title']}',{$time},'{$data['type']}')";
		}
		
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'编辑失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑成功');
	}
	
	public function LinkCateShow($data){
		$groupId = intval($data['group_id']);
		if($groupId < 1 || !in_array($data['type'], array('txt', 'img'))){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".`link_cate` WHERE group_id={$groupId} AND type='".$data['type']."'";
		$id = $this->db->get_var($sql);
		if(!$id){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$time = time();
		$sql = "UPDATE ".DB_NAME_GROUP.".`link_cate` SET is_show={$data['is_show']},uptime={$time} WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'编辑失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑成功');
	}
	
	public function linkList($data){
		$groupId = intval($data['group_id']);
		if($groupId < 1 || !in_array($data['type'], array('txt', 'img'))){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".`link_cate` WHERE group_id={$groupId} AND type='".$data['type']."'";
		$cate_id = $this->db->get_var($sql);
		if(!$cate_id){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT count(*) FROM ".DB_NAME_GROUP.".`link` WHERE group_id={$groupId} AND cate_id=".$cate_id;
		$total = $this->db->get_var($sql);
		if($total > 0){
			$page_arr = $this->showpage($total);
			$sql = "SELECT * FROM `".DB_NAME_GROUP."`.`link` WHERE group_id={$groupId} AND cate_id={$cate_id} ORDER BY `order` ASC LIMIT ".$page_arr['limit'];
			$data = $this->db->get_results($sql, "ASSOC");
			return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>(array)$data,'Page'=>$page_arr['page']);
		}
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>array());
	}
	
	public function linkSave($data){
		$groupId = intval($data['group_id']);
		if($groupId < 1 || !in_array($data['type'], array('txt', 'img'))){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".`link_cate` WHERE group_id={$groupId} AND type='".$data['type']."'";
		$cate_id = $this->db->get_var($sql);
		if(!$cate_id){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$time = time();
		if($data['id']){//修改
			$sql = "UPDATE ".DB_NAME_GROUP.".link set `name`='{$data['name']}',`url`='{$data['url']}',`order`={$data['order']},`logo`='{$data['logo']}',`uptime`={$time} WHERE id=".$data['id']." AND group_id=".$groupId." AND cate_id=".$cate_id;
		}else{
			$sql = "INSERT INTO ".DB_NAME_GROUP.".link(`group_id`, `cate_id`, `name`, `url`, `order`, `uptime`, `logo`) values(".$groupId.",".$cate_id.",'".$data['name']."','".$data['url']."'".",".$data['order'].",".time().",'".$data['logo']."')";
		}
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'保存成功');
		}else{
			return array('Flag'=>101,'FlagString'=>'保存失败');
		}
		exit;
	}
	
	public function linkInfo($id){
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".link WHERE id=".$id;
		$info = $this->db->get_row($sql);
		return array('Flag'=>100,'FlagString'=>'查询成功','Data'=>(array)$info);
	}
	
	public function linkDel($id){
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "DELETE FROM ".DB_NAME_GROUP.".link WHERE id=".$id;
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'删除成功');
		}else{
			return array('Flag'=>101,'FlagString'=>'删除失败');
		}
	}
	
	//检测参数
	private function checkParam($data){
		$groupId = intval($data['group_id']);
		if($groupId < 1 || !in_array($data['type'], array('txt', 'img'))){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		return array('Flag'=>100,'FlagString'=>'参数正确');
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
