<?php
/**
* 支票
* @author dl
* @version $Id$
* @copyright (c) 奥点科技
*/
class Zhipiao extends AbstractClass
{
	protected $db;
	const PAY_KMONEY   = 101; //V豆支付
	const STORE_KMONEY   = 102; //V豆存入
	const STORE_INCOME = 901;  //公司税金存入
	const VIP_RATE = 5;  //VIP税收比率
	const USER_RATE = 10;  //普通用户税收比率

	function __construct(){
		$this->db = db::connect(config('database','default'));
        $this->mongo = domain::main()->GroupDBConn('mongo');
	}

	/**
	 * [SendCheck 支票送礼]
	 * @param  [array] $param    [基础参数]
	 * @param  [array] $extparam [扩展参数]
	 * @return [array]           [支票处理结果]
	 */
	function SendCheck($param,$extparam){
		$bigcase = $param['BigCaseId'];
		$case = $param['CaseId'];
		$parent = $param['ParentId'];
		$num = (int)$param['DoingWeight'];
		$user = $param['Uin'];
		$to = $param['TargetUin'];
		$roomid = $param['ChannelId'];
		$client = $param['Client'];
		$param['ActorUin'] = 0;
		
		$info = $this->checkGift($bigcase,$case,$parent,'CMD_ZHIPIAO');
		if($info['Flag'] != 100){
			return $info;
		}
		$config = unserialize($info['config']);
		$pay_money = $info['info']['props_money'] * $num; //支付金额
		if($extparam['IS_VIP'] == 1){ //是会员
			$tax_income = $pay_money * $config['extend']['VIP_RATE'] * 0.01; //税收
			$uin_income = $pay_money * (100-$config['extend']['VIP_RATE']) * 0.01; 
		}else{
			$tax_income = $pay_money * $config['extend']['USER_RATE'] * 0.01; //税收
			$uin_income = $pay_money * (100-$config['extend']['USER_RATE']) * 0.01; 
		}
		$desc = "{$user}在{$roomid}房间送{$num}个{$info['info']['parent_name']}给{$to}总金额{$pay_money}";
		
		//扣除送礼者V豆
		$trade_user = $this->trade($param,self::PAY_KMONEY,$pay_money,$desc,$extparam);
		$param['ChildId'] = self::PAY_KMONEY;
		$param['MoneyWeight'] = $pay_money;
		$param['Desc'] = $desc;
		
		if($trade_user['Flag'] != 100){
			return $trade_user;
		}
		$log[] = $trade_user['log'];
		isset($trade_user['balance']) ? $result['UserBalance'] =$trade_user['balance'] : '';
		isset($trade_user['VoucherBalance']) ? $result['VoucherBalance'] =$trade_user['VoucherBalance'] : '';
		
		//接收者存入V豆
		if($uin_income > 0){
			$trade_target = $this->trade($param,self::STORE_KMONEY,$uin_income,$desc,$extparam);
			if($trade_target['Flag'] != 100){
				$result['Flag'] = $trade_target['Flag'];
				$result['FlagString'] = $trade_target['FlagString'];
				return $result;
			}
			$log[] = $trade_target['log'];
			isset($trade_target['balance']) ? $result['ToBalance'] =$trade_target['balance'] : '';
			isset($trade_target['VoucherBalance']) ? $result['ToVoucherBalance'] =$trade_target['VoucherBalance'] : '';
		}
		
		//税收存入
		if($tax_income > 0){
			$param['TaxType'] = 2;
			$trade_tax = $this->trade($param,self::STORE_INCOME,$tax_income,$desc.',税收'.$tax_income,$extparam);
			if($trade_tax['Flag'] != 100){
				$result['Flag'] = $trade_tax['Flag'];
				$result['FlagString'] = $trade_tax['FlagString'];
				return $result;
			}
			$log[] = $trade_tax['log'];
		}
		$result['Flag'] = 100;
		$result['FlagString'] = '操作成功';
		$result['LogData'] = $log;
        $result['DayPropNum'] = $num + $this->gift_num_day($this->mongo, $to, $param['GroupId'], $parent);
        
		return $result;
	}
}