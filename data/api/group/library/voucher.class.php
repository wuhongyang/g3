<?php

/**
 *   代理账户操作接口
 *   文件: vip.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
include_once __ROOT__.'/api/group/library/group_manage.class.php';
class voucher
{
	//数据库指针
	protected $db = null;
	private static $instance;
	protected $expire = 180;
	
	//获取单例对象
    function instance(){
        if(!is_object(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	//构造函数
	public function __construct() {
        $this->db = domain::main()->GroupDBConn();
        $this->cache = cache::connect(config('cache','memcache'));
	}
	
	public function VoucherBalance($info){
		if($info['GroupId'] <1 ){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		$where = " group_id = {$info['GroupId']}";
		if($info['Uin'] >1){
			$where .= ' AND `uin` = '.$info['Uin'];
		}		
		$status = $info['status']-1;
		if($status >=0){
			$where .= ' AND `balance_status` = '.$status;
		}
		
		$sql="SELECT COUNT(*) FROM ".DB_NAME_VOUCHER.".voucher_balance WHERE ".$where;
		$count=$this->db->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据');
		}

		$pageArr=$this->showPage($count);
		
		$sql="SELECT * FROM ".DB_NAME_VOUCHER.".voucher_balance WHERE ".$where." LIMIT ".$pageArr['limit'];
		$list=$this->db->get_results($sql,'ASSOC');
		foreach($list as $key=>$value){
			$result = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$value['uin'])));
			$list[$key]['nick'] = empty($result['Nick']) ? $value['uin'] : $result['Nick'];
			$list[$key]['balance_status'] = $value['balance_status']+1;
		}
		$user_balance = $this->db->get_var("SELECT sum(last_balance) FROM ".DB_NAME_VOUCHER.".voucher_balance WHERE group_id = {$info['GroupId']}");
		$list['user_balance'] = $user_balance;
		return array('Flag'=>100,'FlagString'=>'V点用户余额列表','List'=>$list,'Page'=>$pageArr['page'],'total'=>$count);
	}
	
	public function VoucherRunning($info){
		if($info['GroupId'] <1){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		$where = " group_id = {$info['GroupId']}";
		
		$startdate = $info['StartDate']? strtotime($info['StartDate']) :strtotime(date('Ymd'));
		$enddate = $info['EndDate'] ? strtotime($info['EndDate']) : time();
		if(!empty($startdate) && !empty($enddate)){
			$where .= ' AND `uptime` BETWEEN '.$startdate.' AND '.$enddate;
		}
		if($info['Uin'] > 0){
			$where .= ' AND uin = '.$info['Uin']; 
		}
		if($info['BigCaseId'] > 0){
			$where .= ' AND bigcase_id='.intval($info['BigCaseId']);
		}
		if($info['CaseId'] > 0){
			$where .= ' AND case_id='.intval($info['CaseId']);
		}
		if($info['ParentId'] > 0){
			$where .= ' AND parent_id='.intval($info['ParentId']);
		}

		$where .= ' AND trade_property = 5';
		$table = DB_NAME_VOUCHER.'.voucher_running';
		
		$sql="SELECT COUNT(*) FROM ".$table." WHERE ".$where;
		$count=$this->db->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据');
		}

		$pageArr=$this->showPage($count);
		
		$sql="SELECT * FROM ".$table." WHERE ".$where." ORDER BY id DESC LIMIT ".$pageArr['limit'];
		$list=$this->db->get_results($sql,'ASSOC');
		/*
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
		}*/
		
		$total_array = $this->cache->get("{$table}_{$where}");
		if(empty($total_array)){
			$long_info = $this->cache->long_get("{$table}_{$where}");
			$this->cache->set("{$table}_{$where}",$long_info,$this->expire);
			$total_array['pay_total'] = $this->db->get_var('SELECT SUM(trade_money) FROM '.$table .' WHERE  '.$where .' AND trade_type = 1');
			$total_array['deposit_total'] = $this->db->get_var('SELECT SUM(trade_money) FROM '.$table .' WHERE  '.$where .' AND trade_type = 2');
			$this->cache->set("{$table}_{$where}",$total_array,$this->expire);
		}
		$list['pay_total'] = $total_array['pay_total'];
		$list['deposit_total'] = $total_array['deposit_total'];
		return array('Flag'=>100,'FlagString'=>'V点流水列表','List'=>$list,'Page'=>$pageArr['page'],'total'=>$count);
	}
	
	public function VoucherParent($info){
		if($info['GroupId'] <1){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		$where = "  group_id = {$info['GroupId']}";
		$startdate = $info['StartDate']? strtotime($info['StartDate']) :strtotime(date('Ymd'));
		$enddate = $info['EndDate'] ? strtotime($info['EndDate']) : time();
		if(!empty($startdate) && !empty($enddate)){
			$where .= ' AND `uptime` BETWEEN '.$startdate.' AND '.$enddate;
		}
		
		$sql="SELECT COUNT(*) FROM ".DB_NAME_VOUCHER.".voucher_net_detail WHERE ".$where;
		$count=$this->db->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据');
		}

		$pageArr=$this->showPage($count);
		
		$sql="SELECT * FROM ".DB_NAME_VOUCHER.".voucher_net_detail WHERE ".$where." ORDER BY id DESC LIMIT ".$pageArr['limit'];
		$list=$this->db->get_results($sql,'ASSOC');
		return array('Flag'=>100,'FlagString'=>'会员列表','List'=>$list,'Page'=>$pageArr['page'],'total'=>$count);
	}
	
	public function TaxRecharge($param,$info){
		if($info['Weight'] < 1 || $info['Uin'] < 1 || $info['GroupId'] < 1 ){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$channelId = intval($info['ChannelId']);
		$room_id = $info['RoomId'];
		$param['Uin'] = $info['Uin'];
		$param['TargetUin'] = $info['Uin'];
		$rst = $this->trade('TaxTrade',array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10264),105,$info['Weight'],'税收兑换金币',$info['GroupId']);
		if($rst['Flag'] !== 100){
			return $rst;
		}
		$rst = $this->trade('Kmoney',$param,$param['ChildId'],$info['Weight'],'税收兑换金币',$info['GroupId']);
		return $rst;
	}
	
	public function GetChannelTax($info){
		if($info['Uin'] < 1 || $info['GroupId'] < 1 ){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$gm = new GroupManage();
		$balance_array = $gm->getBalance($info['Uin']);
		return array('Flag'=>100,'FlagString'=>'成功','Balance'=>$balance_array['Result']['Weight']);
	}
	
	public function VipRecharge($param,$info){
		if($info['Weight'] < 1 || $info['Uin'] < 1 || $info['GroupId'] < 1|| $info['TargetUin'] < 1 ){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		// $result = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uin'=>$info['TargetUin'])));
		$result = getGroupVip($info['TargetUin'],$info['GroupId']);
		if(empty($result)){
			return array('Flag'=>101,'FlagString'=>'用户id不正确');
		}
		$param['Uin'] = $info['TargetUin'];
		$param['TargetUin'] = $info['TargetUin'];
		$rst = $this->trade('Kmoney',$param,$param['ChildId'],$info['Weight'],'站长给用户'.$info['TargetUin']."充值金额".$info['Weight'],$info['GroupId']);
		return $rst;
	}
	
	public function VipDeduct($param,$info){
		if($info['Weight'] < 1 || $info['Uin'] < 1 || $info['GroupId'] < 1|| $info['TargetUin'] < 1 ){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		// $result = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uin'=>$info['TargetUin'])));
		$result = getGroupVip($info['TargetUin'],$info['GroupId']);
		if(empty($result)){
			return array('Flag'=>101,'FlagString'=>'用户id不正确');
		}
		$param['Uin'] = $info['TargetUin'];
		$param['TargetUin'] = $info['TargetUin'];
		$rst = $this->trade('Kmoney',$param,$param['ChildId'],$info['Weight'],'站长扣除用户'.$info['TargetUin']."金额".$info['Weight'],$info['GroupId']);
		return $rst;
	}
	
	public function Account_balacne($info){
		$uin = $info['Uin'];
		$group_id = $info['GroupId'];
		$status  = $info['Status'] % 2;
		$table = DB_NAME_VOUCHER.'.voucher_balance' ;
		if($uin > 0&& $group_id > 0){
			$where = ' uin = '.$uin;
			$where .= ' AND group_id = '.$group_id;
			
			$sql = 'UPDATE '.$table .' SET balance_status = '.$status.' WHERE '.$where;
			if($this->db->query($sql)){
				return array('Flag'=>'100','FlagString'=>'[提示]：操作成功!');
			}
			return array('Flag'=>'101','FlagString'=>'[提示]：操作失败!');
		}
		return array('Flag'=>'101','FlagString'=>'[提示]：参数有误!');
	
	}
	
	/**
	* 交易金额操作
	* @parent interge 一级业务
	* @child interge 二级业务
	* @uin interge 用户id
	* @money floot 交易金额
	* @desc string 描述
	*/
	private function trade($tag='Kmoney',$param,$child,$money,$desc,$GroupId){
		$param['ChildId'] = $child;
		$param['MoneyWeight'] = $money;
		$param['Desc'] = $desc;
		$request = array('param'=>$param,'extparam'=>array('Tag'=>$tag,'Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$GroupId));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		if($islog){
			$log = getLogData($param,$extparam);
		}
		return array('Flag'=>100,'balance'=>$rst['LastBalance'],'log'=>$log);
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
