<?php
/**
* 砸金蛋
* @author
* @version $Id$
* @copyright (c) 奥点科技
*/
class GoldenEggs extends AbstractClass
{
	protected $db;
	const PAY_KMONEY   = 101; //V豆支付
	const STORE_KMONEY = 103; //V豆存入(中奖)
	const STORE_INCOME = 901;  //公司税收
	const ACTOR_INCOME = 908;  //演绎税收


	function __construct(){
		$this->db = db::connect(config('database','default'));
	}
	
	//发起
	public function StartEggs($param,$extparam){
		$logbuild = new logbuild();
		$checkWork = $logbuild->checkWork($param,$extparam);
		if($checkWork['Flag'] != 100){
			return $checkWork;
		}
		$num = (int)$param['DoingWeight'];
		$gift_info = $this->checkGift($param['BigCaseId'],$param['CaseId'],$param['ParentId'],$extparam['Cmd']);
		$pay_money = floor($gift_info['info']['props_money']) * $num; //支付金额
		$config['extend']['PRIZEINRATE'] = $config['extend']['PRIZEINRATE'] > 0? $config['extend']['PRIZEINRATE'] : 0.1; //税率
		$tax_income = $pay_money * $config['extend']['PRIZEINRATE']; //公司税收
		$desc = "{$param['Uin']}在{$param['ChannelId']}房间发起{$num}个{$gift_info['info']['parent_name']}总金额{$pay_money}";
		//扣除V豆
		$trade_user = $this->trade($param,self::PAY_KMONEY,$pay_money,$desc,$extparam);
		if($trade_user['Flag'] != 100){
			return $trade_user;
		}
		$log[] = $trade_user['log'];
		unset($trade_user['log']);
		//税收
		$param['TaxType'] = 2;
		$trade_tax = $this->trade($param,self::STORE_INCOME,$tax_income,$desc."税收:".$tax_income,$extparam);
		if($trade_tax['Flag'] != 100){
			return $trade_tax;
		}
		$log[] = $trade_tax['log'];
		//返回
		$trade_user['LogData'] = $log;
		return $trade_user;
	}
	
	//砸蛋
	public function SmashEggs($param,$extparam){
		$gift_info = $this->checkGift($param['BigCaseId'],$param['CaseId'],$param['ParentId'],$extparam['Cmd']);
		$config = unserialize($gift_info['config']);
		list($prize_array,$j,$prize_money) = $this->winMoney($config['extend']['PRIZEBASENUM'],$config['extend']['PRIZEOUTRATE'],100-$config['extend']['PRIZEINRATE'],$gift_info['info']['props_money'],1);
		if($prize_money > 0){ //中奖
			$rate = $prize_money / $gift_info['info']['props_money'];
			$desc = $gift_info['info']['parent_name'].'中奖'.$rate.'倍，总金额'.$prize_money;
			$trade_user = $this->trade($param,self::STORE_KMONEY,$prize_money,$desc,$extparam);
			if($trade_user['Flag'] != 100) return $trade_user;
			$log[] = $trade_user['log'];
			$result = array('Flag'=>100,'FlagString'=>$desc,'WinMoney'=>$prize_money,'Rate'=>$rate,'LogData'=>$log);
			if($trade_user['balance']) $result['Balance'] = $trade_user['balance'];
			if($trade_user['VoucherBalance']) $result['VoucherBalance'] = $trade_user['VoucherBalance'];
			// $moneylog = array(
				// 'extparam' => array('Tag'=>'setlog','GroupId'=>$param['GroupId']),
				// 'param' => array('BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>self::STORE_KMONEY)
			// );
		//	httpPOST(KMONEY_API_PATH,$moneylog);
			return $result;
		}else{ //未中奖 
			return array('Flag'=>100,'FlagString'=>'很遗憾，没有中奖！');
		}
	}
}