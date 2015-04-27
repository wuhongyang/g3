<?php

/**
 *   站内文章接口
 *   文件: join.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
 
class GroupNotice{
	
	public function __construct(){
		$this->db = domain::main()->GroupDBConn();
	}
	
	/**
	 *   查询文章详情
	 *   @param	int $groupId 站ID
	 *   @param	int $type 文章类型
	 *   @return array $array 返回需要查找的文章详情
	 */
	public function getNoticeInfo($groupId,$type,$id){
		$groupId=intval($groupId);
		$id=intval($id);
		if($groupId<=0||(!in_array($type,array('help','about_us','notice'))&&$id<=0)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$types=array('help'=>1,'notice'=>2,'about_us'=>3);
		
		if($id>0){
			$sql="SELECT * FROM ".DB_NAME_GROUP.".notice WHERE id=$id AND category=".$types[$type];
		}
		else{
			$sql="SELECT * FROM ".DB_NAME_GROUP.".notice WHERE group_id=$groupId AND category=".$types[$type]." ORDER BY id ASC LIMIT 1";
		}

		$info=$this->db->get_row($sql,'ASSOC');
		
		return array('Flag'=>100,'FlagString'=>'文章详情','info'=>$info);
	}
	
}