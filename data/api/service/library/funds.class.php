<?php
class funds
{
	private $mongodb, $db, $mongo_table;

	public function __construct() {
		$this->mongodb = domain::main()->GroupDBConn('mongo');
		$this->db = domain::main()->GroupDBConn();
	}
	
	function __call($method, $arg){
		/*
		 * 由于站的模板不同 使用的方法也不同 
		 * 比如findsList 在aisvv用的是aisvv_findsList 
		 * 而cc51用的是cc51_findsList
		*/
		$template 		= array_pop($arg);
		$method_name 	= $template."_".$method;
		if(method_exists($this, $method_name)){
			return call_user_method_array($method_name, $this, $arg);
		}else{
			return array("Flag"=>101, "FlagString"=>"不存在功能");
		}
	}
	
	function aisvv_findsList($uin, $group_id){
		$roleid_to_ruleid 				= array("10185"=>31, "10534"=>32);
		$ruleid_to_weight_multiplying 	= array("31"=>3, "32"=>1);
		$fund_rule_id					= 30;
	
		return $this->common_findsList($uin, $group_id, $roleid_to_ruleid, $fund_rule_id, $ruleid_to_weight_multiplying);
	}
	
	function cc51_findsList($uin, $group_id){
		$roleid_to_ruleid 				= array("10535"=>35);
		$ruleid_to_weight_multiplying 	= array("35"=>1);
		$fund_rule_id					= 34;
	
		return $this->common_findsList($uin, $group_id, $roleid_to_ruleid, $fund_rule_id, $ruleid_to_weight_multiplying);
	}
	
	function aisvv_fundsExchange($uin, $group_id, $room_id, $rule_id, $weight){
		$ruleid_to_weight_multiplying 	= array("31"=>3, "32"=>1);
		$fund_rule_id					= 30;
	
		return $this->common_fundsExchange($uin, $group_id, $room_id, $rule_id, $weight, $ruleid_to_weight_multiplying, $fund_rule_id);
	}
	
	function cc51_fundsExchange($uin, $group_id, $room_id, $rule_id, $weight){
		$ruleid_to_weight_multiplying 	= array("35"=>1);
		$fund_rule_id					= 34;
	
		return $this->common_fundsExchange($uin, $group_id, $room_id, $rule_id, $weight, $ruleid_to_weight_multiplying, $fund_rule_id);
	}
	
	function aisvv_exchangeDetails($uin, $group_id, $room_id, $start_date, $end_date){
		$role_ids = array(10534, 10185);
	
		return $this->common_exchangeDetails($uin, $group_id, $room_id, $start_date, $end_date, $role_ids);
	}
	
	function cc51_exchangeDetails($uin, $group_id, $room_id, $start_date, $end_date){
		$role_ids = array(10535);
	
		return $this->common_exchangeDetails($uin, $group_id, $room_id, $start_date, $end_date, $role_ids);
	}
	
	function aisvv_groupFunds($group_id, $room_id){
		$fund_rule_id = 30;
	
		return $this->common_groupFunds($group_id, $room_id, $fund_rule_id);
	}
	
	function cc51_groupFunds($group_id, $room_id){
		$fund_rule_id = 34;
	
		return $this->common_groupFunds($group_id, $room_id, $fund_rule_id);
	}
	
	private function common_findsList($uin, $group_id, $roleid_to_ruleid, $fund_rule_id, $ruleid_to_weight_multiplying){
		//获取角色的所有房间
		$roles_res = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'GetRole','Uin'=>$uin,'GroupId'=>$group_id, "RoleId"=>array_keys($roleid_to_ruleid))));
		if($roles_res['Flag'] != 100){
			return array("Flag"=>101, "FlagString"=>"系统错误");
		}
		
		$yesterday 	= intval(date("Ymd",time()-24*60*60));
		$today 		= intval(date("Ymd"));
		
		$online 		= array();
		$room_ids 		= array();
		$all_room_ids 	= array();
		//角色所有房间今天的时间
		$query_condition['ExtendUin'] 	= intval($group_id);
		$query_condition['UinId'] 		= intval($uin);
		$query_condition['Uptime'] 		= $today;
		$query_condition['$or'] 		= array();
		foreach($roles_res['Roles'] as $one){
			$rule_id = $roleid_to_ruleid[$one['RoleId']];
			!in_array(array('Ruleid'=>$rule_id), $query_condition['$or'])?$query_condition['$or'][] = array('Ruleid'=>$rule_id):"";
		
			$all_room_ids[] = intval($one['ChannelId']);
		}
		
		$user_today_weight_result = $this->mongodb->get_results(
				"kkyoo_integral.day_weight",
				$query_condition,
				array(
						'sort'=>array('Weight'=>-1)
				)
		);
		
		foreach((array)$user_today_weight_result as $one){
			$online[$one['ChannelUin']."_".$one['Ruleid']] 	= $one;
		
			$room_ids[] 									= intval($one['ChannelUin']);
		}
		//角色所有房间昨天的时间
		$query_condition['Uptime'] 		= $yesterday;
		$user_yesterday_weight_result 	= $this->mongodb->get_results(
				"kkyoo_integral.day_weight",
				$query_condition,
				array(
						'sort'=>array('Weight'=>-1)
				)
		);
		
		foreach((array)$user_yesterday_weight_result as $one){
			$key 			= $one['ChannelUin']."_".$one['Ruleid'];
		
			$one['YWeight'] = $one['Weight'];
			unset($one['Weight']);
			$online[$key] 	= array_merge($one, (array)$online[$key]);
		
			$room_ids[] 	= intval($one['ChannelUin']);
		}
		
		$room_ids = array_unique($room_ids);
		//各个房间昨日总时间
		$total_time 	= array();
		$time_room_ids 	= $room_ids;
		$storage 		= cache::connect(config('cache','memcache'));
		foreach($time_room_ids as $k=>$one){
			$data = unserialize($storage->get("tt_".$group_id."_".$one));
			if($data){
				$total_time[$yesterday][$data['room_id']] = $data['weight'];
				unset($time_room_ids[$k]);
			}
		}
		
		if($time_room_ids){
			$query_condition2['Ruleid']['$in'] 		= array_values($roleid_to_ruleid);
			$query_condition2['ChannelUin']['$in'] 	= $time_room_ids;
			$query_condition2['Uptime'] 			= $yesterday;
			$query_condition2['ExtendUin']		 	= intval($group_id);
			$all_user_time_yesterday_result = $this->mongodb->get_results(
					"kkyoo_integral.day_weight",
					$query_condition2
			);
			foreach((array)$all_user_time_yesterday_result as $one){
				$weight = $ruleid_to_weight_multiplying[$one['Ruleid']]*$one['Weight'];
				(int)$total_time[$yesterday][$one['ChannelUin']] += $weight;
			}
		
			foreach((array)$total_time[$yesterday] as $room_id=>$weight){
				$storage->set("tt_".$group_id."_".$room_id, serialize(array("room_id"=>$room_id, "weight"=>$weight)), 24*60*60);
			}
		}
		
		//各个房间昨日和今天总余额
		$total_money 							= array();
		$query_condition3['Ruleid'] 			= $fund_rule_id;
		$query_condition3['ChannelUin']['$in'] 	= $all_room_ids;
		$query_condition3['Uptime'] 			= $today;
		$query_condition3['ExtendUin'] 			= intval($group_id);
		$all_room_balance_today_result = $this->mongodb->get_results(
				"kkyoo_integral.day_weight",
				$query_condition3
		);
		foreach((array)$all_room_balance_today_result as $one){
			$total_money[$today][$one['ChannelUin']] = $one['Weight'];
		}
		
		$query_condition4['Ruleid'] 			= $fund_rule_id;
		$query_condition4['ChannelUin']['$in'] 	= $all_room_ids;
		$query_condition4['Uptime'] 			= $today;	//这里存储的时间是往前一天的 所以用today
		$query_condition4['ExtendUin'] 			= intval($group_id);
		$all_room_balance_yesterday_result = $this->mongodb->get_results(
				"parter_income.Fund",
				$query_condition4
		);
		foreach((array)$all_room_balance_yesterday_result as $one){
			$total_money[$yesterday][$one['ChannelUin']] = $one['Weight'];
		}
		
		//获得相关房间名称
		$room_names = array();
		$sql 		= "SELECT `id`,`name` FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE id IN (".join(",", $all_room_ids).")";
		$result 	= $this->db->get_results($sql, "ASSOC");
		foreach((array)$result as $one){
			$room_names[$one['id']] = $one['name'];
		}
		
		//数据整理
		foreach($online as $k=>$one){
			$weight = $ruleid_to_weight_multiplying[$one['Ruleid']]*$one['Weight'];
			$y_weight = $ruleid_to_weight_multiplying[$one['Ruleid']]*$one['YWeight'];
		
			$online[$k]['RoomName'] 	= $room_names[$one['ChannelUin']];
		
			$room_today_money			= $total_money[$today][$one['ChannelUin']];
			$room_yesterday_money 		= $total_money[$yesterday][$one['ChannelUin']];
			$room_yesterday_time 		= $total_time[$yesterday][$one['ChannelUin']];
			$online[$k]['GetWeight'] 	= $room_yesterday_time?floor($y_weight*$room_yesterday_money/$room_yesterday_time):0;
			$online[$k]['YRoomTotal'] 	= $room_yesterday_money?$room_yesterday_money:0;
			$online[$k]['RoomTotal'] 	= $room_today_money?$room_today_money:0;
			//时间用小时表示
			$online[$k]['Weight'] 		= round($one['Weight']/3600, 1);
			$online[$k]['YWeight'] 		= round($one['YWeight']/3600, 1);
		
			//查询兑换记录 设置兑换状态
			if($online[$k]['GetWeight']>0){
				$query_condition5 = array(
						"BigCaseId"=>10006,
						"CaseId"=>10047,
						"ParentId"=>10632,
						"ChildId"=>102,
						"UinId"=>intval($uin),
						"ExtendUin"=>intval($group_id),
						"ChannelId"=>intval($one['ChannelUin']),
						"Ruleid"=>intval($one['Ruleid']),
						"ExchangeDate"=>$today
				);
				$row = $this->mongodb->get_row(
						"parter_income.details",
						$query_condition5
				);
				$online[$k]['ExchangeAble'] = $row?2:0;
			}else{
				$online[$k]['ExchangeAble'] = 1;
			}
		}
		
		foreach($roles_res['Roles'] as $one){
			$rule_id = $roleid_to_ruleid[$one['RoleId']];
			$key = $one['ChannelId']."_".$rule_id;
		
			if(!$online[$key]){
				$room_yesterday_money 	= $total_money[$yesterday][$one['ChannelId']];
				$room_today_money 		= $total_money[$today][$one['ChannelId']];
				$online[$key] = array(
						"ChannelUin"=>$one['ChannelId'],
						"ExtendUin"=>$group_id,
						"Ruleid"=>$rule_id,
						"UinId"=>$uin,
						"Weight"=>0,
						"YWeight"=>0,
						"RoomName"=>$room_names[$one['ChannelId']],
						"GetWeight"=>0,
						"YRoomTotal"=>$room_yesterday_money?$room_yesterday_money:0,
						"RoomTotal"=>$room_today_money?$room_today_money:0,
						"ExchangeAble"=>1
				);
			}
		}
		
		return array("Flag"=>100, "Data"=>$online, "FlagString"=>"查询成功");
	}
	
	private function common_fundsExchange($uin, $group_id, $room_id, $rule_id, $weight, $ruleid_to_weight_multiplying, $fund_rule_id){
		$query_condition = array("ExtendUin" 	=> intval($group_id),
				"ChannelUin" 	=> intval($room_id),
				"Ruleid" 		=> intval($rule_id),
				"UinId" 		=> intval($uin),
				"Uptime" 		=> intval(date("Ymd", time()-24*60*60)));
		$user_weight_result = $this->mongodb->get_row("kkyoo_integral.day_weight", $query_condition);
		$user_weight = $ruleid_to_weight_multiplying[$user_weight_result['Ruleid']]*$user_weight_result['Weight'];
		
		if(!$user_weight_result){
			return array("Flag"=>102, "FlagString"=>"不存在改记录");
		}
		
		$query_condition6['ChannelId'] 		= intval($room_id);
		$query_condition6['ExchangeDate'] 	= intval(date("Ymd"));
		$query_condition6['ExtendUin'] 		= intval($group_id);
		$query_condition6['Ruleid'] 		= intval($rule_id);
		$query_condition6['UinId'] 			= intval($uin);
		$query_condition6['ParentId'] 		= 10632;
		$exchange_history_result = $this->mongodb->get_row("parter_income.details", $query_condition6);
		if($exchange_history_result){
			return array("Flag"=>102, "FlagString"=>"已经兑换过");
		}
		
		$storage = cache::connect(config('cache','memcache'));
		$data = unserialize($storage->get("tt_".$group_id."_".$room_id));
		if($data){
			$total_weight = $data['weight'];
		}else{
			$query_condition = array(
					"ExtendUin" 	=> intval($group_id),
					"ChannelUin" 	=> intval($room_id),
					"Ruleid" 		=> array('$in'=>array_keys($ruleid_to_weight_multiplying)),
					"Uptime" 		=> intval(date("Ymd", time()-24*60*60))
			);
			$all_user_time_result = $this->mongodb->get_results(
					"kkyoo_integral.day_weight",
					$query_condition
			);
			foreach($all_user_time_result as $one){
				$total_weight += $ruleid_to_weight_multiplying[$one['Ruleid']]*$weight;
			}
		}
		
		$query_condition2['ChannelUin'] 	= $room_id;
		$query_condition2['Uptime'] 		= intval(date("Ymd"));
		$query_condition2['ExtendUin'] 		= intval($group_id);
		$query_condition2['Ruleid'] 		= $fund_rule_id;
		$room_yesterday_money_result = $this->mongodb->get_row(
				"parter_income.Fund",
				$query_condition2
		);
		$record_id 		= $room_yesterday_money_result['_id'];
		$total_money 	= $room_yesterday_money_result['Weight'];
		
		$user_money 	= $total_weight?floor($total_money*$user_weight/$total_weight):0;
		if($user_money != $weight){
			return array("Flag"=>102, "FlagString"=>"资金错误");
		}
		
		//调用资金的三个科目
		$request 	= array(
				"param"=>array("ParentId"=>10264,"ChildId"=>106,"Uin"=>$uin,"TargetUin"=>$uin,"MoneyWeight"=>$user_money,"Desc"=>"站内税收-公积金兑换V点净支出"),
				"extparam"=>array("Operator"=>"CF9BEF26F303FF9DDA3F5DED2AA7C3C5","Tag"=>'TaxTrade','GroupId'=>$group_id),
		);
		$res 		= httpPOST(KMONEY_API_PATH,$request);
		if($res['Flag'] != 100){
			return $res;
		}
		$request 	= array(
				"param"=>array("ParentId"=>10632,"ChildId"=>102,"Uin"=>$uin,"TargetUin"=>$uin,"MoneyWeight"=>$user_money,"Desc"=>"公积金兑换-V点存入"),
				"extparam"=>array("Operator"=>"CF9BEF26F303FF9DDA3F5DED2AA7C3C5","Tag"=>'Kmoney','GroupId'=>$group_id),
		);
		$res 		= httpPOST(KMONEY_API_PATH,$request);
		if($res['Flag'] != 100){
			return $res;
		}
		$request 	= array(
				"param"=>array("ParentId"=>10632,"ChildId"=>103,"Uin"=>$uin,"TargetUin"=>$uin,"MoneyWeight"=>$user_money,"Desc"=>"公积金兑换-V点支出"),
				"extparam"=>array("Operator"=>"CF9BEF26F303FF9DDA3F5DED2AA7C3C5","Tag"=>'Kmoney','GroupId'=>$group_id),
		);
		$res 		= httpPOST(KMONEY_API_PATH,$request);
		if($res['Flag'] != 100){
			return $res;
		}
		
		//扣除公积金余额
		$query_condition4 = array(
				"ChannelUin" 	=> intval($room_id),
				"ExtendUin" 	=> intval($group_id),
				"Ruleid" 		=> $fund_rule_id
		);
		$record = array('$inc'=>array("Weight"=>-intval($user_money)));
		$this->mongodb->query(
				"parter_income.balance",
				$record,
				$query_condition4
		);
		
		//添加兑换记录
		$query_condition3 = array(
				"BigCaseId"		=>10006,
				"CaseId"		=>10047,
				"ParentId"		=>10632,
				"ChildId"		=>102,
				"UinId"			=>intval($uin),
				"ExtendUin"		=>intval($group_id),
				"ChannelId"		=>intval($room_id),
				"Ruleid"		=>intval($rule_id),
				"ExchangeDate"	=>intval(date("Ymd")),
				"Weight"		=>intval($user_money),
				"DoWeight"		=>1,
				"Desc"			=>$uin."在房间".$room_id."兑换公积金".$user_money,
				"Uptime"		=>intval(time()),
		);
		$this->mongodb->query("parter_income.details", $query_condition3);
		
		//添加到公积金改变数量中
		$query_condition5 = array(
				"_id" => $record_id
		);
		$record2 = array('$inc'=>array("Change"=>intval($user_money)));
		$this->mongodb->query("parter_income.Fund",
				$record2,
				$query_condition5
		);
		return array("Flag"=>100, "FlagString"=>"兑换成功");
	}
	
	private function common_exchangeDetails($uin, $group_id, $room_id, $start_date, $end_date, $role_ids){
		$rooms = array();
		if($uin){
			$role_res = httpPOST(ROLE_API_PATH, array('extparam'=>array('Tag'=>'GetRole','Uin'=>$uin,'GroupId'=>$group_id, "RoleId"=>$role_ids)));
			if($role_res['Flag'] != 100){
				return array("Flag"=>101, "FlagString"=>"系统错误");
			}
			foreach($role_res['Roles'] as $one){
				$sql = "SELECT `name` FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE id = ".$one['ChannelId'];
				$rooms[$one['ChannelId']] = $this->db->get_var($sql);
			}
		}else{
			$sql = "SELECT `id`,`name` FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE `group` = ".$group_id;
			$res = $this->db->get_results($sql, "ASSOC");
			foreach($res as $one){
				$rooms[$one['id']] = $one['name'];
			}
		}
		
		if($room_id>0){
			$query_condition['ChannelId'] = intval($room_id);
		}
		if($start_date>0){
			$query_condition['Uptime']['$gte'] = intval($start_date);
		}
		if($end_date>0){
			$query_condition['Uptime']['$lte'] = intval($end_date);
		}
		if($uin){
			$query_condition['UinId'] = intval($uin);
		}
		
		$page_arr = $this->showPage('');
		
		$query_condition['ExtendUin'] = intval($group_id);
		$query_condition['ParentId'] = 10632;
		list($offset,$rows) = explode(',',$page_arr['limit']);
		$limit = array('offset'=>$offset,'rows'=>$rows);
		
		$res = $this->mongodb->get_results("parter_income.details",
				$query_condition,
				array(
						'sort' =>array('Uptime'=>-1),
						'limit'=>$limit
				)
		);
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$res, "Rooms"=>$rooms, 'Page'=>$page_arr['page']);
	}
	
	private function common_groupFunds($group_id, $room_id, $fund_rule_id){
		$room_funds = array();
		$page_str = "";
		if($room_id){
			$room_id = intval($room_id);
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE `group` = ".$group_id." AND id = ".$room_id;
			$count = $this->db->get_var($sql);
			if(!$count){
				return array("Flag"=>101, "FlagString"=>"不存在该房间");
			}
			$weight = 0;
			$query_condition['ChannelUin'] 	= intval($room_id);
			$query_condition['ExtendUin'] 	= intval($group_id);
			$query_condition['Ruleid'] 		= $fund_rule_id;
			$result = $this->mongodb->get_row("parter_income.balance", $query_condition);
			$weight += $result['Weight']?$result['Weight']:0;
		
			$room_funds[$room_id] = $weight;
		}else{
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE `group` = ".$group_id;
			$total = $this->db->get_var($sql);
			$page_arr = $this->showPage($total);
		
			$sql = "SELECT `id` FROM ".DB_NAME_NEW_ROOMS.".`rooms` WHERE `group` = ".$group_id." LIMIT ".$page_arr['limit'];
			$res = $this->db->get_results($sql, "ASSOC");
			$room_ids = array();
			foreach($res as $one){
				$room_ids[] = intval($one['id']);
			}
		
			$query_condition['ChannelUin']['$in'] 	= $room_ids;
			$query_condition['ExtendUin'] 			= intval($group_id);
			$query_condition['Ruleid'] 				= $fund_rule_id;
			$result = $this->mongodb->get_results("parter_income.balance", $query_condition);
			foreach((array)$result as $one){
				$room_funds[$one['ChannelUin']] = $one['Weight']?$one['Weight']:0;
			}
			foreach($room_ids as $one){
				if(!$room_funds[$one]){
					$room_funds[$one] = 0;
				}
			}
		
			$page_str = $page_arr['page'];
		}
		
		return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$room_funds, "Page"=>$page_str);
	}
	
	//分页
	private function showPage($total, $perpage = 15,$nowindex=false) {
		require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
		$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
		));
		$pageArr['page'] = $page->simple_page($total);
		$pageArr['limit'] = $page->simple_limit();
		if($nowindex)$pageArr['index'] = $page->nowindex;
		unset ($page);
		return $pageArr;
	}
}