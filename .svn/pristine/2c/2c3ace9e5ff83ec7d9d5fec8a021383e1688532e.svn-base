<?php

/**
 *   V宝交易查询
 *   文件: vdmanage.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class vdmanage
{

	//构造函数
	public function __construct() 
	{
		$this->db = domain::main()->GroupDBConn();
		$this->platform_db = db::connect(config('database','default'));
	}

	/**
	 *   V豆明细查询
	 *   @param	array	$message
	 *   @return	array	$return
	 */
	public function showVdList($uin,$message)
	{
		if($uin > 0){
			$where = " uin = '$uin' ";
			//日期条件
			if( !empty($message['startDate']) )	{
				$start = strtotime($message['startDate']);
				$where .= " AND uptime >= '$start' ";
			}
			if( !empty($message['endDate']) ){
				$end = strtotime($message['endDate'])+3600*24;
				$where .= " AND uptime <= '$end' ";
			}
			//类型条件
			$case = $message['Case'];
			if( !empty($case) ){
				if( $case == 'deposit' ) $where .= " AND trade_type = '2' ";

				if( $case == 'pay'  ) $where .= " AND trade_type = '1' ";
			}
			$where .= ' AND trade_property = 5';
			//查询V宝交易记录
			$table = DB_NAME_KMONEY.'.kmoney_running';
			$dl = new dlhelper($this->db);
			$dl->isShow = 1;
			$lists = $dl->findAllPage($table, $where, 'id DESC', 'uin,trade_type,trade_money,trade_desc,last_balance,uptime,id');
			$page = $dl->getPage();
			$array = array(
				'lists'    => $lists,
				'page'     => $page,
				'Flag'	   => '100',
				'FlagString'=> 'success'
			);
		}else{
			$array = array(
				'Flag'	   => 101,
				'FlagString'=> '参数有误'
			);
		}
		return $array;
	}
	
	public function VChange($info){
		$info['Uin'] = (int)$info['Uin'];
		$info['Group_id'] = (int)$info['Group_id'];
		$info['Weight'] = (int)$info['Weight'];
		if($info['Uin'] > 0 && $info['Group_id'] > 0 && $info['Weight'] > 0){
			$param['Uin'] = $info['Uin'];
			$param['TargetUin'] = $info['Uin'];
			$param['ChannelId'] = 1;
			$param['MoneyWeight'] = $info['Weight'];
			$param['BigCaseId'] = 10005;
			$param['CaseId'] = 10052;
			$param['ParentId'] = 10316;
			$param['Desc']  = "使用金豆余额兑换金币";			
		//	$logbuild = new logbuild();
			
			$group_info = $this->GroupInfo(array('Group_id'=>$info['Group_id']));
			if($group_info['Flag'] != 100){
				return $group_info;
			}
			$param['ChildId'] = 101;
			$kmoney_result = $this->trade($param);
			if($kmoney_result['Flag'] != 100){
				return $kmoney_result;
			}
			$param['ChildId'] = 102;
			$kmoney_result1 = $this->trade($param);
			if($kmoney_result1['Flag'] != 100){
				return $kmoney_result1;
			}
		//	$logbuild->setlog(array('param'=>$param));
			$param['ChildId'] = 103;
			$voucher_result = $this->trade($param,$info['Group_id'] );
			if($voucher_result['Flag'] != 100){
				return $voucher_result;
			}
			$log[] = $voucher_result['log'];
			$param['ChildId'] = 104;
			$voucher_result = $this->trade($param,$info['Group_id'] );
			if($voucher_result['Flag'] == 100){
				$voucher_result['LogData'] = $log;
			}
			$param['GroupId'] = $info['Group_id'];
		//	$logbuild->setlog(array('param'=>$param));
			unset($voucher_result['log']);
			return $voucher_result;
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	public function VdianList($info){
		if($info['Uin'] > 0 && $info['Group_id'] > 0){
			$where = " uin = {$info['Uin']} AND group_id = {$info['Group_id']} ";
			//日期条件
			if( !empty($info['startDate']) ){
				$start = strtotime($info['startDate']);
				$where .= " AND uptime >= '$start' ";
			}
			if( !empty($info['endDate']) ){
				$end = strtotime($info['endDate'])+3600*24;
				$where .= " AND uptime <= '$end' ";
			}
			//类型条件
			$case = $info['Case'];
			if( !empty($case) ){
				if( $case == 'deposit' ) $where .= " AND trade_type = '2' ";

				if( $case == 'pay'  ) $where .= " AND trade_type = '1' ";
			}
			$where .= ' AND trade_property = 5';
			//查询V宝交易记录
			$table = DB_NAME_VOUCHER.'.voucher_running';
			$dl = new dlhelper($this->db);
			$dl->isShow = 1;
			$lists = $dl->findAllPage($table, $where, 'id DESC', 'uin,trade_type,trade_money,trade_desc,last_balance,uptime,id,group_id');
			$page = $dl->getPage();
			$group_info = $this->GroupInfo(array('Group_id'=>$info['Group_id']));
			$array = array(
				'List'    => $lists,
				'Page'     => $page,
				'group_name'     => $group_info['GroupInfo']['name'],
				'Flag'	   => '100',
				'FlagString'=> 'success'
			);
			return $array;
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
		
	/*
	 *   V豆捐赠
	 *   @param	array	$param	V豆捐赠的信息
	 *   @return	array	$return		捐赠结果
	 */
	public function vdDonate($param)
	{
		$uin = $param['Uin'];
		$vdnum = $param['MoneyWeight'];
		$desc = $param['Desc'];
		if($uin >0 && $vdnum > 0 && !empty($desc)){
			$param = array(
				'extparam' => array(
					'Tag'	=> 'Kmoney',
					'Operator' => '3C873E4DE4EA98C1C738836502BACDAC',
				),
				'param'	=> array(
					'Uin'	=> $uin,
					'TargetUin' => '8888',
					'ChannelId' => '1',
					'MoneyWeight'=> $vdnum,
					'ParentId' => '10032',
					'ChildId' => '102',
					'Client' => 'WEB',
					'Desc'	=> '金豆捐赠:'.$desc,
				),
			);
			$kmoney = httpPOST(KMONEY_API_PATH, $param, true);
			if( $kmoney['Flag'] != '100' ){
			//	$this->rollback_transaction(KMONEY_API_PATH);
				$array = array(
					'Flag'	=> '101',
					'FlagString' => '捐赠失败',
				);
			}else{
			//	$this->commit_transaction(KMONEY_API_PATH);
				$array = array(
					'Flag'	=> '100',
					'FlagString' => '捐赠成功',
				);
			}
		}else{
			$array = array(
				'Flag'	=> '102',
				'FlagString' => '捐赠失败,参数有误',
			);
		}
		return $array;
	}
	
	public function ShowVDiList($uin){
		if($uin > 0){
			$sql = "SELECT COUNT(*) FROM ".DB_NAME_VOUCHER.".`voucher_balance` WHERE uin = {$uin}";
			$count = $this->db->get_var($sql);
			if($count<=0){
				return array('Flag'=>100,'FlagString'=>'没有数据');
			}
			$pageArr=$this->showPage($count);
			$sql = "SELECT * FROM ".DB_NAME_VOUCHER.".`voucher_balance` WHERE uin = {$uin} LIMIT ".$pageArr['limit'];
			$result = $this->db->get_results($sql,'ASSOC');
			// foreach($result as $key=>$value){
				// $group_info = $this->GroupInfo(array('Group_id'=>$value['group_id']));
				// $result[$key]['group_name'] = $group_info['GroupInfo']['name'];
			// }
			return array('Flag'=>100,'FlagString'=>'成功','List'=>$result,'Page'=>$pageArr['page'],'total'=>$count);
		}
		
	}
	
	public function GroupInfo($info){
		if($info['Group_id'] > 0){
			$sql = "SELECT * FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid = {$info['Group_id']}";
			$row = $this->platform_db->get_row($sql,'ASSOC');
			if(!empty($row)){
				return array('Flag'=>100,'FlagString'=>'成功','GroupInfo'=>$row);
			}
			return array('Flag'=>101,'FlagString'=>'站不存在');
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	private function commit_transaction($api_url){
		httpPOST($api_url,array("extparam"=>array('Tag'=>'Commit')));
	}

	private function rollback_transaction($api_url){
		httpPOST($api_url,array("extparam"=>array('Tag'=>'Rollback')));
	}
	
		/**
	* 交易金额操作
	* @parent interge 一级业务
	* @child interge 二级业务
	* @uin interge 用户id
	* @money floot 交易金额
	* @desc string 描述
	*/
	private function trade($param,$GroupId=0){
		$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$GroupId));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		return array('Flag'=>100,'balance'=>$rst['LastBalance'],'FlagString'=>'成功','log'=>$request);
	}
	
	private function showPage($total, $perpage = 20) {
		if ($total > 0) {
			$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
			));
			$page_arr['page'] = $page->show();
			$page_arr['limit'] = $page->limit();
			unset ($page);
		}
		return $page_arr;
	}
}


