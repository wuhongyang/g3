<?php
/**
* 疯狂转转
* @author dl
* @version $Id$
* @copyright (c) 奥点科技
*/
class crazy_roll
{
	protected $db;

	public function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	public function getConfig($cmd){
		if(empty($cmd)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$rows = $this->db->get_row("SELECT `value`,`status` FROM ".DB_NAME_PROPS.".props_config WHERE `key`='{$cmd}_config' LIMIT 1",'ASSOC');
		$config = unserialize($rows['value']);
		if($rows['status'] < 1){
			return array('Flag'=>102,'FlagString'=>'游戏正在维护中，请稍候再进入！');
		}
		$default = array('Flag'=>100,'FlagString'=>'ok','status'=>$rows['status']);
		foreach((array)$config['extend'] as $key=>$val){
			$config['extend'][$key] = htmlspecialchars_decode($val,ENT_QUOTES);
		}
		$config = array_merge($default,(array)$config['fixed'],(array)$config['extend']);
		return $config;
	}
	
	/**
	* 发起游戏（判断是否艺人，只有艺人才能发起）
	* @param $param array
	* @return array
	*/
	public function startGame($param,$extparam){
		//检测是否为艺人
		$uin_type = getChannelType($param['Uin'],$param['ChannelId']);
		if($uin_type < 1){
			return array('Flag'=>101,'FlagString'=>'权限不足，无法启动游戏！');
		}
		
		$users = json_encode(array('users'=>array(),'down'=>array()));
		$sql = "INSERT INTO ".DB_NAME_PROPS.".games_crazy_roll(roomid,artist,money,users,uptime)
				VALUES({$param['ChannelId']},{$param['Uin']},'{$param['MoneyWeight']}','{$users}',".time().")";
		$rst = $this->db->query($sql);
		if(!$rst){
			return array('Flag'=>104,'FlagString'=>'无法启动游戏');
		}
		$gameid = $this->db->insert_id();
		
		//得到游戏配置
		//$config = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'FunctionPropsList','Data'=>array('cmd'=>'CMD_FENGKUANGZHUANZHUAN'))));
		return array('Flag'=>100,'FlagString'=>'ok','Gameid'=>$gameid);
	}
	
	//抢座位
	public function grapSeat($param,$extparam){
		//是否为游客
		if($param['Uin']>=GUEST_UIN_START && $param['Uin']<=GUEST_UIN_END){
			return array('Flag'=>101,'FlagString'=>'游客不能抢座位');
		}

		$gameInfo = $this->getGameInfo($extparam['Gameid']);		
		$users = json_decode($gameInfo['users'],true);
		
		//判断是否下座,下座不能抢本轮座位
		if(in_array($param['Uin'],$users['down'])){
			return array('Flag'=>104,'FlagString'=>'您已在本轮内抢座位过了！');
		}
		
		//发起者不能抢座
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_PROPS.".games_crazy_roll WHERE roomid={$param['ChannelId']} AND uin={$param['Uin']}";
		$isSelf = $this->db->get_var($sql);
		if($isSelf > 0){
			return array('Flag'=>102,'FlagString'=>'发起者不能抢座位！');
		}
		//余额是否够
		$balance = get_money($param['Uin'],$extparam['GroupId']);
		if($balance < $param['MoneyWeigth']){
			return array('Flag'=>103,'FlagString'=>'您的金币余额不足，不能抢座位！');
		}
		
		//记录抢座位的人
		$users['users'][] = $param['Uin'];
		$users = json_encode($users);
		
		$this->db->start_transaction();
		$sql = "UPDATE ".DB_NAME_PROPS.".games_crazy_roll SET `users`='{$users}' WHERE gameid={$extparam['Gameid']}";
		$rst = $this->db->query($sql);
		if(!$rst){
			$this->db->rollback();
			return array('Flag'=>105,'FlagString'=>'抢座位失败');
		}
		//扣除V豆
		$trade_rst = $this->trade($param,102,$gameInfo['money'],"疯狂转转转---抢座位");
		if($trade_rst['Flag'] != 100){
			$this->db->rollback();
			return $trade_rst;
		}
		$this->db->commit();
		return $trade_rst;
	}
	
	//下座
	public function downSeat($param,$gameid){
		$uin = intval($param['Uin']);
		$gameid = intval($gameid);
		if($uin<1 || $gameid<1){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		$gameInfo = $this->getGameInfo($gameid);
		//删除座上序列
		$users = json_decode($gameInfo['users'],true);
		if(!in_array($uin,$users['users'])){
			return array('Flag'=>102,'FlagString'=>'不在座位序列，不能下座');
		}
		$key = array_search($uin, $users['users']); 
		unset($users['users'][$key]);
		//加到下座序列
		array_push($users['down'],$uin);
		
		$users = json_encode($users);
		
		$this->db->start_transaction();
		$sql = "UPDATE ".DB_NAME_PROPS.".games_crazy_roll SET `users`='{$users}' WHERE gameid={$gameid}";
		$rst = $this->db->query($sql);
		if(!$rst){
			$this->db->rollback();
			return array('Flag'=>104,'FlagString'=>'下座失败');
		}
		//给用户加V豆
		$trade_rst = $this->trade($param,103,$gameInfo['money'],"疯狂转转转---下座位");
		if($trade_rst['Flag'] != 100){
			$this->db->rollback();
			return $trade_rst;
		}
		$this->db->commit();
		return $trade_rst['balance'];
	}
	
	//游戏结算
	public function gameResult($param,$extparam){
		$gameid = intval($extparam['Gameid']);
		$uin = intval($param['Uin']);
		$isPlayed = intval($extparam['IsPlayed']);
		if($gameid<=0 || $uin<=0){
			return array('Flag'=>101,'FlagString'=>'参数不正确');
		}
		$gameInfo = $this->getGameInfo($gameid);
		$users = json_decode($gameInfo['users'],true);
		//玩家不在其中
		if(!in_array($uin,$users['users'])){
			return array('Flag'=>102,'FlagString'=>'该玩家不在座位序列');
		}
		$key = array_search($uin, $users['users']); 
		unset($users['users'][$key]);
		
		$sql = "UPDATE ".DB_NAME_PROPS.".games_crazy_roll SET users='".json_encode($users)."' WHERE gameid={$gameid}";
		$rst = $this->db->query($sql);
		if(!$rst){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'结算失败');
		}
		if($isPlayed == 1){
			//把钱存入税收
			$artistMoneyPerPerson = $gameInfo['money'] * 0.4;
			$companyMoneyPerPerson = $gameInfo['money'] * 0.6;
			
			$channel_relation = getChannelRelation($param['ChannelId']);
			$extparam['RegionId'] = (int)$channel_relation['RegionId'];
			$extparam['GroupId'] = (int)$channel_relation['GroupId'];
			
			$desc = '疯狂转转转，游戏结算，公司税收:'.$companyMoneyPerPerson;
			$trade_rst = $this->trade($param,901,$companyMoneyPerPerson,$desc);			
			
			$log[] = array('param'=>array('Uin'=>$param['Uin'],'TargetUin'=>$param['TargetUin'],'OwnUin'=>(int)$channel_relation['OwnUin'],'CompanyUin'=>(int)$channel_relation['CompanyUin'],'RegionUin'=>(int)$channel_relation['RegionUin'],'GroupUin'=>(int)$channel_relation['GroupUin'],'ChannelId'=>$param['ChannelId'],'BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>901,'MoneyWeight'=>$param['MoneyWeight'],'DoingWeight'=>$param['DoingWeight'],'Desc'=>$desc,'TaxType'=>2),'extparam'=>$extparam);
			
			$desc = '疯狂转转转，游戏结算，艺人税收:'.$artistMoneyPerPerson;
			$trade_rst = $trade_rst = $this->trade($param,908,$artistMoneyPerPerson,$desc);
			
			$log[] = array('param'=>array('Uin'=>$param['Uin'],'TargetUin'=>$param['TargetUin'],'OwnUin'=>(int)$channel_relation['OwnUin'],'CompanyUin'=>(int)$channel_relation['CompanyUin'],'RegionUin'=>(int)$channel_relation['RegionUin'],'GroupUin'=>(int)$channel_relation['GroupUin'],'ChannelId'=>$param['ChannelId'],'BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>908,'MoneyWeight'=>$param['MoneyWeight'],'DoingWeight'=>$param['DoingWeight'],'Desc'=>$desc,'TaxType'=>1),'extparam'=>$extparam);
		}else{
			$trade_rst = $this->trade($param,103,$gameInfo['money'],"疯狂转转转,游戏结算，V豆返回");
		}
		if($trade_rst['Flag'] != 100){
			$this->db->rollback();
			return $trade_rst;
		}
		$trade_rst['LogData'] = $log;
		return $trade_rst;
	}
	
	//游戏结束
	public function gameOver($gameid){
		$gameid = intval($gameid);
		if($gameid <= 0){
			return array('Flag'=>101,'FlagString'=>'参数不正确');
		}
		$users = json_encode(array('users'=>array(),'down'=>array()));
		$sql = "DELETE FROM ".DB_NAME_PROPS.".games_crazy_roll WHERE gameid={$gameid}";
		$rst = $this->db->query($sql);
		if(!$rst){
			return array('Flag'=>102,'FlagString'=>'游戏结束失败');
		}
		return array('Flag'=>100,'FlagString'=>'游戏结束成功');
	}
	
	private function getGameInfo($gameid){
		$sql = "SELECT artist,users,money FROM ".DB_NAME_PROPS.".games_crazy_roll WHERE gameid={$gameid}";
		$gameInfo = $this->db->get_row($sql,ASSOC);
		return $gameInfo;
	}
	
	/**
	* 交易金额操作
	*/
	private function trade($param,$child,$money,$desc){
		$param['ChildId'] = $child;
		$param['MoneyWeight'] = $money;
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
}
