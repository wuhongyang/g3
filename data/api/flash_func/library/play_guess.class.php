<?php
/**
* 彩票
* @author dl
* @version $Id$
* @copyright (c) 奥点科技
*/
class play_guess
{
	protected $db;
	const ARTIST_RATE = 0.4; //艺人分成比率
	const USER_RATE  = 0.4; //用户分成比率剩余归公司税收

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
	public function StartGame($param){
		$uin_type = getChannelType($param['Uin'],$param['ChannelId']);
		if($uin_type > 0){
			return array('Flag'=>100,'FlagString'=>'发起成功');
		}else{
			return array('Flag'=>101,'FlagString'=>'权限不足，无法启动游戏！');
		}
	}
	
	/**
	* 当选题人（扣除当选题人费用并存入冻结库）
	* @param $param array
	* @param $extparam array
	* @return array
	*/
	public function SelectTopic($param,$extparam){
		$this->db->start_transaction();
		$uptime = time();
		$sql = "INSERT INTO ".DB_NAME_PROPS.".games_guess_freeze(uin,roomid,money,uptime)
				VALUES({$param['Uin']},{$param['ChannelId']},{$param['MoneyWeight']},{$uptime})";
		$rst = $this->db->query($sql);
		$gameid = $this->db->insert_id();
		if($rst){
			$rst = $this->trade($param,102,$param['MoneyWeight'],"当选题人");
			if($rst['Flag'] != 100){
				$this->db->rollback();
				return $rst;
			}
			$this->db->commit();
			$result = array('Flag'=>100,'FlagString'=>'当选题人成功！','Gameid'=>$gameid,'VoucherBalance'=>(int)$rst['VoucherBalance']);
			if(!empty($rst['Balance'])) $result['Balance'] = (int)$rst['Balance'];
			return $result;
		}else{
			return array('Flag'=>101,'FlagString'=>'当选题人失败！');
		}
	}
	
	/**
	* 刷新题目（往冻结库累加余额）
	* @param $param array
	* @param $extparam array
	* @return array
	*/
	public function RefreshTopic($param,$extparam){
		$this->db->start_transaction();
		$uptime = time();
		$sql = "UPDATE ".DB_NAME_PROPS.".games_guess_freeze SET money=money+{$param['MoneyWeight']}
				WHERE uin={$param['Uin']} AND roomid={$param['ChannelId']} AND gameid='{$extparam['Gameid']}'";
		$rst = $this->db->query($sql);
		if( ! $rst){
			$this->db->rollback();
			return array('Flag'=>100,'FlagString'=>'刷新题目失败！');
		}
		$rst = $this->trade($param,103,$param['MoneyWeight'],"刷新题目");
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return $rst;
		}
		$this->db->commit();
		$rst['FlagString'] = '刷新题目成功！';
		return $rst;
	}
	
	/**
	* 提交答案(记录日志)
	* @return array
	*/
	public function SubmitTopic(){
		return array('Flag'=>100,'FlagString'=>'ok');
	}
	
	/**
	* 用户结算（公司，艺人，用户赢得存入）
	* @param $param array
	* @param $extparam array
	* @return array 结算结果
	*/
	public function WinUsers($param,$extparam){
		$sql = "SELECT money FROM ".DB_NAME_PROPS.".games_guess_freeze WHERE roomid={$param['ChannelId']} AND gameid='{$extparam['Gameid']}'";
		$money = intval($this->db->get_var($sql));
		$usernum = count($extparam['Users']);
		$artist_money = floor($money * self::ARTIST_RATE); //艺人税收部份
		$user_money = floor($money * self::USER_RATE); //用户所得部份
		$tax_money = floor($money - ($artist_money + $user_money)); //公司税收部份
		
		if($usernum > 0){
			$user_money = $user_money / $usernum; //平均每个用户所得
			$tax_money += floor(($user_money - floor($user_money)) * $usernum); //除不尽剩余的计入公司税收
			$user_money = floor($user_money); //平均用户所得V豆
		}else{
			$tax_money += $user_money; //没有用户答对，则将用户部份记录公司税收
			$user_money = 0;
		}
		if($tax_money < 0){
			return array('Flag'=>101,'FlagString'=>'结算失败！');
		}
		//公司税收收入
		$tax_rst = $this->trade($param,901,$tax_money,"房间:{$param['ChannelId']},你演我猜游戏,税收:{$tax_money}");
		if($tax_rst['Flag'] != 100){
			return $tax_rst;
		}
		
		$channel_relation = getChannelRelation($param['ChannelId']);
		$extparam['RegionId'] = (int)$channel_relation['RegionId'];
		$extparam['GroupId'] = (int)$channel_relation['GroupId'];
		
		$log[] = array('param'=>array('Uin'=>$param['Uin'],'TargetUin'=>$param['TargetUin'],'OwnUin'=>(int)$channel_relation['OwnUin'],'CompanyUin'=>(int)$channel_relation['CompanyUin'],'RegionUin'=>(int)$channel_relation['RegionUin'],'GroupUin'=>(int)$channel_relation['GroupUin'],'ChannelId'=>$param['ChannelId'],'BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>901,'MoneyWeight'=>$tax_money,'DoingWeight'=>$param['DoingWeight'],'TaxType'=>2,'Desc'=>"你演我猜税收{$tax_money}"),'extparam'=>$extparam);
		
		//艺人税收收入
		$artist_rst = $this->trade($param,908,$artist_money,"房间:{$param['ChannelId']},你演我猜游戏,艺人税收:{$artist_money}");
		if($artist_rst['Flag'] != 100){
			return $artist_rst;
		}
		$artist_rst['Uin'] = $param['Uin'];
		$log[] = array('param'=>array('Uin'=>$param['Uin'],'TargetUin'=>$param['TargetUin'],'ActorUin'=>$param['TargetUin'],'OwnUin'=>(int)$channel_relation['OwnUin'],'CompanyUin'=>(int)$channel_relation['CompanyUin'],'RegionUin'=>(int)$channel_relation['RegionUin'],'GroupUin'=>(int)$channel_relation['GroupUin'],'ChannelId'=>$param['ChannelId'],'BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>908,'MoneyWeight'=>$artist_money,'DoingWeight'=>$param['DoingWeight'],'TaxType'=>1,'Desc'=>"你演我猜税收{$artist_money}"),'extparam'=>$extparam);
		
		//多用户结算
		$user_rst = array();
		foreach((array)$extparam['Users'] as $uin){
			$param['Uin'] = $uin;
			$param['TargetUin'] = $uin;
			$rst = $this->trade($param,106,$user_money,'用户结算');
			$rst['Uin'] = $uin;
			$user_rst[] = $rst;
		}
		
		//清除冻结余额
		$sql = "DELETE FROM ".DB_NAME_PROPS.".games_guess_freeze WHERE roomid={$param['ChannelId']} AND gameid='{$extparam['Gameid']}'";
		$this->db->query($sql);
		return array('Flag'=>100,'FlagString'=>'结算成功！','Artist'=>$artist_rst,'Users'=>$user_rst,'ArtistMoney'=>$artist_money,'UserMoney'=>$user_money,'LogData'=>$log);
	}
	
	/**
	* 交易金额操作
	*/
	private function trade($param,$child,$money,$desc){
		$param['ChildId'] = $child;
		$param['MoneyWeight'] = $money;
		$param['Desc'] = $desc;
		$extparam = array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$param['GroupId']);
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