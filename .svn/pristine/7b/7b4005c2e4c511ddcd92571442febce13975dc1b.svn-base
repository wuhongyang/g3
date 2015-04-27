<?php
class kcost{
	
	private $_Uin			=0, 
			$_operator		="CF9BEF26F303FF9DDA3F5DED2AA7C3C5",
			$_auto_commit 	= true,
			$_rate			= 10000;

	private function _mRequest($p){
		$info = httpPOST(KMONEY_API_PATH, $p);
		return $info;
	}

	public function Charge($info){
		if($info['Group_id'] >0 && $info['MoneyWeight'] > 0 && ($info['Child_id']== 106 || $info['Child_id']== 107)){
			$group = new group();
			$group_info = $group->GroupInfo($info);
			if($group_info['Flag'] != 100){
				return $group_info;
			}
			$param = array(
				'extparam' => array('Tag'=>"Kmoney","Operator"=>$this->_operator,"GroupId"=>$info['Group_id']),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>$info['Child_id'],'MoneyWeight'=>$info['MoneyWeight'],'Desc'=>$info['Desc'],"Uin"=>$info['Group_id'],"TargetUin"=>$info['Group_id'])
			);
			$result = $this->_mRequest($param);
			$param['param']['GroupId'] = $info['Group_id'];
			// $logbuild = new logbuild();
			// $logbuild->setlog($param);
			return $result;
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	public function PCharge($info){
		if($info['Group_id'] > 0 && $info['MoneyWeight'] > 0  && $info['BigCaseId'] > 0  && $info['CaseId'] > 0  && $info['ParentId'] > 0  && ($info['ChildId'] == 909 || $info['ChildId'] == 910) ){
			$group = new group();
			$group_info = $group->GroupInfo($info);
			if($group_info['Flag'] != 100){
				return $group_info;
			}
			if($info['ChildId'] == 909){//
				$parent_money = get_parent_money($info['BigCaseId'],$info['CaseId'],$info['ParentId'],$info['Group_id']);
			}elseif($info['ChildId'] == 910){//
				$parent_money = get_parent_money(10002,10007,10275,$info['Group_id']);
			}
			if($parent_money < $info['MoneyWeight']){
				return array('Flag'=>101,'FlagString'=>'科目余额不足');
			}
			$param = array(
				'extparam' => array('Tag'=>"Kmoney","Operator"=>$this->_operator,"GroupId"=>$info['Group_id']),
				'param' => array('BigCaseId'=>$info['BigCaseId'],'CaseId'=>$info['CaseId'],'ParentId'=>$info['ParentId'],'ChildId'=>$info['ChildId'],'MoneyWeight'=>$info['MoneyWeight'],'Desc'=>$info['Desc'],"Uin"=>$info['Group_id'],"TargetUin"=>$info['Group_id'])
			);
			$result = $this->_mRequest($param);
			if($result['Flag'] == 100){
				$child_id = $info['ChildId'] == 909 ? 110 : 111;
				$param = array(
					'extparam' => array('Tag'=>"Kmoney","Operator"=>$this->_operator,"GroupId"=>$info['Group_id']),
					'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10275,'ChildId'=>$child_id,'MoneyWeight'=>$info['MoneyWeight'],'Desc'=>$info['Desc'],"Uin"=>$info['Group_id'],"TargetUin"=>$info['Group_id'])
				);
				$result = $this->_mRequest($param);
			}
			return $result;
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	public function UCharge($info){
		if( $info['Group_id'] > 0  && $info['Uin'] > 0  && $info['MoneyWeight'] > 0  && ($info['ChildId'] == 106 || $info['ChildId'] == 107)){
			$param = array(
				'extparam' => array('Tag'=>"Kmoney","Operator"=>$this->_operator,"GroupId"=>$info['Group_id']),
				'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10275,'ChildId'=>$info['ChildId'],'MoneyWeight'=>$info['MoneyWeight'],'Desc'=>$info['Desc'],"Uin"=>$info['Uin'],"TargetUin"=>$info['Uin'])
			);
			$result = $this->_mRequest($param);
			return $result;
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	public function MCharge($info){
		if($info['Group_id'] > 0 && $info['MoneyWeight'] > 0  && ($info['ChildId'] == 108 || $info['ChildId'] == 109)){
			$param = array(
				'extparam' => array('Tag'=>"Kmoney","Operator"=>$this->_operator,"GroupId"=>$info['Group_id']),
				'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10275,'ChildId'=>$info['ChildId'],'MoneyWeight'=>$info['MoneyWeight'],'Desc'=>$info['Desc'],"Uin"=>$info['Group_id'],"TargetUin"=>$info['Group_id'])
			);
			$result = $this->_mRequest($param);
			return $result;
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
}
