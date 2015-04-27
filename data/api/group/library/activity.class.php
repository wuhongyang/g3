<?php
 
class Activity{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db = domain::main()->GroupDBConn();
	}

	public function activityListNoPage($groupId,$where){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if(is_array($where)){
			foreach ($where as $key => $val) {
				$condition = " AND `{$key}` {$val['operate']} '{$val['value']}'";
			}
		}
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`activity` WHERE group_id={$groupId} {$condition} ORDER BY `uptime` DESC";
		$list = $this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'获取活动','List'=>$list);
	}

	//活动列表
	public function activityList($groupId){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_GROUP.".`activity` WHERE group_id={$groupId}";
		$total = $this->db->get_var($sql);
		$list = array();
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = "SELECT * FROM ".DB_NAME_GROUP.".`activity` WHERE group_id={$groupId} ORDER BY `recommend` DESC,`order` DESC LIMIT {$page_arr['limit']}";
			$list = $this->db->get_results($sql,ASSOC);
		}
		return array('Flag'=>100,'FlagString'=>'获取活动','List'=>$list,'Page'=>$page_arr['page']);
	}

	//添加活动
	public function activityAdd($data){
		/*$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);*/
		$correct = $this->checkParam($data);
		if($correct['Flag'] != 100){
			return $correct;
		}
		$time = time();
		$data['title'] = addslashes($data['title']);
		$data['content'] = addslashes($data['content']);
		$sql = "INSERT INTO ".DB_NAME_GROUP.".`activity`(`group_id`,`title`,`content`,`range`,`start_time`,`end_time`,`image`,`uptime`) VALUES({$data['groupId']},'{$data['title']}','{$data['content']}','{$data['roomid']}','{$data['start_time']}','{$data['end_time']}','{$data['image']}',{$time})";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'活动添加失败');
		}
		return array('Flag'=>100,'FlagString'=>'活动添加成功');
	}

	//编辑活动
	public function activityUpdate($id,$data){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'活动不存在');
		}
		/*$data['start_time'] = strtotime($data['start_time']);
		$data['end_time'] = strtotime($data['end_time']);*/
		$correct = $this->checkParam($data);
		if($correct['Flag'] != 100){
			return $correct;
		}
		$info = $this->activityInfo($id);
		$info = $info['Info'];
		if(empty($info)){
			return array('Flag'=>102,'FlagString'=>'不存在该活动');
		}
		if($info['group_id'] != $data['groupId']){
			return array('Flag'=>103,'FlagString'=>'数据异常');
		}
		$data['title'] = addslashes($data['title']);
		$data['content'] = addslashes($data['content']);
		$sql = "UPDATE ".DB_NAME_GROUP.".`activity` SET `title`='{$data['title']}',`content`='{$data['content']}',`range`={$data['roomid']},`start_time`='{$data['start_time']}',`end_time`='{$data['end_time']}',`image`='{$data['image']}' WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>104,'FlagString'=>'活动更新失败');
		}
		return array('Flag'=>100,'FlagString'=>'活动更新成功');
	}

	//推荐/不推荐
	public function activityRecommend($id,$groupId){
		$id = intval($id);
		$groupId = intval($groupId);
		if($id < 1 || $groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$info = $this->activityInfo($id);
		$info = $info['Info'];
		if(empty($info)){
			return array('Flag'=>102,'FlagString'=>'不存在该活动');
		}
		if($info['group_id'] != $groupId){
			return array('Flag'=>103,'FlagString'=>'数据异常');
		}
		$recommend = $info['recommend']=='1' ? '0' : '1';
		if($recommend == '1'){
			$sql = "SELECT COUNT(1) FROM ".DB_NAME_GROUP.".`activity` WHERE group_id={$groupId} AND `recommend`=1";
			$count = $this->db->get_var($sql);
			if($count >= 5){
				return array('Flag'=>104,'FlagString'=>'最多能推荐5个活动轮播图');
			}
		}
		$desc = $recommend=='1' ? '' : '不';
		$set = '';
		if($recommend == '1'){
			$set = ",`order`=1";
		}
		$sql = "UPDATE ".DB_NAME_GROUP.".`activity` SET `recommend`='{$recommend}'{$set} WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>105,'FlagString'=>"活动设为{$desc}推荐失败");
		}
		return array('Flag'=>100,'FlagString'=>"活动设为{$desc}推荐成功");
	}

	//活动轮播图排序
	public function activityOrder($data){
		if(empty($data) || !is_array($data)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		foreach ($data as $key => $val) {
			$id_array = explode("_", $key);
			$id = intval(trim($id_array[1]));
			if(intval($val) < 1){
				$info = $this->activityInfo($id);
				return array('Flag'=>102,'FlagString'=>"“{$info['Info']['title']}” 排序值错误");
			}
			$sqls[] = "UPDATE ".DB_NAME_GROUP.".`activity` SET `order`={$val} WHERE id={$id}";
		}
		foreach ($sqls as $sql) {
			$rst = $this->db->query($sql);
		}
		if(!$rst){
			return array('Flag'=>102,'FlagString'=>'活动轮播图排序失败');
		}
		return array('Flag'=>100,'FlagString'=>'活动轮播图排序成功');
	}

	public function activityInfo($id){
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`activity` WHERE id={$id}";
		$info = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'获取活动详情','Info'=>$info);
	}

	public function activityDel($data){
		$data = array_map('intval', $data);
		if($data['id'] < 1 || $data['group_id'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "DELETE FROM ".DB_NAME_GROUP.".`activity` WHERE id={$data['id']} AND group_id={$data['group_id']}";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'服务器错误，删除失败');
		}
		return array('Flag'=>100,'FlagString'=>'删除成功');
	}

	private function checkParam($data){
		if($data['groupId'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数异常');
		}
		if(empty($data['title'])){
			return array('Flag'=>101,'FlagString'=>'活动标题不能为空');
		}
		if(empty($data['start_time'])){
			return array('Flag'=>101,'FlagString'=>'活动开始时间不能为空');
		}
		if(empty($data['end_time'])){
			return array('Flag'=>101,'FlagString'=>'活动结束时间不能为空');
		}
		if(empty($data['content'])){
			return array('Flag'=>101,'FlagString'=>'活动规则描述不能为空');
		}
		if(empty($data['image'])){
			return array('Flag'=>101,'FlagString'=>'活动宣传图不能为空');
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
