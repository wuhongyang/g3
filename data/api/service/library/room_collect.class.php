<?php

/**
 *   微博管理
 *   文件: vdmanage.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class roomCollect
{
	
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db = domain::main()->GroupDBConn();
	}

	//收藏房间
	public function collectRoom($uin,$roomId){
		$uin=intval($uin);
		$roomId=intval($roomId);
		
		if($uin<=0||$roomId<=0){
			return array('Flag'=>101,'FlagString'=>'关键参数错误');
		}
		
		include_once __ROOT__.'/api/rooms/library/room_common.class.php';
		$roomCommon=new RoomCommon();
		$roomInfo=$roomCommon->get_roominfo($roomId);
		if(empty($roomInfo)){
			return array('Flag'=>102,'FlagString'=>'房间不存在');
		}
		
		//include_once __ROOT__.'/api/service/library/pass_manager.class.php';
		//$passManager=new PassManager();
		//$ssoInfo=$passManager->uin2uid($uin);
		$ssoInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$uin)));
		if($ssoInfo['Flag'] != 100){
			return array('Flag'=>103,'FlagString'=>'用户不存在');
		}
		
		$this->db->start_transaction();
		$sql="SELECT uin FROM ".DB_NAME_NEW_ROOMS.".user_collect_rooms WHERE uin=$uin AND room_id=$roomId";
		$info=$this->db->get_var($sql);
		if(!empty($info)){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'已经收藏过该房间');
		}
		$sql="INSERT INTO ".DB_NAME_NEW_ROOMS.".user_collect_rooms (uin,room_id) VALUES ($uin,$roomId)";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>110,'FlagString'=>'操作失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'操作成功');
	}
	
	//取消收藏房间
	public function cancelRoom($uin,$roomId){
		$uin=intval($uin);
		$roomId=intval($roomId);
		
		if($uin<=0||$roomId<=0){
			return array('Flag'=>101,'FlagString'=>'关键参数错误');
		}
		
		$this->db->start_transaction();
		$sql="SELECT uin FROM ".DB_NAME_NEW_ROOMS.".user_collect_rooms WHERE uin=$uin AND room_id=$roomId";
		$info=$this->db->get_var($sql);
		if(empty($info)){
			$this->db->rollback();
			return array('Flag'=>102,'FlagString'=>'还没有收藏过该房间');
		}
		$sql="DELETE FROM ".DB_NAME_NEW_ROOMS.".user_collect_rooms WHERE uin=$uin AND room_id=$roomId";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>110,'FlagString'=>'操作失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'操作成功');
	}
	
	//收藏房间列表
	public function getCollectList($uin,$channelid){
		$uin=intval($uin);
		
		if($uin<=0){
			return array('Flag'=>101,'FlagString'=>'关键参数错误');
		}
		if($channelid > 0){
			$where = " AND room_id = ".$channelid;
		}
		
		$sql="SELECT * FROM ".DB_NAME_NEW_ROOMS.".user_collect_rooms WHERE uin=$uin".$where;
		$roomIds=$this->db->get_results($sql,'ASSOC');
		$roomList=array();
		if(!empty($roomIds) && $channelid <1){
			include_once __ROOT__.'/api/rooms/library/room_common.class.php';
			$roomCommon=new RoomCommon();
			foreach($roomIds as $val){
				$roomInfo=$roomCommon->get_roominfo($val['room_id']);
				if(!empty($roomInfo)){
					$roomList[]=$roomInfo;
				}
			}
		}
		//针对单个房间查询是否已收藏
		if($channelid>1 && !empty($roomIds)){
			return array('Flag'=>100,'FlagString'=>'房间已收藏');
		}elseif($channelid>1 ){
			return array('Flag'=>101,'FlagString'=>'房间未收藏');
		}
		
		return array('Flag'=>100,'FlagString'=>'房间列表','roomList'=>$roomList);
	}
	
	//收藏房间数量
	public function getCollectNum($uin){
		$uin=intval($uin);
		
		if($uin<=0){
			return array('Flag'=>101,'FlagString'=>'关键参数错误');
		}
		
		$sql="SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".user_collect_rooms WHERE uin=$uin";
		$num=$this->db->get_var($sql);
		
		return array('Flag'=>100,'FlagString'=>'房间列表','num'=>$num);
	}
	
}