<?php

/*奥点网络媒体互动用户计费管理平台软件
 *模块: 奥点网络媒体互动用户计费管理平台软件 大厅基础模块
 *文件: rooms.class.php
 *copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
require_once 'room_common.class.php';

class rooms extends RoomCommon
{
    protected $expire = 120;
    protected $rank_expire = 180;
    private $mongo;

    function __construct(){
        $this->cache = cache::connect(config('cache','memcache'));
        $this->db = db::connect(config('database','default'));
        $this->mongo = db::connect(config('mongodb','ktv'),'mongo');
    }

    /**
     *   查询站点首页底部信息
     *   @param int $groupId 站ID
     *   @return array $array 返回需要查找的底部信息
     */
	/*
    public function getGroupFoot($groupId=0,$domain=''){
        $groupId=intval($groupId);
        if($groupId<=0&&empty($domain)){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        
        if($groupId>0){
            $where="group_id=$groupId";
        }
        if(!empty($domain)){
            $where="domain='$domain'";
        }
        
        $sql="SELECT * FROM ".DB_NAME_GROUP.".footer WHERE $where";
        $info=$this->db->get_row($sql,'ASSOC');
        
        if(empty($info)){
            return array('Flag'=>102,'FlagString'=>'没有这个站');
        }

        return array('Flag'=>100,'FlagString'=>'成功','footerInfo'=>$info);
    }*/
	
	public function getActivityRooms($param){
		$this->groupMysql = domain::main()->GroupDBConn();
		$gameid = $param['Gameid'];
		$where = $gameid>0? "gameid={$gameid}" : 1;
		$sql = "SELECT rooms FROM ".DB_NAME_REGION.".tbl_interact_rooms WHERE {$where}";
		$rooms = $this->db->get_results($sql,'ASSOC');
		$roomids = array();
		foreach($rooms as $room){
			$roomids = array_merge($roomids,(array)json_decode($room['rooms'],true));
		}
		$roomid = implode(',',array_unique($roomids));
		$sql = "SELECT * FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id IN({$roomid}) ORDER BY hasplay DESC,curip DESC,curuser DESC,id ASC";
		$rooms = $this->groupMysql->get_results($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$rooms);
	}
    
    //用户等级信息
    public function regalLevel($uin,$type=10){
        $uin = intval($uin);
        if($uin <= 0){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
		$rule = new rule($this->mongo);
		$level = $rule->getRuleLevel($uin,0,0,$type,'total');
		return array('Flag'=>100,'FlagString'=>'成功','Regal'=>intval($level));
		
        /*$table_name = 'rank_'.$type.'.total_weight';
        $query_condition = array('UinId'=>$uin);
        $fields = array('Weight');
        $level = $this->mongo->get_var($table_name,$query_condition,$fields);
        return array('Flag'=>100,'FlagString'=>'成功','Regal'=>intval($level));*/
    }
	
	public function getHotGroups(){
		$sql = "SELECT * FROM ".DB_NAME_GROUP.".tbl_groups WHERE recommend=1 ORDER BY id ASC LIMIT 6";
		$result = $this->db->get_results($sql,'ASSOC');
		$this->groupMysql = domain::main()->GroupDBConn();
		$totaluser = $this->groupMysql->get_var("SELECT SUM(curuser) FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE status>0");
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$result,'TotalUser'=>intval($totaluser));
	}
	
	public function getGroupInfo($id,$uin){
		if($id <= 0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT groupid,name,module_id,currency_unit,games,role_order_type,logo,notice,client_version,room_ui FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$id} AND is_use=1 LIMIT 1";
        $result = $this->db->get_row($sql,'ASSOC');
		if(empty($result)){
			return array('Flag'=>102,'FlagString'=>'站不存在');
		}
		if($result['module_id'] > 0){//商城套餐包商品类别详情
			// $sql = 'SELECT * FROM '.DB_NAME_SHOP.'.scheme WHERE id = 1';
			// $row = $this->db->get_row($sql,"ASSOC");
			// $content = json_decode($row['content'],true);
			// foreach($content as $key=>$value){
				$sql = "SELECT * FROM ".DB_NAME_SHOP.".category WHERE id =4 AND `status` = 1";
				$res = $this->db->get_row($sql,'ASSOC');
                if($res){
                    $shop_package = array('pack_name'=>$res['name'],'is_entry_props'=>$res['is_entry_props'],'group_back_config'=>$res['group_back_config'],'list'=>$value);
                }
			// }
			$result['scheme_info'] = $shop_package;
			$business_rule = $this->db->get_results('SELECT user_id_type FROM '.DB_NAME_TPL.'.business_rule WHERE tpl_id = '.$result['module_id'].' AND `status`="1" AND scope = 2','ASSOC');
			foreach($business_rule as $key=>$value){
				$user_type_array = json_decode($value['user_id_type'],true);
				foreach($user_type_array as $kk=>$val){
					if(!empty($val[0])) $business_array[$val[0]] = $val;
				}
			}
			$result['business_array'] = $business_array;
		}
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$result);
	}
	
	public function GetActivityRanks($type,$ruleid,$uptime,$rows=10,$roomid=1398){
		$result = $this->cache->get("ACTIVITY_RANKS_{$uptime}{$type}{$ruleid}{$rows}");
		if(empty($result)){
			$long_info = $this->cache->long_get("ACTIVITY_RANKS_{$uptime}{$type}{$ruleid}{$rows}");
			$this->cache->set("ACTIVITY_RANKS_{$uptime}{$type}{$ruleid}{$rows}",$long_info,$this->rank_expire);
			$query = array();
			if($uptime > 0) $query['Uptime'] = intval($uptime);
			if($roomid > 0) $query['ChannelUin'] = intval($roomid);
			if($ruleid > 0) $query['Ruleid'] = intval($ruleid);
			$table = $type.'_weight';
            $result_query = array('sort'=>array('Weight'=>-1),'limit'=>array('rows'=>$rows));
            $result = $this->mongo->get_results('kkyoo_integral.'.$table,$query,$result_query,array('UinId','Weight'));
			foreach((array)$result as $key=>$value){
				//获取用户信息
				$info = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$value['UinId'])));
				$result[$key]['Nick'] = empty($info['Nick']) ? $value['UinId'] : $info['Nick'];
				//$result[$key]['Weight'] = intval($value['Weight']);
			}
            $this->cache->set("ACTIVITY_RANKS_{$uptime}{$type}{$ruleid}{$rows}",$result,$this->rank_expire);
		}
		$info = array(
			'Flag' => 100,
			'FlagString' => '活动排行榜',
			'Result' => (array)$result
		);


		return $info;
	}
	
    /**
    *财富排行
    *@param $region_id int 地区ID
    *@param $rows int 记录条数
    */
    public function getMoneyRank($region_id,$uptime,$type='week',$rows=10){
        $info = $this->cache->get("ROOMS_getMoneyRank_{$region_id}{$uptime}{$type}{$rows}");
        if(empty($info)){
			$this->groupMysql = domain::main()->GroupDBConn();
            //避免在缓存过期结点上雪崩效应
            $long_info = $this->cache->long_get("ROOMS_getMoneyRank_{$region_id}{$uptime}{$type}{$rows}");
            $this->cache->set("ROOMS_getMoneyRank_{$region_id}{$uptime}{$type}{$rows}",$long_info,$this->rank_expire);
            
            $query = array("Uptime"=>intval($uptime));
            //得到地区所在房间
            if($region_id > 0){
                $sql = "SELECT id FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE region_id='{$region_id}'";
                $result = $this->groupMysql->get_results($sql,'ASSOC');
                foreach((array)$result as $val){
                    if($val['id'] > 0)
                        $roomid_arr[] = intval($val['id']);
                }
                if(empty($roomid_arr)){
                    return array('Flag'=>102,'FlagString'=>'该地区没有房间');
                }
                $query['Roomid'] = array('$in' => $roomid_arr);
            }
            
            $table = $type.'_weight';
            $result = $this->mongo->get_results('rank_1.'.$table,$query,array(),array('Uin','Weight'));
            foreach((array)$result as $rekey => $reval){
                $info[1][$reval['Uin']] += $reval['Weight'];
            }
            //送礼数据
            arsort($info[1]);
            $info[1] = array_slice($info[1],0,$rows,true);
            if(!empty($info[1])){
                foreach ($info[1] as $key => $val) {
                    $send[] = array('uin'=>$key,'money'=>floor($val));
                    $sInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$key)));
                    if($sInfo['Flag'] == 100){
                        $sendInfo[$key] = $sInfo['Nick'];
                    }else{
                        $sendInfo[$key] = $val;
                    }
                }
            }
            $result = $this->mongo->get_results('rank_4.'.$table,$query,array(),array('Uin','Weight'));
            foreach((array)$result as $rekey => $reval){
                $info[4][$reval['Uin']] += $reval['Weight'];
            }
            //收礼数据
            arsort($info[4]);
            $info[4] = array_slice($info[4],0,$rows,true);
            if(!empty($info[4])){
                foreach ($info[4] as $key => $val) {
                    $receive[] = array('uin'=>$key,'money'=>floor($val));
                    $rInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$key)));
                    if($rInfo['Flag'] == 100){
                        $receiveInfo[$key] = $rInfo['Nick'];
                    }else{
                        $receiveInfo[$key] = $val;
                    }
                }
            }
            $info = array(
                'Flag' => 100,
                'FlagString' => '财富排行榜',
                'send' => (array)$send,
                'sendInfo' => (array)$sendInfo,
                'receive' => (array)$receive,
                'receiveInfo' => (array)$receiveInfo
            );
            $this->cache->set("ROOMS_getMoneyRank_{$region_id}{$uptime}{$type}{$rows}",$info,$this->rank_expire);
        }
        return $info;
    }
    
	/*
	*总站消费排行
	*
	*/
	public function consumeRank($uptime,$type,$rows){
		$info = $this->cache->get("CONSUMERANK{$uptime}{$type}{$rows}");
		if(empty($info)){
			$long_info = $this->cache->long_get("CONSUMERANK{$uptime}{$type}{$rows}");
			$this->cache->set("CONSUMERANK{$uptime}{$type}{$rows}",$long_info,$this->rank_expire);
			$query['Ruleid'] = 10;
			if($type != 'total') $query['Uptime'] = intval($uptime);
			$table = $type.'_weight';
            $result_query = array('sort'=>array('Weight'=>-1),'limit'=>array('rows'=>$rows));
            $result = $this->mongo->get_results('kkyoo_integral.'.$table,$query,$result_query,array('UinId','Weight'));
			foreach((array)$result as $key=>$value){
				//获取用户信息
				$info = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$value['UinId'])));
				$result[$key]['Nick'] = empty($info['Nick']) ? $value['UinId'] : $info['Nick'];
				$result[$key]['Weight'] = intval($value['Weight']/10000);
				//前20名 获取富豪等级
				if($key<20){
					$level_info = $this->regalLevel($value['UinId']);
					$result[$key]['Level'] = $level_info['Regal'];
				}
			}
			$info = array(
                'Flag' => 100,
                'FlagString' => '总站消费排行',
                'Result' => (array)$result
            );
            $this->cache->set("ROOMS_artistPopularity_{$uptime}{$type}{$rows}",$result,$this->rank_expire);
		}
		return $info;
	}
	
	
    /**
    * 歌手人气排行
    */  
    public function artistPopularity($region_id,$uptime,$type='week',$row=6){
        $info = $this->cache->get("ROOMS_artistPopularity_{$region_id}{$uptime}{$type}{$row}");
        if(empty($info['Artist'])){
			$this->groupMysql = domain::main()->GroupDBConn();
            //避免在缓存过期结点上雪崩效应
            $long_info = $this->cache->long_get("ROOMS_artistPopularity_{$region_id}{$uptime}{$type}{$row}");
            $this->cache->set("ROOMS_artistPopularity_{$region_id}{$uptime}{$type}{$row}",$long_info,$this->rank_expire);

            if($uptime > 0){
                $query = array("Uptime"=>intval($uptime));
            }
			$query['Ruleid'] = 13;
            //得到地区所在房间
            if($region_id > 0){
                $sql = "SELECT id FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE region_id={$region_id}";
                $result = $this->groupMysql->get_results($sql,'ASSOC');
                foreach((array)$result as $val){
                    if($val['id'] > 0)
                        $roomid_arr[] = intval($val['id']);
                }
                
                if(empty($roomid_arr)){
                    return array('Flag'=>102,'FlagString'=>'该地区没有房间');
                }
                $query['ChannelUin'] = array('$in' => $roomid_arr);
            }
            $table = $type.'_weight';
            $result_query = array('sort'=>array('Weight'=>-1),'limit'=>array('rows'=>$row));
            $result = $this->mongo->get_results('kkyoo_integral.'.$table,$query,$result_query,array('UinId','Weight','ChannelUin'));
            if(!empty($result)){
                $uins = array();
                foreach ($result as $val) {
					$channelinfo = current(getChannelUserInfo($val['UinId'],15));
                    if(empty($channelinfo)){
                        continue; //是否是艺人
                    }
                    if(in_array($val['UinId'],$uins)){
                        continue; //去除重复的
                    }
                    $uins[] = $val['UinId'];
                    //得到用户信息
                    $info = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val['UinId'])));
                    //得到房间信息
                    //$roomInfo = $this->get_roominfo($channelinfo['room_id']);
                    //得到城市名称
                    //$city = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetCityName','CityId'=>$roomInfo['region_id'])));
					$level_info = $this->regalLevel($val['UinId'],13);
                    $artist[] = array('uin'=>$val['UinId'],'money'=>floor($val['Weight']/10000),'online'=>$channelinfo['is_online'],'roomid'=>$channelinfo['room_id'],'city_name'=>rtrim($city['cityName'],'市'),'region_id'=>$roomInfo['region_id'],'level'=>$level_info['Regal']);
                    $artistInfo[$val['UinId']] = empty($info['Nick']) ? $val['UinId'] : $info['Nick'];
                }
            }
            $info = array(
                'Flag' => 100,
                'FlagString' => '歌手人气排行',
                'Artist' => (array)$artist,
                'ArtistInfo' => (array)$artistInfo
            );
            $this->cache->set("ROOMS_artistPopularity_{$region_id}{$uptime}{$type}{$row}",$info,$this->rank_expire);
        }
        return $info;
    }
    
    //房间排行
    public function roomPopularity($region_id,$uptime,$type='week',$rows=6){
        $info = $this->cache->get("ROOMS_roomPopularity_{$region_id}{$uptime}{$type}{$rows}");
        if(empty($info)){
			$this->groupMysql = domain::main()->GroupDBConn();
            //避免在缓存过期结点上雪崩效应
            $long_info = $this->cache->long_get("ROOMS_roomPopularity_{$region_id}{$uptime}{$type}{$rows}");
            $this->cache->set("ROOMS_roomPopularity_{$region_id}{$uptime}{$type}{$rows}",$long_info,$this->rank_expire);
            
            $query = array();
			
            if(intval($uptime) > 0)
                $query = array("Uptime"=>intval($uptime));
            //得到地区所有房间
            if($region_id > 0){
                $sql = "SELECT id,name FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE region_id='{$region_id}' AND `status`>0";
            }else{
                $sql = "SELECT id,name FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE `status`>0";
            }
			$query['Ruleid'] = 8;
            $result = $this->groupMysql->get_results($sql,'ASSOC');
            $roomInfo = array();
            foreach((array)$result as $val){
                $roomInfo[$val['id']] = $val['name'];
            }
            if(empty($roomInfo)){
                return array('Flag'=>102,'FlagString'=>'该地区没有房间');
            }
            $query['ChannelUin'] = array('$in' => array_keys($roomInfo));
            $table = $type.'_weight';
            $result = $this->mongo->get_results('kkyoo_integral.'.$table,$query,array(),array('Weight','ChannelUin'));
            foreach((array)$result as $rekey => $reval){
                $info[$reval['ChannelUin']] += $reval['Weight'];
            }
            arsort($info);
            $info = array_slice($info,0,$rows,true);
            if(!empty($info)){
                $room = array();
                foreach($info as $key => $val) {
                    foreach($roomInfo as $roomid=>$roomname){
                        if($roomid == $key){
                            $r = $this->getRoomInfo($key);
                            $room[$key] = array('uin'=>$key,'money'=>floor($val/10000),'region_id'=>$r['region_id']);
                        }
                    }
                }
            }
            $info = array(
                'Flag' => 100,
                'FlagString' => '房间人气排行',
                'Room' => array_values($room),
                'RoomInfo' => $roomInfo
            );
            $this->cache->set("ROOMS_roomPopularity_{$region_id}{$uptime}{$type}{$rows}",$info,$this->rank_expire);
        }
        return $info;
    }
    
    
    /*热荐房间节目单*/
    // public function getProgramRoom($region_id){
		// $this->groupMysql = domain::main()->GroupDBConn();
        // $sql = "SELECT id,name,ownuin,curuser,maxuser,program_list FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE region_id={$region_id} AND program_list != '' AND `status`>0 LIMIT 1";
		// $room = $this->groupMysql->get_row($sql,'ASSOC');
        // if(empty($room)){
            // return array('Flag'=>101,'FlagString'=>'暂无热荐房间');
        // }else{
            // $room['program_list'] = json_decode(urldecode($room['program_list']),true);
            // foreach($room['program_list']['rule'] as $key=>$val){
                // $actor = json_decode($val,true);
                // $actorinfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$actor['actorid'])));
                // $actor['nick'] = isset($actorinfo['Nick'])? $actorinfo['Nick'] : $actor['actorid'];
                // $room['program_list']['rule'][$key] = $actor;
            // }
            // $ownuin = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$room['ownuin'])));
            // $room['ownnick'] = isset($ownuin['Nick'])? $ownuin['Nick'] : $room['ownuin'];
            // return array('Flag'=>100,'Result'=>$room);
        // }
    // }
    
    function getRecommedRooms($region_id){
		$this->groupMysql = domain::main()->GroupDBConn();
        $region_where = '';
        if($region_id > 0){
            $region_where = " AND a.region_id={$region_id}";
        }
        $sql = "SELECT b.id,b.name,b.host,b.port,b.curuser,b.maxuser,b.group,b.description,b.city_id,b.hasplay  FROM ".DB_NAME_NEW_ROOMS.".recommend AS a LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS b ON a.roomid=b.id WHERE a.type=2 {$region_where} AND a.status=1 ORDER BY b.hasplay DESC,b.curip DESC,b.curuser DESC LIMIT 80";
		$hot = $this->groupMysql->get_results($sql,ASSOC);
		foreach($hot as $key=>$val){
			$sql = "SELECT name FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$val['group']}";
			$hot[$key]['groupname'] = $this->db->get_var($sql);
		}	
        return array('Flag'=>100,'Title'=>'热门房间','HotRooms'=>$hot);
    }
    
    /**
    * 热门房间
    *
    */
    function getHotRooms($region_id,$groupid=0) {
		$this->groupMysql = domain::main()->GroupDBConn();
       /* $r = $this->getRecommedRooms($region_id);
        $rec = $r['HotRooms'];
        $recommend_rooms = array();
        foreach((array)$rec as $key => $val){
            if($key < 2){
                $recommend_rooms[$key] = $val['id'];
            }
        }*/
		$where = '';
        if(intval($region_id) > 0){
            $param['region_id'] = $region_id;
            $where .= " AND a.region_id=".intval($param['region_id']);
        }
		if($groupid > 0){
			$where .= " AND a.`group`={$groupid}";
		}
        /*
        $sql = "SELECT a.id,a.name,a.host,a.port,a.curuser,a.maxuser,a.description,a.city_id,a.hasplay,a.group,g.name AS groupname
                FROM ".DB_NAME_NEW_ROOMS.".rooms AS a,".DB_NAME_GROUP.".tbl_groups AS g WHERE a.status > 0 AND a.group=g.groupid {$where} 
                ORDER BY a.hasplay DESC,a.curip DESC,a.curuser DESC LIMIT 17";*/
        $sql = "SELECT `id`,`name`,`host`,`port`,`curuser`,`maxuser`,`description`,`city_id`,`hasplay`,`group` FROM ".DB_NAME_NEW_ROOMS.".rooms AS a WHERE status>0 {$where} ORDER BY hasplay DESC,curip DESC,curuser DESC LIMIT 17";
        $hot = $this->groupMysql->get_results($sql, ASSOC);
        foreach ((array)$hot as $key => $val) {
            $sql = "SELECT name FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid={$val['group']}";
            $hot[$key]['groupName'] = $this->db->get_var($sql);
        }
        //$hot = $this->db->get_results($sql,'ASSOC');
        //去除推荐的
        /*foreach($hot as $key => $val){
            if(in_array($val['id'], $recommend_rooms)){
                unset($hot[$key]);
            }
        }*/
        //$hot = array_slice($hot,0,15);
        return array('Flag'=>100,'Title'=>'热门房间','HotRooms'=>$hot);
    }
    
    /**
    * 房间列表
    *
    */
    function getRoomsList($param) {
		$this->groupMysql = domain::main()->GroupDBConn();
        if(intval($param['region_id']) > 0){
            $where = " AND a.region_id=".intval($param['region_id']);
        }
        //if($param['sortID'] > 0) $where = " AND b.sortID={$param['sortID']}";
        if(is_numeric($param['keyword'])){
            $where .= " AND (a.id='{$param['keyword']}' OR a.name LIKE '%{$param['keyword']}%')";
        }elseif($param['keyword']){
            $where .= " AND a.name LIKE '%{$param['keyword']}%'";
        }
        $sql = "SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".rooms AS a WHERE a.status > 0 {$where}";
        $total = $this->groupMysql->get_var($sql);
        $limit = $this->getLimit(10,$total,$param['page']);
        $sql =  "SELECT a.id,a.region_id,a.name,a.host,a.port,a.curuser,a.maxuser,a.group,a.description FROM ".DB_NAME_NEW_ROOMS.".rooms AS a
                WHERE a.status > 0 {$where} ORDER BY a.hasplay DESC,a.curip DESC,a.curuser DESC LIMIT {$limit}";
        $rooms = $this->groupMysql->get_results($sql,'ASSOC');
        $page = $this->getPage(10,$total,$param);
        if(!empty($param['keyword'])){
            $title = '搜索 &gt;&gt; '.$param['keyword'];
        }else{
            $title = '热门房间';
        }
        return array_merge(array('Flag'=>100,'Title'=>$title,'RoomsList'=>$rooms),$page);
    }
    
    public function GetRoomsListByArtist($param){
		$this->groupMysql = domain::main()->GroupDBConn();
		$where = " AND c.type=15";
        if(intval($param['region_id']) > 0){
            $sql = "SELECT id,group FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE region_id={$param['region_id']}";
            $result = $this->groupMysql->get_results($sql,'ASSOC');
            foreach((array)$result as $val){
                if($val['id'] > 0)
                    $roomid_arr[] = intval($val['id']);
            }
            $where .= " AND c.room_id IN(".implode(',',$roomid_arr).")";
        }
        if(is_numeric($param['keyword'])){
            $where .= " AND (c.uid='{$param['keyword']}' OR b.nick LIKE '%{$param['keyword']}%')";
        }elseif($param['keyword']){
            $where .= " AND b.nick LIKE '%{$param['keyword']}%'";
        }
        if(!empty($where)){
            $where = ltrim($where, ' AND');
        }
        $sql = "SELECT COUNT(1) FROM ".DB_NAME_PARTNER.".channel_user AS c LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin WHERE {$where}";
        $total = $this->groupMysql->get_var($sql);
        if($total <= 0){return array('Flag'=>102,'FlagString'=>'艺人搜索');}
        $limit = $this->getLimit(10,$total,$param['page']);
        $sql = "SELECT c.room_id FROM ".DB_NAME_PARTNER.".channel_user AS c LEFT JOIN ".DB_NAME_IM.".basic_tbl AS b ON c.uid=b.uin WHERE {$where} LIMIT {$limit}";
        $rooms = $this->groupMysql->get_results($sql,'ASSOC');
        $page = $this->getPage(10,$total,$param);
        //foreach((array)$rooms as $key => $val){
        //    $rooms[$key] = $this->getRoomInfo($val['room_id']);
        //}
        if(empty($param['keyword'])){
            $title = '热门房间';
        }else{
            $title = '搜索 &gt;&gt; '.$param['keyword'];
        }
        if(!empty($rooms)){
            return array_merge(array('Flag'=>100,'FlagString'=>'艺人搜索','Title'=>$title,'RoomsList'=>$rooms),$page);
        }
        return array('Flag'=>102,'FlagString'=>'艺人搜索');
    }
    
    function getRoomInfo($roomid){
		parent::__construct();
		$info = $this->get_roominfo($roomid);
        if(!empty($info)){
			$module_id = domain::main()->GroupKeyVal($info['group'],'module_id');
			$sql = "SELECT * FROM ".DB_NAME_TPL.".media_config WHERE tpl_id={$module_id}";
            $uinfo = $this->db->get_row($sql,'ASSOC');
            $info['play_media_conf']  = $uinfo['play_media_conf'];
            $info['admin_media_conf'] = $uinfo['admin_media_conf'];
            $info['p2p_media_conf']   = $uinfo['p2p_media_conf'];
            if($info['ui_version'] > 0){
				$sql = "SELECT r.files,r.start_skin,r.room_skin,r.layout_file FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_rooms_ui AS r WHERE r.id={$info['ui_version']}";
			}
			$uinfo = $this->db->get_row($sql,'ASSOC');
            $info['start_skin']  = $uinfo['start_skin'];
            $info['room_skin'] = $uinfo['room_skin'];
            $info['layout_file']   = $uinfo['layout_file'];
            $info['width'] = '100%';
            $info['height']   = '100%';
            $info['ui_path'] = ROOMUI_API_PATH."/{$uinfo['files']}";
        }
		return $info;
    }
    
    //导航站热门艺人
    public function hotArtist($json){
		$this->groupMysql = domain::main()->GroupDBConn();
		if($json['Groupid'] > 0){
			$sql = "SELECT a.uid AS uin,a.room_id AS roomid,r.curuser,c.photo FROM ".DB_NAME_PARTNER.".channel_user AS a,".DB_NAME_NEW_ROOMS.".rooms AS r,".DB_NAME_NEW_ROOMS.".recommend AS c
					WHERE a.up_uid={$json['Groupid']} AND a.type=15 AND a.room_id=r.id AND c.uin=a.uid
					ORDER BY r.curuser DESC LIMIT 500";
		}else{
			$sql = "SELECT a.uin,a.roomid,r.curuser,a.photo FROM ".DB_NAME_NEW_ROOMS.".recommend AS a 
					LEFT JOIN ".DB_NAME_NEW_ROOMS.".rooms AS r ON a.roomid=r.id WHERE a.uin>0 AND a.status=1 
					ORDER BY r.curuser DESC LIMIT 500";
        }
		$result = $this->groupMysql->get_results($sql,'ASSOC');
        if($result){
            foreach((array)$result AS $key => $val){
                //得到昵称
                $uInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val['uin'])));
                $result[$key]['Nick'] = empty($uInfo['Nick']) ? $val['uin'] : $uInfo['Nick'];
                
				//得到所属站
                //$sql = "SELECT g.groupid,g.name FROM ".DB_NAME_GROUP.".tbl_groups AS g,".DB_NAME_PARTNER.".channel_user AS c WHERE g.groupid=c.up_uid AND c.uid={$val['uin']} AND type=15";
				//$group_info = $this->db->get_row($sql);
                //$result[$key]['groupid'] = $group_info['groupid'];
                //$result[$key]['groupname'] = $group_info['name'];
				
                //得到靓照
                //$sql = "SELECT user_photo FROM ".DB_NAME_IM.".join_apply WHERE uid={$val['uin']} AND `role_type`=2";
                //$photos = $this->db->get_var($sql);
                //$photos = json_decode($photos,true);
                $result[$key]['photo'] = !empty($val['photo']) ? 'p/'.$val['photo'].'/150/110.jpg' : 'images/user_photo.png';
            }
            return array('Flag'=>100,'FlagString'=>'热门艺人','HotArtist'=>$result);
        }
        return array('Flag'=>102,'FlagString'=>'获取热门艺人失败');
    }
    
    private function getUserInfo($uin){
		$this->groupMysql = domain::main()->GroupDBConn();
        $sql = "SELECT * FROM ".DB_NAME_IM.".basic_tbl WHERE uin={$uin}";
        return $this->groupMysql->get_row($sql);
    }

    protected function getPage($perPage, $total, $param) {
        $param = array_filter($param);
        $curPage = $param['page'] < 1 ? 1 : $param['page'];
        $firstPage = 1;
        $lastPage = ($total % $perPage) == 0 ? floor($total / $perPage) : floor($total / $perPage) + 1;
        $prevPage = ($curPage -1) < $firstPage ? $firstPage : ($curPage -1);
        $nextPage = ($curPage +1) > $lastPage ? $lastPage : ($curPage +1);
        $pageer = '<div class="page">';
        $pageer .= '共有'.$total.'条记录';
        $pageer .= '共'.$lastPage.'页';
        $pageer .= '当前第'.$curPage.'页';
        if($curPage == $firstPage) {
            $pageer .= '<span>首页</span>';
        } else {
            $param['page'] = $firstPage;
            $pageer .= "<a href=\"javascript:ajaxList('".urlencode(json_encode($param))."');\">首页</a>";
        }
        if($curPage <= $firstPage) {
            $pageer .= '<span>上一页</span>';
        } else {
            $param['page'] = $prevPage;
            $pageer .= "<a href=\"javascript:ajaxList('".urlencode(json_encode($param))."');\">上一页</a>";
        }
        if($curPage >= $lastPage) {
            $pageer .= '<span>下一页</span>';
        } else {
            $param['page'] = $nextPage;
            $pageer .= "<a href=\"javascript:ajaxList('".urlencode(json_encode($param))."');\">下一页</a>";
        }
        if($curPage == $lastPage) {
            $pageer .= '<span>末页</span>';
        } else {
            $param['page'] = $lastPage;
            $pageer .= "<a href=\"javascript:ajaxList('".urlencode(json_encode($param))."');\">末页</a>";
        }
        $pageer .= '</div>';
        if($lastPage < 2) $pageer = '';
        $page = array('Page'=>$pageer,'curPage'=>$curPage,'prevPage'=>$prevPage,'nextPage'=>$nextPage,'lastPage'=>$lastPage,'showNum'=>$perPage);
        return $page;
    }

    protected function getLimit($perPage, $total,$page) {
        $curPage = $page < 1 ? 1 : $page;
        $offer = ($curPage -1) * $perPage;
        if ($offer <= 0 || $curPage > $total) {
            $offer = 0;
        }
        return $offer . ',' . $perPage;
    }
}
