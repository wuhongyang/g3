<?php
class RoomCommon{
	private $db, $attr = "ASSOC";
	
	function __construct(){
		$this->db = domain::main()->GroupDBConn();
	}
	
	//获取成员
	function get_member($table, $room_id, $uin=null){
		$where = '';
		$room_col = $table == 'mike_members'?'roomid':'room_id';
		if(!empty($room_id))
			$where .= " AND `".$room_col."` ='".$room_id."'";
		if(!empty($uin))
			$where .= " AND `uin`=".$uin." LIMIT 1";
		if(!empty($where))
			$where = ' WHERE '.ltrim($where,' AND ');
		//$sql = "SELECT uin FROM ".DB_NAME_NEW_ROOMS.".`".$table."`".$where;
		$sql = "SELECT * FROM ".DB_NAME_NEW_ROOMS.".`".$table."`".$where;
		if(!empty($uin))
			$result = $this->db->get_row($sql,'ASSOC');
		else
			$result = $this->db->get_results($sql, $this->attr);
		return $result;
	}
	
	//搜索提出者
	function get_kick($room_id, $args){
		$id     = $args['id'];
		$ip     = $args['ip'];
		$has_id = false;
		$has_ip = false;
		if($id){
			$sql    = "SELECT kick_id FROM ".DB_NAME_NEW_ROOMS.".`roomkick_tbl` WHERE room_id ='".$room_id."' and kick_id = '".$id."'";
			$has_id = $this->db->get_var($sql);
		}
		if($ip){
			$sql    = "SELECT kick_id FROM ".DB_NAME_NEW_ROOMS.".`roomkick_ip_tbl` WHERE room_id ='".$room_id."' and kick_ip = '".$ip."'";
			$has_ip = $this->db->get_var($sql);
		}
		return $has_id || $has_ip;
	}

//获取房间信息
	function get_roominfo($room_id, $uin = ''){
		//查找
		if($room_id)
			$where .= ' AND id='.$room_id;
		if($uin)
			$where .= ' AND ownuin = '.$uin;
		
		$sql = "SELECT * FROM ".DB_NAME_NEW_ROOMS.".rooms AS a WHERE a.status > 0 {$where} LIMIT 1";
		$rst = $this->db->get_row($sql, "ASSOC");
		if(empty($rst)) return array();
		
		//房间排行榜显示配置
		$rank = json_decode($rst['ranks'],true);
		/*if(!empty($rank)){
			foreach((array)$rank as $key=>$value){
				foreach((array)$value['rules'] as $kk=>$vv){
					$tt++;
					$rst['rank'][$tt]['id'] = $vv['id'];
					$rst['rank'][$tt]['name'] = urldecode($value['name']);
				}
			}
		}*/
		if(!empty($rank)){
			foreach((array)$rank as $key=>$value){
				$tt++;
				$rst['rank'][$tt]['id'] = $value['Ruleid'];
				$rst['rank'][$tt]['name'] = urldecode($value['name']);
				$rst['rank'][$tt]['Rows'] = $value['Rows'];
			}
		}
		
		//过滤过期守护艺人
		$guardian = (array)json_decode($rst['guardian'],true);
		$curdate = date('Y-m-d');
		foreach((array)$guardian as $key=>$val){
			if($val['Start'] > $curdate || $val['End'] < $curdate || $val['Start'] > $val['End']){
				unset($guardian[$key]);
			}else{
                $userinfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val['Uin'])));
                $guardian[$key]['UinNick'] = $userinfo['Nick'];
            }
			unset($guardian['Start'],$guardian['End']);
            
		}
		$rst['guardian'] = array_values($guardian);
		unset($rst['province_id'],$rst['city_id'],$rst['area_id'],$rst['date'],$rst['ranks']);
		return $rst;
	}
	
	function set_attr($attr){
		$this->attr = $attr;
	}
}