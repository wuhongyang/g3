<?php
class UserGameInfo{
	protected $rank_expire = 180;
	
	function __construct(){
		$this->mongodb = domain::main()->GroupDBConn('mongo');
		$this->cache = cache::connect(config('cache','memcache'));
	}
	
	function getList($data){
		$parent_to_id = array("10114"=>17, "10109"=>19, "10110"=>18);
		$parent_to_limit = array("10114"=>10, "10109"=>30, "10110"=>30);
		$default_room_id = 571615;
		$table_name = 'kkyoo_integral.total_weight';
		
		$roomid = $data['RoomId']?intval($data['RoomId']):$default_room_id;
		$ruleid = $parent_to_id[$data['ParentId']];
		$limit = $parent_to_limit[$data['ParentId']];
		if(!$ruleid){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$query_condition = array(
			"UinId"=>$roomid,
			 "Ruleid"=>$ruleid,
			 '$or'=>array(
							array("ChannelUin"=>array('$gt'=>intval(GUEST_UIN_END))), 
							array("ChannelUin"=>array('$lt'=>intval(GUEST_UIN_START)))
						)
		);
		$result_condition = array(
			'sort'=>array('Weight'=>-1),
			'limit'=>array('rows'=>$limit)
		);
		$field = array(
			"Weight"=>1,
			"ChannelUin"=>1
		);
		
		$result = $this->mongodb->get_results(
				$table_name,
				$query_condition,
				$result_condition,
				$field
		);
		$data = array();
		$i = 0;
		foreach($result as $one){
			$param = array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$one['ChannelUin']));
			$userinfo = httpPOST(KBASIC_API_PATH,$param);
			
			$data[$i]['Uin'] = $one['ChannelUin'];
			$data[$i]['Weight'] = $one['Weight'];
			$data[$i]['Nick'] = $userinfo['Nick'];
			$i++;
		}
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>$data);
	}
	/*
	*全站幸运排行榜
	*/
	public function getLuckyRank($rows=20){
		$result = $this->cache->get("LUCKRANK_{$rows}");
		if(empty($result)){
			$long_info = $this->cache->long_get("LUCKRANK_{$rows}");
			$this->cache->set("LUCKRANK_{$rows}",$long_info,$this->rank_expire);
			$table = 'month_weight';
			$query['Ruleid'] = 21;
			$uptime = (int)date('Ym');
			$query['Uptime'] = $uptime;
			$result = $this->mongodb->get_results('kkyoo_integral.'.$table,$query,array("sort"=>array("Weight"=>-1),'limit'=>array('rows'=>$rows)),array('Weight','UinId'));
			foreach((array)$result as $key=>$value){
				$sInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$value['UinId'])));
				if($sInfo['Flag'] == 100){
					$result[$key]['Nick'] = $sInfo['Nick'];
				}else{
					$result[$key]['Nick'] = $value['UinId'];
				}
			}
			$this->cache->set("LUCKRANK_{$rows}",$result,$this->rank_expire);
		}
		return array('Flag'=>100,'FlagSting'=>'全站幸运排行榜','Result'=>$result);
	}
}