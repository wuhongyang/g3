<?php

class kmoney {

	private $db = null; 
	
	protected $expire = 180;

	public function __construct($group_id) {
		$this->db = $group_id > 0 ? domain::main()->GroupDBConn('mysql', $group_id) : db::connect(config('database','default'));
		$this->platform_db = db::connect(config('database','default'));
		$this->cache = cache::connect(config('cache','memcache'));
	}

	public function __destruct() {
		unset ($this->db);
	}
	
	public function FundSystem($array){
		$database = $array['Database'];
		$table = $array['Table'];
		$uin = $array['Data']['Uin'];
		$group_id = $array['Data']['Group_id'];
		$bigcase = $array['Data']['Bigcase_id'];
		$case = $array['Data']['Case_id'];
		$parent = $array['Data']['Parent_id'];
		$child = $array['Data']['Child_id'];
		$balance_status = $array['Data']['Balance_status'];
		$trade_property = $array['Data']['Trade_property'];
		$trade_type = $array['Data']['Trade_type'];
		$income_pay = $array['Data']['Income_pay'];
		$start_date = $array['Data']['StartDate'];
		$end_date = $array['Data']['EndDate'];
		
		if($uin > 0){
			$where .= ' AND uin = '.$uin;
		}
		if($group_id > 0){
			$where .= ' AND group_id = '.$group_id;
		}
		if($bigcase > 0){
			$where .= ' AND bigcase_id = '.$bigcase;
		}
		if($case > 0){
			$where .= ' AND case_id = '.$case;
		}
		if($parent > 0){
			$where .= ' AND parent_id = '.$parent;
		}
		if($child > 0){
			$where .= ' AND child_id = '.$child;
		}
		if($balance_status != '' && $balance_status >= 0){
			$where .= ' AND balance_status = '.$balance_status;
		}
		if($trade_property > 0){
			$where .= ' AND trade_property = '.$trade_property;
		}
		if($trade_type > 0){
			$where .= ' AND trade_type = '.$trade_type;
		}
		if($income_pay != '' && $income_pay >= 0){
			$where .= ' AND income_pay = '.$income_pay;
		}
		if($start_date != '' && $end_date != ''){
			$where .= ' AND uptime >= '.strtotime($start_date). ' AND uptime <= '.strtotime($end_date);
		};
		$total = $this->db->get_var('SELECT count(*) FROM '.$database.'.'.$table .' WHERE 1' .$where);
		if($total > 0 ){
			$page_arr = $this->showpage($total,20);
			$tabels = explode('_',$table);
			switch ($table){
				case 'kmoney_balance':
				case 'voucher_balance':
				case 'tax_balance':
				case 'kmoney_parent_balance':
				case 'voucher_parent_balance':
				case 'tax_parent_balance':
					$order = ' uptime ';
					break;
				default :
					$order = ' id ';
			}
			$sql = 'SELECT * FROM '.$database.'.'.$table .' WHERE 1 '.$where.' ORDER BY '.$order.' DESC LIMIT '.$page_arr['limit'];
			$list = $this->db->get_results($sql,'ASSOC');
			$bigcase_id = $list[0]['bigcase_id'];
			if($bigcase_id > 0){
				foreach($list as $key=>$value){
					$config_table ='config' ;
					$database = $database== 'kkyoo_kmoney' ? $database : 'kkyoo_voucher_plat';
					$where = '';
					if($value['child_id'] > 0 && $value['child_id'] < 900){
						$where = ' AND  c.child_id='.$value['child_id'];
					}
					list($list[$key]['bigcase_name'],$list[$key]['case_name'],$list[$key]['parent_name'],$list[$key]['child_name']) = $this->platform_db->get_row('SELECT c.bigcase_name ,c.case_name ,c.parent_name,c.child_name FROM '.$database.'.'.$config_table.' c WHERE c.parent_id='.$value['parent_id'].' AND c.case_id ='.$value['case_id'].' AND  c.bigcase_id='.$value['bigcase_id'].$where);
					if($value['child_id'] > 900){
						$config_table = 'common_child_config';
						$where = '';
						$list[$key]['child_name'] = $this->platform_db->get_var('SELECT child_name FROM '.$database.'.'.$config_table.'  WHERE child_id='.$value['child_id']);
					}
				}
			}
			$list['page'] = $page_arr['page'];
			$list['Flag'] = 100;
			$list['FlagString'] = "成功";
			return $list;
		}
		return array('Flag'=>100,'FlagString'=>'成功');
	}
	
	public function FundSystemStatistics($array){
		$database = $array['Database'];
		$table = $array['Table'];
		$uin = $array['Data']['Uin'];
		$group_id = $array['Data']['Group_id'];
		$bigcase = $array['Data']['Bigcase_id'];
		$case = $array['Data']['Case_id'];
		$parent = $array['Data']['Parent_id'];
		$child = $array['Data']['Child_id'];
		$balance_status = $array['Data']['Balance_status'];
		$trade_property = $array['Data']['Trade_property'];
		$trade_type = $array['Data']['Trade_type'];
		$income_pay = $array['Data']['Income_pay'];
		$start_date = $array['Data']['StartDate'];
		$end_date = $array['Data']['EndDate'];
		if($uin > 0){
			$where_basic .= ' AND uin = '.$uin;
		}
		if($group_id > 0){
			$where_basic .= ' AND group_id = '.$group_id;
		}
		if($bigcase > 0){
			$where_basic .= ' AND bigcase_id = '.$bigcase;
		}
		if($case > 0){
			$where_basic .= ' AND case_id = '.$case;
		}
		if($parent > 0){
			$where_basic .= ' AND parent_id = '.$parent;
		}
		if($child > 0){
			$where_basic .= ' AND child_id = '.$child;
		}
		if($balance_status != '' && $balance_status >= 0){
			$where_basic .= ' AND balance_status = '.$balance_status;
		}
		if($trade_property > 0){
			$where_basic .= ' AND trade_property = '.$trade_property;
		}
		if($trade_type > 0){
			$where_basic .= ' AND trade_type = '.$trade_type;
		}
		if($income_pay != '' && $income_pay >= 0){
			$where_basic .= ' AND income_pay = '.$income_pay;
		}
		if($start_date != '' && $end_date != ''){
			$where_basic .= ' AND uptime >= '.strtotime($start_date). ' AND uptime <= '.strtotime($end_date);
		};
		
		foreach($array['Statistics_data'] as $key=>$val){
			if(isset($val['other_where'])){
				$where=$where_basic." AND {$val['other_where']}";
			}
			else{
				$where=$where_basic;
			}
			if(is_array($val['group_field'])){
				$group_by=implode(',',$val['group_field']);
				$total_cache=md5($database.$table.$where.$val['field'].$group_by);
				$total=$this->cache->get($total_cache);
				if($total!==NULL){
					$total=number_format($total);
					$array['Statistics_data'][$key]['total']=$total;
					continue;
				}
				$long_info = $this->cache->long_get($total_cache);
				$this->cache->set($total_cache,$long_info,$this->expire);
				if($bigcase>0&&$case>0&&$parent>0&&$group_by=='parent_id'){
					$sql="SELECT {$val['field']} AS total FROM $database.$table WHERE 1 $where AND {$val['field']} > 0 ORDER BY uptime DESC LIMIT 1";
					$total=$this->db->get_var($sql);
				}
				else{
					$sql="SELECT $group_by FROM $database.$table WHERE 1 $where AND {$val['field']} > 0 GROUP BY $group_by";
					$group_field_res=$this->db->get_results($sql,'ASSOC');
					$total=0;
					foreach($group_field_res as $val2){
						$group_where=$where;
						foreach($val['group_field'] as $val3){
							$group_where.=" AND $val3='{$val2[$val3]}'";
						}
						$sql="SELECT {$val['field']} FROM $database.$table WHERE 1 $group_where ORDER BY uptime DESC LIMIT 1";
						$group_total=$this->db->get_var($sql);
						$total+=$group_total;
					}
				}
			}
			else{
				$total_cache=md5($database.$table.$where.$val['field']);
				$total=$this->cache->get($total_cache);
				if($total!==NULL){			
					$total=number_format($total);
					$array['Statistics_data'][$key]['total']=$total;
					continue;
				}
				$long_info = $this->cache->long_get($total_cache);
				$this->cache->set($total_cache,$long_info,$this->expire);
				$sql="SELECT SUM({$val['field']}) FROM $database.$table WHERE 1 $where AND {$val['field']} > 0";
				$total=$this->db->get_var($sql);
			}
			$total=number_format($total);
			$array['Statistics_data'][$key]['total']=$total;
		}
		
		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$array['Statistics_data']);
	}
	
	public function FinanceMargin($array){
		$date=$array['Date'];
		if(empty($date)){
			$start_time = strtotime(date('Y-m').'-01 00:00:00');
			$end_time = strtotime(date('Y-m'.'-01 00:00:00',strtotime('+1 month')));
		}else{
			$start_time = strtotime($date);
			$end_time = (strtotime($date)+24*3600);
		}
		$sql="SELECT * FROM ".DB_NAME_MARGIN.".finance_margin WHERE uptime>={$start_time} AND uptime<{$end_time} ORDER BY uptime DESC";
		$list=$this->db->get_results($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'Get record success','map'=>(array)$list);
	}
	
	public function FinanceGroupMargin($array){
		$group_id = intval($array['GroupId'])?intval($array['GroupId']):0;
		if($group_id) $where = " AND group_id='".$group_id."'";
		$date=$array['Date'];
		if(empty($date)){
			$start_time = strtotime(date('Y-m').'-01 00:00:00');
			$end_time = strtotime(date('Y-m'.'-01 00:00:00',strtotime('+1 month')));			
		}else{
			$start_time = strtotime($date);
			$end_time = strtotime($date)+24*3600;
		}
		$sql="SELECT * FROM ".DB_NAME_MARGIN.".finance_group_margin WHERE uptime>={$start_time} AND uptime<{$end_time} ".$where." ORDER BY uptime DESC";
		$list=$this->db->get_results($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'Get record success','map'=>(array)$list);
	}
	
	public function FinanceAbnormalRunning($array){
		$group_id = intval($array['GroupId'])?intval($array['GroupId']):0;
		if($group_id) $where = " AND group_id='".$group_id."'";
		$date=$array['Date'];
		$list = array();
		if($date==''){
			$this_month=date('Y-m').'-01 00:00:00';
			$this_month_end=date('Y-m'.'-01 00:00:00',strtotime('+1 month'));
			$total = $this->db->get_var("SELECT count(*) FROM ".DB_NAME_MARGIN .".finance_abnormal_running WHERE uptime>='$this_month' AND uptime<'$this_month_end' ".$where);
			if($total > 0 ){
				$page_arr = $this->showpage($total,20);
				$sql="SELECT * FROM ".DB_NAME_MARGIN.".finance_abnormal_running WHERE uptime>='$this_month' AND uptime<'$this_month_end' ".$where." ORDER BY uptime DESC limit ".$page_arr['limit'];;
				$list=$this->db->get_results($sql,'ASSOC');
				foreach($list as $key=>$value){
					$param = array(
						'param'=>array(
							"BigCaseId"   => $value['bigcase_id'],
							"CaseId"   => $value['case_id'],
							"ParentId"   => $value['parent_id'],
							"ChildId"   => $value['child_id'],
						),
						'extparam'=>array(
							"Tag" 		 => "GetBusinessConfig",
						)
					);
					$result = httpPOST(CCS_API_PATH,$param);
					$list[$key]['bigcase_name'] = $result['Result']['bigcase_name'];
					$list[$key]['case_name'] = $result['Result']['case_name'];
					$list[$key]['parent_name'] = $result['Result']['parent_name'];
					$list[$key]['child_name'] = $result['Result']['child_name'];
				}
				$page = $page_arr['page'];
			}
		}
		else{
			$total = $this->db->get_var("SELECT count(*) FROM ".DB_NAME_MARGIN.".finance_abnormal_running WHERE uptime>='".$date."' AND uptime<='".$date." 23:59:59'".$where);
			if($total > 0 ){
				$page_arr = $this->showpage($total,20);
				$sql="SELECT * FROM ".DB_NAME_MARGIN.".finance_abnormal_running WHERE uptime>='".$date."' AND uptime<='".$date." 23:59:59'".$where." ORDER BY uptime DESC limit ".$page_arr['limit'];;
				$list=$this->db->get_results($sql,'ASSOC');
				foreach($list as $key=>$value){
					$param = array(
						'param'=>array(
							"BigCaseId"   => $value['bigcase_id'],
							"CaseId"   => $value['case_id'],
							"ParentId"   => $value['parent_id'],
							"ChildId"   => $value['child_id'],
						),
						'extparam'=>array(
							"Tag" 		 => "GetBusinessConfig",
						)
					);
					$result = httpPOST(CCS_API_PATH,$param);
					$list[$key]['bigcase_name'] = $result['Result']['bigcase_name'];
					$list[$key]['case_name'] = $result['Result']['case_name'];
					$list[$key]['parent_name'] = $result['Result']['parent_name'];
					$list[$key]['child_name'] = $result['Result']['child_name'];
				}
				$page = $page_arr['page'];
			}
		}
		return array('Flag'=>100,'FlagString'=>'Get record success','map'=>$list, 'page'=>$page);
	}
	
	public function FinanceManage($array){
		$group_id = intval($array['GroupId']);
		$s_date = empty($array['StartDate']) ? date('Y-m').'-01' : $array['StartDate'];
		$e_date = empty($array['EndDate']) ? date('Y-m-d') : $array['EndDate'];

		$where = '';
		if($group_id > 0){
			$where .= " AND group_id={$group_id}";
		}
		$where .= ' AND uptime BETWEEN '.strtotime($s_date.' 00:00:00').' AND '.strtotime($e_date.' 23:59:59');
		if(!empty($where)){
			$where = ltrim($where,' AND');
		}
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_MARGIN.".finance_group_purse WHERE {$where}";
		$total = $this->db->get_var($sql);
		if($total < 1){
			return array('Flag'=>101,'FlagString'=>'没有记录');
		}

		$page_arr = $this->showpage($total,20);

		$sql = "SELECT * FROM ".DB_NAME_MARGIN.".finance_group_purse WHERE {$where} ORDER BY uptime DESC,group_id LIMIT {$page_arr['limit']}";
		$res = $this->db->get_results($sql, ASSOC);
		$result = array();
		foreach ((array)$res as $key => $val) {
			$val['tax_income'] = 0;
			$val['tax_pay'] = 0;
			$val['user_income'] = 0;
			$val['user_pay'] = 0;
			$val['subject_income'] = 0;
			$val['subject_pay'] = 0;
			$result[date('Ymd',$val['uptime'])][$val['group_id']] = $val;
		}
		//税收
		$t_info = array(
			'Database'=>'kkyoo_tax',
			'Table'=>'tax_ledger_detail',
			'Data'=>array(
				'database'=>'kkyoo_tax',
				'table'=>'tax_ledger_detail',
				'Group_id' => $group_id,
				'StartDate' => $s_date,
				'EndDate' => $e_date,
				'startDate' => date('Y-m').'-01'
			)
		);
		$taxInfo = $this->FundSystem($t_info);
		unset($taxInfo['page'],$taxInfo['Flag'],$taxInfo['FlagString']);
		foreach ((array)$taxInfo as $val) {
			if(isset($result[date('Ymd',$val['uptime'])][$val['group_id']])){
				$result[date('Ymd',$val['uptime'])][$val['group_id']]['tax_income'] = $val['deposit_money'];
				$result[date('Ymd',$val['uptime'])][$val['group_id']]['tax_pay'] = $val['pay_money'];
			}
		}
		//用户
		$u_info = array(
			'Database'=>'kkyoo_voucher',
			'Table'=>'voucher_user_summary',
			'Data'=>array(
				'database'=>'kkyoo_voucher',
				'table'=>'voucher_user_summary',
				'Group_id' => $group_id,
				'StartDate' => $s_date,
				'EndDate' => $e_date,
				'startDate' => date('Y-m').'-01'
			)
		);
		$userInfo = $this->FundSystem($u_info);
		unset($userInfo['page'],$userInfo['Flag'],$userInfo['FlagString']);
		foreach ((array)$userInfo as $val) {
			if(isset($result[date('Ymd',$val['uptime'])][$val['group_id']])){
				$result[date('Ymd',$val['uptime'])][$val['group_id']]['user_income'] = $val['deposit_money'];
				$result[date('Ymd',$val['uptime'])][$val['group_id']]['user_pay'] = $val['pay_money'];
			}
		}
		//科目
		$s_info = array(
			'Database'=>'kkyoo_voucher',
			'Table'=>'voucher_parent_ledger',
			'Data'=>array(
				'database'=>'kkyoo_voucher',
				'table'=>'voucher_parent_ledger',
				'Group_id' => $group_id,
				'StartDate' => $s_date,
				'EndDate' => $e_date,
				'startDate' => date('Y-m').'-01'
			)
		);
		$subjectInfo = $this->FundSystem($s_info);
		unset($subjectInfo['page'],$subjectInfo['Flag'],$subjectInfo['FlagString']);
		foreach ((array)$subjectInfo as $val) {
			if(isset($result[date('Ymd',$val['uptime'])][$val['group_id']])){
				$result[date('Ymd',$val['uptime'])][$val['group_id']]['subject_income'] = $val['deposit_money'];
				$result[date('Ymd',$val['uptime'])][$val['group_id']]['subject_pay'] = $val['pay_money'];
			}
		}
		foreach ($result as $val) {
			foreach ((array)$val as $v) {
				$r[] = $v;
			}
		}
		return array('Flag'=>100,'FlagString'=>'Get record success','map'=>$r,'page'=>$page_arr['page']);
	}
	
	private function showPage($total, $perpage = 10) {
		if ($total > 0) {
			require_once (dirname(dirname(dirname(dirname(__FILE__)))).'/library/page.class.php');
			$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
			));
			$pageArr['page'] = $page->show();
			$pageArr['limit'] = $page->limit();
			unset ($page);
		}
		return $pageArr;
	}
}
?>