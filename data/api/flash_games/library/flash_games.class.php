<?php
class FlashGame
{
	//db操作对象
	protected $db;
	protected $extradesc = '结算';

	/**
	* 构造函数
	*@param object $db 数据库驱动
	*@return 无
	*/
	public function __construct(){
		$this->db = db::connect(config('database','default'));
		//$this->mongodb = db::connect(config('mongodb','ktv'),'mongo');
        $this->mongodb = domain::main()->GroupDBConn('mongo');
	}

	//进入场次
	public function authRequest($param,$extparam){
		$param['Uin'] = intval($param['Uin']);
		if($param['Uin'] <= 0) return array('Flag'=>101,'FlagString'=>'参数错误');
		$interactinfo = $this->getCmd($param,1);
		$cmd = $interactinfo['cmd'];
		$interactid = $interactinfo['id'];
		$interact_group = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetInteractGropuConfig','Interact'=>$interactid,'Groupid'=>(int)$param['GroupId'])));
		$configure = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'InteractConfigList','Cmd'=>$cmd)));
		$configure = $configure['Result']['value'];
		$configure['group_robot'] = $interact_group['Result']['robot'];
		//返还冻结金额
		$sql = "SELECT gameid FROM ".DB_NAME_FLASH_GAME.".{$cmd}_user_bank WHERE uin={$param['Uin']} FOR UPDATE";
		$backmoney = $this->db->get_results($sql,'ASSOC');
		foreach($backmoney as $user){
			$param['ChildId'] = 104;
			$this->extradesc = '返还历史冻结余额';
			$this->backMoney($param,$user['gameid'],$extparam);
		}
		$param['Desc'] = '进入场次-获取配置信息';
		$log = getLogData($param,$extparam);
		$logData[] = $log;
		$money = get_money($param['Uin'],$param['GroupId']);
		return array('Flag'=>100,'FlagString'=>'成功进入场次','VoucherBalance'=>$money,'GameConfig'=>$configure,'LogData'=>$logData);
	}
	
	//游戏点击开始	
	public function carryMoney($param,$gameid,$extparam){
		$cmd = $this->getCmd($param);
		$gameinfo = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetInfoByCmd','Cmd'=>$cmd)));
		if(empty($gameinfo)) return array('Flag'=>101,'FlagString'=>'游戏不存在');
		$this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
		$this->db->start_transaction();
		
		//保存冻结资金
		$uptime = time();
		$bank = $this->db->get_row("SELECT * FROM ".DB_NAME_FLASH_GAME.".`{$cmd}_user_bank` WHERE uin={$param['Uin']} AND gameid='{$gameid}' LIMIT 1 FOR UPDATE",'ASSOC');
		if(empty($bank)){
			$sql = "INSERT INTO ".DB_NAME_FLASH_GAME.".`{$cmd}_user_bank`(uin,gameid,roomid,money,uptime)VALUES({$param['Uin']},'{$gameid}',{$param['ChannelId']},{$param['MoneyWeight']},{$uptime})";
		}else{
			$sql = "UPDATE ".DB_NAME_FLASH_GAME.".`{$cmd}_user_bank` SET money=money+{$param['MoneyWeight']},uptime={$uptime} WHERE uin={$param['Uin']} AND gameid='{$gameid}'";
		}
		$query = $this->db->query($sql);
		if( ! $query){
			$this->rollback();
			return array('Flag'=>101,'FlagString'=>'冻结金额存入失败！');
		}
		
		//机器人业务余额库存入
		if($param['Uin']>=GUEST_UIN_START && $param['Uin']<=GUEST_UIN_END){
			$p = $param;
			$p['BigCaseId'] = 10006;
			$p['CaseId'] = 10049;
			$p['ParentId'] = 10326;
			$p['ChildId'] = 102;
			$logbuild = new logbuild();
			$query = $logbuild->checkWork($p,$extparam);
			if($query['Flag'] != 100){
				$this->rollback();
				return $query;
			}
			$query = $this->trade($p,"业务余额库-{$gameinfo['interact_name']}机器人带入");
			$param['ChildId'] = 105;
			$query = $this->trade($param,"业务余额库-{$gameinfo['interact_name']}机器人带入");
			if($query['Flag'] != 100){
				$this->rollback();
				return $query;
			}
		}else{
			//扣除带入金额
			$query = $this->trade($param,"房间：{$param['ChannelId']}游戏：{$gameinfo['interact_name']}局号：{$gameid}带入：{$param['MoneyWeight']}");
			if($query['Flag'] != 100){
				$this->rollback();
				if($param['MoneyWeight'] > $query['Balance']){
					return array('Flag'=>102,'FlagString'=>'您的帐户余额不足，无法继续游戏，请充值。');
				}else{
					return $query;
				}
			}
		}
		//带入成功
		$this->commit();
		$param['Desc'] = $query['Desc'];
		$logData[] = getLogData($param,$extparam);
		$result = array('Flag'=>100,'FlagString'=>'success','FrozenMoneyBalance'=>$bank['money']+$param['MoneyWeight'],'VoucherBalance'=>$query['VoucherBalance'],'LogData'=>$logData);
		if(!empty($query['Balance'])) $result['UserBalance'] = $query['Balance'];
		return $result;
	}
	
	//离开游戏
	public function backMoney($param,$gameid,$extparam){
		$param['Uin'] = intval($param['Uin']);
		$param['TargetUin'] = ($param['TargetUin']==0 || empty($param['TargetUin'])) ? $param['Uin'] : $param['TargetUin'];
		if($param['Uin']<=0 || empty($gameid) || $param['ChannelId']<=0){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$cmd = $this->getCmd($param);
		$info = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetInfoByCmd','Cmd'=>$cmd)));
		if(empty($info)) return array('Flag'=>101,'FlagString'=>'游戏不存在');
		
		//事务开始
		$this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
		$this->db->start_transaction();
		$bank = $this->getFrozenMoneyBalance($param['Uin'],$cmd,$gameid);
		if(empty($bank)) return array('Flag'=>101,'FlagString'=>'该局已结算');
		$param['MoneyWeight'] = $bank['money'];
		if(empty($bank)) return array('Flag'=>102,'FlagString'=>'没有可用的冻结金额！');
		
		//清除冻结余额
		$query = $this->db->query("DELETE FROM ".DB_NAME_FLASH_GAME.".`{$cmd}_user_bank` WHERE uin={$param['Uin']} AND gameid='{$gameid}'");
		if( ! $query){
			$this->rollback();
			return array('Flag'=>105,'FlagString'=>'返还冻结金额失败！');
		}
		
		if($param['Uin']>=GUEST_UIN_START && $param['Uin']<=GUEST_UIN_END){
			$param['ChildId'] = 106;
		}
		
		//游戏余额存入
		if($bank['money'] > 0){
			//余额返还
			$extparam['Desc'] = "房间：{$param['ChannelId']}游戏：{$info['interact_name']}局号：{$gameid}{$this->extradesc}：{$param['MoneyWeight']}-{$param['Desc']}";
			$back = $this->trade($param,$extparam['Desc']);
			$logData[] = getLogData($param,$extparam);
			if($back['Flag'] != 100){
				$this->rollback();
				return $back;
			}
			
			if($param['Uin']>=GUEST_UIN_START && $param['Uin']<=GUEST_UIN_END){
				$p = $param;
				$p['BigCaseId'] = 10006;
				$p['CaseId'] = 10049;
				$p['ParentId'] = 10326;
				$p['ChildId'] = 101;
				$query = $this->trade($p,"业务余额库-{$info['interact_name']}机器人带出-{$param['Desc']}");
				if($query['Flag'] != 100){
					$this->rollback();
					return $query;
				}
			}
		}
		
		//游戏税收存入
		if($bank['tax'] > 0){
			if($bank['tax'] >= 1){
				$param['ChildId'] = 901;
				$param['MoneyWeight'] = $bank['tax'];
				$param['Desc'] = "房间：{$param['ChannelId']}游戏：{$info['interact_name']}局号：{$gameid}{$this->extradesc}税收：{$bank['tax']}-{$param['Desc']}";
				$param['TaxType'] = 2;
				$query = $this->trade($param,$param['Desc']);
				$logData[] = getLogData($param,$extparam);
				if($query['Flag'] != 100){
					$this->rollback();
					return $query;
				}
				
			}
		}
		//返还成功
		$this->commit();
		$result = array('Flag'=>100,'FlagString'=>'游戏离开','VoucherBalance'=>$back['VoucherBalance'],'LogData'=>(array)$logData);
		if(!empty($back['Balance'])) $result['UserBalance'] = $back['Balance'];
		return $result;
	}

	function gameLevelImfomation($param, $uin){
		if(!$uin || !is_array($uin)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$count = count($uin);
		for($i=0;$i<$count;$i++){
			$uin[$i] = intval($uin[$i]);
		}
		$parentToRule = array(10109=>12, 10110=>13, 10114=>11);
		$parentToRuleId = array(10109=>15, 10110=>16, 10114=>14);
		if(!array_key_exists($param['ParentId'], $parentToRule)){
			return array('Flag'=>102,'FlagString'=>'该游戏没有等级设置');
		}
		//获得相关等级信息
		$rule_id = $parentToRule[$param['ParentId']];
		if($rule_id == 0){
			return array('Flag'=>102,'FlagString'=>'该游戏等级规则没有设置');
		}
		$sql = "SELECT integration FROM `".DB_NAME_TPL."`.`business_param_config` WHERE id = ".$rule_id;
		$json = $this->db->get_var($sql);
		$rule = json_decode(urldecode($json), true);
		
		//获得用户积分
		$data = array();
		$table = "kkyoo_integral.total_weight";
		$query = array("Ruleid"=>$parentToRuleId[$param['ParentId']], "UinId"=>array('$in'=>$uin));
		$user_info = $this->mongodb->get_results($table, $query);
		if(!$user_info)
			$user_info = array();
		
		//处理积分信息
		$no_scroe_uin = array();
		$scroed_uin = array();
		foreach($user_info as $one_info){
			$scroed_uin[] = $one_info['UinId'];
			
			$level_data = $this->parseLevelInfo($rule, $one_info['Weight']);
			$data[] = array("Uin"=>$one_info['UinId'], "LevelData"=>$level_data);
		}
		$no_scroe_uin = array_diff($uin, $scroed_uin);
		foreach($no_scroe_uin as $one_uin){
			$data[] = array("Uin"=>$one_uin, "LevelData"=>array("Level"=>0, "NeedScore"=>($rule[0]['two']+1), "Percentage"=>0, "Score"=>0));
		}
		
		return array('Flag'=>100,'FlagString'=>'等级信息查询成功', 'Data'=>$data);
	}

	private function parseLevelInfo(&$rule, $score){
		$level_data["Score"] = $score;
		if($score < 0){
			$level_data["Level"] = 0;
			$level_data["NeedScore"] = $rule[0]['two'] + 1 - $score;
			$level_data["Percentage"] = 0;
		}
		foreach($rule as $one){
			if($score >= $one['one'] && $score <= $one['two']){
				$level_data["Level"] = $one['value'];
				$level_data["NeedScore"] = $one['two'] + 1 - $score;
				$level_data["Percentage"] = (($score - $one['one'])/($one['two'] - $one['one']+1))*100;
				break;
			}
		}
		
		return $level_data;
	}
	
	/**
	* 查询用户冻结余额
	*@parame $uin
	*@parame $cmd
	*@parame $roomid
	*/
	public function getFrozenMoneyBalance($uin,$cmd,$gameid){
		return $this->db->get_row("SELECT money,tax FROM ".DB_NAME_FLASH_GAME.".`{$cmd}_user_bank` WHERE uin={$uin} AND gameid='{$gameid}' LIMIT 1 FOR UPDATE",'ASSOC');
	}
	
	//游戏结算
	public function gameResult($param,$gameid,$users,$extparam){
		//得到cmd
		$cmd = $this->getCmd($param);
		if(empty($cmd) || empty($gameid) || empty($users)) return array('Flag'=>101,'FlagString'=>'参数错误');
		$this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
		$this->db->start_transaction();
		$froze_money_balance = array();
		$add_total = $sub_total = 0;
		$uptime = time();
		
		$date = strtotime(date("Ymd", $uptime));
		$parentId_arr = array(10109, 10110, 10114);
		$need_count = in_array($param['ParentId'], $parentId_arr);
		$ip_arr = array();
		
		foreach((array)$users as $user){
			$user  = (array)$user;
			$uin   = intval($user['Uin']);
			$roomid= intval($user['Roomid']);
			$money = intval($user['Value']);
			$tax   = intval($user['Tax']);
			$descr = $user['Describe'];
			$ip    = $user['Ip'];
			if($uin <= 0 || $money < 0 || $roomid <= 0) return array('Flag'=>101,'FlagString'=>'用户参数错误！');
			
			//加币
			if($user['Win'] == 1){
				$trade_money = $money-$tax;
				$bank = $this->db->get_row("SELECT * FROM ".DB_NAME_FLASH_GAME.".`{$cmd}_user_bank` WHERE uin={$uin} AND gameid='{$gameid}' LIMIT 1 FOR UPDATE");
				if(empty($bank)){
					$sql = "INSERT INTO ".DB_NAME_FLASH_GAME.".`{$cmd}_user_bank` (uin,gameid,roomid,money,tax,uptime)VALUES({$uin},'{$gameid}',{$roomid},{$trade_money},{$tax},{$uptime})";
				}else{
					$sql = "UPDATE ".DB_NAME_FLASH_GAME.".`{$cmd}_user_bank` SET money=money+{$trade_money},tax=tax+{$tax},uptime={$uptime} WHERE uin={$uin} AND gameid='{$gameid}'";
				}
				$balance = $bank['money']+$trade_money;
				$a = $add_total;
				$add_total += $money;
			//减币
			}else{
				$trade_money = $money+$tax;
				$bank = $this->db->get_row("SELECT * FROM ".DB_NAME_FLASH_GAME.".`{$cmd}_user_bank` WHERE uin={$uin} AND gameid='{$gameid}' LIMIT 1 FOR UPDATE");
				if(empty($bank) || $bank['money'] < $trade_money){
					$this->db->rollback();
					return array('Flag'=>102,'FlagString'=>'用户冻结账户余额不足！');
				}else{
					$sql = "UPDATE ".DB_NAME_FLASH_GAME.".`{$cmd}_user_bank` SET money=money-{$trade_money},tax=tax+{$tax},uptime={$uptime} WHERE uin={$uin} AND gameid='{$gameid}'";
				}
				$balance = $bank['money']-$trade_money;
				$a = $sub_total;
				$sub_total += $money;
			}
			
			//正常开始事务操作
			$query1 = $this->db->query($sql);
			if(!$query1){
				$this->db->rollback();
				return array('Flag'=>103,'FlagString'=>'操作失败！');
			}else{
				$result = array('Flag'=>100,'Uin'=>$uin,'FrozenMoneyBalance'=>$balance);
				$log = array('param'=>array('Uin'=>$uin,'TargetUin'=>$uin,'ChannelId'=>$roomid,'BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],
						'ChildId'=>$param['ChildId'],'MoneyWeight'=>$trade_money,'DoingWeight'=>$param['DoingWeight'],'Desc'=>"用户：{$uin}，房间：{$roomid}，{$descr}，金豆：{$money}，交税：{$tax}"),'extparam'=>$extparam);
				
				$logData[] = getLogData($log['param'],$log['extparam']);
				$froze_money_balance[] = $result;
				
				$extlog = $this->extlog($param['ParentId'], $uin, $roomid, $user['Win'], $param['DoingWeight'], $money, $extparam);
				if($extlog){
					$logData[] = getLogData($extlog['param'],$extlog['extparam']);
				}
			}
			
			//计入用户局数
			if($need_count && !($uin>=GUEST_UIN_START && $uin<=GUEST_UIN_END)){
				$sql = "SELECT id FROM ".DB_NAME_FLASH_GAME.".`player_day` WHERE uin = '".$uin."' AND `date` = '".$date."' AND parentId = '".$param['ParentId']."'";
				$id = $this->db->get_var($sql);
				if($id){
					$sql = "UPDATE ".DB_NAME_FLASH_GAME.".`player_day` SET `counter`= counter + 1 WHERE `id`='".$id."'";
				}else{
					$sql = "INSERT INTO ".DB_NAME_FLASH_GAME.".`player_day`(`id`,`uin`,`date`,`parentId`,`counter`) VALUES ( NULL,'".$uin."','".$date."','".$param['ParentId']."','1')";
				}
				$this->db->query($sql);
				
				$ip_arr[] = $ip;
			}
		}

		//计入ip局数
		if($ip_arr){
			$ip_arr = array_unique($ip_arr);
			foreach($ip_arr as $one_ip){
				$one_ip = ip2long($one_ip);
				
				$sql = "SELECT id FROM ".DB_NAME_FLASH_GAME.".`ip_day` WHERE ip = '".$one_ip."' AND `date` = '".$date."' AND parentId = '".$param['ParentId']."'";
				$id = $this->db->get_var($sql);
				if($id){
					$sql = "UPDATE ".DB_NAME_FLASH_GAME.".`ip_day` SET `counter`= counter + 1 WHERE `id`='".$id."'";
				}else{
					$sql = "INSERT INTO ".DB_NAME_FLASH_GAME.".`ip_day`(`id`,`ip`,`date`,`parentId`,`counter`) VALUES ( NULL,'".$one_ip."','".$date."','".$param['ParentId']."','1')";
				}
				$this->db->query($sql);
			}
		}
		
		//是否有减币操作
		if($sub_total < $add_total){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'非法操作！');
		}
		$this->db->commit();
		return $game_result = array('Flag'=>100,'FrozenMoneyBalanceDetail'=>$froze_money_balance,'LogData'=>$logData);
	}

	public function getUserMoneyBalance($uin){
		if($uin <= 0) return 0;
		return array('Flag'=>100,'UserBalance'=>get_money($uin));
	}
	
	private function extlog($parentId, $uin, $roomid, $win, $DoingWeight, $MoneyWeight, $extparam){
		$parentIdLog = array(10109=>array("win"=>108, "lose"=>109),
							 10110=>array("win"=>108, "lose"=>109),
							 10114=>array("win"=>108, "lose"=>109));
		if(!array_key_exists($parentId, $parentIdLog)){
			return false;
		}
		if($win == 1){
			//胜利
			$log = array(
					'param'=>array(
							"BigCaseId"  => 10001,
							"CaseId"	 => 10032,
							"ParentId"   => $parentId,
							"ChildId"	 => $parentIdLog[$parentId]["win"],
							"Uin"		 => $uin,
							"ChannelId"  => $roomid,
							"TargetUin"  => 1,
							"Client"	 => "WEB ADMIN",
							"DoingWeight"=> $DoingWeight,
							"MoneyWeight"=> $MoneyWeight,
							"Desc"		 => "游戏结算-胜利"
					),
					'extparam'=>$extparam
			);
		}else{
			//失败
			$log = array(
					'param'=>array(
							"BigCaseId"  => 10001,
							"CaseId"	 => 10032,
							"ParentId"   => $parentId,
							"ChildId"	 => $parentIdLog[$parentId]["lose"],
							"Uin"		 => $uin,
							"ChannelId"  => $roomid,
							"TargetUin"  => 1,
							"Client"	 => "WEB ADMIN",
							"DoingWeight"=> $DoingWeight,
							"MoneyWeight"=> $MoneyWeight,
							"Desc"		 => "游戏结算-失败"
					),
					'extparam'=>$extparam
			);
		}
		return $log;
	}
	
	/**
	* 交易金额操作
	*/
	private function trade($param,$desc){
		$param['Desc'] = $desc;
		$extparam = array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$param['GroupId']);
		//$log = getLogData($param,$extparam);
		//$extparam['GroupId'] = $log['extparam']['GroupId'];
		$request = array('param'=>$param,'extparam'=>$extparam);
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		$array = array('Flag'=>100,'Balance'=>$rst['LastBalance'],'MoneyWeight'=>$param['MoneyWeight'],'Desc'=>$desc);
		if($rst['fund_type'] == 'Kmoney'){
			return $array;
		}else{
			$array['VoucherBalance'] = $rst['LastBalance'];
			unset($array['Balance']);
			if(isset($rst['KmoneyBalance'])) $array['Balance'] =$rst['KmoneyBalance'];
		}
		return $array;
	}
	
	//得到cmd
	private function getCmd($param,$type=0){
		$cmdInfo = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetGameInfo','BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'])));
		if(!$type){
			return $cmdInfo['Info']['cmd'];
		}else{
			return $cmdInfo{'Info'};
		}
	}
	
	private function commit(){
		$this->db->commit();
		//httpPOST(KMONEY_API_PATH,array('extparam'=>array('Tag'=>'Commit')));
	}
	
	private function rollback(){
		$this->db->rollback();
		//httpPOST(KMONEY_API_PATH,array('extparam'=>array('Tag'=>'Rollback')));
	}
}