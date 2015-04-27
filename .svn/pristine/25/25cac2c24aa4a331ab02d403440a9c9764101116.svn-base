<?php
/*
class RMB
{
	function __construct(){
	    $this->mongodb = db::connect(config('mongodb','ktv'),'mongo');	   
        //$this->mongodb = domain::main()->GroupDBConn('mongo');
	}
	
	//人民币账户流水
	function detail($data){
		$table = "parter_income.parter_rmb_details";
		$page_num = 20;
		$page_arr = $this->showPage($total,$page_num);
		list($offset,$rows) = explode(',',$page_arr['limit']);
		$limit = array('offset'=>$offset,'rows'=>$rows);

		$query = array();
		if($data['Uin']){
			$query['Uin'] = intval($data['Uin']);
		}
		if($data['ChannelType']){
			$query['ChannelType'] = intval($data['ChannelType']);
		}
		if(isset($data['ChildType'])){
			$query['ChildType'] = intval($data['ChildType']);
		}
		if($data['StartTime']){
			$query['Uptime']['$gte'] = intval($data['StartTime']);
		}
		if($data['EndTime']){
			$query['Uptime']['$lte'] = intval($data['EndTime']) + 24*60*60-1;
		}
		$result_condition = array("limit"=>$limit, "sort"=>array("Uptime"=>-1,"ChannelType"=>1));
		$fields = array();
		$result = $this->mongodb->get_results(
				$table,
				$query,
				$result_condition,
				$fields
		);
		$page_arr = $this->showPage(count($result),$page_num);
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$result, 'Page'=>$page_arr['page']);
	}
	
	//人民币账户余额
	function balance($Uin=""){
		$Uin = intval($Uin);
		$table = "parter_income.parter_rmb_balance";
		$page_num = 20;
		$page_arr = $this->showPage($total,$page_num);
		list($offset,$rows) = explode(',',$page_arr['limit']);
		$limit = array('offset'=>$offset,'rows'=>$rows);
		
		$query = array();
		if($Uin){
			$query['Uin'] = $Uin;
		}
		$result_condition = array("limit"=>$limit, "sort"=>array("Uptime"=>-1));
		$fields = array();
		
		$result = $this->mongodb->get_results(
				$table,
				$query,
				$result_condition,
				$fields
		);
		
		$page_arr = $this->showPage(count($result),$page_num);
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$result, 'Page'=>$page_arr['page']);
	}
	
	//提现流水
	public function cashList($data){
		$table = "parter_income.parter_cash_detail";
		$page_num = 20;
		$page_arr = $this->showPage($total,$page_num);
		list($offset,$rows) = explode(',',$page_arr['limit']);
		$limit = array('offset'=>$offset,'rows'=>$rows);
		$query = array();
		if($data['Uin'] > 0){
			$query['Uin'] = intval($data['Uin']);
		}
		if($data['State'] > 0){
			$query['State'] = intval($data['State']);
		}
		if($data['StartTime']){
			$query['Uptime']['$gte'] = intval($data['StartTime']);
		}
		if($data['EndTime']){
			$query['Uptime']['$lte'] = intval($data['EndTime']) + 24*60*60-1;
		}
		$result_condition = array("limit"=>$limit, "sort"=>array("Uptime"=>-1));
		$fields = array();
		$result = $this->mongodb->get_results(
				$table,
				$query,
				$result_condition,
				$fields
		);
		$page_arr = $this->showPage(count($result),$page_num);
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$result, 'Page'=>$page_arr['page']);
	}
	
	public function depositCheck(){
		$table = "parter_income.parter_cash_total";
		$page_num = 20;
		$page_arr = $this->showPage($total,$page_num);
		list($offset,$rows) = explode(',',$page_arr['limit']);
		$limit = array('offset'=>$offset,'rows'=>$rows);
		
		$query = array();
		$result_condition = array("limit"=>$limit, "sort"=>array("Uptime"=>-1));
		$fields = array();
		
		$result = $this->mongodb->get_results(
			$table,
			$query,
			$result_condition,
			$fields
		);
		$page_arr = $this->showPage(count($result),$page_num);
		return array('Flag'=>100,'FlagString'=>'查询成功','Result'=>(array)$result, 'Page'=>$page_arr['page']);
	}
	
	//1用户提交申请 2运营审核失败 3运营审核成功 4财务审核失败 5财务审核成功 6打款失败 7打款成功
	public function cashCheck($parentid,$childid,$data){
		$data['State'] = intval($data['State']);
		if($parentid !=10188 || $childid < 100 || $data['State'] <0 || empty($data['Ids'])){return array('Flag'=>101,'FlagString'=>'参数有误');}
		$ids = $data['Ids'];
		if($parentid == 10188 && $childid == 105 && in_array($data['State'],array(2,3))){//运营审核
			$updetail = $this->updateCashDetail($ids,$data['State'],1);
		}elseif($childid == 106 && in_array($data['State'],array(4,5))){ //财务审核
			$updetail = $this->updateCashDetail($ids,$data['State'],3);
		}elseif($childid == 107 && in_array($data['State'],array(6,7))){ //打款审核
			$updetail = $this->updateCashDetail2($ids,$data['State'],5);
		}else{
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		if($updetail[1] == 0){
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}else{
			return array('Flag'=>101,'FlagString'=>'有'.$updetail[1].'笔操作失败');
		}
	}
	
	//人民币账户划入划出
	public function rmbAdd($parentid,$childid,$data){
		if($data['Uin'] < 1 || $data['Weight'] < 1 || $data['ChannelType'] < 0 || empty($data['Desc'])){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		$table = "parter_income.parter_rmb_balance";
		$query = array();
		$query['Uin'] = intval($data['Uin']);
		$fields = array("Balance"=>1);
		$result_condition = array();
		$lastbalance = $this->mongodb->get_row(
			$table,
			$query,
			$result_condition,
			$fields
		);
		if($parentid == 10235 && $childid == 101){//划入
			$Type = 1;
			empty($lastbalance) ? $uptype =3 : $uptype =0;
			$upbalance = json_decode($this->updateRmbBalance($data['Uin'],$data['Weight'],$uptype),true);
			$balance = $lastbalance['Balance'] + $data['Weight'];
			$this->upCaseMonth(date('Ym'),$data['Weight']);
		}
		if($parentid == 10235 && $childid == 102){ //划出
			if($lastbalance['Balance'] < $data['Weight'] ){
				return array('Flag'=>101,'FlagString'=>'余额不足');
			}
			$Type = 2;
			$upbalance = json_decode($this->updateRmbBalance($data['Uin'],-$data['Weight']),true);
			$balance = $lastbalance['Balance'] - $data['Weight'];
			$this->upCaseMonth(date('Ym'),-$data['Weight']);
		}
		if($upbalance['success'] == 100){
			$table = "parter_income.parter_rmb_details";
			$datas = $this->mongodb->query(
				$table,
				array("Uin"=>$data['Uin'],"TargetUin"=>$data['Uin'],"Weight"=>$data['Weight'],"Uptime"=>time(),"Balance"=>$balance,"Type"=>$Type,"ChannelType"=>(int)$data['ChannelType'],"ChildType"=>$childid,'Desc'=>$data['Desc'])
			);
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}
		return array('Flag'=>101,'FlagString'=>'操作失败');
	}
	
	//更新人民币账户 ,划入/划出 默认 审核不通过 1  审核通过 2  新用户划入 3
	private function updateRmbBalance($uin,$weight,$type=0){
		$table = "parter_income.parter_rmb_balance";
		$query_condition['Uin'] = intval($uin);
		$record = array('$inc'=>array('Balance'=>$weight));
		if($type ==1){//审核不通过
			$record = array('$inc'=>array('Balance'=>$weight,'FreezeWeight'=>-$weight));
		}
		if($type ==2){//审核通过
			$record = array('$inc'=>array('FreezeWeight'=>-$weight));
		}
		if($type ==3){//新用户划入
			$record = array('Balance'=>$weight,'Uin'=>$uin);
			$query_condition = array();
		}
		$de_array = array(
			'db'=>'parter_income',
			'table'=>'parter_rmb_balance',
			'record'=>$record,
			'where'=>$query_condition
		);
		return socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($de_array)));
	}
	
//需更新数据   更新后的审核状态   可审核状态 
	private function updateCashDetail($ids,$state,$c_state){
		$fail = 0;
		foreach($ids as $key=>$value){
			list($id,$now_state) = explode('_',$value);
			if($now_state != $c_state){
				$fail++;
				continue;
			}
			$query['_id'] = new MongoId($id);
			$query['State'] = $c_state;
			$row = $this->mongodb->get_row(
				'parter_income.parter_cash_detail',
				$query,
				array(),
				array('Uin'=>1,'Weight'=>1)
			);
			print_r($row);exit;
			if(empty($row)){
				$fail++;
				continue;
			}
			$up_array = array(
				'db'=>'parter_income',
				'table'=>'parter_cash_detail',
				'record'=>array('$set'=>array('State'=>$state,'Uptime'=>time())),
				'where'=>array('_id'=>$id)
			);
			$upback = json_decode(socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($up_array))),true);
			if($c_state+1 == $state){//如果是审核不通过状态,减掉冻结金额
				$this->updateRmbBalance($row['Uin'],$row['Weight'],1);
			}
			if($state == 7){//财务打款成功,减掉冻结金额,同时记录汇总
				$this->updateRmbBalance($row['Uin'],$row['Weight'],2);
				$lastbalance = $this->mongodb->get_row(
					"parter_income.parter_rmb_balance",
					array('Uin'=>$row['Uin']),
					array(),
					array('Balance'=>1)
				);
				$data = $this->mongodb->query(
					"parter_income.parter_rmb_details",
					array("Uin"=>$row['Uin'],"TargetUin"=>$row['Uin'],"Weight"=>$row['Weight'],"Uptime"=>time(),"Balance"=>$lastbalance['Balance'],"Type"=>2,"ChannelType"=>0,"ChildType"=>107)
				);
				$this->upCaseMonth(date('Ym'),-$row['Weight']);
			}
			if($upback['success'] == 100){
				$suc++;
			}else{
				$fail++;
			}
		}
		return array($suc,$fail);
	}
	
	//需更新数据   更新后的审核状态   可审核状态
	private function updateCashDetail2($ids,$state,$c_state){
		$fail = 0;
		foreach($ids as $key=>$value){
			list($id,$now_state) = explode('_',$value);
			if($now_state != 1){
				$fail++;
				continue;
			}
			$query['_id'] = new MongoId($id);
			$row = $this->mongodb->get_row(
					'parter_income.parter_cash_detail',
					$query,
					array(),
					array('Uin'=>1,'Weight'=>1)
			);
			if(empty($row)){
				$fail++;
				continue;
			}
			$up_array = array(
					'db'=>'parter_income',
					'table'=>'parter_cash_detail',
					'record'=>array('$set'=>array('State'=>$state,'Uptime'=>time())),
					'where'=>array('_id'=>$id)
			);
			$upback = json_decode(socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($up_array))),true);
			if($c_state+1 == $state){//如果是审核不通过状态,减掉冻结金额
				$this->updateRmbBalance($row['Uin'],$row['Weight'],1);
			}
			if($state == 7){//财务打款成功,减掉冻结金额,同时记录汇总
				$this->updateRmbBalance($row['Uin'],$row['Weight'],2);
				$lastbalance = $this->mongodb->get_row(
						"parter_income.parter_rmb_balance",
						array('Uin'=>$row['Uin']),
						array(),
						array('Balance'=>1)
				);
				$data = $this->mongodb->query(
						"parter_income.parter_rmb_details",
						array("Uin"=>$row['Uin'],"TargetUin"=>$row['Uin'],"Weight"=>$row['Weight'],
						"Uptime"=>time(),"Balance"=>$lastbalance['Balance'],"Type"=>2,"ChannelType"=>0,
						"ChildType"=>107,"ChannelType"=>8)
				);
				$this->upCaseMonth(date('Ym'),-$row['Weight']);
			}
			if($upback['success'] == 100){
				$suc++;
			}else{
				$fail++;
			}
		}
		return array($suc,$fail);
	}
	
	//更新人民币汇总表
	private function upCaseMonth($uptime ,$weight){
		$uptime = intval($uptime);
		if($weight > 0){//存入
			$field = 'Deposit';
		}elseif($weight < 0){//支出
			$field = 'Pay';
		}
		$row = $this->mongodb->get_row(
			'parter_income.parter_cash_total',
			array(),
			array("sort"=>array("Uptime"=>-1)),
			array('Balance'=>1,'Uptime'=>1)
		);
		if(empty($row)){
			$up_array = array(
				'db'=>'parter_income',
				'table'=>'parter_cash_total',
				'record'=>array($field=>abs($weight),'Balance'=>$weight,'Uptime'=>$uptime)
			);	
		}else{
			if($uptime == $row['Uptime']){
				$up_array = array(
					'db'=>'parter_income',
					'table'=>'parter_cash_total',
					'record'=>array('$inc'=>array($field=>abs($weight),'Balance'=>$weight)),
					'where'=>array('Uptime'=>(int)$uptime)
				);
			}else{
				$balance = $weight + $row['Balance'];
				$up_array = array(
					'db'=>'parter_income',
					'table'=>'parter_cash_total',
					'record'=>array($field=>abs($weight),'Balance'=>$balance,'Uptime'=>$uptime)
				);
			}
		}
		socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($up_array)));
	}
	
	private function showPage($total, $perpage = 10) {
		$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
		));
		$pageArr['page'] = $page->simple_page($total);
		$pageArr['limit'] = $page->simple_limit();
		return $pageArr;
	}
}
*/