<?php
class Vip
{
	private $db;
	private static $rate=10000;
	public function __construct(){
		//$this->db = db::connect(config('database','default'));
	}
	/*
	public function buyVip($info){
		$info['VipInfo'] = $info['Vips'][$info['RoleId']];
		$parent_id = $info['VipInfo']['parent_id'];
		$price = $info['VipInfo']['price'] * self::$rate;
		$desc = '用户：'.$info['Uin'].' 从用户余额库扣除 '.$price;
		$param = array(
            'extparam' => array('Tag'=>'Kmoney', 'Operator'=>'574B9AEC5E7BB01C96730C1B9E05C0E2','GroupId'=>$info['GroupId']),
            'param' => array('BigCaseId'=>10005,'CaseId'=>10018,'ParentId'=>$parent_id, 'ChildId'=>101, 'Desc'=>$desc,'MoneyWeight'=>$price,'Uin'=>$info['Uin']),
        );
        $rst = httpPOST(KMONEY_API_PATH,$param);
        if($rst['Flag'] != 100){
        	return $rst;
        }
        //全额存到渠道税收
        $desc = '用户：'.$info['Uin'].' 购买 “'.$info['VipInfo']['name'].'“ 价格：'.$price." 全额入税收";
        $param = array(
            'extparam' => array('Tag'=>'Kmoney', 'Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$info['GroupId']),
            'param' => array('BigCaseId'=>10005,'CaseId'=>10018,'ParentId'=>$parent_id, 'ChildId'=>901, 'Desc'=>$desc,'MoneyWeight'=>$price,'Uin'=>$info['Uin']),
        );
		$rst = httpPOST(KMONEY_API_PATH,$param);
		if($rst['Flag'] != 100){
			return $rst;
		}
		//记录会员
		$vipInfo = array('group_id'=>$info['GroupId'],'uin'=>$info['Uin'],'vip_uin'=>$info['Uin'],'vip_grade'=>$info['RoleId'],'buy_expense'=>$info['VipInfo']['price'],'pay_expense'=>$info['VipInfo']['price'],'use_expire'=>365);
		$rst = $this->recordVipBuy($vipInfo);

		//授予用户角色
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'AddGroupRole',
				'GroupId'=>$info['GroupId'],
				'Uin'=>$info['Uin'],
				'RoleId'=>intval($info['RoleId']),
				'Ruleid'=>10255
			)
		);
		$rst = httpPOST(ROLE_API_PATH,$roleData);

		return $rst;
	}

	public function getVipPrice($group_id,$uin){
		$info = $this->getVipInfo($group_id,$uin);
		$info = $info['Info'];
		return array('Flag'=>100,'FlagString'=>'会员价格','Price'=>intval($info['buy_expense']));
	}
*/
/*	public function getVipInfo($group_id,$uin){
		$group_id = intval($group_id);
		$uin = intval($uin);
		if($group_id < 1 || $uin < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT * FROM ".DB_NAME_COMMON.".tbl_vip_info WHERE group_id={$group_id} AND vip_uin={$uin}";
		$info = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'会员信息','Info'=>$info);
	}

	public function isVipCanBuy($group_id,$uin,$price){
		$info = $this->getVipInfo($group_id,$uin);
		$info = $info['Info'];
		if($info['buy_expense'] < $price){
			return array('Flag'=>100,'FlagString'=>'可以购买');
		}
		return array('Flag'=>101,'FlagString'=>'不能购买');
	}

	public function recordVipBuy($info){
		$priceInfo = $this->getVipPrice($info['group_id'],$info['uin']);
		$time = time();
		if(empty($priceInfo['Price'])){
			$sql = "INSERT INTO ".DB_NAME_COMMON.".tbl_vip_info(`trade_id`,`group_id`,`uin`,`vip_uin`,`vip_grade`,`use_expire`,`buy_expense`,`pay_expense`,`operator_id`,`uptime`) VALUES('{$info['trade_id']}',{$info['group_id']},{$info['uin']},{$info['vip_uin']},{$info['vip_grade']},{$info['use_expire']},{$info['buy_expense']},{$info['pay_expense']},'{$info['operator_id']}',{$time})";
		}elseif($priceInfo['Price'] < $info['pay_expense']){
			$sql = "UPDATE ".DB_NAME_COMMON.".tbl_vip_info SET uin={$info['uin']},buy_expense={$info['buy_expense']},pay_expense={$info['pay_expense']},uptime={$time} WHERE group_id={$info['group_id']} AND vip_uin={$info['vip_uin']}";
		}else{
			return array('Flag'=>101,'FlagString'=>'已有高等级会员，不能购买');
		}
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'记录会员购买失败');
		}
		return array('Flag'=>100,'FlagString'=>'记录会员购买成功');
	}*/

}