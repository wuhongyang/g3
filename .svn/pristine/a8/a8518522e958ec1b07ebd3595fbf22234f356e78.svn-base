<?php
abstract class flash_bet_game
{
	//db操作对象
	protected $param;
	protected $extparam;
	protected $cmd;
	public $logdata = array();

	/**
	* 构造函数
	*@param object $db 数据库驱动
	*@return 无
	*/
	public function __get($name){
		$dbkey = $name=='db'? 'default' : $name;
		return $this->$name = db::connect(config('database',$dbkey));
	}
	
	/**
	* 游戏开始初始化
	* @return array
	*/
	abstract public function GameStart();
	
	/**
	* 游戏结束
	* @return array
	*/
	abstract public function GameOver();

	/**
	* 构造函数，初始化请求参数
	*/
	public function __construct(){
		$_POST['param']['Uin'] = intval($_POST['param']['Uin']);
		$_POST['param']['TargetUin'] = empty($_POST['param']['TargetUin'])? $_POST['param']['Uin'] : intval($_POST['param']['TargetUin']);
		$_POST['param']['ChannelId'] = intval($_POST['param']['ChannelId']);
		$_POST['param']['MoneyWeight'] = intval($_POST['param']['MoneyWeight']);
		$this->param = $_POST['param'];
		$this->extparam = $_POST['extparam'];
		$this->cmd = $_POST['param']['flash_cmd'];
	}

	/*
	* 进入游戏
	* @return array
	*/
	public function AuthRequest(){
		if($this->param['Uin'] <= 0) return array('Flag'=>101,'FlagString'=>'参数错误');
		
		//返还历史冻结金额
		$sql = "SELECT SUM(money) FROM ".DB_NAME_FLASH_GAME.".{$this->cmd}_user_bank WHERE uin={$this->param['Uin']} AND roomid={$this->param['ChannelId']} FOR UPDATE";
		$gamebalance = (int)$this->db->get_var($sql);
		
		//游戏配置
		$configure = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'InteractConfigList','Cmd'=>$this->cmd)));
		$configure = (array)$configure['Result']['value'];
		$interactinfo = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetGameInfo','BigCaseId'=>$this->param['BigCaseId'],'CaseId'=>$this->param['CaseId'],'ParentId'=>$this->param['ParentId'])));
		$interactid = $interactinfo['Info']['id'];
		$interact_group = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GetInteractGropuConfig','Interact'=>$interactid,'Groupid'=>(int)$this->param['GroupId'])));
		$configure['group_robot'] = (int)$interact_group['Result']['robot'];
		
		$this->param['Desc'] = '进入场次-获取配置信息';
		$log = getLogData($this->param,$this->extparam);
		$logData[] = $log;
		$money = get_money($this->param['Uin'],$this->param['GroupId']);
		return array('Flag'=>100,'FlagString'=>'成功进入场次','GameBalance'=>$gamebalance,'VoucherBalance'=>$money,'GameConfig'=>$configure);
	}

	/**
	* 将v豆账户余额划入到游戏账户
	* @param $param array 科目参数
	* @param $extparam array 扩展参数 
	*/
	public function CarryMoney(){
		$this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
		$this->db->start_transaction();
		if($this->extparam['Gameid'] <= 0) $this->extparam['Gameid'] = $this->param['ChannelId'];
		//保存冻结资金
		$uptime = time();
		$bank = $this->db->get_row("SELECT * FROM ".DB_NAME_FLASH_GAME.".`{$this->cmd}_user_bank` WHERE uin={$this->param['Uin']} AND gameid='{$this->extparam['Gameid']}' LIMIT 1 FOR UPDATE",'ASSOC');
		if(empty($bank)){
			$sql = "INSERT INTO ".DB_NAME_FLASH_GAME.".`{$this->cmd}_user_bank`(uin,gameid,roomid,money,uptime)VALUES({$this->param['Uin']},'{$this->extparam['Gameid']}',{$this->param['ChannelId']},{$this->param['MoneyWeight']},{$uptime})";
		}else{
			$sql = "UPDATE ".DB_NAME_FLASH_GAME.".`{$this->cmd}_user_bank` SET money=money+{$this->param['MoneyWeight']},uptime={$uptime} WHERE uin={$this->param['Uin']} AND gameid='{$this->extparam['Gameid']}'";
		}
		$query = $this->db->query($sql);
		if( ! $query){
			$this->rollback();
			return array('Flag'=>101,'FlagString'=>'冻结金额存入失败！');
		}
		//机器人业务余额库存入
		if($this->param['Uin']>=GUEST_UIN_START && $this->param['Uin']<=GUEST_UIN_END){
			$p = $this->param;
			$p['BigCaseId'] = 10006;
			$p['CaseId'] = 10049;
			$p['ParentId'] = 10326;
			$p['ChildId'] = 102;
			$logbuild = new logbuild();
			$checkWork = $logbuild->checkWork($p,$this->extparam);
			if($checkWork['Flag'] != 100){
				return $checkWork;
			}
			$query = $this->trade($p);
			if($query['Flag'] != 100){
				$this->rollback();
				return $query;
			}
			$this->param['ChildId'] = 109;
			$this->param['Desc'] = "业务余额库-机器人带入";
			$query = $this->trade($this->param);
			if($query['Flag'] != 100){
				$this->rollback();
				return $query;
			}
		}else{
			//扣除带入金额
			$query = $this->trade($this->param);
			if($query['Flag'] != 100){
				$this->rollback();
				return $query;
			}
			
		}
		//带入成功
		$this->commit();
		$result =  array('Flag'=>100,'FlagString'=>'带入成功！','GameBalance'=>$bank['money']+$this->param['MoneyWeight'],'VoucherBalance'=>$query['VoucherBalance']);
		if(!empty($query['Balance'])) $result['UserBalance'] = $query['Balance'];
		return $result;
	}

	/**
	* 将游戏中的余额全部返回至v豆账户
	* @return array
	*/
	public function BackMoney(){
		//事务开始
		$this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
		$this->db->start_transaction();
		if($this->extparam['Gameid'] <= 0) $this->extparam['Gameid'] = $this->param['ChannelId'];
		$bank = $this->GetGameMoney();
		if($bank['Flag'] != 100) return $bank;

		//清除冻结余额
		if($this->param['MoneyWeight'] > $bank['Money']){
			return array('Flag'=>101,'FlagString'=>'您的游戏币余额不足！');
		}elseif($this->param['MoneyWeight'] > 0 && $this->param['MoneyWeight'] < $bank['Money']){
			$sql = "UPDATE ".DB_NAME_FLASH_GAME.".`{$this->cmd}_user_bank` SET money=money-{$this->param['MoneyWeight']} WHERE uin={$this->param['Uin']} AND gameid='{$this->extparam['Gameid']}'";
            $this->param['Desc'] .= ' 游戏中带出';
        }else{
			$sql = "DELETE FROM ".DB_NAME_FLASH_GAME.".`{$this->cmd}_user_bank` WHERE uin={$this->param['Uin']} AND gameid='{$this->extparam['Gameid']}'";
			$this->param['MoneyWeight'] = $bank['Money'];
            //游戏税收存入
            if($bank['Tax'] > 0){
                $param = $this->param;
                $param['MoneyWeight'] = $bank['Tax'];
                $param['Desc'] .= "退出扣除税收：{$bank['Tax']}";
                $param['TaxType'] = 2;
                $param['ChildId'] = 901;
                $query = $this->trade($param);
                $this->logdata[] = getLogData($param,$this->extparam);
                if($query['Flag'] != 100){
                    $this->rollback();
                    return $query;
                }
            }
		}
		$backmoney = $this->param['MoneyWeight'];
		
		$query = $this->db->query($sql);
		if( ! $query){
			$this->rollback();
			return array('Flag'=>102,'FlagString'=>'返还冻结金额失败！');
		}
		
		if($this->param['Uin']>=GUEST_UIN_START && $this->param['Uin']<=GUEST_UIN_END){
			$this->param['ChildId'] = 110;
		}
		
		if($bank['Money'] > 0){
			//用户存入
			$this->param['Desc'] .= " 局号：{$this->extparam['Gameid']}金额：{$this->param['MoneyWeight']}";
			$back = $this->trade($this->param);
			$this->logdata[] = array('param'=>$this->param,'extparam'=>$this->extparam);
			if($back['Flag'] != 100){
				$this->rollback();
				return $back;
			}
			//机器人带出
			if($this->param['Uin'] >= GUEST_UIN_START && $this->param['Uin'] <= GUEST_UIN_END){
				$p = $this->param;
				$p['BigCaseId'] = 10006;
				$p['CaseId'] = 10049;
				$p['ParentId'] = 10326;
				$p['ChildId'] = 101;
				$p['Desc'] .= " 业务余额库-机器人带出";
				$query = $this->trade($p);
				if($query['Flag'] != 100){
					$this->rollback();
					return $query;
				}
			}
		}
		//返还成功
		$this->commit();
		$result = array('Flag'=>100,'FlagString'=>'带出游戏币成功！','GameBalance'=>$bank['Money']-$backmoney,'VoucherBalance'=>$back['VoucherBalance']);
		if(!empty($back['Balance'])) $result['UserBalance'] = $back['Balance'];
		return $result;
	}

	//游戏结算
	public function GameResult(){
		//事务开始
		$this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
		$this->db->start_transaction();
		if($this->extparam['Gameid'] <= 0) $this->extparam['Gameid'] = $this->param['ChannelId'];
		$usercount = $this->getUserCount();
		$rst = $this->UserLostWin($this->param['Uin'],$this->extparam['Gameid'],$this->extparam['Money'],$this->extparam['Tax'],$this->extparam['Win']);
		if($rst['Flag'] != 100){
			$this->rollback();
			$this->param['Desc'] = $rst['FlagString'];
			$this->logdata[] = array('param'=>$this->param,'extparam'=>$this->extparam);
			return $rst;
		}
		$usercount['betted'] += $this->extparam['BetMoney'];
		if($this->extparam['Win'] == 1){
			$usercount['win'] += $this->extparam['Money'];
		}else{
			$usercount['lost'] += $this->extparam['Money'];
		}
		$sql = "REPLACE INTO ".DB_NAME_FLASH_GAME.".`{$this->cmd}_user_count`(uin,betted,lost,win,uptime)
				VALUES({$this->param['Uin']},{$usercount['betted']},{$usercount['lost']},{$usercount['win']},{$usercount['uptime']})";
		if( ! $this->db->query($sql)){
			$this->rollback();
			$this->param['Desc'] = "用户押注统计失败";
			$this->logdata[] = array('param'=>$this->param,'extparam'=>$this->extparam);
			return array('Flag'=>101,'FlagString'=>'用户押注统计失败！');
		}
		$this->param['Desc'] = $this->extparam['Desc'];
		$this->logdata[] = array('param'=>$this->param,'extparam'=>$this->extparam);
		$this->commit();
		return array('Flag'=>100,'FlagString'=>'ok','TodayBet'=>$usercount['betted'],'TodayWin'=>$usercount['win'],'TodayLost'=>$usercount['lost'],'GameBalance'=>intval($rst['GameBalance']));
	}
	
	public function Betting(){
		$this->param['Desc'] = $this->extparam['Desc'];
		$this->logdata[] = array('param'=>$this->param,'extparam'=>$this->extparam);
		return array('Flag'=>100,'FlagString'=>'ok');
	}
	
	public function Winid(){
		$this->param['Desc'] = $this->extparam['Desc'];
		$this->logdata[] = array('param'=>$this->param,'extparam'=>$this->extparam);
		return array('Flag'=>100,'FlagString'=>'ok');
	}
	
	/**
	* 查询用户冻结余额
	* @return array
	*/
	public function GetGameMoney(){
		if($this->extparam['Gameid'] <= 0) $this->extparam['Gameid'] = $this->param['ChannelId'];
		$sql = "SELECT money,tax FROM ".DB_NAME_FLASH_GAME.".`{$this->cmd}_user_bank` WHERE uin={$this->param['Uin']} AND roomid={$this->param['ChannelId']} AND gameid='{$this->extparam['Gameid']}' LIMIT 1 FOR UPDATE";
		$balance = $this->db->get_row($sql,'ASSOC');
		if(empty($balance) || ($balance['money'] < 0 && $balance['tax'] < 0)){
			return array('Flag'=>101,'FlagString'=>'用户余额不存在');
		}
		return array('Flag'=>100,'FlagString'=>'ok','Money'=>$balance['money'],'Tax'=>$balance['tax']);
	}
	
	//加减游戏币
	protected function UserLostWin($uin,$gameid,$trade_money,$tax,$win){
		if($win==1){
			$type = '+';
			$trade_money -= $tax;
		}else{
			$type = '-';
			$trade_money += $tax;
		}
		$sql = "UPDATE ".DB_NAME_FLASH_GAME.".`{$this->cmd}_user_bank` SET money=money{$type}{$trade_money},tax=tax+{$tax} WHERE uin={$uin} AND gameid='{$gameid}'";
		if( ! $this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'游戏币账户操作失败！');
		}
		$sql = "SELECT * FROM ".DB_NAME_FLASH_GAME.".`{$this->cmd}_user_bank` WHERE uin={$uin} AND gameid='{$gameid}'";
		$bank = $this->db->get_row($sql,'ASSOC');
		return array('Flag'=>100,'GameBalance'=>$bank['money']);
	}
	
	/**
	* 交易金额操作
	*/
	private function trade($param){
		$extparam = array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$param['GroupId']);
		//$log = getLogData($param,$extparam);
		//$extparam['GroupId'] = $log['extparam']['GroupId'];
		$request = array('param'=>$param,'extparam'=>$extparam);
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		$array = array('Flag'=>100,'Balance'=>$rst['LastBalance'],'MoneyWeight'=>$param['MoneyWeight'],'Desc'=>$param['Desc']);
		if($rst['fund_type'] == 'Kmoney'){
			return $array;
		}else{
			$array['VoucherBalance'] = $rst['LastBalance'];
			unset($array['Balance']);
			if(isset($rst['KmoneyBalance'])) $array['Balance'] = $rst['KmoneyBalance'];
		}
		return $array;
	}
	
	/**
	* 提交游戏处理事务
	*/
	protected function commit(){
		$this->db->commit();
	}
	
	/**
	* 回滚游戏处理事务
	*/
	protected function rollback(){
		$this->db->rollback();
	}

}