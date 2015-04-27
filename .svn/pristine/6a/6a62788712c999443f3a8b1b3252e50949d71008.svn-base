<?php

/**
 *   渠道收入管理
 *   文件: channel.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class channel
{

	//构造函数
	public function __construct(){
		$this->db = domain::main()->GroupDBConn();
		$this->now_time = time();
		$this->mongodb = domain::main()->GroupDBConn('mongo');
	}
	
	/*
	室主所有房间
	*/
	private function getOwnRooms($own,$roomid=0){
		$sql = "SELECT id,name,room_status FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE ownuin={$own}";
		$room_array = $this->db->get_results($sql,"ASSOC");
		if($roomid > 0){
			foreach($room_array as $key=>$value){
				$value['id'] = (int)$value['id'];
				$room_array[$key] = $value;
				if($roomid == $value['id']){
					$selected = $value;
					break;
				}
			}
		}else{
			$selected = $room_array[0];
		}
		return array('RoomList'=>$room_array,'Selected'=>$selected);
	}
	
	//室主活动状态
	private function hd_CountStages($roomid){
		$roomid = intval($roomid);
		$has_apply = $this->mongodb->get_row('parter_income.apply_reward',array('roomid'=>$roomid));
		$total_week = 26; //总共参加周数
		$week = (int)date('oW'); //当前星期
		$start_week = (int)date('oW',$has_apply['uptime']);//活动开始周
		$end_week = (int)date('oW',strtotime('+25 week',$has_apply['uptime']));//活动结束周
		$all_weeks = array();//参加活动的周
		$has_apply['status'] = 1; //使永远达标
		$is_save = false;
		
		//活动状态检查
		if(empty($has_apply['count_week'])){
			$flag = array('Flag'=>101,'FlagString'=>'您还没有申请该活动！');
		}elseif($has_apply['status'] == 0){
			$flag = array('Flag'=>102,'FlagString'=>'您已连续两周未达标，失去本活动资格！');
		}elseif(date('Ymd') > 20130331){
			$flag = array('Flag'=>103,'FlagString'=>'活动已结束！');
		}else{
			//参加活动的所有周
			$flag = array('Flag'=>100,'FlagString'=>'ok');
			for($i=0;$i<$total_week;$i++){
				$all_weeks[] = (int)date('oW',strtotime("+{$i} week",$has_apply['uptime']));
			}
			foreach($all_weeks as $key=>$countweek){
				if((!isset($has_apply['count_week'][$countweek]) && $countweek < $week) || ($has_apply['count_week'][$countweek]['RoomConsume'] < 1 && $countweek < $week)){//查出未统计的周排名（如果next_week的消费指标小于1则当作未统计处理）
					$has_apply['count_week'][$countweek] = $this->hd_getStageDetail($roomid,$countweek);
					$is_save = true;
				}
				if($countweek < $week && $has_apply['count_week'][$countweek]['CurStage'] < 3){ //找到未达标的周并判断是否是连续未达标
					$next_week = $all_weeks[$key+1];
					if($next_week < $week){//查出未统计的周排名
						$has_apply['count_week'][$next_week] = $this->hd_getStageDetail($roomid,$next_week);
						$is_save = true;
						if(isset($has_apply['count_week'][$next_week]) && $has_apply['count_week'][$next_week]['CurStage'] < 3){
							$has_apply['status'] = 0;
							$flag = array('Flag'=>105,'FlagString'=>'您已连续两周未达标，失去本活动资格！');
							$is_save = true;
							break;
						}
					}
				}
			}
			if($is_save) $this->mongodb->query('parter_income.apply_reward',$has_apply,array('roomid'=>$roomid)); //更新活动状态
		}
		$curstagedetail = $this->hd_getStageDetail($roomid,$week);
		if($flag['Flag'] != 100) $curstagedetail['RMB'] = 0;
		$curstagedetail = array_merge($flag,$curstagedetail);
		return $curstagedetail;
	}

	public function account($uin){
		$uin = intval($uin);
		if($uin < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_PARTNER.".account WHERE uin={$uin}";
		$row = $this->db->get_row($sql,ASSOC);

		$banks = array(1=>'中国招商银行',2=>'中国工商银行',3=>'中国建设银行',4=>'中国农业银行');

		$row['bankName'] = $banks[$row['bank_name']];

		//require_once 'pass_manager.class.php';
		//$pm = new PassManager();
		//$uid = $pm->uin2uid($uin);
		$ssoInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$uin)));
		//$uid = $uid['Uid'];
		if($ssoInfo['Flag'] == 100){
			//$info = $pm->info($uid);
			$row['name'] = $ssoInfo['Name'];
			$row['idcard'] = $ssoInfo['IdCard'];
		}
		return array('Flag'=>100,'FlagString'=>'成功','Info'=>$row);
	}

	public function saveAccount($uin,$data){
		$uin = intval($uin);
		if($uin<1 || empty($data)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$sql = "SELECT uin FROM ".DB_NAME_PARTNER.".account WHERE uin={$uin}";
		if($this->db->get_var($sql) > 0){
			$sql = "UPDATE ".DB_NAME_PARTNER.".account SET bank_name={$data['bank_name']},bank_id='{$data['bank_id']}',bank_address='{$data['bank_address']}' WHERE uin={$uin}";
		}else{
			$sql = "INSERT INTO ".DB_NAME_PARTNER.".account(uin,bank_name,bank_id,bank_address) VALUES($uin,{$data['bank_name']},'{$data['bank_id']}','{$data['bank_address']}')";
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'设置提现账户失败');
		}
		return array('Flag'=>100,'FlagString'=>'设置提现账户成功');
	}
	
	//获取最近的一条提现记录
	private function getCashRecord($uin){
		$table = "parter_income.parter_cash_detail";
		$time = time()-37*86400;
		$query['Uin'] = intval($uin);
		if($data['StartTime']){
			$query['Uptime']['$gte'] = intval($data['StartTime']);
		}
		$result_condition = array("limit"=>$limit, "sort"=>array("Uptime"=>-1));
		$row = $this->mongodb->get_row(
			$table,
			$query,
			$result_condition
		);
		if(empty($row)){
			return 0;
		}elseif($row['Uptime'] >= $time){
			return 1;
		}
	}
	
	//更新人民币余额账户，可使用人民币金额及冻结金额
	private function updateRmbBalance($uin,$weight){
		$table = "parter_income.parter_rmb_balance";
		$query_condition['Uin'] = intval($uin);
		$de_array = array(
			'db'=>'parter_income',
			'table'=>'parter_rmb_balance',
			'record'=>array('$inc'=>array('FreezeWeight'=>$weight,'Balance'=>-$weight)),
			'where'=>$query_condition
		);
		socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($de_array)));
	}
	
	//获取人民币余额冻结金额
	private function getRmbBalance($uin){
		$table = "parter_income.parter_rmb_balance";
		$query = array();
		$query['Uin'] = intval($uin);
		$fields = array("Balance"=>1,"FreezeWeight"=>1);
		$result_condition = array();
		$data = $this->mongodb->get_row(
			$table,
			$query,
			$result_condition,
			$fields
		);
		return $data;
	}
	
	//活动阶段
	private function hd_getStageDetail($roomid,$week){
		$stages = array(
			1=>array('RoomFlow'=>0,'RoomConsume'=>0,'RMB'=>0),
			2=>array('RoomFlow'=>3360,'RoomConsume'=>0,'RMB'=>0),
			3=>array('RoomFlow'=>3360,'RoomConsume'=>2500,'RMB'=>550),
			4=>array('RoomFlow'=>8400,'RoomConsume'=>5000,'RMB'=>1150),
			5=>array('RoomFlow'=>16800,'RoomConsume'=>10000,'RMB'=>2300),
			6=>array('RoomFlow'=>50000,'RoomConsume'=>30000,'RMB'=>6900),
		);
		$roomid = intval($roomid);
		$week = intval($week);
		$RoomConsume = $this->mongodb->get_row('kkyoo_integral.week_weight',array('Ruleid'=>9,'ChannelUin'=>$roomid,'Uptime'=>$week));
		$RoomConsume = number_format($RoomConsume['Weight'] / 10000,2,'.','');
		$curstage = 1;
		$curmoney = 0;
		foreach($stages as $key=>$val){
			if($RoomConsume >= $val['RoomConsume']){
				$curstage = $key;
				$curmoney = $val['RMB'];
			}
		}		
		return array('RoomFlow'=>0,'RoomConsume'=>$RoomConsume,'CurStage'=>$curstage,'RMB'=>$curmoney);
	}

	//获取房间人气排行和收入
	private function hd_getRoomWeekRank($roomid){
		$moneys = array(1800,1500,800,500,400,300,200,150,100,50);
		$week = (int)date('oW');
		$renqi = $this->mongodb->get_row('kkyoo_integral.week_weight',array('Ruleid'=>8,'ChannelUin'=>$roomid,'Uptime'=>$week));
		$renqi = floor($renqi['Weight'] / 10000);
		
		$cache = cache::connect(config('cache','memcache'));
		$paihang = $cache->get("INCOME_ROOM_WEEK_RANK");
		if(empty($paihang)){
			$paihang = $this->mongodb->get_results('rank_8.week_weight',array('Uptime'=>$week),array('sort'=>array('Weight'=>-1),'limit'=>10,'skip'=>0));
			$cache->set("INCOME_ROOM_WEEK_RANK", $paihang, 300);
		}
		$rank = 0;
		$money = 0;
		foreach((array)$paihang as $key=>$val){
			if($renqi > 0 && $val['ChannelUin'] == $roomid){
				$money = $moneys[$key];
				$rank = $key + 1;
			}
		}
		return array('Score'=>$renqi,'Rank'=>$rank,'RMB'=>$money);
	}
	
	/* 计算一周开始日期结束日期,暂时没有用 */
	private function GetWeekDate($uptime){
		$uptime = strtotime($uptime);
		$dayofweek = date("w",$uptime);
		$week = date('oW',$uptime);
		switch ($dayofweek){
			case 0 : //星期天
				$dayofweek = 7;
			default:
				$startime = $uptime-($dayofweek-1)*86400;
				$endtime = $uptime+(7-$dayofweek)*86400+86399;
		}
		return array($startime,$endtime,$week);
	}
	
	private function getRMB($balance){
		$balance = $balance / 10000;
		if($balance > 10000){
			$rate = 0.90;
			$now_weight = $balance-10000;
			$int_banalce = intval($now_weight * $rate) +3400+2400+1500+700;
		}elseif($balance > 6000){
			$rate = 0.85;
			$now_weight = $balance-6000;
			$int_banalce = intval($now_weight * $rate) +2400+1500+700;
		}elseif($balance > 3000){
			$rate = 0.80;
			$now_weight = $balance-3000;
			$int_banalce = intval($now_weight * $rate) +1500+700;
		}elseif($balance > 1000){
			$rate = 0.75;
			$now_weight = $balance-1000;
			$int_banalce = intval($now_weight * $rate) +700;
		}else{
			$rate = 0.70;
			$now_weight = $balance;
			$int_banalce = intval($now_weight * $rate);
		}
		return array('rate'=>$rate,'int_banalce'=>$int_banalce);
	}
	
	private function channelincomePool($uin,$search_time,$type='ownuin'){
		$stoe = $this->GetWeekDate($search_time);
		$year_week = (int)$stoe[2];//当前周,查询周
		if($year_week > 0 ){
			$sql = "SELECT id,ownuin,`date` FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE {$type}={$uin} AND `status` = 1";
			$result = $this->db->get_results($sql,'ASSOC');
			//基础工资配置
			$node_base_wage = 4;//周节点
			$base_wage_config[$node_base_wage] = array(8400=>650,3360=>300,1680=>250,0=> 0 );
			$base_wage_config[$node_base_wage+1] = array(8400=>650,3360=>300,1680=>0,0=> 0 );
			//扶持奖金配置  数字对应意义  周排行,艺人数量,艺人排行,金额
			$node_add_wage = 3360;
			$node_add_wage2 = 8400;
			//11.26以后
			$add_wage_array[201248] = array(
				$node_add_wage=>array(1,120000,251),
				$node_add_wage2=>array(2,240000,502),
			);
			//11.5-11.25
			$add_wage_array[201245] = array(
				$node_add_wage=>array(1,80000,251),
				$node_add_wage2=>array(2,160000,502),
			);
			//10.15-11.4
			$add_wage_array[201242] = array(
				$node_add_wage=>array(1,50000,251),
				$node_add_wage2=>array(2,100000,502),
			);
			//10.1-10.14
			$add_wage_array[201240] = array(
				$node_add_wage=>array(1,25000,251),
				$node_add_wage2=>array(2,50000,502),
			);
			foreach($add_wage_array as $add_key=>$add_val){
				if($year_week >= $add_key){
					$add_wage_config = $add_val;
					break;
				}
			}
			if(empty($add_wage_config)){ return array('Flag'=>101,'FlagString'=>"配置获取失败");}
			foreach($result as $key=>$row){
				$open_week = date('oW',strtotime($row['date']));//开房间所在周
				$already_week = $year_week - $open_week + 1; //房间距离计算周差
				$own_nick = $this->db->get_var('SELECT name FROM '.DB_NAME_IM.'.join_apply WHERE uid = '.$row['ownuin'].' AND `role_type` = 1 AND apply_status = 1','ASSOC');     //签约艺人
				if(empty($own_nick)){
					continue;
				}
				$actor_var = $this->db->get_var('SELECT COUNT(*) FROM '.DB_NAME_PARTNER.'.channel_user WHERE room_id = '.$row['id'].' AND `type` = 15 AND flag = 1','ASSOC');     //签约艺人
				/**           开始计算室主基本工资       */
				$base_wage = 0;
				$week_rank = array(
					'db'=>'kkyoo_integral',
					'table'=>'week_weight',
					'fields'=>array('Weight'=>''),
					'where'=>array(
						"Ruleid"=>6,
						"Uptime"=>$year_week,
						"ChannelUin"=> (int)$row['id']
					),
					'option'=>array(
						'sort'=>array(
							array('_id','desc')
						)
					)
				);
				$week_rank_result = json_decode(socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($week_rank))),true);//查询房间周排行
				$week_rank_val2 = $week_rank_result['record'][0]['Weight'];
				$week_rank_val = intval($week_rank_val2/1800);
				if($week_rank_val > 1679){ //计算基本工资开关
					$count_node = $already_week > $node_base_wage ? $node_base_wage+1 : $node_base_wage;
					foreach($base_wage_config[$count_node] as $kk=>$val){
						if($week_rank_val >= $kk){
							$base_wage = $val;
							break;
						}
					}
				}
				/**           结束计算室主基本工资       */
				/**           开始计算室主扶持奖金       */
				$add_wage = 0;$roomrank = 0;$total_wage =$base_wage;
				//计算查询周房间总人气
				$query = array("Uptime"=>$year_week,"ChannelUin"=>(int)$row['id']);
				$result = $mongo->get_results('rank_2.week_weight',$query,array(),array('ChannelUin','Weight'));
	            foreach((array)$result as $rekey => $reval){
	                $roomrank += $reval['Weight'];
	            }
				if($week_rank_val >= $node_add_wage && $actor_var >0){ //计算扶持奖金第一个需满足的条件,判断人气值,及艺人数量
					if($roomrank >= $add_wage_config[$node_add_wage][1]){
						$add_wage = $add_wage_config[$node_add_wage][2];
					}
					if($week_rank_val >= $node_add_wage2 && $actor_var >=$add_wage_config[$node_add_wage2][0] && $roomrank >= $add_wage_config[$node_add_wage2][1]){
						$add_wage = $add_wage_config[$node_add_wage2][2];
					}
					$total_wage += $add_wage;
				}
				/**           结束计算室主扶持奖金       */
				if($total_wage >= 0){
					$result_array[$stoe[0]][] = array(
						'roomid'=> $row['id'],
						'ownuin'=>$row['ownuin'],
						'opendate'=>$row['date'],
						'already_week'=>$already_week,
						'week_rank_val'=>number_format($week_rank_val2/1800,2,'.',''),
						'base_wage'=>$base_wage,
						'roomrank'=>$roomrank,
						'actor_var'=>$actor_var,
						'add_wage'=>$add_wage,
						'total_wage'=>$total_wage,
						'nick'=>$own_nick,
						'endtime'=>$stoe[1],
					);
					$sql = "SELECT * FROM ".DB_NAME_PARTNER.".channel_pool WHERE uptime = ".$year_week.' AND roomid = '.$row['id'];
					$pool_row = $this->db->get_row($sql,"ASSOC");
					if(!empty($pool_row)){
						$sql = "UPDATE ".DB_NAME_PARTNER.".channel_pool SET week_rank_val = {$week_rank_val} , base_wage = {$base_wage},roomrank={$roomrank},actor_var={$actor_var},add_wage={$add_wage},total_wage={$total_wage},starttime={$stoe[0]},endtime ={$stoe[1]} WHERE uptime = ".$year_week." AND roomid = ".$row['id'];
					}else{
						$sql = "INSERT INTO ".DB_NAME_PARTNER.".channel_pool (uptime,roomid,ownuin,opendate,already_week,week_rank_val,base_wage,roomrank,actor_var,add_wage,total_wage,nick,starttime,endtime) VALUES (".$year_week.",".$row['id'].",".$row['ownuin'].",'".$row['date']."',{$already_week},{$week_rank_val},{$base_wage},{$roomrank},{$actor_var},{$add_wage},".$total_wage.",'{$own_nick}',{$stoe[0]},{$stoe[1]})";
					}
					$this->db->query($sql);
				}
			}
			return array('Flag'=>100,'FlagString'=>'成功','Data'=>$result_array);	
		}else{
			$sql = "SELECT * FROM ".DB_NAME_PARTNER.".channel_pool ORDER BY uptime DESC";
			$result = $this->db->get_results($sql,"ASSOC");
			foreach($result as $key=>$value){
				$result_array[$value['starttime']][] = $value;
			}
			return array('Flag'=>100,'FlagString'=>'成功','Data'=>$result_array);
		}
	}
	
    private function showPage($total, $perpage = 10) {
        $page = new extpage(array (
            'total' => $total,
            'perpage' => $perpage
        ));
        $pageArr['page'] = $page->simple_page($total);
        $pageArr['limit'] = $page->simple_limit();
        return $pageArr;
    }

}