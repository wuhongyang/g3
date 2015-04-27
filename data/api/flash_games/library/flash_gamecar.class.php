<?php
require_once dirname(__FILE__).'/base/flash_bet_game.class.php';
class Flash_gamecar extends flash_bet_game
{
	public function AuthRequest(){
		$result = parent::AuthRequest();
		if($result['Flag'] != 100) return $result;
		$usercount = $this->getUserCount();
		$usercount = array('TodayBet'=>$usercount['betted'],'TodayWin'=>$usercount['win'],'TodayLost'=>$usercount['lost']);
		return array_merge($result,$usercount);
	}
	
	public function GameStart(){
		$history = $this->db->get_row("SELECT `gameid` FROM ".DB_NAME_FLASH_GAME.".`{$this->cmd}_game_result` WHERE roomid={$this->param['ChannelId']} ORDER BY `gameid` DESC LIMIT 1 FOR UPDATE",'ASSOC');
		$gameid = intval($history['gameid']) +1;
		$uptime = time();
		$sql = "INSERT INTO ".DB_NAME_FLASH_GAME.".`{$this->cmd}_game_result`(gameid,roomid,uptime)values({$gameid},{$this->param['ChannelId']},{$uptime})";
		if( ! $this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'游戏开始失败，无法生成局号！');
		}
		$this->param['Desc'] = "第：{$gameid} 局开始";
		$this->logdata[] = getLogData($this->param,$this->extparam);
		return array('Flag'=>100,'FlagString'=>'游戏开始','Gameid'=>$gameid);
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
