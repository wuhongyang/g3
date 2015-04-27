<?php
class Tax{

	public function __construct(){
		//$this->db = domain::main()->GroupDBConn();
		$this->mongodb = domain::main()->GroupDBConn('mongo');
	}

	function taxDetail($uin, $group_id, $case_id, $parent_id, $child_id, $start_time, $end_time,$ruleid){
		$query = array();
		if($uin){
			$query['UinId'] = intval($uin);
		}
		if($group_id){
			$query['ExtendUin'] = intval($group_id);
		}
		if($case_id){
			$query['CaseId'] = intval($case_id);
		}
		if($parent_id){
			$query['ParentId'] = intval($parent_id);
		}
		if($child_id){
			$query['ChildId'] = intval($child_id);
		}
		if($start_time){
			$query['Uptime']['$gte'] = intval($start_time);
		}
		if($end_time){
			$query['Uptime']['$lte'] = intval($end_time);
		}
		if($ruleid){
			$query['Ruleid'] = intval($ruleid);
		}
		
		$page_arr = $this->showPage($total,15);
		$table = "parter_income.details";
		
		list($offset,$rows) = explode(',',$page_arr['limit']);
		$limit = array('offset'=>$offset,'rows'=>$rows);
		$result = $this->mongodb->get_results(
				$table,
				$query,
				array(
						'limit'=>$limit,
						'sort'=>array('Uptime'=>-1),
				)
		);
		
		return array("Flag"=>100, "FlagString"=>"查询成功", "Result"=>$result, 'Page'=>$page_arr['page']);
	}
	
	function getBalance($uin, $group_id){
		if(!$group_id){
			return array("Flag"=>102, "FlagString"=>"参数错误");
		}
		$table = "parter_income.balance";
		if(!$uin){
			$page_arr = $this->showPage($total,15);
			$query = array("ExtendUin"=>intval($group_id), "Ruleid"=>19);
			list($offset,$rows) = explode(',',$page_arr['limit']);
			$limit = array('offset'=>$offset,'rows'=>$rows);
			$result = $this->mongodb->get_results(
					$table,
					$query,
					array(
							'limit'=>$limit
					)
			);
			return array("Flag"=>100, "FlagString"=>"查询成功", "Result"=>$result, 'Page'=>$page_arr['page']);
		}else{
			$query = array("ExtendUin"=>intval($group_id), "UinId"=>intval($uin), "Ruleid"=>19);
			$result = $this->mongodb->get_row(
					$table,
					$query
			);
			return array("Flag"=>100, "FlagString"=>"查询成功", "Result"=>$result);
		}
	}
	
	
	private function trade($tag,$param,$child){
		$param['ChildId'] = $child;
		$request = array('param'=>$param,'extparam'=>array('Tag'=>$tag,'Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$param['GroupId']));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		return array('Flag'=>100,'balance'=>$rst['LastBalance']);
	}
	
	private function showPage($total, $perpage = 20) {
		require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
		$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
		));
		$pageArr['page'] = $page->simple_page($total);
		$pageArr['limit'] = $page->simple_limit();
		unset ($page);
		return $pageArr;
	}
}