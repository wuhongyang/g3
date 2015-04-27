<?php
class CommodityCategory{

	protected $db;
	
	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	public function lists($data){
		$where = '';
		foreach ((array)$data as $key => $val) {
			$where .= " AND `{$key}`='{$val}'";
		}
		if(!empty($where)){
			$where = ' WHERE '.ltrim($where, ' AND');
		}
		$sql = 'SELECT * FROM '.DB_NAME_SHOP.'.category '.$where.' ORDER BY uptime ASC';
		$list = $this->db->get_results($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'商品类别列表','List'=>$list);
	}
	
	public function info($id){
		$id = intval($id);
		$sql = "SELECT * FROM ".DB_NAME_SHOP.".category WHERE id={$id}";
		$info = $this->db->get_row($sql, ASSOC);
		if(empty($info)){
			return array('Flag'=>101,'FlagString'=>'获取商品类别详情失败');
		}
		return array('Flag'=>100,'FlagString'=>'获取商品类别详情成功','Info'=>$info);
	}

	public function add($data){
		$data = $this->filterParam($data);
		$time = time();
		$sql = "INSERT INTO ".DB_NAME_SHOP.".category(`name`,`is_entry_props`,`group_back_config`,`status`,`uptime`) VALUES('{$data['name']}',{$data['is_entry_props']},{$data['group_back_config']},{$data['status']},{$time})";
		if(!$this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'添加商品类别失败');
		}
		return array('Flag'=>100,'FlagString'=>'添加商品类别成功');
	}

	public function edit($id,$data){
		$id = intval($id);
		if($id < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$data = $this->filterParam($data);
		$sql = "UPDATE ".DB_NAME_SHOP.".category SET `name`='{$data['name']}',`is_entry_props`={$data['is_entry_props']},`group_back_config`={$data['group_back_config']},`status`={$data['status']} WHERE id={$id}";
		if(!$this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'编辑商品类别失败');
		}
		return array('Flag'=>100,'FlagString'=>'编辑商品类别成功');
	}

	private function filterParam($data){
		$data['name'] = addslashes(htmlspecialchars(trim($data['name'])));
		$data['is_entry_props'] = intval($data['is_entry_props']);
		$data['group_back_config'] = intval($data['group_back_config']);
		$data['status'] = intval($data['status']);
		return $data;
	}
}
