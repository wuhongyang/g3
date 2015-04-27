<?php
/**
* 彩票
* @author dl
* @version $Id$
* @copyright (c) 奥点科技
*/
class Chongbai extends AbstractClass
{
	protected $db;
	const PAY_KMONEY   = 101; //V豆支付
	const STORE_INCOME = 901;  //公司税金存入

	function __construct(){
		$this->db = db::connect(config('database','default'));
        $this->mongo = domain::main()->GroupDBConn('mongo');
	}

	/**
	* 彩票送礼
	* @param $param array 基础参数
	* @param $extparam array 扩展参数
	*/
	function deduct($param,$extparam){
		$bigcase = $param['BigCaseId'];
		$case = $param['CaseId'];
		$parent = $param['ParentId'];
		$num = (int)$param['DoingWeight'];
		$user = $param['Uin'];
		$to = $param['TargetUin'];
		$roomid = $param['ChannelId'];
		$client = $param['Client'];
		$param['ActorUin'] = 0;

		//礼物信息
		$info = $this->checkGift($bigcase,$case,$parent);
		
		if($info['Flag'] != 100){
			return $info;
		}
		$pay_money = floor($info['info']['props_money']) * $num; //支付金额
		$tax_income = $pay_money * $info['info']['tax_percent'] * 0.01; //税收
		$desc = "购买{$num}个{$info['info']['parent_name']}总金额{$pay_money}";
		
		//扣除送礼者V豆
		$trade_user = $this->trade($param,self::PAY_KMONEY,$pay_money,$desc,$extparam);
		if($trade_user['Flag'] != 100){
			return $trade_user;
		}
		$log[] = $trade_user['log'];
		isset($trade_user['balance']) ? $result['UserBalance'] =$trade_user['balance'] : '';
		isset($trade_user['VoucherBalance']) ? $result['VoucherBalance'] =$trade_user['VoucherBalance'] : '';
		$trade_user['log']['param']['ChildId'] = 102;
		$trade_user['log']['param']['MoneyWeight'] = 0;
		$trade_user['log']['param']['Desc'] = "收到{$num}个{$info['info']['parent_name']}";
		$log[] = $trade_user['log'];

		//税收存入
		if($tax_income > 0){
			$param['TaxType'] =2;
			$trade_tax = $this->trade($param,self::STORE_INCOME,$tax_income,$desc.'税收：'.$tax_income,$extparam);
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
