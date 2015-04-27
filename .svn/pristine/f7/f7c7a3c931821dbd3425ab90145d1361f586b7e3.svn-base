<?php
class RoomSongs {

	function __construct($param){
		$this->db = domain::main()->GroupDBConn();
		$this->param = $param;
	}
	
	function listSong($page, $page_size){
		$page_size 	= $page_size?$page_size:5;
		$page 		= $page?$page:1;
		
		$offset 		= ($page-1)*$page_size;
		$sql 			= "SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".`song` WHERE group_id = ".$this->param['GroupId']." AND room_id = ".$this->param['ChannelId']." AND uin = ".$this->param['TargetUin'];
		$count_of_song 	= $this->db->get_var($sql);
		
		$total_page 	= ceil($count_of_song/$page_size);
		$sql 			= "SELECT * FROM ".DB_NAME_NEW_ROOMS.".`song` WHERE group_id = ".$this->param['GroupId']." AND room_id = ".$this->param['ChannelId']." AND uin = ".$this->param['TargetUin']." ORDER by id LIMIT ".$offset.", ".$page_size;
		$page_song_data = $this->db->get_results($sql, "ASSOC");
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$page_song_data, "Total"=>$total_page, "Count"=>$count_of_song);
	}
	
	function addSong($name, $author, $add_only){
		$name   = htmlspecialchars(addslashes($name));
		$author = htmlspecialchars(addslashes($author));
		
		$sql   = "SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".`song` WHERE group_id = ".$this->param['GroupId']." AND room_id = ".$this->param['ChannelId']." AND uin = ".$this->param['Uin']." AND `name` = '".$name."' AND `author` = '".$author."'";
		$exist = $this->db->get_var($sql);
		if($exist){
			return array("Flag"=>103, "FlagString"=>"歌曲已存在");
		}
		
		$sql  = "INSERT INTO ".DB_NAME_NEW_ROOMS.".`song` (`group_id`, `room_id`, `uin`, `name`, `author`) VALUES ('".$this->param['GroupId']."', '".$this->param['ChannelId']."', '".$this->param['Uin']."', '".$name."', '".$author."'); ";
		$done = $this->db->query($sql);
		
		if($done && $add_only){
			return array("Flag"=>100, "FlagString"=>"操作成功");
		}elseif($done){
			$sql   = "SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".`song` WHERE group_id = ".$this->param['GroupId']." AND room_id = ".$this->param['ChannelId']." AND uin = ".$this->param['Uin'];
			$count = $this->db->get_var($sql);
			return array("Flag"=>100, "FlagString"=>"操作成功", "Count"=>$count);
		}else{
			return array("Flag"=>102, "FlagString"=>"操作失败");
		}
	}
	
	function editSong($oldname, $name, $author){
		$name    = htmlspecialchars(addslashes($name));
		$oldname = htmlspecialchars(addslashes($oldname));
		$author  = htmlspecialchars(addslashes($author));
		
		$sql   = "SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".`song` WHERE group_id = ".$this->param['GroupId']." AND room_id = ".$this->param['ChannelId']." AND uin = ".$this->param['Uin']." AND `name` = '".$name."' AND `author` = '".$author."'";
		$exist = $this->db->get_var($sql);
		if($exist){
			return array("Flag"=>103, "FlagString"=>"相同歌曲已存在");
		}
		
		$sql  = "UPDATE `kkyoo_new_rooms`.`song` SET `name` = '".$name."' ,`author` = '".$author."' WHERE  group_id = ".$this->param['GroupId']." AND room_id = ".$this->param['ChannelId']." AND uin = ".$this->param['Uin']." AND `name` = '".$oldname."'";
		$done = $this->db->query($sql);
		
		if($done){
			return array("Flag"=>100, "FlagString"=>"操作成功");
		}else{
			return array("Flag"=>102, "FlagString"=>"操作失败");
		}
	}
	
	function delSong($name, $page_num){
		$name = htmlspecialchars(addslashes($name));
		
		$sql  = "DELETE FROM `kkyoo_new_rooms`.`song` WHERE group_id = ".$this->param['GroupId']." AND room_id = ".$this->param['ChannelId']." AND uin = ".$this->param['Uin']." AND `name` = '".$name."'";
		$done = $this->db->query($sql);
		
		if($done && $page_num){
			$res = array_merge($this->listSong($page_num, ''), array("Flag"=>100, "FlagString"=>"操作成功"));
		}elseif($done){
			$res = array("Flag"=>100, "FlagString"=>"操作成功");
		}else{
			$res = array("Flag"=>102, "FlagString"=>"操作失败");
		}
		return $res;
	}
	
	function pickSong(){
		$sql = "SELECT `pick_price`, `act_percentage`, `tax_percentage` FROM ".DB_NAME_NEW_ROOMS.".`tbl_roomsbroadcast_config` WHERE group_id = ".$this->param['GroupId'];
		$row = $this->db->get_row($sql, "ASSOC");
			
		$pick_price     = $row['pick_price'] 		== '' ? 100000 	: $row['pick_price'];
		$act_percentage = $row['act_percentage'] 	== '' ? 0.6 	: $row['act_percentage']/100;;
		$tax_percentage = $row['tax_percentage'] 	== '' ? 0.4 	: $row['tax_percentage']/100;
		$log = array();
		
		$request = array(
			"param"=>array(
				'BigCaseId'	  =>10001,
				'CaseId'	  =>10027,
				"ParentId"	  =>10639,
				"ChildId"	  =>104,
				"Uin"		  =>$this->param['Uin'],
				"TargetUin"	  =>$this->param['TargetUin'],
				"MoneyWeight" =>$pick_price,
				"DoingWeight" =>1,
				"GroupId"	  =>$this->param['GroupId'],
				"ChannelId"	  =>$this->param['ChannelId'],
				"Desc"=>"点歌-用户点播"
				),
			"extparam"=>array(
				"Operator"	  =>"CF9BEF26F303FF9DDA3F5DED2AA7C3C5",
				"Tag"		  =>'Kmoney',
				'GroupId'	  =>$this->param['GroupId']
				),
		);
		$res = httpPOST(KMONEY_API_PATH,$request);
		if($res['Flag'] != 100){
			return $res;
		}
		$user_balance = $res['LastBalance'];
		$log[] = getLogData($request['param'], $request['extparam']);
		
		$act_balance = -1;
		if($this->param['Uin'] == $this->param['TargetUin']){
			 $act_balance = $user_balance;
		}
		if($act_percentage > 0){
			$request = array(
				"param"=>array(
					'BigCaseId'		=>10001,
					'CaseId'		=>10027,
					"ParentId"		=>10639,
					"ChildId"		=>908,
					"Uin"			=>$this->param['Uin'],
					"TargetUin"		=>$this->param['TargetUin'],
					"MoneyWeight"	=>$pick_price*$act_percentage,
					"DoingWeight" 	=>1,
					"GroupId"	  	=>intval($this->param['GroupId']),
					"ChannelId"	  	=>$this->param['ChannelId'],
					"Desc"			=>"点歌-主播接收"
					),
				"extparam"=>array(
					"Operator"		=>"CF9BEF26F303FF9DDA3F5DED2AA7C3C5",
					"Tag"			=>'Kmoney',
					'GroupId'		=>intval($this->param['GroupId'])
					),
			);
			$res = httpPOST(KMONEY_API_PATH,$request);
			if($res['Flag'] != 100){
				return $res;
			}
			$log[] = array('param'=>array_merge($log[0]['param'], $request['param']), 'extparam'=>array_merge($log[0]['extparam'], $request['extparam']));
		}
		
		if($tax_percentage > 0){
			$request = array(
				"param"=>array(
					'BigCaseId'		=>10001,
					'CaseId'		=>10027,
					"ParentId"		=>10639,
					"ChildId"		=>901,
					"Uin"			=>$this->param['Uin'],
					"TargetUin"		=>$this->param['TargetUin'],
					"MoneyWeight"	=>$pick_price*$tax_percentage,
					"DoingWeight" 	=>1,
					"GroupId"	  	=>intval($this->param['GroupId']),
					"ChannelId"	  	=>$this->param['ChannelId'],
					"Desc"			=>"渠道税收"),
				"extparam"=>array(
					"Operator"		=>"CF9BEF26F303FF9DDA3F5DED2AA7C3C5",
					"Tag"			=>'Kmoney',
					'GroupId'		=>intval($this->param['GroupId'])
					),
			);
			$res = httpPOST(KMONEY_API_PATH,$request);
			if($res['Flag'] != 100){
				return $res;
			}
			$log[] = array('param'=>array_merge($log[0]['param'], $request['param']), 'extparam'=>array_merge($log[0]['extparam'], $request['extparam']));
		}
		return array("Flag"=>100, "FlagString"=>"操作成功", "UserBalance"=>$user_balance, "ActBalance"=>$act_balance, "LogData"=>$log);
	}
	
}