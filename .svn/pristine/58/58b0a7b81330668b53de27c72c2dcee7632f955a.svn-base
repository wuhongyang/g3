<?php
class CommonChildConfig
{
	private $db,$dl;
	function __construct(){
		$this->db = db::connect(config('database','default'));
		$this->dl = new dlhelper($this->db);
	}
	
	public function commonChildList($data){
		if($data['child_name']){
			$where .= " AND child_name LIKE '{$data['child_name']}%'";
		}
		if(isset($data['child_status']) && $data['child_status']!=-1){
			$where .= " AND child_status='{$data['child_status']}'";
		}
		if(!empty($where)){
			$where = ltrim($where,' AND');
		}
		$result = $this->dl->findAllPage(DB_NAME_CCS.'.tbl_child_common',$where);
		$page = $this->dl->getPage();
		return array('Flag'=>100,'FlagString'=>'常规四级科目列表','Result'=>$result,'Page'=>$page);
	}
	
	public function commonChildInfo($id){
		if($id <= 0) return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = "SELECT * FROM ".DB_NAME_CCS.".tbl_child_common WHERE id='{$id}'";
		$row = $this->db->get_row($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'常规科目配置详情','Info'=>$row);
	}
	
	public function commonChildAdd($data){
		if(empty($data['child_name'])) return array('Flag'=>101,'FlagString'=>'参数错误');
		$child_id = $this->db->get_var("SELECT MAX(child_id) FROM ".DB_NAME_CCS.".tbl_child_common LIMIT 1");
		$data['child_id'] = empty($child_id)? 101 : $child_id+1;
		$sql = "INSERT INTO ".DB_NAME_CCS.".tbl_child_common(child_id,child_name,child_desc,bind_fund,trade_type,fund_type,trade_property,is_income_pay,is_power,is_log,is_auth,bind_child,child_status) VALUES('{$data['child_id']}','{$data['child_name']}','{$data['child_desc']}','{$data['bind_fund']}','{$data['trade_type']}','{$data['fund_type']}','{$data['trade_property']}','{$data['is_income_pay']}','{$data['is_power']}','{$data['is_log']}','{$data['is_auth']}','{$data['bind_child']}','{$data['child_status']}')";
		if($this->db->query($sql))
			return array('Flag'=>100,'FlagString'=>'添加成功');
		return array('Flag'=>102,'FlagString'=>'添加失败');
	}
	
	public function commonChildUpdate($data){
		if(empty($data['child_name'])) return array('Flag'=>101,'FlagString'=>'参数错误');
		$sql = "UPDATE ".DB_NAME_CCS.".tbl_child_common SET child_name='{$data['child_name']}',child_desc='{$data['child_desc']}',bind_fund='{$data['bind_fund']}',trade_type='{$data['trade_type']}',fund_type='{$data['fund_type']}',trade_property='{$data['trade_property']}',is_income_pay='{$data['is_income_pay']}',is_power='{$data['is_power']}',is_log='{$data['is_log']}',is_auth='{$data['is_auth']}',bind_child='{$data['bind_child']}',child_status='{$data['child_status']}' WHERE id='{$data['id']}'";
		if($this->db->query($sql))
			return array('Flag'=>100,'FlagString'=>'修改成功');
		return array('Flag'=>102,'FlagString'=>'修改失败');
	}
	
	public function commonChildSync($ids){
		if(empty($ids)) return array('Flag'=>101,'FlagString'=>'参数错误');
		$id = implode(',',(array)$ids);
		$sql = "SELECT * FROM ".DB_NAME_CCS.".tbl_child_common WHERE child_id IN({$id})";
		//child_id,child_name,child_desc,trade_type,fund_type,is_income_pay,is_power,is_log,child_status
		$chlids = $this->db->get_results($sql,'ASSOC');
		$results = array();
		foreach((array)$chlids as $child){
			if(intval($child['fund_type'])==1 && $child['bind_fund']==1){
				$sql = "REPLACE INTO ".DB_NAME_KWEALTH.".common_child_config(id,child_id,trade_type,fund_type,trade_type,is_income_pay,is_power,is_log,child_status,child_name,child_desc)
				VALUES({$child['id']},{$child['child_id']},{$child['trade_type']},{$child['fund_type']},{$child['trade_property']},{$child['is_income_pay']},{$child['is_power']},{$child['is_log']},{$child['child_status']},'{$child['child_name']}','{$child['child_desc']}')";
			}elseif((int)$child['fund_type'] == 2 && $child['bind_fund'] == 1){
				$sql = "REPLACE INTO ".DB_NAME_KMONEY.".common_child_config(id,child_id,trade_type,fund_type,trade_property,is_income_pay,is_power,is_log,child_status,child_name,child_desc)
				VALUES({$child['id']},{$child['child_id']},{$child['trade_type']},{$child['fund_type']},{$child['trade_property']},{$child['is_income_pay']},{$child['is_power']},{$child['is_log']},{$child['child_status']},'{$child['child_name']}','{$child['child_desc']}')";
			}elseif($child['bind_fund'] == 1){
				$sql = "REPLACE INTO ".DB_NAME_VOUCHER_PLAT.".common_child_config(id,child_id,trade_type,fund_type,trade_property,is_income_pay,is_power,is_log,child_status,child_name,child_desc)
				VALUES({$child['id']},{$child['child_id']},{$child['trade_type']},{$child['fund_type']},{$child['trade_property']},{$child['is_income_pay']},{$child['is_power']},{$child['is_log']},{$child['child_status']},'{$child['child_name']}','{$child['child_desc']}')";
			}
			if($this->db->query($sql)){
				$results[] = array('id'=>$child['id'],'Flag'=>100,'FlagString'=>'同步成功');
			}else{
				$results[] = array('id'=>$child['id'],'Flag'=>102,'FlagString'=>'同步失败');
			}
		}
		return $results;
	}
}
