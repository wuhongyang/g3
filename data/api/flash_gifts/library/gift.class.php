<?php
/**
* 情感礼物送礼
* @author dl
* @version $Id$
* @copyright (c) 奥点科技
*/
class Gift extends AbstractClass
{
	//db操作对象
	protected $db;
	const PAY_KMONEY   = 101; //V豆支付(赠送)
	const STORE_KMONEY = 102; //V豆存入(接收)
	const STORE_INCOME = 901; //公司税金存入(渠道税收)
	const ACTOR_INCOME = 908;  //演绎税收

	/**
	* 构造函数
	* @param object $db 数据库驱动
	* @return 无
	*/
	function __construct(){
		$this->db = db::connect(config('database','kkyoo_new_rooms'));
		$this->mongo = domain::main()->GroupDBConn('mongo');
	}

	/**
	* 送礼操作
	* @cmd string 礼物名称
	* @num interge 数量
	* @user interge 送礼人
	* @to interge 接收人(送给谁)
	*/
	function sendGift($param,$extparam){
		$bigcase = $param['BigCaseId'];
		$case = $param['CaseId'];
		$parent = $param['ParentId'];
		$num = $param['DoingWeight'];
		$user = $param['Uin'];
		$to = $param['TargetUin'];
		$roomid = $param['ChannelId'];
		$client = $param['Client'];
		
		//分站礼物信息
		$region_info = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'PropsList','Data'=>array('big_case_id'=>$bigcase,'case_id'=>$case,'parent_id'=>$parent),'Id'=>$extparam['ID'])));
		if($region_info['Flag'] !== 100 || empty($region_info['list'])) return array('Flag'=>101,'FlagString'=>'礼物不存在');
		
		//礼物资金信息
		$pay_money  = floor($region_info['list']['props_money'] * $num); //支付金额
		$uin_income = $pay_money * $region_info['list']['receive_percent'] * 0.01; //接收得到
		$tax_income = $pay_money * $region_info['list']['tax_percent'] * 0.01; //税收
		$pool_income = $pay_money * (int)$region_info['list']['pool_percent'] * 0.01; //奖池税收
		$desc = "{$user}在{$roomid}房间送{$num}个{$region_info['list']['props_name']}给{$to}总金额{$pay_money}";

		//开始交易

		//接收者得到，判断收礼人是否是艺人
		$uin_type = getChannelType($param['TargetUin'],$param['ChannelId']);
		$param['ActorUin'] = $uin_type > 0 ? (int)$param['TargetUin'] :0;
		
		//扣除送礼者V豆
		$param['ChildId'] = self::PAY_KMONEY;
		$param['MoneyWeight'] = $pay_money;
		$param['Desc'] = $desc;
		$trade_user = $this->trade($param,self::PAY_KMONEY,$pay_money,$desc,$extparam);
		if($trade_user['Flag'] != 100){
			return $trade_user;
		}
		$log = getLogData($param,$extparam);
		$log_data[] = $log;
		unset($trade_user['log']);
		isset($trade_user['balance']) ? $result['balance'] =$trade_user['balance'] : '';
		isset($trade_user['VoucherBalance']) ? $result['VoucherBalance'] =$trade_user['VoucherBalance'] : '';
		//公司税收存入
		if($tax_income > 0){
			$param['TaxType'] = 2; 
			$trade_tax = $this->trade($param,self::STORE_INCOME,$tax_income,$desc."税收{$tax_income}",$extparam);
			if($trade_tax['Flag'] != 100){
				$result['Flag'] = $trade_tax['Flag'];
				$result['FlagString'] = $trade_tax['FlagString'];
				return $result;
			}
			$log['param']['ChildId'] = self::STORE_INCOME;
			$log['param']['MoneyWeight'] = $tax_income;
			$log['param']['Desc'] = $desc."税收{$tax_income}";
			$log_data[] = $log;
			unset($trade_user['log']);
		}		
		
		if($uin_income > 0){
			$child_type = self::STORE_KMONEY; //税收者存入
			$param['TaxType'] = 0; //不启用艺人渠道税收
			if($uin_type > 0){ //是艺人
				if($region_info['list']['actor_tax'] == 1){ //启用艺人渠道税收
					$param['TaxType'] = (int)$region_info['list']['actor_tax'];
					$child_type = self::ACTOR_INCOME;
				}
			}
			//用户或艺人税收存入
			$trade_to = $this->trade($param,$child_type,$uin_income,$desc,$extparam);
			if($trade_to['Flag'] != 100){
				$result['Flag'] = $trade_to['Flag'];
				$result['FlagString'] = $trade_to['FlagString'];
				return $result;
			}
			$log['param']['ChildId'] = $child_type;
			$log['param']['MoneyWeight'] = $uin_income;
			$log['param']['Desc'] = $desc;
			$log_data[] = $log;
			unset($trade_user['log']);
			if(!$param['ActorUin']>0){
				isset($trade_to['balance']) ? $result['ToBalance'] =$trade_to['balance'] : '';
				isset($trade_to['VoucherBalance']) ? $result['ToVoucherBalance'] =$trade_to['VoucherBalance'] : '';
			}
		}
		
		//如果礼物设置为中奖礼物
		$prize_config = json_decode($region_info['list']['value'],true);
		if($region_info['list']['is_prize']  && $prize_config['PRIZEOUTRATE']['value'] > 0){
			
			$param['BigCaseId'] = $bigcase;
			$param['CaseId'] = $case;
			$param['ParentId'] = $parent;
			$param['TargetUin'] = $param['Uin'];
			if($pool_income > 0){
				$trade_prize = $this->trade($param,911,$pool_income,$desc."奖池".$pool_income,$extparam);
				
				$log['param']['ChildId'] = 911;
				$log['param']['MoneyWeight'] = $pool_income;
				$log['param']['Desc'] = $desc."奖池".$pool_income;
				$log_data[] = $log;
				
				$param['BigCaseId'] = 10006;
				$param['CaseId'] = 10049;
				$param['ParentId'] = 10616;
				$param['TargetUin'] = $param['Uin'];
				$trade_prize = $this->trade($param,102,$pool_income,$desc."奖池".$pool_income,$extparam);
				
				$log['param']['BigCaseId'] = 10006;
				$log['param']['CaseId'] = 10049;
				$log['param']['ParentId'] = 10616;
				$log['param']['ChildId'] = 102;
				$log['param']['TargetUin'] = $param['Uin'];
				$log['param']['MoneyWeight'] = $pool_income;
				$log['param']['Desc'] = $desc;
				$log_data[] = $log;
			}
			//是否开启判断
			$logbuild = new logbuild();
			$extparam['GroupId'] = $param['GroupId'];
			$checkWork = $logbuild->checkWork($param,$extparam);
			if($checkWork['Flag'] == 100){
				list($prize_array,$j,$prize_money,$rate_array) = $this->winMoney($prize_config['PRIZEBASENUM']['value'],$prize_config['PRIZEOUTRATE']['value'],100-$prize_config['PRIZEINRATE']['value'],$region_info['list']['props_money'],$num);
				if($prize_money > 0){
					$desc = $user.'赠送'.$region_info['list']['props_name'].'共中奖'.$j.'次,中奖总金额'.$prize_money;
					$trade_prize = $this->trade($param,101,$prize_money,$desc,$extparam);
					if($trade_prize['Flag'] !=100){
						$prize_array = array();
						$rate_array = array();
					}else{
						$result['VoucherBalance'] = $trade_prize['VoucherBalance'];
						$user == $to ? $result['ToVoucherBalance'] = $trade_prize['VoucherBalance'] : '';
						$log['param']['ChildId'] = 101;
						$log['param']['MoneyWeight'] = $prize_money;
						$log['param']['Desc'] = $desc;
						$log_data[] = $log;
					}
				}	
			}
		}
		
		$result['Flag'] = 100;
		$result['FlagString'] = '操作成功';
		$result['LogData'] = $log_data;
		$result['PropsName'] = $region_info['list']['props_name'];
		$result['PropsIco'] = $region_info['list']['swf_img_path'];
        $result['BigPropsIco'] = $region_info['list']['big_swf_img_path'];
		$result['PropsSize'] = $region_info['list']['props_size'];
		$result['PropsPrice'] = $region_info['list']['props_money'];
		$result['CmdPath'] = $region_info['list']['cmd_path'];
		$result['PrizeArray'] = $prize_array;
		$result['rate_array'] = $rate_array;
		$result['DayPropNum'] = $num + $this->gift_num_day($this->mongo, $to, $param['GroupId'], $parent);
		return $result;
	}

	/**
	* 交易金额操作
	* @parent interge 一级业务
	* @child interge 二级业务
	* @uin interge 用户id
	* @money floot 交易金额
	* @desc string 描述
	*/
	protected function trade($param,$child,$money,$desc,$extparam=array()){
		$param['ChildId'] = $child;
		$param['MoneyWeight'] = $money;
		$param['Desc'] = $desc;
		$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$param['GroupId']));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		$array = array('Flag'=>100,'balance'=>$rst['LastBalance']);
		if($rst['fund_type'] == 'Kmoney'){
			return $array;
		}else{
			$array['VoucherBalance'] = $rst['LastBalance'];
			unset($array['balance']);
			isset($rst['KmoneyBalance']) ? $array['balance'] =$rst['KmoneyBalance'] : '';
			return $array;
		}
	}

}
