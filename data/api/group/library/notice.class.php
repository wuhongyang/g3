<?php
 
class Notice{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= domain::main()->GroupDBConn();
	}

	//公告列表
	public function noticeList($groupId,$data){
		$groupId = intval($groupId);
		if($groupId < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		//第一次访问把默认的初始化进去
		if($this->getByGroup($groupId) < 3){ 
			$this->db->start_transaction();
			$info = array('group_id'=>$groupId,'title'=>'帮助中心','category'=>1,'is_default'=>1);
			$rst = $this->noticeAdd($info);
			if($rst['Flag'] != 100){
				$this->db->rollback();
				return array('Flag'=>102,'FlagString'=>'异常');
			}
			$info = array('group_id'=>$groupId,'title'=>'关于我们','category'=>3,'is_default'=>1);
			$rst = $this->noticeAdd($info);
			if($rst['Flag'] != 100){
				$this->db->rollback();
				return array('Flag'=>102,'FlagString'=>'异常');
			}
			$info = array('group_id'=>$groupId,'title'=>'公告中心','category'=>2,'is_default'=>1);
			$rst = $this->noticeAdd($info);
			if($rst['Flag'] != 100){
				$this->db->rollback();
				return array('Flag'=>102,'FlagString'=>'异常');
			}
			$this->db->commit();
		}
		return $this->show($groupId,$data);
	}

	//公告列表
	private function show($groupId,$data){
		$where = '';
		if($data['category'] > 0){
			$where .= ' AND `category`='.intval($data['category']);
		}
		if(!empty($data['title'])){
			$where .= " AND `title` LIKE '{$data['title']}%'";
		}
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_GROUP.".`notice` WHERE group_id={$groupId} {$where}";
		$total = $this->db->get_var($sql);
		$list = array();
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = "SELECT * FROM ".DB_NAME_GROUP.".`notice` WHERE group_id={$groupId} {$where} ORDER BY `is_default` DESC,`uptime` DESC LIMIT {$page_arr['limit']}";
			$list = $this->db->get_results($sql,ASSOC);
		}
		return array('Flag'=>100,'FlagString'=>'获取公告','List'=>$list,'Page'=>$page_arr['page']);
	}

	//添加公告
	public function noticeAdd($data){
		$correct = $this->checkParam($data);
		if($correct['Flag'] != 100){
			return $correct;
		}
		$time = time();
		$data = $this->filterParam($data);
		$sql = "INSERT INTO ".DB_NAME_GROUP.".`notice`(`group_id`,`title`,`category`,`content`,`is_default`,`uptime`,`keywords`) VALUES({$data['group_id']},'{$data['title']}',{$data['category']},'{$data['content']}',{$data['is_default']},{$time}, '{$data['keywords']}')";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'添加失败');
		}
		return array('Flag'=>100,'FlagString'=>'添加成功');
	}

	//编辑公告
	public function noticeUpdate($id,$data){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$correct = $this->checkParam($data);
		if($correct['Flag'] != 100){
			return $correct;
		}
		$info = $this->noticeInfo($id,$data['group_id']);
		$info = $info['Info'];
		if(empty($info)){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		$time = time();
		$data = $this->filterParam($data);
		$sql = "UPDATE ".DB_NAME_GROUP.".`notice` SET `title`='{$data['title']}',`content`='{$data['content']}',`category`={$data['category']},`uptime`={$time},`keywords`='{$data['keywords']}' WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>104,'FlagString'=>'更新失败');
		}
		return array('Flag'=>100,'FlagString'=>'更新成功');
	}

	public function noticeDel($id,$group_id){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$info = $this->noticeInfo($id,$group_id);
		$info = $info['Info'];
		if(empty($info)){
			return array('Flag'=>102,'FlagString'=>'数据异常');
		}
		if($info['is_default'] == 1){
			return array('Flag'=>103,'FlagString'=>'默认项不能删除');
		}
		$sql = "DELETE FROM ".DB_NAME_GROUP.".`notice` WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>104,'FlagString'=>'删除失败');
		}
		return array('Flag'=>100,'FlagString'=>'删除成功');
	}

	//公告详情
	public function noticeInfo($id,$group_id){
		$id = intval($id);
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".`notice` WHERE id={$id} AND group_id={$group_id}";
		$info = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'获取公告详情','Info'=>$info);
	}

	//站下的公告数量
	private function getByGroup($group_id){
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_GROUP.".`notice` WHERE group_id={$group_id}";
		$count = $this->db->get_var($sql);
		return intval($count);
	}

	//过滤参数
	private function filterParam($data){
		$data['title'] = addslashes($data['title']);
		$data['content'] = addslashes($data['content']);
		$data['category'] = intval($data['category']);
		$data['is_default'] = intval($data['is_default']);
		$data['keywords'] = addslashes($data['keywords']);
		return $data;
	}

	//检测参数
	private function checkParam($data){
		if($data['group_id'] < 1){
			return array('Flag'=>101,'FlagString'=>'参数异常');
		}
		if(empty($data['title'])){
			return array('Flag'=>101,'FlagString'=>'标题不能为空');
		}
		if(!in_array($data['category'], array(1,2,3))){
			return array('Flag'=>101,'FlagString'=>'请选择分类');
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
