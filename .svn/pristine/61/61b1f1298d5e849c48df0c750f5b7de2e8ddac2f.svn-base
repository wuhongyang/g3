<?php
class get_level{
	
	public function __construct(){
		$this->db = db::connect(config('database','default'));
		$this->mongo = domain::main()->GroupDBConn('mongo');
	}
	
	function get_level($data){
		$rule_id = 28;
		//获取全站等级
		$param = array(
				'extparam' => array('Tag'=>'GetLevelVaule','UinId'=>$data['UinId'],'Ruleid'=>$rule_id, "Period"=>"total"),
				'param'    => array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>115)
		);
		$result = request($param);
		$level = $result['Level'];
		//获得全站等级积分
		$param = array(
				'extparam' => array('Tag'=>'GetPointVaule','UinId'=>$data['UinId'],'Ruleid'=>$rule_id, "Period"=>"total"),
				'param'    => array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>113)
		);
		$result = request($param);
		$score = $result['Weight'];
		//获得下个等级需要积分
		$sql = "SELECT integration FROM `".DB_NAME_TPL."`.`business_param_config` WHERE rule_id = ".$rule_id;
		$in = json_decode(urldecode($this->db->get_var($sql)), true);
		
		$need = $in[$level]['one'] - $score;
		
		return array('Flag'=>100, 'Data'=>array('Level'=>$level, 'Need'=>$need));
	}
	/*
	function get_vip_level($data){
		$rule_id = 31;
		$levelinfo = array();
		//获取站点会员等级
		$rule = new rule($this->mongo);
		$result = $rule->getRuleLevelResult($data['UinId'], "", "", $rule_id, "", array("total"));
		$levels = $result['total'];
		
		//获得全站等级积分
		foreach($levels as $key=>$one){
			$levels[$key]['Weight'] = $levels[$key]['Weight']?$levels[$key]['Weight']:0;
			$param = array(
					'extparam' => array('Tag'=>'GetPointVaule','UinId'=>$data['UinId'], "ExtendUin"=>$one['ExtendUin'],'Ruleid'=>31, "Period"=>"total"),
					'param'    => array('BigCaseId'=>10001,'CaseId'=>10027,'ParentId'=>10074,'ChildId'=>113)
			);
			$result = request($param);
			$levels[$key]['score'] = $result['Weight'];
		}

		//等级补全
		$egroup_ids = array();
		foreach($levels as $one){
			$egroup_ids[] = $one['ExtendUin'];
		}
		$sql = "SELECT group_id FROM ".DB_NAME_GROUP.".`tbl_vip` WHERE uin = ".$data['UinId'];
		$result2 = $this->db->get_results($sql, "ASSOC");
		$group_ids = array();
		foreach($result2 as $key=>$one){
			$group_ids[] = $one['group_id'];
		}
		$need_add_ids = array_diff($group_ids, $egroup_ids);
		foreach($need_add_ids as $one){
			$levels[] = array('UinId'=>$data['UinId'], 'ExtendUin'=>$one, 'Weight'=>0, 'score'=>0);
		}
		
		//获得下个等级需要积分
		$sql = "SELECT integration FROM `".DB_NAME_TPL."`.`business_param_config` WHERE rule_id = ".$rule_id;
		$in = json_decode(urldecode($this->db->get_var($sql)), true);
		
		foreach($levels as $one){
			$need = $in[$one['Weight']]['one'] - $one['score'];
			$levelinfo[] = array('GroupId'=>$one['ExtendUin'], 'Level'=>$one['Weight'], 'Need'=>$need);
		}
		
		return array('Flag'=>100, 'Data'=>$levelinfo);
	}
	*/
}