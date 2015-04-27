<?php

/**
 *   站内文章接口
 *   文件: join.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
 
class GroupLink{
	
	public function __construct(){
		$this->db = domain::main()->GroupDBConn();
	}
	
	/**
	 *   查询友情链接分类
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回站下友情链接分类
	 */
	public function getLinkCateList($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql="SELECT * FROM ".DB_NAME_GROUP.".link_cate WHERE group_id=$groupId AND is_show=1";
		$list=$this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'获取友情链接分类列表','List'=>$list);
	}
	
	/**
	 *   查询友情链接列表
	 *   @param	int $groupId 站ID
	 *   @param	int $cateId 分类ID
	 *   @return array $array 返回站下友情链接列表
	 */
	public function getLinkList($groupId,$cateId){
		$groupId=intval($groupId);
		$cateId=intval($cateId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$where="group_id=$groupId";
		if($cateId>0){
			$where.=' AND cate_id='.$cateId;
		}
		$sql="SELECT * FROM ".DB_NAME_GROUP.".link WHERE $where ORDER BY `order` ASC";
		$list=$this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'获取友情链接列表','List'=>$list);
	}
	
}