<?php
abstract class AbstractClass{

	function __construct(){
	
	}
	
	/**
	 *[checkGift 获取礼物信息]
	 *@param [integer] $bigcase [一级业务]
	 *@param [integer] $case [二级业务]
	 *@param [integer] $parent [三级业务]
	 *@param [string] $cmd [道具前缀]
	 *@return [array]	[配置信息 礼物信息]
	 *
	*/
	protected function checkGift($bigcase,$case,$parent,$cmd=''){
		$sql = "SELECT props_money,props_name AS parent_name,actor_tax,tax_percent FROM ".DB_NAME_TPL.".tbl_function_props WHERE big_case_id={$bigcase} AND case_id={$case} AND parent_id={$parent} AND props_status=1";
		$info = $this->db->get_row($sql,'ASSOC');
		if(empty($info)){
			return array('Flag'=>101,'FlagString'=>'礼物不存在');
		}
		if(!empty($cmd)){
			$vars = $this->db->get_var("SELECT `value` FROM ".DB_NAME_PROPS.".props_config WHERE `key`='{$cmd}_config' AND `status` = 1 LIMIT 1",'ASSOC');
			if(empty($vars)){
				return array('Flag'=>101,'FlagString'=>'礼物不存在');
			}	
		}
		
		return array('Flag'=>100,'FlagString'=>'成功','config'=>$vars,'info'=>$info);
	}
	
	/**
	 * [trade 交易金额操作]
	 * @param  [array] $param [基础参数]
	 * @param  [integer] $child [二级业务]
	 * @param  [floot] $money [交易金额]
	 * @param  [string] $desc  [描述]
	 * @return [array]        [余额]
	 */
	protected function trade($param,$child,$money,$desc,$extparam=array()){
		$param['ChildId'] = $child;
		$param['MoneyWeight'] = $money;
		$param['Desc'] = $desc;
		$log = getLogData($param,$extparam);
		$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$param['GroupId']));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		$array = array('Flag'=>100,'balance'=>$rst['LastBalance'],'log'=>$log);
		if($rst['fund_type'] == 'Kmoney'){
			return $array;
		}else{
			$array['VoucherBalance'] = $rst['LastBalance'];
			unset($array['balance']);
			isset($rst['KmoneyBalance']) ? $array['balance'] =$rst['KmoneyBalance'] : '';
		}
		return $array;
	}
	
	/**
	 * [winMoney 中奖计算]
	 * @param  [array] $param  [基础参数]
	 * @param  [floot] $config [中奖基数]
	 * @param  [floot] $r_rate [中奖概率]
	 * @param  [floot] $p_rate [奖池比例]
	 * @param  [floot] $u_rate [接收比例]
	 * @param  [floot] $money  [礼物价格]
	 * @param  [integer] $num    [礼物数量]
	 * @return [array]         [中奖条数]
	 */
	protected function winMoney($config,$r_rate,$p_rate,$money,$num){
		$prize_array = array();
		$config_array = json_decode($config);
		$config_count = count($config_array);
		$wins = $money * $p_rate*0.01 *$r_rate*0.01;//奖池金额
		$lv = $wins/$money ; // 60%*k
		$win_rand2 = $lv/$config_count;
		$sum =1;
		$j=0;
		$prize_money = 0;
		$total_money = 0 ;
		foreach($config_array as $key=>$value){
			$sum = $sum * $value;
		}
		foreach($config_array as $key=>$value){
			$last = $array[$config_array[$key-1]];
			$array[$value] = $last+( $sum/$value*$win_rand2);
		}
		for($i=0;$i<$num;$i++){
			$rand2 = rand(1,$sum);
			$rate = 0;//
			foreach($array as $kk=>$vv){
				if($rand2 <= $vv){
					$j++;
					$rate = $kk;
					break;
				}
			}
			$prize_money = $money * $rate;
			if($prize_money > 0){//用户中奖了
				$total_money += $prize_money;
				$prize_array[] = $prize_money;
				$rate_array[] = $rate;
			}
		}
		return array($prize_array,$j,$total_money,$rate_array);
	}
    
    protected function gift_num_day($mongo, $uin, $group_id, $parent_id){
        $rule     = new rule($mongo);
		$gift_num = $rule->getRuleRank($uin,0,$group_id,$parent_id,36,1,array('day'));
        return $gift_num['day'][0]['Weight'];
    } 
}
