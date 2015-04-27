<?php
class GroupTax{
	
	public function __construct(){
		$this->mongodb = domain::main()->GroupDBConn('mongo');
	}
	
	function exchange($param, $money){
		$table_name = "parter_income.balance";
		$where = array("UinId"=>intval($param['TargetUin']), "ExtendUin"=>intval($param['GroupId']), "Ruleid"=>19);
		$row = $this->mongodb->get_row($table_name, $where);
		$weight = $row['Weight'] - $param['MoneyWeight'];
		if($weight < 0){
			return array("Flag"=>102, "FlagString"=>"税收余额不足");
		}
		$record = array("Weight"=>intval($weight));
		$this->mongodb->query($table_name,array_merge($record, $where),$where);
	
		$table_name = "parter_income.details";
		$query = array("BigCaseId"=>intval($param['BigCaseId']),
				"CaseId"=>intval($param['CaseId']),
				"ParentId"=>intval($param['ParentId']),
				"ChildId"=>intval($param['ChildId']),
				"Desc"=>$param['Uin']."给".$param['TargetUin']."兑换积分".$param['MoneyWeight']."发放人民币".$money."元",
				"Weight"=>intval($param['MoneyWeight']),
				"DoWeight"=>intval($param['DoingWeight']),
				"RMBWeight"=>intval($money),
				"Uptime"=>time(),
				"UinId"=>intval($param['TargetUin']),
				"ExtendUin"=>intval($param['GroupId']));
		$this->mongodb->query($table_name, $query);
	
		return array("Flag"=>100, "FlagString"=>"操作成功");
	}
	
	function kmoneyExchange($param){
		$table_name = "parter_income.balance";
		$where = array("UinId"=>intval($param['Uin']), "ExtendUin"=>intval($param['GroupId']), "Ruleid"=>19);
		$row = $this->mongodb->get_row($table_name, $where);
		$weight = $row['Weight'] - $param['MoneyWeight'];
		if($weight < 0){
			return array("Flag"=>102, "FlagString"=>"税收余额不足");
		}
		$param['Desc'] = $param['Uin']."兑换税收积分".$param['MoneyWeight']."获取金币".$param['MoneyWeight'];
		$aparam = $param;
		$aparam['BigCaseId'] = 10006;
		$aparam['CaseId'] = 10049;
		$aparam['ParentId'] = 10269;
		$rst = $this->trade('Kmoney', $aparam, 109);
		if($rst['Flag'] != 100){
			return array("Flag"=>102, "FlagString"=>"站预存账户余额不足");
		}
		$record = array("Weight"=>intval($weight));
		$this->mongodb->query($table_name,array_merge($record, $where),$where);
		
		$table_name = "parter_income.details";
		$query = array("BigCaseId"=>intval($param['BigCaseId']),
						"CaseId"=>intval($param['CaseId']),
						"ParentId"=>intval($param['ParentId']),
						"ChildId"=>intval($param['ChildId']),
						"Desc"=>$param['Desc'],
						"Weight"=>intval($param['MoneyWeight']),
						"DoWeight"=>intval($param['DoingWeight']),
						"Uptime"=>time(),
						"UinId"=>intval($param['Uin']),
						"ExtendUin"=>intval($param['GroupId']));
		$this->mongodb->query($table_name, $query);
		
		// $param['Desc'] = $query['Desc'];
		$rst = $this->trade('Kmoney', $param, 105);
		if($rst['Flag'] != 100){
			return array("Flag"=>102, "FlagString"=>"系统错误");
		}
		$rst = $this->trade('Kmoney', $param, 106);
		if($rst['Flag'] != 100){
			return array("Flag"=>102, "FlagString"=>"系统错误");
		}
		
		return array("Flag"=>100, "FlagString"=>"操作成功");
	}
	
	private function trade($tag,$param,$child){
		$param['ChildId'] = $child;
		$request = array('param'=>$param,'extparam'=>array('Tag'=>$tag,'Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$param['GroupId']));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		return array('Flag'=>100,'balance'=>$rst['LastBalance']);
	}
}