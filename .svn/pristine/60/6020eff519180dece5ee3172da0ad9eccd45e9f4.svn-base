<?php

/**
 *   站活动接口
 *   文件: active.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
 
class GroupActive{
	
	public function __construct(){
		$this->db = domain::main()->GroupDBConn();
	}
	
	/**
	 *   查询站点活动轮播图
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的活动轮播图信息
	 */
	public function getActiveAd($groupId){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".activity WHERE group_id=$groupId AND recommend=1 ORDER BY `order` ASC";
		$list=$this->db->get_results($sql,'ASSOC');
		
		return array('Flag'=>100,'FlagString'=>'轮播图列表','activeAdList'=>$list);
	}
	
	/**
	 *   查询站点活动列表
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的活动列表信息
	 */
	public function getActiveList($groupId,$limit=0){
		$groupId=intval($groupId);
		if($groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT COUNT(*) FROM ".DB_NAME_GROUP.".activity WHERE group_id=$groupId";
		$count=$this->db->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据','activeList'=>array());
		}
		
		$limit=intval($limit);
		if($limit<=0){
			$limit=9;
		}
		$pageArr=$this->showPage($count,$limit,$_SERVER['REDIRECT_URL'].'');
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".activity WHERE group_id=$groupId ORDER BY uptime DESC,id DESC LIMIT ".$pageArr['limit'];
		$list=$this->db->get_results($sql,'ASSOC');
		
		$now=time();
		foreach($list as $key=>$val){
			if(empty($val['players'])){
				$list[$key]['player_num']=0;
			}
			else{
				$players=explode(',',$val['players']);
				$list[$key]['player_num']=count($players);
			}
			//活动状态
			$list[$key]['status']=1;	
			if(strtotime($val['start_time'])>$now){
				$list[$key]['status']=2;
			}
			if((strtotime($val['end_time'])+3600*24)<$now){
				$list[$key]['status']=3;
			}
		}
		
		return array('Flag'=>100,'FlagString'=>'站点活动列表','activeList'=>$list,'page'=>$pageArr['page'],'total'=>$count);
	}
	
	/**
	 *   查询站点活动列表
	 *   @param	int $groupId 站ID
	 *   @return array $array 返回需要查找的活动列表信息
	 */
	public function getActiveListJson($activeIds){
		if(empty($activeIds)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql="SELECT * FROM ".DB_NAME_GROUP.".activity WHERE id IN ($activeIds)";
		$list=$this->db->get_results($sql,'ASSOC');
		
		$now=time();
		foreach($list as $key=>$val){
			//活动状态
			$list[$key]['status']=1;	
			if(strtotime($val['start_time'])>$now){
				$list[$key]['status']=2;
			}
			if((strtotime($val['end_time'])+3600*24)<$now){
				$list[$key]['status']=3;
			}
		}
		
		return array('Flag'=>100,'FlagString'=>'站点活动列表','activeList'=>$list);
	}
	
	/**
	 *   查询站点活动详情
	 *   @param	int $activeId 活动ID
	 *   @return array $array 返回需要查找的活动信息
	 */
	public function getActiveInfo($activeId){
		$activeId=intval($activeId);
		if($activeId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT * FROM ".DB_NAME_GROUP.".activity WHERE id=$activeId";
		$info=$this->db->get_row($sql,'ASSOC');
		
		//参与者
		if(empty($info['players'])){
			$info['player_num']=0;
		}
		else{
			$players=explode(',',$info['players']);
			$info['player_num']=count($players);
			$playList=array();
			foreach($players as $key=>$val){
				//最多显示20个人
				if($key>19){
					break;
				}
				$userInfo=httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val)));
				$playList[]=array(
					'uin'=>$val,
					'nick'=>$userInfo['Nick'],
					'avatar'=>cdn_url(PIC_API_PATH.'/uin/'.$val.'/30/30.jpg')
				);
			}
			$info['playList']=$playList;
		}
		
		//活动房间
		if(empty($info['range'])){
			include_once('group_site.class.php');
			$group_site=new GroupSite();
			$roomList=$group_site->getGroupRoomList(array('groupId'=>$info['group_id']));
			$roomInfo=current($roomList['roomList']);
			$roomInfo['name']='全部房间';
		}
		else{
			include_once(__ROOT__.'/api/rooms/library/room_common.class.php');
			$room=new RoomCommon();
			$roomInfo=$room->get_roominfo($info['range']);
		}
		$info['roomInfo']=$roomInfo;
		
		//活动状态
		$now=time();
		$info['status']=1;
		if(strtotime($info['start_time'])>$now){
			$info['status']=2;
		}
		if((strtotime($info['end_time'])+3600*24)<$now){
			$info['status']=3;
		}
		
		return array('Flag'=>100,'FlagString'=>'活动信息','info'=>$info);
	}
	
	/**
	 *   参与站点活动
	 *   @param	int $activeId 活动ID
	 *   @param	int $uin 用户ID
	 *   @return array $array 返回结果
	 */
	public function joinActive($activeId,$uin){
		$activeId=intval($activeId);
		$uin=intval($uin);
		if($activeId<=0||$uin<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$this->db->start_transaction();
		$sql="SELECT * FROM ".DB_NAME_GROUP.".activity WHERE id=$activeId FOR UPDATE";
		$info=$this->db->get_row($sql,'ASSOC');
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'活动已关闭');
		}
		$now=time();
		if(strtotime($info['start_time'])>$now){
			return array('Flag'=>104,'FlagString'=>'活动还未开始');
		}
		if(strtotime($info['end_time'])+3600*24<$now){
			return array('Flag'=>104,'FlagString'=>'活动已结束');
		}
		
		if(empty($info['players'])){
			$players=$uin;
		}
		else{
			$players=explode(',',$info['players']);
			if(in_array($uin,$players)){
				return array('Flag'=>105,'FlagString'=>'您已经参与了该活动');
			}
			$players[]=$uin;
			$players=implode(',',$players);
		}
		
		$sql="UPDATE ".DB_NAME_GROUP.".activity SET players='$players' WHERE id=$activeId";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>110,'FlagString'=>'操作失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'参与成功！');
	}
	
	/**
	 *   查询活动参与者
	 *   @param	int $activeId 活动ID
	 *   @return array $array 返回需要查找的活动参与者信息
	 */
	public function getActivePlayers($activeId){
		$activeId=intval($activeId);
		if($activeId<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$sql="SELECT players FROM ".DB_NAME_GROUP.".activity WHERE id=$activeId";
		$info=$this->db->get_row($sql,'ASSOC');
		if(empty($info)){
			return array('Flag'=>102,'FlagString'=>'活动已关闭');
		}
		$players=explode(',',$info['players']);
		$count=count($players);
		
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据','playList'=>array());
		}

		$limit=24;
		
		$pageArr=$this->showPage($count,$limit,$_SERVER['REDIRECT_URL'].'');
		
		$page=intval($_GET['page']);
		if($page<=0){
			$page=1;
		}
		$begin=($page-1)*$limit;
		$end=$begin+$limit;
		$players=array_slice($players,$begin,$end);
		
		if(empty($players)){
			return array('Flag'=>100,'FlagString'=>'没有数据','playList'=>array());
		}
		
		$playList=array();
		foreach($players as $key=>$val){
			$userInfo=httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val)));
			$playList[]=array(
				'uin'=>$val,
				'nick'=>$userInfo['Nick'],
				'avatar'=>cdn_url(PIC_API_PATH.'/uin/'.$val.'/30/30.jpg')
			);
		}
		
		return array('Flag'=>100,'FlagString'=>'活动参与者列表','playList'=>$playList,'page'=>$pageArr['page'],'total'=>$count);
	}
	
	//分页
	private function showPage($total,$perpage=20,$url=''){
		if($total>0){
			$page=new extpage(array (
				'total'=>$total,
				'perpage'=>$perpage,
				'url'=>$url
			));
			$page_arr['page']=$page->show();
			$page_arr['limit']=$page->limit();
			unset($page);
		}
		return $page_arr;
	}
	
}