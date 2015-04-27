<?php

/**
 *   群组操作接口
 *   文件: group.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class room
{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct($group_id) {
		$this->db = db::connect(config('database','default'));
		$this->groupMysql = ($group_id==10000) ? db::connect(config('database','default')) : domain::main()->GroupDBConn('mysql', $group_id);
	}
	
	//列表显示
	public function roomsList($data,$roomId){
		if($data['group'] > 0){
			$where .= " AND r.`group`={$data['group']}";
		}
		if($data['keyword'] && !empty($data['keyword'])){
			$where .= ' and r.'.$data['option'].' like "'.$data['keyword'].'%"';
		}
		if($data['maxuser']!='0' && !empty($data['maxuser'])){
			$where .= ' and r.maxuser="'.$data['maxuser'].'"';
		}
		if($data['city_id']>-1){
			$where .= ' and r.city_id="'.$data['city_id'].'"';
		}
		if($data['room_version'] == '1'){
			$where .= " and r.room_version !=''";
		}
		if(!empty($roomId))
			$where .= " and r.id='".$roomId."'";

		$total = $this->groupMysql->get_var('SELECT COUNT(*) FROM '.DB_NAME_NEW_ROOMS.'.rooms as r where true'.$where.';');
		$list=array();
		if($total > 0){
			$page_arr = $this->showPage($total);
			$sql = 'select r.* from '.DB_NAME_NEW_ROOMS.'.rooms as r where true '.$where.' ORDER BY r.date DESC limit '.$page_arr['limit'].';';
			$list = $this->groupMysql->get_results($sql,'ASSOC');
		}
		//$sql = 'select city_id from '.DB_NAME_NEW_ROOMS.'.rooms GROUP BY city_id';
		//$city_ids = $this->groupMysql->get_results($sql,'ASSOC');
		if(empty($roomId)) $list['page'] = $page_arr['page'];
		return empty($roomId) ? array('li'=>$list) : array('li'=>$list[0]);
	}
	
	//冻结
	public function freeze($id){
		//得到当前状态
		$sql = 'select status from '.DB_NAME_NEW_ROOMS.'.rooms where id="'.$id.'"';
		$status = $this->groupMysql->get_var($sql);
		$flagString = ($status=='1') ? '冻结' : '解冻';
		$status = ($status=='1') ? '0' : '1';
		
		//冻结/解冻
		$sql = 'update '.DB_NAME_NEW_ROOMS.'.rooms set status="'.$status.'" where id="'.$id.'"';
		if($this->groupMysql->query($sql)){
			return array('Flag'=>100,'FlagString'=>$flagString.'成功');
		}else{
			return array('Flag'=>101,'FlagString'=>$flagString.'失败');
		}
	}

	//房间绑定室主
	public function bindRoomer($roomid,$uin){
		if($roomid<=0 || $uin<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		//要绑定房间是否有室主
		$sql = "SELECT ownuin FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid} AND `status`>0";
		$ownuin = $this->groupMysql->get_var($sql);
		if($ownuin > 0 && $ownuin != $uin){
			return array('Flag'=>103,'FlagString'=>'房间已经有室主');
		}

		//用户原始房间
		$sql = "SELECT `id` FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE ownuin={$uin} AND `status`>0";
		$id = $this->groupMysql->get_var($sql);

		//把用户ID绑到新房间上
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET ownuin={$uin},`status`=1 WHERE id={$roomid}";
		$rst1 = $this->groupMysql->query($sql);

		//把原始房间的状态改为冻结
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET `status`='0' WHERE `id`={$id}";
		$rst2 = $this->groupMysql->query($sql);

		if($rst1 && $rst2){
			return array('Flag'=>100,'FlagString'=>'绑定室主成功');
		}else{
			return array('Flag'=>102,'FlagString'=>'绑定室主失败');
		}
	}

	public function updateRoomRegion($info){
		if($info['regionId']<0 || $info['province']<1 || $info['city']<1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET province_id={$info['province']},city_id={$info['city']},area_id={$info['area']},region_id={$info['regionId']} WHERE `group`={$info['groupId']}";
		if(!$this->groupMysql->query($sql)){
			return array('Flag'=>102,'FlagString'=>'房间地域更新失败');
		}
		return array('Flag'=>100,'FlagString'=>'房间地域更新成功');
	}
	
	public function roomUpdate($array){
		if($array['Id'] <=0 || $array['Maxuser'] <=0){
			return array("Flag"=>101,"FlagString"=>"参数有误");
		}
		if(!empty($array['RoomVersion']) && !file_exists($_SERVER['DOCUMENT_ROOT'].'/g3_ktv_flash_'.$array['RoomVersion'])){
			return array('Flag'=>103,'FlagString'=>'房间版本号错误');
		}
		if(empty($array['EntertainerQuota'])||$array['EntertainerQuota']<=0){
			$array['EntertainerQuota']=100;
		}
		$sql = "update ".DB_NAME_NEW_ROOMS.".rooms set name='".$array['Name']."',robot_num='".$array['RobotNum']."',room_version='".$array['RoomVersion']."',maxuser=".$array['Maxuser'].",entertainer_quota = ".$array['EntertainerQuota'].",ui_version = ".$array['ui_version']." where id='".$array['Id']."'";
		$result = $this->groupMysql->query($sql);
		//$sql = "update ".DB_NAME_NEW_ROOMS.".tbl_rooms set `sortID`='".$array['SortId']."' where room_id='".$array['Id']."'";
		//$result = $this->groupMysql->query($sql);
		if($result){
			return array('Flag'=>100,"FlagString"=>"修改成功");
		}else{
			return array('Flag'=>102,'FlagString'=>'修改失败');
		}
	}
	
	private function getHostAndPort(){
		return $this->groupMysql->get_row('SELECT host,port FROM '.DB_NAME_NEW_ROOMS.'.tbl_roomhost');
	}

	//房间是否存在
	public function roomExists($roomid){
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid}";
		$count = $this->groupMysql->get_var($sql);
		return intval($count);
	}
	
	public function roomAdd($array){
		if($array['id'] <=0 || $array['region_id'] <=-1){
			return array("Flag"=>101,"FlagString"=>"参数有误");
		}
		if(empty($array['EntertainerQuota'])||$array['EntertainerQuota']<=0){
			$array['EntertainerQuota']=100;
		}
		//判断房间是否存在
		$count = $this->roomExists($array['id']);
		if($count > 0) return array('Flag'=>103,'FlagString'=>'该房间已经存在');
		
		$alias = $this->getHostAndPort();
		$rooms_site = 'sx';
		if(!empty($alias['host']) && !empty($alias['port'])) {
			if(empty($array['name']))
				$array['name'] = $array['id'];
			
			if(!empty($array['room_version']) && !file_exists($_SERVER['DOCUMENT_ROOT'].'/g3_ktv_flash_'.$array['room_version'])){
				return array('Flag'=>103,'FlagString'=>'房间版本号错误');
			}
			$sql1 = 'INSERT INTO '.DB_NAME_NEW_ROOMS.'.rooms(id,province_id,city_id,area_id,region_id,`group`,name,description,host,port,maxuser,date,robot_num,room_version,room_status,publictalkstat,privatetalkstat,entertainer_quota) VALUES ("'.$array['id'].'","'.$array['province'].'","'.$array['city'].'","'.$array['area'].'","'.$array['region_id'].'","'.$array['group'].'","'.$array['name'].'","'.$array['name'].'","'.$alias['host'].'","'.$alias['port'].'","'.$array['maxuser'].'",NOW(),"'.$array['robot_num'].'","'.$array['room_version'].'","'.$array['room_status'].'","'.$array['publictalkstat'].'","'.$array['privatetalkstat'].'","'.$array['EntertainerQuota'].'");';
			//$sql2 = 'INSERT INTO '.DB_NAME_NEW_ROOMS.'.tbl_rooms(room_id,buytime,expiretime,paytime,sortID) VALUES ("'.$array['id'].'","'.time().'","'.(time() + (86400 * $array['expire'])).'","'.time().'","'.$array['sortid'].'");';
			$sql3 = 'INSERT INTO '.DB_NAME_NEW_ROOMS.'.roommanager_tbl(room_id,uin) VALUES ("'.$array['id'].'","'.$array['ownuin'].'");';
			$this->groupMysql->start_transaction();
			$query1 = $this->groupMysql->query($sql1);
			//$query2 = $this->groupMysql->query($sql2);
			$query3 = $this->groupMysql->query($sql3);
			if($query1 && $query3) {
				$this->groupMysql->commit();
				//授予室主角色
				//httpPOST(ROLE_API_PATH,array('extparam'=>array("Tag"=>"SetRole2User","Uin"=>$array['ownuin'],"Role"=>10001)));
				return array('Flag'=>100,'FlagString'=>'开房间成功');
			} else {
				$this->groupMysql->rollback();
				return array('Flag'=>102,'FlagString'=>'开房间失败');
			}
		}
	}

	public function openRoomNoTransaction($array){
		if($array['id'] <=0 || $array['region_id'] <=-1 || $array['ownuin'] <= 0){
			return array("Flag"=>101,"FlagString"=>"参数有误");
		}
		//判断房间是否存在
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$array['id']}";
		$count = $this->groupMysql->get_var($sql);
		if($count > 0) return array('Flag'=>103,'FlagString'=>'该房间已经存在');
		
		$alias = $this->getHostAndPort();
		$rooms_site = 'sx';
		if(!empty($alias['host']) && !empty($alias['port'])) {
			if(empty($array['name']))
				$array['name'] = $array['id'];
			
			if(!empty($array['room_version']) && !file_exists($_SERVER['DOCUMENT_ROOT'].'/g3_ktv_flash_'.$array['room_version'])){
				return array('Flag'=>103,'FlagString'=>'房间版本号错误');
			}
			$sql1 = 'INSERT INTO '.DB_NAME_NEW_ROOMS.'.rooms(id,province_id,city_id,area_id,region_id,`group`,name,description,host,port,maxuser,ownuin,date,robot_num,room_version,room_status,publictalkstat,privatetalkstat) VALUES ("'.$array['id'].'","'.$array['province'].'","'.$array['city'].'","'.$array['area'].'","'.$array['region_id'].'","'.$array['group'].'","'.$array['name'].'","'.$array['name'].'","'.$alias['host'].'","'.$alias['port'].'","'.$array['maxuser'].'","'.$array['ownuin'].'",NOW(),"'.$array['robot_num'].'","'.$array['room_version'].'","'.$array['room_status'].'","'.$array['publictalkstat'].'","'.$array['privatetalkstat'].'");';
			$sql2 = 'INSERT INTO '.DB_NAME_NEW_ROOMS.'.roommanager_tbl(room_id,uin) VALUES ("'.$array['id'].'","'.$array['ownuin'].'");';
			$query1 = $this->groupMysql->query($sql1);
			$query2 = $this->groupMysql->query($sql2);
			if($query1 && $query2) {
				return array('Flag'=>100,'FlagString'=>'开房间成功');
			} else {
				return array('Flag'=>102,'FlagString'=>'开房间失败');
			}
		}
	}
	
	public function setRoomRec($info){
		if($info['Roomid'] > 0){
			if($info['Recstatus'] == 0){//取消推荐
				$sql = "UPDATE `".DB_NAME_NEW_ROOMS."`.`rooms` SET `adminRecmd`='".$info['Recstatus']."',program_list='' WHERE `id`='".$info['Roomid']."'";
			}elseif($info['Recstatus'] == 1){//推荐
				$sql = "SELECT city_id, adminRecmd FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE id = '".$info['Roomid']."'";
				$one = $this->groupMysql->get_row($sql);
				$sql = "SELECT id FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE city_id='".$one['city_id']."' AND adminRecmd= '1'";
				$var = $this->groupMysql->get_var($sql);
				if($var >= 1){
					return array('Flag'=>101, 'FlagString'=>'已经存在一个推荐房间');
				}else{
					if($one['adminRecmd'] == 0){
						$status = "1";
					}else{
						$status = "0";
					}
					$s = urlencode(json_encode($info['Showlist']));
					$sql = "UPDATE `".DB_NAME_NEW_ROOMS."`.`rooms` SET `adminRecmd`='".$info['Recstatus']."',program_list = '".$s."' WHERE `id`='".$info['Roomid']."'";	
				}
			}
			$done = $this->groupMysql->query($sql);
			if($done){
				return array('Flag'=>100, 'FlagString'=>'成功');
			}else{
				return array('Flag'=>101, 'FlagString'=>'失败');
			}
		}else{
			return array('Flag'=>102, 'FlagString'=>'参数错误');
		}
	}
	
	public function getRoomRec($info){
		if($info['Roomid'] > 0){
			$sql = "SELECT * FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id = ".$info['Roomid'];
			$row = $this->groupMysql->get_row($sql,"ASSOC");
			return array('Flag'=>100, 'FlagString'=>'成功','Data'=>$row);
		}else{
			return array('Flag'=>102, 'FlagString'=>'参数错误');
		}
	}
	
	public function getRoomInfo($roomid){
		if($roomid <= 0) return array('Flag'=>101,'FlagString'=>'core.region.room参数错误');
		$sql = "SELECT r.id,r.region_id,r.group,r.name FROM ".DB_NAME_NEW_ROOMS.".rooms AS r WHERE r.id={$roomid}";
		$info = $this->groupMysql->get_row($sql,'ASSOC');
		if($info){
			return array('Flag'=>100,'FlagString'=>'房间信息','Info'=>$info);
		}
		return array('Flag'=>102,'FlagString'=>'获取房间信息失败');
	}
	
	public function roomsUp($info){
		if($info['Id'] <= 0 || $info['Ownuin'] <=0) return array('Flag'=>101,'FlagString'=>'core.region.room参数错误');
		$sql = 'UPDATE '.DB_NAME_NEW_ROOMS.'.rooms SET ownuin = '.$info['Ownuin'].' WHERE id = '.$info['Id'];
		if($this->groupMysql->query($sql)){
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}
		return array('Flag'=>102,'FlagString'=>'操作失败');
	}
    
    public function getRoomsUi($data,$getpic,$is_page=false){
        $where = '1';
		foreach((array)$data as $key=>$val){
			if(is_array($val)){
				$where .= ' AND '.$key.' IN (';
				foreach ($val as $vv) {
					$where .= "{$vv},";
				}
				$where = rtrim($where, ',');
				$where .= ')';
			}else{
				$where .= " AND `{$key}`='{$val}'";
			}
		}
        if($is_page){
        	$sql = "SELECT count(*) FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_rooms_ui WHERE {$where}";
        	$count = $this->db->get_var($sql);
        	if($count > 0){
        		$page_arr = $this->showPage($count);
        		$sql = "SELECT * FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_rooms_ui WHERE {$where} LIMIT {$page_arr['limit']}";
        	}else{
        		return array('Flag'=>101,'FlagString'=>'无信息');
        	}
        }else{
        	$sql = "SELECT * FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_rooms_ui WHERE {$where}";
        }
        $rst = $this->db->get_results($sql,'ASSOC');
        if($getpic){
            foreach($rst as $key=>$val){
                $pic = json_decode($val['pics'],true);
                $sql = "SELECT img_path FROM ".DB_NAME_SYSTEM_CONFIG.".pic_manager WHERE id={$pic['pic_id']}";
                $rst[$key]['pics'] = $this->db->get_var($sql);
            }
        }
        $return = array('Flag'=>100,'FlagString'=>'ok','Result'=>(array)$rst);
        if($is_page){
        	$return['Page'] = $page_arr['page'];
        }
        return $return;
    }
    
    public function updateRoomUi($post){
        $keys = implode('`,`',array_keys($post));
        $vals = implode("','",array_values($post));
        $sql = "REPLACE INTO ".DB_NAME_SYSTEM_CONFIG.".tbl_rooms_ui(`{$keys}`)VALUES('{$vals}')";
        $rst = $this->db->query($sql);
        if(!$rst) return array('Flag'=>101,'FlagString'=>'操作失败');
        return array('Flag'=>100,'FlagString'=>'操作成功');
    }
    
    public function deleteRoomUi($id){
        $sql = "DELETE FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_rooms_ui WHERE id={$id}";
        $rst = $this->db->query($sql);
        if(!$rst) return array('Flag'=>101,'FlagString'=>'删除失败');
        return array('Flag'=>100,'FlagString'=>'删除成功');
    }
    
    public function getUiPackage($data,$getpic){
        $where = '1';
        $page = $data['page'];
        unset($data['page']);
        foreach((array)$data as $key=>$val){
            $where .= " AND `{$key}`='{$val}'";
        }
        if($page){
        	$sql = "SELECT COUNT(*) FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_ui_package WHERE {$where}";
        	$page_arr = $this->showPage($this->db->get_var($sql));
        	
        	$sql = "SELECT * FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_ui_package WHERE {$where} LIMIT ".$page_arr['limit'];
        }else{
        	$sql = "SELECT * FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_ui_package WHERE {$where}";
        }
        $rst = $this->db->get_results($sql,'ASSOC');
        if($getpic){
            foreach($rst as $key=>$val){
                $uinfo = $this->getRoomsUi(array('id'=>$val['ui_id']),true);
                $rst[$key]['pics'] = $uinfo['Result'][0]['pics'];
            }
        }
        if($page){
        	return array('Flag'=>100,'FlagString'=>'ok','Result'=>(array)$rst, 'Page'=>$page_arr['page']);
        }else{
        	return array('Flag'=>100,'FlagString'=>'ok','Result'=>(array)$rst);
        }
    }
    
    function CopyUiPackage($id){
    	$sql = "INSERT INTO ".DB_NAME_SYSTEM_CONFIG.".`tbl_ui_package`(`ui_id`,`name`,`play_media_conf`,`admin_media_conf`,`p2p_media_conf`,`gifts`,`expression`,`stamp`,`func_props`,`game_props`,`flash_games`,`width`,`height`,`status`,`uptime`) (SELECT `ui_id`,(CONCAT(`name`,'_复制')) AS `name`,`play_media_conf`,`admin_media_conf`,`p2p_media_conf`,`gifts`,`expression`,`stamp`,`func_props`,`game_props`,`flash_games`,`width`,`height`,`status`,UNIX_TIMESTAMP() AS `uptime` FROM ".DB_NAME_SYSTEM_CONFIG.".`tbl_ui_package` WHERE id = ".$id.")";
    	if($this->db->query($sql)){
    		return array('Flag'=>100,'FlagString'=>'复制成功');
    	}else{
    		return array('Flag'=>102,'FlagString'=>'复制失败');
    	}
    }
    
    public function updateUiPackage($post){
        $keys = implode('`,`',array_keys($post));
        $vals = implode("','",array_values($post));
        $sql = "REPLACE INTO ".DB_NAME_SYSTEM_CONFIG.".tbl_ui_package(`{$keys}`)VALUES('{$vals}')";
        $rst = $this->db->query($sql);
        if(!$rst) return array('Flag'=>101,'FlagString'=>'操作失败');
		/*if($post['status'] == 0 && $post['id'] > 0){
			$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET ui_version=1 WHERE ui_version={$post['id']}";
			$this->groupMysql->query($sql);
		}*/
        return array('Flag'=>100,'FlagString'=>'操作成功');
    }
    
    public function deleteUiPackage($id){
        $sql = "DELETE FROM ".DB_NAME_SYSTEM_CONFIG.".tbl_ui_package WHERE id={$id}";
        $rst = $this->db->query($sql);
        if(!$rst) return array('Flag'=>101,'FlagString'=>'删除失败');
        return array('Flag'=>100,'FlagString'=>'删除成功');
    }
	
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
