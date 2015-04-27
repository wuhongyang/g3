<?php

/*奥点网络媒体互动用户计费管理平台软件
 *模块: 奥点网络媒体互动用户计费管理平台软件 大厅基础模块
 *文件: FootPrint.class.php
 *copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */

class FootPrint {
	const PERPAGE = 27;
	protected $db;
	
	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	//记录历史访问
	public function historyAccess($uin,$roomid){
		$uin = intval($uin);
		$roomid = intval($roomid);
		if($uin<=0 || $roomid<=0) return array('Flag'=>101,'FlagString'=>'参数错误');
		
		$flag = $this->write2DB('tbl_history',$uin,$roomid);
		if($flag > 0){
			return array('Flag'=>100,'FlagString'=>'历史访问成功');
		}
		return array('Flag'=>102,'FlagString'=>'历史访问失败');
	}
	
	//得到历史访问
	public function getHistoryAccess($uin,$group_id){
		$uin = intval($uin);
		$group_id = intval($group_id);
		if($uin<=0 || $group_id<0) return array('Flag'=>101,'FlagString'=>'参数错误');
		
		$result = $this->getResults('tbl_history',$uin,$group_id);
		if($result == -1){
			return array('Flag'=>102,'FlagString'=>'获取历史访问失败');
		}
		include_once __ROOT__.'/api/rooms/library/room_common.class.php';
		$roomCommon=new RoomCommon();
		foreach($result['Result'] as $key=>$val){
			$roomInfo=$roomCommon->get_roominfo($val['room_id']);
			if(!empty($roomInfo)){
				$result['Result'][$key]['RoomInfo']=$roomInfo;
			}
		}
		return array('Flag'=>100,'FlagString'=>'获取历史访问成功','HistoryAccess'=>$result['Result'],'Page'=>$result['Page']);
	}
	
	//记录我的收藏
	public function myFavorite($uin,$roomid){
		$uin = intval($uin);
		$roomid = intval($roomid);
		if($uin<=0 || $roomid<=0) return array('Flag'=>101,'FlagString'=>'参数错误');
		
		$flag = $this->write2DB('tbl_myfavorite',$uin,$roomid);
		if($flag > 0){
			return array('Flag'=>100,'FlagString'=>'我的收藏成功');
		}
		return array('Flag'=>102,'FlagString'=>'我的收藏失败');
	}
	
	//得到我的收藏的房间
	public function getMyFavorite($uin,$group_id){
		$group_id = intval($group_id);
		$uin = intval($uin);
		if($uin<=0 || $group_id<0) return array('Flag'=>101,'FlagString'=>'参数错误');
		
		$result = $this->getResults('tbl_myfavorite',$uin,$group_id);
		if($result == -1){
			return array('Flag'=>102,'FlagString'=>'获取我的收藏失败');
		}
		return array('Flag'=>100,'FlagString'=>'获取我的收藏成功','MyFavorite'=>$result['Result'],'Page'=>$result['Page']);
	}
	
	//从数据库得到记录
	private function getResults($table,$uin,$group_id){
		$time = strtotime('-30 days');
		$where = " WHERE uin={$uin} AND uptime>{$time}";
		if($group_id > 0){
			$where .= " AND group_id={$group_id}";
		}
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_REGION.".{$table} {$where}";
		$count = $this->db->get_var($sql);
		if($count < 1){
			return -1;
		}
		$page = $_GET['page']<1 ? 1 : intval($_GET['page']);
		$start = ($page - 1) * self::PERPAGE;
		$sql = "SELECT room_id FROM ".DB_NAME_REGION.".{$table} {$where} ORDER BY uptime DESC LIMIT {$start},".self::PERPAGE;
		$result = $this->db->get_results($sql,'ASSOC');

		//得到房间信息
		foreach($result as $key => $val){
			$roomInfo = $this->getRoomInfo($val['room_id']);
			//$r = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetNameByRegion','RegionId'=>$roomInfo['region_id'])));
			//$roomInfo['RegionName'] = rtrim($r['SiteName'],'市');
			$result[$key] = $roomInfo;
		}
		$res['Page'] = array('CurPage'=>$page,'Pages'=>floor($count/self::PERPAGE));
		$res['Result'] = $result;
		return $res;
	}
	
	//记录进数据库
	private function write2DB($table,$uin,$roomid){
		if(empty($table) || $uin<=0 || $roomid<=0) return array('Flag'=>101,'FlagString'=>'参数错误');
		
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_REGION.".{$table} WHERE uin={$uin} AND room_id={$roomid}";
		$count = $this->db->get_var($sql);
		if($count > 0){
			$sql = "UPDATE ".DB_NAME_REGION.".{$table} SET uptime=".time()." WHERE uin={$uin} AND room_id={$roomid}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_REGION.".{$table}(uin,room_id,group_id,uptime) VALUES({$uin},{$roomid},{$room_info['group']},".time().")";
		}
		return $this->db->query($sql) ? 1 : 0;
	}
	
	//得到房间详情
	private function getRoomInfo($roomid){
		require_once 'room_common.class.php';
		$room = new RoomCommon();
		return $room->get_roominfo($roomid);
	}
}
