<?php
/**
* 风车
* @author dl
* @version $Id$
* @copyright (c) 奥点科技
*/
class Windmill extends AbstractClass
{
	protected $db;
//	const PRIZEIN_KMONEY   = 103; //V豆存入(奖池)
//	const PRIZEOUT_KMONEY   = 105; //V豆支出(奖池 用户中奖)
	const PAY_KMONEY   = 101; //V豆支付
	const STORE_KMONEY   = 102; //V豆存入(接收)
	const PRIZEUSER_KMONEY   = 104; //V豆存入(用户中奖)
	const STORE_INCOME = 901;  //渠道税收
	const ACTOR_INCOME = 908;  //演绎税收


	function __construct(){
		$this->db = db::connect(config('database','default'));
        $this->mongo = domain::main()->GroupDBConn('mongo');
	}

	/**
	* 风车
	* @cmd string 礼物名称
	* @user interge 送礼人
	*/
	/**
	 * [deduct 风车送礼]
	 * @param  [type] $param    [基础参数]
	 * @param  [type] $extparam [扩展参数]
	 * @return [type]           [送礼结果]
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
		$cmd = $extparam['Cmd'];
		$param['ActorUin'] = 0;

		//礼物信息
		$gift_info = $this->checkGift($bigcase,$case,$parent,$cmd);
		if($gift_info['Flag'] != 100){
			return $gift_info;
		}
		$config = unserialize($gift_info['config']);
		$pay_money = floor($gift_info['info']['props_money']) * $num; //支付金额	
		$tax_income = $pay_money * $config['extend']['PRIZEINRATE'] * 0.01; //公司税收		
		$desc = "{$user}在{$roomid}房间送{$num}个{$gift_info['info']['parent_name']}给{$to}总金额{$pay_money}";
		//是否开启判断
		$logbuild = new logbuild();
		$checkWork = $logbuild->checkWork($param,$extparam);
		if($checkWork['Flag'] != 100){
			return $checkWork;
		}
		//扣除送礼者V豆
		$trade_user = $this->trade($param,self::PAY_KMONEY,$pay_money,$desc,$extparam);
		if($trade_user['Flag'] != 100){
			return $trade_user;
		}
		$log[] = $trade_user['log'];
		isset($trade_user['balance']) ? $result['UserBalance'] =$trade_user['balance'] : '';
		isset($trade_user['VoucherBalance']) ? $result['VoucherBalance'] =$trade_user['VoucherBalance'] : '';
		//税收存入
		if($tax_income > 0){
			$param['TaxType'] = 2;
			$trade_tax = $this->trade($param,self::STORE_INCOME,$tax_income,$desc.'税收：'.$tax_income,$extparam);
			if($trade_tax['Flag'] != 100){
				$result['Flag'] = $trade_tax['Flag'];
				$result['FlagString'] = $trade_tax['FlagString'];
				return $result;
			}
			$log[] = $trade_tax['log'];
		}
		//计算中奖金额
		list($prize_array,$j,$prize_money) = $this->winMoney($config['extend']['PRIZEBASENUM'],$config['extend']['PRIZEOUTRATE'],100-$config['extend']['PRIZEINRATE'],$gift_info['info']['props_money'],$num);
		if($prize_money > 0){
			$param['TaxType'] = 0;
			$targetprize = $prize_money*$config['extend']['TARGETRATE']*0.01;
			$desc = $user.'赠送'.$gift_info['info']['parent_name'].'共中奖'.$j.'次,中奖总金额'.$prize_money.',赠与'.$param['TargetUin'].':'.$targetprize;
			$total_money = $prize_money - $targetprize;
			$targetuin = $param['TargetUin'];
			$param['TargetUin'] = $param['Uin'];
			$trade_prize = $this->trade($param,self::PRIZEUSER_KMONEY,$total_money,$desc,$extparam);
			isset($trade_prize['balance']) ? $result['UserBalance'] =$trade_prize['balance'] : '';
			isset($trade_prize['VoucherBalance']) ? $result['VoucherBalance'] =$trade_prize['VoucherBalance'] : '';
			if($trade_prize['Flag'] !=100){
				$result['Flag'] = $trade_prize['Flag'];
				$result['FlagString'] = $trade_prize['FlagString'];
				return $result;
			}
			$log[] = $trade_prize['log'];
			if($targetprize > 0){
				//判断收礼人是否是艺人
				$param['TargetUin'] = $targetuin;
				$uin_type = getChannelType($param['TargetUin'],$param['ChannelId']);
				$child_type = self::STORE_KMONEY; //税收者存入
				$param['TaxType'] = 0;
				if($uin_type > 0){
					$param['ActorUin'] = (int)$param['TargetUin'];
					if($gift_info['info']['actor_tax'] == 1){//启用艺人渠道税收
						$param['TaxType'] = (int)$gift_info['info']['actor_tax'];
						$child_type = self::ACTOR_INCOME;
					}
				}
				$uin_income = $targetprize; //接收金额
				//接收者存入V豆
				$trade_target = $this->trade($param,$child_type,$uin_income,$desc,$extparam);
				if($trade_target['Flag'] != 100){
					$result['Flag'] = $trade_target['Flag'];
					$result['FlagString'] = $trade_target['FlagString'];
					return $result;
				}
				$log[] = $trade_target['log'];
			}
		}
		isset($trade_target['balance']) ? $result['TargetBalance'] =$trade_target['balance'] : '';
		isset($trade_target['VoucherBalance']) ? ($result['ToVoucherBalance'] =$trade_target['VoucherBalance']) : '';
		$result['Flag'] = 100;
		$result['FlagString'] = '送礼成功';
		$result['LogData'] = $log;
		$result['PrizeArray'] = $prize_array;
		$result['PropsMoney'] = $gift_info['info']['props_money'];
		$result['TargetPrize'] = $uin_income;
		$result['ActorUin'] = $param['ActorUin'];
		$result['DayPropNum'] = $num + $this->gift_num_day($this->mongo, $to, $param['GroupId'], $parent);
        
		return $result;
	}
}