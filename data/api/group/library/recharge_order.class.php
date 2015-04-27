<?php
class recharge_order{
	//数据库指针
	protected $db = null;
	
	//构造函数
	public function __construct() {
		$this->db	= db::connect(config('database','default'));
	}
	
	function get_list($search){
		$where = "";
		if($search['uin']){
			$where .= " AND uin = '".$search['uin']."'";
		}
		if($search['status'] > -1){
			$where .= " AND `status` = '".$search['status']."'";
		}
		if($search['parent_type'] > -1){
			$where .= " AND parent_type = '".$search['parent_type']."'";
		}else{
			$where .= " AND parent_type IN(10094, 10095, 10320, 10064)";
		}
		if($search['start_time']){
			$where .= " AND uptime >= '".$search['start_time']."'";
		}
		if($search['end_time']){
			$where .= " AND uptime <= '".$search['end_time']."'";
		}
		$sql = "SELECT COUNT(*) FROM ".DB_NAME_COMMON.".tbl_recharge_order where groupid = '".$search['group_id']."' ".$where;
		$total = $this->db->get_var($sql);
		
		$page_arr = $this->showPage($total);
		
		$sql = "SELECT * FROM ".DB_NAME_COMMON.".tbl_recharge_order where groupid = '".$search['group_id']."' ".$where." ORDER BY uptime DESC LIMIT ".$page_arr['limit'];
		$res = $this->db->get_results($sql, "ASSOC");
		
		return array("Flag"=>100, "FlagString"=>"查询成功", "List"=>$res, "Page"=>$page_arr['page']);
	}
	
	function remedy($trade_id){
		$sql = "SELECT * FROM ".DB_NAME_COMMON.".tbl_recharge_order where trade_id = '".$trade_id."'";
		$row = $this->db->get_row($sql, "ASSOC");
		if($row['status'] != 2 && $row['status'] != 0){
			return array("Flag"=>102, "FlagString"=>"此订单不处于可补单状态");
		}
		$groupid = $row['groupid'];
		$balance = get_parent_money(10006, 10049, 10269, $groupid);
		$deposit = $row['rebate']*$row['money']*10000;
		$parent_id = $row['parent_type'];
		$uin = $row['uin'];
		$pay_uin = $row['pay_uin'];
		$channel_id = $row['channel_id'];
		if($balance >= $deposit){
			$content = "未知充值:";
			switch($parent_id){
				case '10064':
					$content = "支付宝充值";
					break;
				case '10094':
					$content = "网银充值";
					break;
				case '10095':
					$content = "财付通";
					break;
				case '10320':
					$content = "储蓄卡充值";
					break;
			}
			$trade_desc3	= $content.',充值ID:'.$uin.',金额:'.$deposit.'金币.从站预存账户扣除'.$deposit.'金币';
			$param = array(
					'extparam' => array('Tag' => 'Kmoney', 'Operator' => '574B9AEC5E7BB01C96730C1B9E05C0E2','GroupId'=>$groupid),
					'param' => array('Uin' => $pay_uin,'TargetUin' => $uin, 'MoneyWeight' => $deposit, 'ParentId' => 10269, 'ChildId' => 108, 'Desc' => $trade_desc3,'ChannelId'=>$channel_id,'BigCaseId'=>10006,'CaseId'=>10049),
			);
			$log[] = $param;
			$pay_result3 = httpPOST(KMONEY_API_PATH, $param);
			
			$trade_desc1	= $content.',充值ID:'.$uin.',金额:'.$deposit.'金币[三级余额库]';
			$param = array(
					'extparam' => array('Tag' => 'Kmoney', 'Operator' => '574B9AEC5E7BB01C96730C1B9E05C0E2','GroupId'=>$groupid),
					'param' => array('Uin' => $pay_uin,'TargetUin' => $uin, 'MoneyWeight' => $deposit, 'ParentId' => $parent_id, 'ChildId' => 101, 'Desc' => $trade_desc1,'ChannelId'=>$channel_id,'BigCaseId'=>10005,'CaseId'=>10024),
			);
			$pay_result1 = httpPOST(KMONEY_API_PATH, $param);
			$log[] = $param;
			$trade_desc2	= $content.',充值ID:'.$uin.',金额:'.$deposit.'金币[用户余额库]';
			$param = array(
					'extparam' => array('Tag' => 'Kmoney', 'Operator' => '574B9AEC5E7BB01C96730C1B9E05C0E2','GroupId'=>$groupid),
					'param' => array('Uin' => $pay_uin,'TargetUin' => $uin, 'MoneyWeight' => $deposit, 'ParentId' => $parent_id, 'ChildId' => 102, 'Desc' => $trade_desc2,'ChannelId'=>$channel_id,'BigCaseId'=>10005,'CaseId'=>10024),
			);
			$pay_result2 = httpPOST(KMONEY_API_PATH, $param);
			$log[] = $param;
			
			if($pay_result1['Flag'] == 100 && $pay_result2['Flag'] == 100 && $pay_result3['Flag'] == 100){
				$sql = 'UPDATE '.DB_NAME_COMMON.'.tbl_recharge_order SET `status`=1 WHERE trade_id="'.$trade_id.'";';
				$this->db->query($sql);
				return array("Flag"=>100, "FlagString"=>"充值成功",'LogData'=>$log);
			}else{
				return array("Flag"=>102, "FlagString"=>"充值失败");
			}
		}else{
			return array("Flag"=>102, "FlagString"=>"余额不足");
		}
	}
	
	//分页
	private function showPage($total, $perpage = 15) {
		$page = new extpage(array (
				'total' => $total,
				'perpage' => $perpage
		));
		$page_arr['page'] = $page->show();
		$page_arr['limit'] = $page->limit();
		unset ($page);
		return $page_arr;
	}
}