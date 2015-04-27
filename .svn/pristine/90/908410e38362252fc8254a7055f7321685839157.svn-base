<?php

/**
 *   站加入我们文章接口
 *   文件: join.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
 
class GroupJoin{
	
	public function __construct(){
		$this->db = domain::main()->GroupDBConn();
	}
	
	/**
	 *   查询文章详情
	 *   @param	int $groupId 站ID
	 *   @param	int $type 文章类型
	 *   @return array $array 返回需要查找的文章详情
	 */
	public function getArticleInfo($groupId,$id){
		$groupId=intval($groupId);
		$id=intval($id);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		if($id>0){
			$sql="SELECT * FROM ".DB_NAME_GROUP.".joinus WHERE group_id=$groupId AND id=".$id." AND status=1";
		}
		else{
			$sql="SELECT * FROM ".DB_NAME_GROUP.".joinus WHERE group_id=$groupId AND status=1 LIMIT 1";
		}
		$info=$this->db->get_row($sql,'ASSOC');
		
		//联系方式
		$phone=array();
		$qq=array();
		if(!empty($info['contact'])){
			$info['contact']=json_decode($info['contact']);
			foreach($info['contact'] as $val){
				if(!empty($val->phone)){
					$phone[]['phone']=$val->phone;
				}
				if(!empty($val->qq)){
					$qq[]['qq']=$val->qq;
				}
			}
		}
		if(!empty($phone)){
			$phone[(count($phone)-1)]['end']=1;
		}
		if(!empty($qq)){
			$qq[(count($qq)-1)]['end']=1;
		}
		$info['phone']=$phone;
		$info['qq']=$qq;
		
		return array('Flag'=>100,'FlagString'=>'文章详情','info'=>$info);
	}
	
	/**
	 *   加入我们列表
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回加入我们列表
	 */
	public function getJoinList($groupId){
		$groupId=intval($groupId);
		if($groupId<1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql="SELECT id,role FROM ".DB_NAME_GROUP.".joinus WHERE group_id=$groupId";
		$list=$this->db->get_results($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'获取加入我们列表','List'=>$list);
	}
	
}