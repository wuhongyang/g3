<?php

/**
 *   群组操作接口
 *   文件: UserPermission.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class GroupFoot
{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}
	
	public function lists($group_id, $no_page=false){
	    if($group_id){
	       $condition = " AND group_id = ".$group_id;   
	    }
        
		$sql = 'SELECT COUNT(1) FROM '.DB_NAME_GROUP.'.footer WHERE 1 '.$condition;
		$total = $this->db->get_var($sql);
		if($total > 0){
			if($no_page){
				$sql = 'SELECT f.*,g.name FROM '.DB_NAME_GROUP.'.footer f LEFT JOIN '.DB_NAME_GROUP.'.tbl_groups g ON f.group_id=g.groupid WHERE 1 '.$condition;
				$list = $this->db->get_results($sql,'ASSOC');
			}else{
				$page_arr = $this->showPage($total);
				$sql = 'SELECT f.*,g.name FROM '.DB_NAME_GROUP.'.footer f LEFT JOIN '.DB_NAME_GROUP.'.tbl_groups g ON f.group_id=g.groupid WHERE 1 '.$condition.' LIMIT '.$page_arr['limit'];
				$list = $this->db->get_results($sql,'ASSOC');
			}
		}
		return array('Flag'=>100,'FlagString'=>'成功','List'=>$list,'Page'=>$page_arr['page']);
	}	

	//站底部信息详情
	public function info($id){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT f.*,g.name FROM ".DB_NAME_GROUP.".footer f LEFT JOIN ".DB_NAME_GROUP.".tbl_groups g ON f.group_id=g.groupid WHERE f.id={$id}";
		$row = $this->db->get_row($sql,ASSOC);
		if($row){
			return array('Flag'=>100,'FlagString'=>'获取站底部信息','Info'=>$row);
		}
		return array('Flag'=>102,'FlagString'=>'获取站底部信息');
	}

	//添加
	public function insert($data){
		$data['group_id'] = intval($data['group_id']);
		if($data['group_id']<1 || empty($data['domain']) || empty($data['icp']) || empty($data['icp_info'])){
			return array('Flag'=>101,'FlagString'=>'请填写完整的信息');
		}
		$data['domain'] = addslashes($data['domain']);
		$data['icp'] = addslashes($data['icp']);
		$data['icp_info'] = addslashes($data['icp_info']);
		$data['template'] = addslashes($data['template']);
		$data['ext'] = json_encode($data['ext']);
		//站是否存在
		$sql = "SELECT groupid FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$data['group_id']}";
		$group_id = $this->db->get_var($sql);
		if($group_id != $data['group_id']){
			return array('Flag'=>102,'FlagString'=>'不存在该站');
		}
		//是否已经添加过
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".footer WHERE group_id={$data['group_id']}";
		$id = $this->db->get_var($sql);
		if($id > 0){
			return array('Flag'=>103,'FlagString'=>'该站已添加底部信息，不能重复添加');
		}
		$time = time();
		$sql = "INSERT INTO ".DB_NAME_GROUP.".footer(`group_id`,`domain`,`icp`,`icp_info`,`template`,`version`,`ktv_template`,`ktv_version`,`uptime`,`ext`) VALUES({$data['group_id']},'{$data['domain']}','{$data['icp']}','{$data['icp_info']}','{$data['template']}','{$data['version']}','{$data['ktv_template']}','{$data['ktv_version']}',{$time},'{$data['ext']}')";
		if(!$this->db->query($sql)){
			return array('Flag'=>104,'FlagString'=>'添加底部信息失败');
		}
		return array('Flag'=>100,'FlagString'=>'添加底部信息成功');
	}

	//更新
	public function update($id,$data){
		$id = intval($id);
		if($id<1 || empty($data['domain']) || empty($data['icp']) || empty($data['icp_info'])){
			return array('Flag'=>101,'FlagString'=>'请填写完整的信息');
		}
		$data['domain'] = addslashes($data['domain']);
		$data['icp'] = addslashes($data['icp']);
		$data['icp_info'] = addslashes($data['icp_info']);
		$data['ext'] = json_encode($data['ext']);
		$sql = "SELECT id FROM ".DB_NAME_GROUP.".footer WHERE id={$id}";
		if($this->db->get_var($sql) != $id){
			return array('Flag'=>102,'FlagString'=>'该站点不存在');
		}
		$sql = "UPDATE ".DB_NAME_GROUP.".footer SET `domain`='{$data['domain']}',`icp`='{$data['icp']}',`icp_info`='{$data['icp_info']}',`template`='{$data['template']}',`version`='{$data['version']}',`ext`='{$data['ext']}',`ktv_template`='{$data['ktv_template']}',`ktv_version`='{$data['ktv_version']}' WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'编辑底部信息失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑底部信息成功');
	}
	
	//站底部信息详情
	public function syncInfo($id){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT group_id,domain FROM ".DB_NAME_GROUP.".footer WHERE id={$id}";
		$row = $this->db->get_row($sql,ASSOC);
		if($row){
			$domain=array($row['group_id']);
			$rst=domain::main()->set_group_info($domain);
			if(!empty($rst['groupid'])){
				return array('Flag'=>100,'FlagString'=>'同步成功');
			}
		}
		return array('Flag'=>102,'FlagString'=>'同步失败');
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


