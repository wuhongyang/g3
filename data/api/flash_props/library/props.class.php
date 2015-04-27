<?php
/**
* 道具游戏接口
* @author dl
* @version $Id$
* @copyright (c) 奥点科技
*/
class Props
{
	//db操作对象
	protected $db;
	const PAY_KMONEY   = 103; //V豆支付
	const STORE_KMONEY = 102; //V豆存入
	const STORE_INCOME = 101; //公司税金存入
	const OPERATOR = '67CB9A8B12FC827EF5C008EE4F1B2E0F';

	/**
	* 构造函数
	*@param object $db 数据库驱动
	*@return 无
	*/
	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	//存储游戏积分
	public function storeGameScore($cmd,$uin,$level,$score,$cleartime){
		$sql = "SELECT score FROM ".DB_NAME_PROPS.".games_result WHERE cmd='{$cmd}' AND uin={$uin} AND `level`={$level}";
		$upscore = intval($this->db->get_var($sql));
		$rst = true;
		if($upscore < $score){
			$uptime = time();
			$sql = "REPLACE INTO ".DB_NAME_PROPS.".games_result(cmd,uin,`level`,score,cleartime,uptime)VALUES('{$cmd}',{$uin},{$level},{$score},{$cleartime},{$uptime})";
			$rst = $this->db->query($sql);
		}
		$sql = "SELECT * FROM ".DB_NAME_PROPS.".games_result WHERE cmd='{$cmd}' AND `level`={$level} ORDER BY score DESC LIMIT 1";
		$first = $this->db->get_row($sql,'ASSOC');
		$record = $score >= $first['score']? 1 : 0;
		if(!empty($first)){
			$userinfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$first['uin'])));
			$first['nick'] = $userinfo['Nick'];
		}
		if($rst){
			return array('Flag'=>100,'FlagString'=>'存入成功','First'=>$first,'Record'=>$record);
		}else{
			return array('Flag'=>102,'FlagString'=>'存入失败','First'=>$first,'Record'=>$record);
		}
	}
	
	//获取游戏积分
	public function getGameScore($cmd,$uin,$level){
		$level = implode(',',(array)$level);
		$sql = "SELECT * FROM ".DB_NAME_PROPS.".games_result WHERE cmd='{$cmd}' AND uin={$uin} AND `level` IN({$level}) ORDER BY `level` ASC";
		$score = $this->db->get_results($sql,'ASSOC');
		if(empty($score)){
			return array('Flag'=>102,'FlagString'=>'没有排行信息');
		}else{
			return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>$score);
		}
	}
	
	//获取游戏排名
	public function getGameScoreRank($cmd,$uin,$level,$num){
		$sql = "SELECT * FROM ".DB_NAME_PROPS.".games_result WHERE cmd='{$cmd}' AND uin={$uin} AND `level`={$level} LIMIT 1";
		$myscore = $this->db->get_row($sql,'ASSOC');
		$myscore['score'] = intval($myscore['score']);
		$userinfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$uin)));
		$myscore['nick'] = $userinfo['Nick'];
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_PROPS.".games_result WHERE score > {$myscore['score']} AND `level`={$level}";
		$myscore['rank'] = intval($this->db->get_var($sql))+1;
		
		$sql = "SELECT * FROM ".DB_NAME_PROPS.".games_result WHERE cmd='{$cmd}' AND `level`={$level} AND score > {$myscore['score']} AND uin!={$uin} ORDER BY score DESC LIMIT {$num}";
		$myup = $this->db->get_results($sql,'ASSOC');
		$sql = "SELECT * FROM ".DB_NAME_PROPS.".games_result WHERE cmd='{$cmd}' AND `level`={$level} AND score <= {$myscore['score']} AND uin!={$uin} ORDER BY score DESC LIMIT {$num}";
		$mydown = $this->db->get_results($sql,'ASSOC');
		
		$myupcount = count($myup);
		foreach($myup as $key=>$val){
			$userinfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val['uin'])));
			$myup[$key]['nick'] = $userinfo['Nick'];
			$myup[$key]['rank'] = $myscore['rank'] - ($myupcount-$key);
		}
		$mydowncount = count($mydown);
		foreach($mydown as $key=>$val){
			$userinfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$val['uin'])));
			$mydown[$key]['nick'] = $userinfo['Nick'];
			$mydown[$key]['rank'] = $myscore['rank'] + $key +1;
		}

		return array('Flag'=>100,'MyScore'=>$myscore,'UpUser'=>$myup,'DownUser'=>$mydown);
	}

	//发起
	public function propApply($param){
		$logbuild = new logbuild();
		$query = $logbuild->checkWork($param,$extparam);
		if($query['Flag'] != 100){
			$this->rollback();
			return $query;
		}
		
		$cmdInfo = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetFlashCMD','BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'])));
		$cmd = $cmdInfo['FlashCMD'];
		$config = $this->getConfig($cmd);
		if($config['Flag'] != 100) return $config;
		//游戏启动时间
		$date = date('H:i:s',time());
		if($date < $config['starttime'] || $date > $config['endtime']){
			return array('Flag'=>103,'FlagString'=>'该游戏道具尚未开启，请稍等片刻！');
		}
		//参与次数上限
		$daycount = $this->getCountPlay($cmd,$param['Uin']);
		if($daycount['playnum'] >= $config['maxplay']){
			return array('Flag'=>104,'FlagString'=>'您已达到该游戏的参与次数上限，不能继续参与！');
		}
		
		$info = array(
			'param'=>array('BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId']),
			'extparam'=>array("Tag"=> "GetBusinessBalance",'GroupId'=>$param['GroupId'])
		);
		$rst = httpPOST(KMONEY_API_PATH,$info);
		
		if($rst['Flag']!=100) return array('Flag'=>102,'FlagString'=>$rst['FlagString']);
		$config['Balance'] = $rst['LastBalance'];
		$config['LastCount'] = $config['maxplay']-$daycount['playnum'];
		return array('Flag'=>100,'FlagString'=>'游戏发起成功','GameConfig'=>$config);
	}

	//结算
	public function storeMoney($param,$extparam){
		//得到CMD
		$cmdInfo = httpPOST(CCS_API_PATH,array('extparam'=>array('Tag'=>'GetFlashCMD','BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'])));
		$cmd = $cmdInfo['FlashCMD'];
		//检测道具是否存在
		$propinfo = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GamePropsInfoByCmd','Cmd'=>$cmd)));
		$propinfo = $propinfo['Result'];
		if(empty($propinfo)) return array('Flag'=>101,'FlagString'=>'道具不存在');
		//取得配置
		$config = $this->getConfig($cmd);
		if($config['Flag'] != 100) return $config;
		$logbuild = new logbuild();
		$checkWork = $logbuild->checkWork($param,$extparam);
		if($checkWork['Flag'] != 100){
			return $checkWork;
		}
		$roomid = $param['ChannelId'];
		$money = $param['MoneyWeight'];
		$this->db->start_transaction();
        
		$daycount = $this->countPlay($cmd,$param['Uin']);
		$lastcount = $config['maxplay'] - $daycount['playnum'];
		if($lastcount < 0){
			$this->rollback();
			return array('Flag'=>107,'FlagString'=>'您已达到该游戏的参与次数上限，请明天继续参与！');
		}
        
		$store = $this->trade($param,"用户{$param['Uin']}房间{$roomid}道具({$propinfo['PropsName']})赢得{$money}");
		if($store['Flag'] != 100){
			$this->rollback();
			return array('Flag'=>107,'FlagString'=>'交易失败');
		}
        
		$this->commit();
		$log = array('param'=>array('Uin'=>$param['Uin'],'TargetUin'=>$param['TargetUin'],'ChannelId'=>$param['ChannelId'],'BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>$param['ChildId'],'MoneyWeight'=>$param['MoneyWeight'],'DoingWeight'=>$param['DoingWeight'],'Desc'=>$store['Desc']),'extparam'=>$extparam);
		$logData[] = $log;
		$store['LogData'] = $logData;
		unset($store['Desc']);
        $store['LastCount'] = $lastcount;
		return $store;
	}
	
	private function getCountPlay($cmd,$user){
		$date = date('Y-m-d',time());
		$sql = "SELECT * FROM ".DB_NAME_PROPS.".games_play_daycount WHERE uin={$user} AND cmd='{$cmd}' AND uptime='{$date}'";
		$daycount = $this->db->get_row($sql,'ASSOC');
		return $daycount;
	}
	
	private function getConfig($cmd){
        if(empty($cmd)) return array('Flag'=>101,'FlagString'=>'参数错误');
		$rows = $this->db->get_row("SELECT `value`,`status` FROM ".DB_NAME_PROPS.".props_config WHERE `key`='{$cmd}_config' LIMIT 1",'ASSOC');
		$config = unserialize($rows['value']);
		if($rows['status'] < 1) return array('Flag'=>102,'FlagString'=>'游戏正在维护中，请稍候再进入！');
		$default = array('Flag'=>100,'FlagString'=>'ok','status'=>$rows['status']);
		foreach((array)$config['extend'] as $key=>$val){
			$config['extend'][$key] = htmlspecialchars_decode($val,ENT_QUOTES);
		}
		$config = array_merge($default,(array)$config['fixed'],(array)$config['extend']);
		return $config;
	}
	
	private function countPlay($cmd,$user){
		//参数次数限制
		$date = date('Y-m-d',time());
		$daycount = $this->getCountPlay($cmd,$user);
		$playnum = $daycount['playnum'] + 1;
		$sql = "REPLACE INTO ".DB_NAME_PROPS.".games_play_daycount(uin,cmd,playnum,uptime)VALUES({$user},'{$cmd}',{$playnum},'{$date}')";
		if( ! $this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'统计失败！');
		}
		return array('Flag'=>100,'FlagString'=>'success','playnum'=>$playnum);
	}

	/**
	* 交易金额操作
	* @parent interge 一级业务
	* @child interge 二级业务
	* @uin interge 用户id
	* @money floot 交易金额
	* @desc string 描述
	*/
	private function trade($param,$desc){
		$param['Desc'] = $desc;

		if($param['DoingWeight'] < 1){
			return array('Flag'=>101,'FlagString'=>'至少送1个以上道具');
		}
		$extparam = array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$param['GroupId']);
		//$log = getLogData($param,$extparam);
		//$extparam['GroupId'] = $log['extparam']['GroupId'];
		$request = array('param'=>$param,'extparam'=>$extparam);
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		$array = array('Flag'=>100,'Balance'=>$rst['LastBalance'],'MoneyWeight'=>$param['MoneyWeight'],'BusinessBalance'=>$rst['BusinessBalance'],'Desc'=>$desc);
		if($rst['fund_type'] == 'Kmoney'){
			return $array;
		}else{
			$array['VoucherBalance'] = $rst['LastBalance'];
			unset($array['Balance']);
			if(isset($rst['KmoneyBalance'])) $array['Balance'] =$rst['KmoneyBalance'];
		}
		return $array;
	}

	private function rollback(){
		$this->db->rollback();
	//	httpPOST(KMONEY_API_PATH,array('extparam'=>array('Tag'=>'Rollback')));
	}

	private function commit(){
		$this->db->commit();
	//	httpPOST(KMONEY_API_PATH,array('extparam'=>array('Tag'=>'Commit')));
	}
}