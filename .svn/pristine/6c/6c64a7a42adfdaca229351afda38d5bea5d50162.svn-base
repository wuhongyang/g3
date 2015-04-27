<?php
require_once dirname(__FILE__).'/base/flash_bet_game.class.php';
class Flash_gamestar extends flash_bet_game
{

	public function AuthRequest(){
		$logbuild = new logbuild();
		$query = $logbuild->checkWork($this->param,$this->extparam);
		if($query['Flag'] != 100){
			return $query;
		}
		$result = parent::AuthRequest();
		if($result['Flag'] != 100) return $result;
		$usercount = $this->getUserCount();
		$total_money = get_parent_money($this->param['BigCaseId'],$this->param['CaseId'],$this->param['ParentId'],$this->param['GroupId']);
		$usercount = array('TotalMoney'=>$total_money,'TodayBet'=>$usercount['betted'],'TodayWin'=>$usercount['win'],'TodayLost'=>$usercount['lost']);
		return array_merge($result,$usercount);
	}

	public function GameStart(){
		$logbuild = new logbuild();
		$query = $logbuild->checkWork($this->param,$this->extparam);
		if($query['Flag'] != 100){
			return $query;
		}
		$gameresult = $this->db->get_row("SELECT `gameid` FROM ".DB_NAME_FLASH_GAME.".`{$this->cmd}_game_result` WHERE roomid={$this->param['ChannelId']} ORDER BY `gameid` DESC LIMIT 1 FOR UPDATE",'ASSOC');
		$gameid = intval($gameresult['gameid']) +1;
		$uptime = time();
		$sql = "INSERT INTO ".DB_NAME_FLASH_GAME.".`{$this->cmd}_game_result`(gameid,roomid,uptime)values({$gameid},{$this->param['ChannelId']},{$uptime})";
		if( ! $this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'游戏开始失败，无法生成局号！');
		}
		$today = date('Ymd');
		$sql = "SELECT * FROM ".DB_NAME_FLASH_GAME.".`{$this->cmd}_game_count` WHERE roomid={$this->param['ChannelId']}";
		$gamecount = $this->db->get_row($sql,'ASSOC');
		if($gamecount['uptime'] == $today){
			$gamecount['today_betted'] = json_decode($gamecount['today_betted'],true);
			foreach($this->extparam['History'] as $key=>$val){
				$this->extparam['History'][$key]['money'] += $gamecount['today_betted'][$key]['money'];
				$this->extparam['History'][$key]['num'] += $gamecount['today_betted'][$key]['num'];
			}
		}
		$this->extparam['History'] = json_encode($this->extparam['History']);
		$this->extparam['LastBetted'] = empty($this->extparam['LastBetted'])? $gamecount['last_betted'] : json_encode($this->extparam['LastBetted']);
		$sql = "REPLACE INTO ".DB_NAME_FLASH_GAME.".`{$this->cmd}_game_count`(roomid,today_betted,last_betted,uptime)
				VALUES({$this->param['ChannelId']},'{$this->extparam['History']}','{$this->extparam['LastBetted']}',{$today})";
		if( ! $this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'游戏开始失败，押注统计错误！');
		}
		$this->param['Desc'] = "第：{$gameid} 局开始";
		$this->logdata[] = getLogData($this->param,$this->extparam);
		return array('Flag'=>100,'FlagString'=>'游戏开始','Gameid'=>$gameid,'History'=>$this->extparam['History'],'LastBetted'=>$this->extparam['LastBetted']);
	}
	
	
	public function CarryMoney(){
		$money = parent::CarryMoney();
		if($money['Flag'] != 100) return $money;
		$money['ParentMoney'] = get_parent_money($this->param['BigCaseId'],$this->param['CaseId'],$this->param['ParentId'],$this->param['GroupId']);
		return $money;
	}
	
	public function BackMoney(){
		$money = parent::BackMoney();
		if($money['Flag'] != 100) return $money;
		$money['ParentMoney'] = get_parent_money($this->param['BigCaseId'],$this->param['CaseId'],$this->param['ParentId'],$this->param['GroupId']);
		return $money;
	}
	
	public function GameOver(){
		$this->param['Desc'] = $this->extparam['Desc'];
		$this->logdata[] = array('param'=>$this->param,'extparam'=>$this->extparam);
		return array('Flag'=>100,'FlagString'=>'ok');
    }
	
	protected function getUserCount(){
		$sql = "SELECT * FROM ".DB_NAME_FLASH_GAME.".`{$this->cmd}_user_count` WHERE uin={$this->param['Uin']}";
		$usercount = $this->db->get_row($sql,'ASSOC');
		$today = date('Ymd');
		if($usercount['uptime'] != $today){
			$usercount['uin'] = $this->param['Uin'];
			$usercount['betted'] = 0;
			$usercount['lost'] = 0;
			$usercount['win'] = 0;
			$usercount['uptime'] = $today;
		}
		return $usercount;
	}
}
