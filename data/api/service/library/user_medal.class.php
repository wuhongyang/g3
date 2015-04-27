<?php
class User_Medal
{
	protected $db,$dlhelper,$mongodb;
	protected $category = array(1=>'游戏勋章');
	public function __construct(){
		$this->db = db::connect(config('database','default'));
		//$this->dlhelper = new dlhelper($this->db);
		$this->mongodb = domain::main()->GroupDBConn('mongo');
	}
	
	public function GetAllMedalType($id){
		/*$where = '';
		if($id > 0){
			$where .= " AND a.category={$id}";
		}
		$from   = DB_NAME_IM.".medal_type AS a LEFT JOIN ".DB_NAME_IM.".medal_list AS b ON a.id=b.typeid";
		$where  = "b.status=1 AND a.status=1 {$where} GROUP BY a.id";
		$order  = "a.order DESC";
		$select = 'a.*,b.icon';
        $this->dlhelper = new dlhelper(domain::main()->GroupDBConn('mysql'));
		$result = $this->dlhelper->findAllPage($from,$where,$order,$select);
		$page   = $this->dlhelper->getPage();
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$result,'Category'=>$this->category,'Page'=>$page);*/
	}
	
	public function GetMedalList($id){
		/*$where = 'status=1';
		if($id > 0){
			$where .= " AND typeid={$id}";
		}
        $db = domain::main()->GroupDBConn('mysql');
		$sql = "SELECT * FROM ".DB_NAME_IM.".medal_list WHERE {$where} ORDER BY `id` ASC";
		$result = $db->get_results($sql);
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>$result);
        */
	}
	
	public function getLevelRate($business,$uin){
		$userlevel = $this->gameLevelImfomation($business,$uin);
		if($userlevel['Flag'] != 100) return $userlevel;
		$userlevel = $userlevel['Result'][0]['LevelData'];
		$level = array(1=>10,2=>30,3=>50);
		foreach($level as $key=>$val){
			if($userlevel['Level'] < $val && $userlevel['Level'] > $level[$key-1]){
				$userlevel['LevelRate'][] = @floor(100 / ((int)$val / (int)$userlevel['Level']));
			}elseif($userlevel['Level'] >= $val){
                $userlevel['LevelRate'][] = 100;
            }else{
				$userlevel['LevelRate'][] = 0;
			}
		}
		return $userlevel;
	}
	
	public function gameLevelImfomation($business_id,$uin){
		if(!$uin || !is_array($uin)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$count = count($uin);
		for($i=0;$i<$count;$i++){
			$uin[$i] = intval($uin[$i]);
		}
		//获得相关等级信息
		if($business_id == 0){
			return array('Flag'=>102,'FlagString'=>'该游戏等级规则没有设置');
		}
		$sql = "SELECT rule_id,integration FROM `".DB_NAME_TPL."`.`business_param_config` WHERE id = ".$business_id;
		$business_cnofig = $this->db->get_row($sql,'ASSOC');
		$rule = json_decode(urldecode($business_cnofig['integration']), true);
		$rule_id = intval($business_cnofig['rule_id']);
		
		//获得用户积分
		$data = array();
		$table = "kkyoo_integral.total_weight";
		$query = array("Ruleid"=>$rule_id, "UinId"=>array('$in'=>$uin));
		$user_info = (array)$this->mongodb->get_results($table, $query);
		
		//处理积分信息
		$no_scroe_uin = array();
		$scroed_uin = array();
		foreach($user_info as $one_info){
			$scroed_uin[] = $one_info['UinId'];
			
			$level_data = $this->parseLevelInfo($rule, $one_info['Weight']);
			$data[] = array("Uin"=>$one_info['UinId'], "LevelData"=>$level_data);
		}
		$no_scroe_uin = array_diff($uin, $scroed_uin);
		foreach($no_scroe_uin as $one_uin){
			$data[] = array("Uin"=>$one_uin, "LevelData"=>array("Level"=>0, "NeedScore"=>($rule[0]['two']+1), "Percentage"=>0, "Score"=>0));
		}
		
		return array('Flag'=>100,'FlagString'=>'等级信息查询成功', 'Result'=>$data);
	}

	private function parseLevelInfo($rule, $score){
		$level_data["Score"] = $score;
		if($score < 0){
			$level_data["Level"] = 0;
			$level_data["NeedScore"] = $rule[0]['two'] + 1 - $score;
			$level_data["Percentage"] = 0;
		}
		foreach($rule as $one){
			if($score >= $one['one'] && $score <= $one['two']){
				$level_data["Level"] = $one['value'];
				$level_data["NeedScore"] = $one['two'] + 1 - $score;
				$level_data["Percentage"] = (($score - $one['one'])/($one['two'] - $one['one']+1))*100;
				break;
			}
		}
		return $level_data;
	}
	
}