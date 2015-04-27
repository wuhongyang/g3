<?php

/**
 *   群组操作接口
 *   文件: props_config.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class props_config
{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db = db::connect(config('database','default'));
	}
	
	//获取三级科目名称
	private function getParentName($parentId){
		return httpPOST(CCS_API_PATH,array("extparam"=>array("Tag"=>"GetParent","ParentId"=>$parentId)));
	}
	
	//列表显示
	public function propsConfigList($array,$id){
		if(!empty($array['parent_name']))
			$where .= ' AND parent_name like "'.$array['parent_name'].'%"';
		if($array['status'] > -1)
			$where .= ' AND status="'.$array['status'].'"';
		if(intval($array['lower']) > 0)
			$where .= ' AND props_money>="'.intval($array['lower']).'"';
		if(intval($array['upper']) > 0)
			$where .= ' AND props_money<="'.intval($array['upper']).'"';
		if($id)
			$where .= ' AND id="'.$id.'"';
		$sql = 'SELECT COUNT(*) FROM '.DB_NAME_SYSTEM_CONFIG.'.props_config WHERE 1 '.$where;
		$count = $this->db->get_var($sql);
		if($count <= 0)	return '';
		$page_arr = $this->showPage($count);
		$sql = 'SELECT * FROM '.DB_NAME_SYSTEM_CONFIG.'.props_config WHERE 1 '.$where.' LIMIT '.$page_arr['limit'].';';
		$list = $this->db->get_results($sql,'ASSOC');
		if(empty($id))
			$list['page'] = $page_arr['page'];
		return empty($id) ? $list : $list[0];
	}
	
	//修改礼物配置
	public function propsConfigUpdate($array){
		//检测参数
		$this->checkParam($array);
		//获取三级科目名称
		$res = $this->getParentName($array['parent_id']);
		$parentName = '';
		if($res['Flag'] == 100) $parentName = $res['Result']['parent_name'];
		$sql = 'UPDATE '.DB_NAME_SYSTEM_CONFIG.'.props_config SET bigcase_id="'.$array['bigcase_id'].'",case_id="'.$array['case_id'].'",parent_id="'.$array['parent_id'].'",parent_name="'.$parentName.'",props_money="'.$array['props_money'].'",tax_percent="'.$array['tax_percent'].'",receive_percent="'.$array['receive_percent'].'",actor_tax="'.$array['actor_tax'].'",status="'.$array['status'].'" WHERE id="'.$array['id'].'"';
		$result = $this->db->query($sql);
		if($result){
			return array('Flag'=>100,'FlagString'=>'修改礼物配置成功');
		}
		return array('Flag'=>102,'FlagString'=>'修改礼物配置失败');
	}
	
	//添加礼物配置
	public function propsConfigAdd($array){
		$this->checkParam($array);
		//获取三级科目名称
		$res = $this->getParentName($array['parent_id']);
		$parentName = '';
		if($res['Flag'] == 100)
			$parentName = $res['Result']['parent_name'];
		$sql = 'INSERT INTO '.DB_NAME_SYSTEM_CONFIG.'.props_config(bigcase_id,case_id,parent_id,parent_name,props_money,tax_percent,receive_percent,status,uptime) VALUES("'.$array['bigcase_id'].'","'.$array['case_id'].'","'.$array['parent_id'].'","'.$parentName.'","'.$array['props_money'].'","'.$array['tax_percent'].'","'.$array['receive_percent'].'","'.$array['status'].'","'.time().'")';
		$result = $this->db->query($sql);
		if($result){
			return array('Flag'=>100,'FlagString'=>'添加礼物配置成功');
		}
		return array('Flag'=>102,'FlagString'=>'添加礼物配置失败');
	}
	
	//检测参数
	private function checkParam($array){
		if(empty($array['bigcase_id']) || empty($array['case_id']) || empty($array['parent_id']) || empty($array['props_money']) || empty($array['tax_percent']) || empty($array['receive_percent']) || empty($array['status']) || empty($array['id'])){
			return array('Flag'=>101,'FlagString'=>'参数不正确');
		}
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


