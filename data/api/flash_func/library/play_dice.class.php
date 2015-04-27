<?php
/**
* 骰子
* @author dl
* @version $Id$
* @copyright (c) 奥点科技
*/
class play_dice
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
	* @param $extparam array
	* @return array
	*/
	public function startGame($param,$extparam){
		$uin_type = getChannelType($param['Uin'],$param['ChannelId']);
		if($uin_type < 1){
			return array('Flag'=>101,'FlagString'=>'权限不足，无法启动游戏！');
		}
		$sql = "INSERT INTO ".DB_NAME_PROPS.".games_dice_freeze(roomid,artist,giftid,giftnum,money,uptime)
				VALUES({$param['ChannelId']},{$param['Uin']},{$extparam['Giftid']},{$extparam['Giftnum']},{$extparam['Money']},".time().")";
		$rst = $this->db->query($sql);
		if( ! $rst){
			return array('Flag'=>102,'FlagString'=>'无法启动游戏');
		}
		$gameid = $this->db->insert_id();
		return array('Flag'=>100,'FlagString'=>'ok','Gameid'=>$gameid);
	}
	
	/**
	* 用户准备
	* @param $param array
	* @param $extparam array
	* @return array
	*/
	public function userReady($param,$extparam){
		$gameinfo = $this->getGameInfo($extparam['Gameid']);
		if(empty($gameinfo)){
			return array('Flag'=>101,'FlagString'=>'该局游戏不存在');
		}
		if(in_array($param['Uin'],$gameinfo['users'])){
			return array('Flag'=>102,'FlagString'=>'您已经准备');
		}
		
		//记录冻结余额
		$this->db->start_transaction();
		$gameinfo['users'][] = $param['Uin'];
		$users = json_encode($gameinfo['users']);
		$sql = "UPDATE ".DB_NAME_PROPS.".games_dice_freeze SET users='{$users}' WHERE gameid={$extparam['Gameid']}";
		$user_ready = $this->db->query($sql);
		if( ! $user_ready){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'准备失败');
		}
		
		//扣除V豆
		$param['TargetUin'] = $param['Uin'];
		$trade_rst = $this->trade($param,102,$gameinfo['money'],"准备大话骰子王");
		if($trade_rst['Flag'] != 100){
			$this->db->rollback();
			return $trade_rst;
		}
		$this->db->commit();
		return $trade_rst;
	}
	
	/**
	* 取消或逃跑游戏
	* @param array $param 业务参数
	* @param array $extparam 业务扩展参数
	* @return array
	*/
	public function cancelGame($param,$extparam){
		$gameinfo = $this->getGameInfo($extparam['Gameid']);
		if(empty($gameinfo)){
			return array('Flag'=>101,'FlagString'=>'该局游戏不存在');
		}
		$trade_rst = array();
		if($gameinfo['artist'] == $param['Uin']){ //艺人取消游戏归还
			foreach($gameinfo['users'] as $key=>$user){
				$param['Uin'] = $user;
				$param['TargetUin'] = $user;
				$trade_rst[] = $this->trade($param,106,$gameinfo['money'],"取消大话骰子游戏归还");
			}
			//艺人取消删除当局游戏
			$del_gameid = "DELETE FROM ".DB_NAME_PROPS.".games_dice_freeze WHERE gameid={$extparam['Gameid']}";
			$this->db->query($del_gameid);
		}else{
			if(count($gameinfo['users']) == 1){ //未开始游戏前取消
				$param['Uin'] = current($gameinfo['users']);
				$param['TargetUin'] = $param['Uin'];
				$trade_rst[] = $this->trade($param,106,$gameinfo['money'],"取消大话骰子游戏归还");
				//玩家取消准备更新参与人员
				$del_gameid = "UPDATE ".DB_NAME_PROPS.".games_dice_freeze SET users='[]' WHERE gameid={$extparam['Gameid']}";
				$this->db->query($del_gameid);
			}else{ //游戏中逃跑
				$key = $this->getInArrayKey($param['Uin'],$gameinfo['users']);
				unset($gameinfo['users'][$key]);
				$money = floor($gameinfo['money'] / (count($gameinfo['users']) +1));
				foreach($gameinfo['users'] as $user){
					$param['Uin'] = $user;
					$param['TargetUin'] = $user;
					$trade_rst[] = $this->trade($param,106,$gameinfo['money']+$money,"大话骰子用户逃跑获得{$money}，带入归还{$gameinfo['money']}");
				}
				$param['Uin'] = $gameinfo['artist'];
				$param['TargetUin'] = $gameinfo['artist'];
				$trade_rst[] = $this->trade($param,106,$money,"大话骰子用户逃跑获得{$money}");
				//玩家逃跑删除当局游戏
				$del_gameid = "DELETE FROM ".DB_NAME_PROPS.".games_dice_freeze WHERE gameid={$extparam['Gameid']}";
				$this->db->query($del_gameid);
			}
			
		}
		return array('Flag'=>100,'FlagString'=>'ok','TradeUser'=>$trade_rst);
	}
	
	/**
	* 游戏结算
	* @param $param array
	* @param $extparam array
	* @return array
	*/
	public function gameOver($param,$extparam){
		$win = $param['TargetUin'];
		$lose = $param['Uin'];
		$gameinfo = $this->getGameInfo($extparam['Gameid']);
		if(empty($gameinfo)){
			return array('Flag'=>101,'FlagString'=>'该局游戏不存在');
		}
		$trade_rst = $send_rst = array();
		if($lose == $gameinfo['artist']){ //艺人输
			$log[] = array('param'=>array('Uin'=>$gameinfo['artist'],'TargetUin'=>$gameinfo['artist'],'ChannelId'=>$param['ChannelId'],'BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>105,'MoneyWeight'=>0,'DoingWeight'=>0,'Desc'=>"艺人输"),'extparam'=>array());
			foreach($gameinfo['users'] as $user){
				$param['Uin'] = $user;
				$param['TargetUin'] = $user;
				$trade_rst[] = $this->trade($param,106,$gameinfo['money'],'大话骰子归还');
			}
		}else{
			//返还用户冻结金额
			foreach($gameinfo['users'] as $user){
				$param['Uin'] = $user;
				$param['TargetUin'] = $user;
				if($user == $lose){
					$trade_rst[] = $this->trade($param,107,$gameinfo['money'],"大话骰子输{$gameinfo['money']}");
				}else{
					$trade_rst[] = $this->trade($param,106,$gameinfo['money'],'大话骰子归还');
				}
			}
			$send = $param;
			$send['BigCaseId'] = 10001;
			$send['CaseId'] = 10022;
			$send['ParentId'] = $gameinfo['giftid'];
			$send['ChildId'] = 101;
			$send['TargetUin'] = $win;
			$send['DoingWeight'] = $gameinfo['giftnum'];
			$send['MoneyWeight'] = $gameinfo['money'];
			$request = json_encode(array('param'=>$send,'extparam'=>array('Tag'=>'SendGift')));
			$send_rst = json_decode(socket_request('http://127.0.0.1/data/index.php',array('parameter'=>$request)),true);
			$trade_rst = array(
				array('Flag'=>$send_rst['Flag'],'Uin'=>$lose,'balance'=>$send_rst['VoucherBalance']),
				array('Flag'=>$send_rst['Flag'],'Uin'=>$win,'balance'=>$send_rst['ToVoucherBalance'])
			);
		}
		
		//删除当局游戏
		$del_gameid = "DELETE FROM ".DB_NAME_PROPS.".games_dice_freeze WHERE gameid={$extparam['Gameid']}";
		$this->db->query($del_gameid);
		
		return array('Flag'=>100,'FlagString'=>'ok','TradeUser'=>$trade_rst);
	}
	
	/**
	* 获取一局游戏信息
	* @param array $gameid 游戏id
	* @return array
	*/
	private function getGameInfo($gameid){
		//查询局号信息
		$sql = "SELECT * FROM ".DB_NAME_PROPS.".games_dice_freeze WHERE gameid={$gameid} FOR UPDATE";
		$gameinfo = $this->db->get_row($sql,'ASSOC');
		if(empty($gameinfo)){
			return false;
		}
		$gameinfo['users'] = empty($gameinfo['users'])? '[]' : $gameinfo['users'];
		$gameinfo['users'] = (array)json_decode($gameinfo['users']);
		return $gameinfo;
	}
	
	private function getInArrayKey($stack,$array){
		foreach($array as $key=>$val){
			if($stack == $val){
				return $key;
			}
		}
	}
	
	/**
	* 交易金额操作
	* @param $parent interge 一级业务
	* @param $child interge 二级业务
	* @param $uin interge 用户id
	* @param $money floot 交易金额
	* @param $desc string 描述
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