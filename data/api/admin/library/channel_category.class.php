<?php

/**
 *   群组操作接口
 *   文件: channel_category.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class ChannelCategory
{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}
	
	//列表
	public function lists($data){
		if(isset($data['status']) && $data['status']>-1){
			$where .= ' AND `status`="'.$data['status'].'"';
		}
		if($data['name']){
			$where .= ' AND `name` LIKE "'.$data['name'].'%"';
		}
		$sql = 'SELECT COUNT(1) FROM '.DB_NAME_PARTNER.'.channel_category WHERE TRUE '.$where;
		$total = $this->db->get_var($sql);
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = 'SELECT * FROM '.DB_NAME_PARTNER.'.channel_category WHERE TRUE '.$where.' LIMIT '.$page_arr['limit'];
			$list = $this->db->get_results($sql,'ASSOC');
		}
		return array('Flag'=>100,'FlagString'=>'成功','li'=>$list,'page'=>$page_arr['page']);
	}
	
	public function add($data){
		if($data['name']=='' || $data['desc']=='')
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = 'INSERT INTO '.DB_NAME_PARTNER.'.channel_category(`name`,`desc`,`rule_id`,`status`) VALUES("'.$data['name'].'","'.$data['desc'].'","'.$data['rule'].'","'.$data['status'].'")';
		if($this->db->query($sql))
			return array('Flag'=>100,'FlagString'=>'添加成功');
		return array('Flag'=>102,'FlagString'=>'添加失败');
	}
	
	public function update($data,$id){
		if($data['name']=='' || $data['desc']=='' || $id<=0)
			return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = 'UPDATE '.DB_NAME_PARTNER.'.channel_category SET `name`="'.$data['name'].'",`desc`="'.$data['desc'].'",`status`="'.$data['status'].'",`rule_id`="'.$data['rule'].'" WHERE id="'.$id.'"';
		if($this->db->query($sql))
			return array('Flag'=>100,'FlagString'=>'修改成功');
		return array('Flag'=>102,'FlagString'=>'修改失败');
	}
	
	//保存
	public function save($data){
		if($data['name']=='' || $data['desc']=='')
			return array('Flag'=>101,'FlagString'=>'参数错误');
		if(intval($data['id']) > 0){
			$sql = 'UPDATE '.DB_NAME_PARTNER.'.channel_category SET `name`="'.$data['name'].'",`desc`="'.$data['desc'].'",`status`="'.$data['status'].'" WHERE id="'.$data['id'].'"';
		}else{
			$sql = 'INSERT INTO '.DB_NAME_PARTNER.'.channel_category(`name`,`desc`,`status`) VALUES("'.$data['name'].'","'.$data['desc'].'","'.$data['status'].'")';
		}
		if($this->db->query($sql))
			return array('Flag'=>100,'FlagString'=>'保存成功');
		return array('Flag'=>102,'FlagString'=>'保存失败');
	}
	
	public function getInfoById($id){
		$sql = 'SELECT * FROM '.DB_NAME_PARTNER.'.channel_category WHERE id="'.$id.'"';
		$result = $this->db->get_row($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$result,'Rules'=>$this->getRules());
	}
	
	private function getRules(){
		include_once dirname(__FILE__).'/rule_define.class.php';
		$rule_define = new RuleDefine();
		return $rule_define->getRuleDefine();
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


