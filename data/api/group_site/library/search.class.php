<?php

/**
 *   站加入我们文章接口
 *   文件: join.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
 
class VipSearch{
	
	public function __construct(){
        $this->db = domain::main()->GroupDBConn("mysql");
	}
	
	/**
	 *   根据条件查询站内会员
	 *   @param	int $groupId 站ID
	 *   @param	array $data 查询条件数组
	 *   @return array $array 返回查询结果数据
	 */
	public function search($groupId,$data){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$where="group_id=$groupId";
		if($data['keywords']!=''){
			$where.=" AND nick LIKE '%".addslashes($data['keywords'])."%'";
		}
		if(in_array($data['gender'],array(1,2))){
			$where.=" AND gender=".$data['gender'];
		}
		if(intval($data['age_min'])>0){
			$where.=" AND age>=".intval($data['age_min']);
		}
		if(intval($data['age_max'])>0){
			$where.=" AND age<=".intval($data['age_max']);
		}
		if(intval($data['province'])>0){
			$where.=" AND province=".intval($data['province']);
		}
		if(intval($data['city'])>0){
			$where.=" AND city=".intval($data['city']);
		}
		
		$sql="SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl WHERE $where";
		$count=$this->db->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据');
		}
		$pageArr=$this->showPage($count);
		
		$sql="SELECT * FROM ".DB_NAME_IM.".basic_tbl WHERE $where ORDER BY uin DESC LIMIT ".$pageArr['limit'];
		$list=$this->db->get_results($sql,'ASSOC');
		
		return array('Flag'=>100,'FlagString'=>'会员列表','list'=>$list,'page'=>$pageArr['page'],'total'=>$count);
	}
	
	//分页
	private function showPage($total,$perpage=20){
		if($total>0){
			$page=new extpage(array (
				'total'=>$total,
				'perpage'=>$perpage
			));
			$page_arr['page']=$page->show();
			$page_arr['limit']=$page->limit();
			unset($page);
		}
		return $page_arr;
	}
	
}