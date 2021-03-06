<?php
 
class Image{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}

	//列表
	public function imageList($groupId){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT id,group_id,name,img,link,`status`,uptime FROM ".DB_NAME_GROUP.".`image` WHERE `group_id`={$groupId} ORDER BY `order` ASC";
		$list = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'图片模块设置列表','List'=>$list);
	}

	//详情
	public function imageInfo($id,$group_id){
		$id = intval($id);
		$group_id = intval($group_id);
		if($id < 1 || $group_id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`image` WHERE `id`={$id} AND group_id={$group_id}";
		$row = $this->db->get_row($sql,ASSOC);
		if(empty($row)){
			return array('Flag'=>102,'FlagString'=>'图片模块设置详情');
		}
		return array('Flag'=>100,'FlagString'=>'图片模块设置详情','Info'=>$row);
	}

	//添加
	public function imageAdd($data){
		$data = $this->filterParam($data);
		$check = $this->checkParam($data);
		if($check['Flag'] != 100){
			return $check;
		}
		$time = time();
		$this->db->start_transaction();
		$sql = "INSERT INTO ".DB_NAME_GROUP.".`image`(`group_id`,`name`,`img`,`link`,`uptime`) VALUES({$data['group_id']},'{$data['name']}','{$data['img']}','{$data['link']}',{$time})";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'添加失败');
		}
		$order = $this->db->insert_id();
		$sql = "UPDATE ".DB_NAME_GROUP.".`image` SET `order`={$order} WHERE id={$order}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'添加失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'添加成功');
	}

	//编辑
	public function imageEdit($id,$data){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$data = $this->filterParam($data);
		$check = $this->checkParam($data);
		if($check['Flag'] != 100){
			return $check;
		}
		$info = $this->imageInfo($id,$data['group_id']);
		if($info['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'非法数据');
		}
		$sql = "UPDATE ".DB_NAME_GROUP.".`image` SET `name`='{$data['name']}',`img`='{$data['img']}',`link`='{$data['link']}' WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'编辑失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑成功');
	}

	//可见不可见
	public function visible($id,$group_id){
		$id = intval($id);
		$group_id = intval($group_id);
		$info = $this->imageInfo($id,$group_id);
		if($info['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		$status = $info['Info']['status'];
		$status = abs($status - 1);
		$sql = "UPDATE ".DB_NAME_GROUP.".image SET `status`={$status}  WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'设置失败');
		}
		return array('Flag'=>100,'FlagString'=>'设置成功');
	}

	//删除
	public function imageDel($id,$group_id){
		$id = intval($id);
		$group_id = intval($group_id);
		$info = $this->imageInfo($id,$group_id);
		if($info['Flag'] != 100){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		$sql = "DELETE FROM ".DB_NAME_GROUP.".`image` WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'删除失败');
		}
		return array('Flag'=>100,'FlagString'=>'删除成功');
	}

	//排序
	public function order($id,$group_id,$type){
		$id = intval($id);
		$group_id = intval($group_id);
		if ($id < 1 || $group_id < 1) {
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if($type == 1){
			return $this->upMove($id,$group_id);
		}else{
			return $this->downMove($id,$group_id);
		}
	}

	private function upMove($id,$group_id){
		//当前操作分类的排序值
		$info = $this->imageInfo($id,$group_id);
		$order = $info['Info']['order'];
		if($order < 1){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		//得到要交换的id,order排序值
		$sql = "SELECT `id`,`order` FROM ".DB_NAME_GROUP.".`image` WHERE group_id={$group_id} AND `order`<{$order} ORDER BY `order` DESC";
		$info = $this->db->get_row($sql,ASSOC);
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'不能上移');
		}
		$source = array('id'=>$id,'order'=>$order);
		return $this->swap($source,$info);
	}

	private function downMove($id,$group_id){
		//当前操作分类的排序值
		$info = $this->imageInfo($id,$group_id);
		$order = $info['Info']['order'];
		if($order < 1){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		//得到要交换的id,order排序值
		$sql = "SELECT `id`,`order` FROM ".DB_NAME_GROUP.".`image` WHERE group_id={$group_id} AND `order`>{$order} ORDER BY `order` ASC";
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
		$sql = "UPDATE ".DB_NAME_GROUP.".`image` SET `order`={$dest['order']} WHERE id={$source['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'移动失败');
		}

		$sql = "UPDATE ".DB_NAME_GROUP.".`image` SET `order`={$source['order']} WHERE id={$dest['id']}";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'移动失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'移动成功');
	}

	private function checkParam($data){
		if($data['group_id'] < 1){
			return array('Flag'=>101,'FlagString'=>'数据异常');
		}
		if(empty($data['name'])){
			return array('Flag'=>101,'FlagString'=>'名称不能为空');
		}
		if(empty($data['img'])){
			return array('Flag'=>101,'FlagString'=>'请先上传图片');
		}
		if(!empty($data['link']) && !preg_match('/^http:\/\//', $data['link'])){
			return array('Flag'=>101,'FlagString'=>'链接必须以http://开头');
		}
		return array('Flag'=>100,'FlagString'=>'参数正确');
	}

	private function filterParam($data){
		$data['group_id'] = intval($data['group_id']);
		$data['name'] = addslashes(htmlspecialchars($data['name']));
		if(!empty($data['link'])){
			$data['link'] = addslashes($data['link']);
		}
		return $data;
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
