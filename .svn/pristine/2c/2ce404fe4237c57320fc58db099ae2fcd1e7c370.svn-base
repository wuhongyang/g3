<?php
class key{
	protected $db = null;

	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}
	
	function key_list($search, $no_page=false){
		$where = " where 1";
		if($search['name']){
			$where .= " AND `name` = '".$search['name']."'";
		}
		if($search['type'] != -1 && $search['type']){
			$where .= " AND `type` = '".$search['type']."'";
		}
		if($search['status'] != -1 && isset($search['status'])){
			$where .= " AND `status` = '".$search['status']."'";
		}
		if($no_page){
			$sql = "SELECT * FROM ".DB_NAME_BEHAVIOR.".`business_key` ".$where;
			$res = $this->db->get_results($sql, "ASSOC");
			return array("Flag"=>100, "FlagString"=>"查询成功", "List"=>(array)$res);
		}else{
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_BEHAVIOR.".`business_key`".$where;
			$total = $this->db->get_var($sql);
			$page_arr = $this->showPage($total);
			$sql = "SELECT * FROM ".DB_NAME_BEHAVIOR.".`business_key` ".$where." LIMIT ".$page_arr['limit'];
			$res = $this->db->get_results($sql, "ASSOC");
			return array("Flag"=>100, "FlagString"=>"查询成功", "List"=>(array)$res, "Page"=>$page_arr['page']);
		}
	}
	
	function key_save($data){
		if($data['id']){
			return $this->key_update($data);
		}else{
			return $this->key_add($data);
		}
	}
	
	function key_detail($key_id){
		$sql = "SELECT * FROM ".DB_NAME_BEHAVIOR.".`business_key` WHERE id = '".$key_id."';";
		$row = $this->db->get_row($sql);
		return array("Flag"=>100, "FlagString"=>"查询成功", "Row"=>$row);
	}
	
	function compose_list(){
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_BEHAVIOR.".`business_compose_key`";
		$total = $this->db->get_var($sql);
		$page_arr = $this->showPage($total);
		$sql = "SELECT * FROM ".DB_NAME_BEHAVIOR.".`business_compose_key` LIMIT ".$page_arr['limit'];
		$res = $this->db->get_results($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "List"=>(array)$res, "Page"=>$page_arr['page']);
	}
	
	function compose_detail($compose_id){
		$sql = "SELECT * FROM ".DB_NAME_BEHAVIOR.".`business_compose_key` WHERE id = ".$compose_id;
		$row = $this->db->get_row($sql);
		return array("Flag"=>100, "FlagString"=>"查询成功", "Row"=>$row);
	}
	
	function compose_key_list(){
		$sql = "SELECT `engname`,`keys`,`name` FROM ".DB_NAME_BEHAVIOR.".`business_compose_key` where `status` = 1";
		$compose_list = $this->db->get_results($sql, "ASSOC");
		
		$key_ids_arr = array();
		$compose_list_arr = array();
		$compose_key_name = array();
		foreach($compose_list as $k=>$v){
			$key_ids = json_decode($v['keys']);
			$compose_list_arr[$v['engname']] = $key_ids;
			$key_ids_arr = array_merge($key_ids_arr, $key_ids);
			$compose_key_name[$v['engname']] = $v['name'];
		}
		$key_ids_arr_str = join(",", array_unique($key_ids_arr));
		$sql = "SELECT id,`name`,`engname` FROM ".DB_NAME_BEHAVIOR.".`business_key` WHERE id IN (".$key_ids_arr_str.") AND `status` = 1";
		$key_list = $this->db->get_results($sql, "ASSOC");
		$key_arr = array();
		foreach($key_list as $v){
			$key_arr[$v['id']] = array($v['name'], $v['engname']);
		}
		foreach($compose_list_arr as $k=>$v){
			$temp_arr = array(array("value"=>0, "name"=>$compose_key_name[$k]));
			foreach($compose_list_arr[$k] as $id){
				$temp_arr[] = array("value"=>$id, "name"=>$key_arr[$id][0], "engname"=>$key_arr[$id][1]);
			}
			$compose_list_arr[$k] = $temp_arr;
		}
		return array("Flag"=>100, "FlagString"=>"查询成功", "List"=>$compose_list_arr, "ComposeList"=>$compose_list);
	}
	
	function compose_save($data){
		if($data['id']){
			return $this->compose_update($data);
		}else{
			return $this->compose_add($data);
		}
	}
	
	private function compose_update($data){
		$sql = "SELECT `name` FROM ".DB_NAME_BEHAVIOR.".`business_compose_key` WHERE `name` = '".$data['name']."' AND `id` != '".$data['id']."'";
		$name = $this->db->get_var($sql);
		if($name){
			return array("Flag"=>102, "FlagString"=>"已存在相同主键组");
		}
		$sql = "UPDATE ".DB_NAME_BEHAVIOR.".`business_compose_key` SET `name` = '".$data['name']."',`engname` = '".$data['engname']."' ,`desc` = '".$data['desc']."' ,`status` = '".$data['status']."' ,`keys` = '".json_encode($data['key_value'])."' WHERE `id` = '".$data['id']."';";
		$this->db->query($sql);
		return array("Flag"=>100, "FlagString"=>"更新成功");
	}
	
	private function compose_add($data){
		$sql = "SELECT `name` FROM ".DB_NAME_BEHAVIOR.".`business_compose_key` WHERE `name` = '".$data['name']."'";
		$name = $this->db->get_var($sql);
		if($name){
			return array("Flag"=>102, "FlagString"=>"已存在相同主键组");
		}
		$sql = "INSERT INTO ".DB_NAME_BEHAVIOR.".`business_compose_key` (`name`, `engname`, `desc`, `status`, `keys`, `uptime`) VALUES ('".$data['name']."', '".$data['engname']."', '".$data['desc']."', '".$data['status']."', '".json_encode($data['key_value'])."', '".time()."'); ";
		$this->db->query($sql);
		return array("Flag"=>100, "FlagString"=>"添加成功");
	}
	
	private function key_update($data){
		$sql = "SELECT `name` FROM ".DB_NAME_BEHAVIOR.".`business_key` WHERE `name` = '".$data['name']."' AND `id` != '".$data['id']."'";
		$name = $this->db->get_var($sql);
		if($name){
			return array("Flag"=>102, "FlagString"=>"已存在相同主键名称");
		}
		$sql = "UPDATE ".DB_NAME_BEHAVIOR.".`business_key` SET `name` = '".$data['name']."', `engname`='".$data['engname']."' ,`type` = '".$data['type']."' ,`extra` = '".$data['extra']."' ,`status` = '".$data['status']."' WHERE `id` = '".$data['id']."';";
		$done = $this->db->query($sql);
		if($done){
			return array("Flag"=>100, "FlagString"=>"更新成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"更新失败");
		}
		
	}
	
	private function key_add($data){
		$sql = "SELECT `name` FROM kkyoo_behavior.`business_key` WHERE `name` = '".$data['name']."'";
		$name = $this->db->get_var($sql);
		if($name){
			return array("Flag"=>102, "FlagString"=>"已存在相同主键名称");
		}
		$sql = "INSERT INTO ".DB_NAME_BEHAVIOR.".`business_key` (`name`, `engname`, `type`, `extra`, `status`) VALUES ('".$data['name']."', '".$data['engname']."', '".$data['type']."', '".$data['extra']."', '".$data['status']."'); ";
		$done = $this->db->query($sql);
		if($done){
			return array("Flag"=>100, "FlagString"=>"添加成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"添加失败");
		}
	}
	
	private function showPage($total, $perpage = 15) {
		$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
		));
		$page_arr['page'] = $page->show();
		$page_arr['limit'] = $page->limit();
		return $page_arr;
	}
}