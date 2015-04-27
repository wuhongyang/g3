<?php

/*奥点网络媒体互动用户计费管理平台软件
 *模块: 奥点网络媒体互动用户计费管理平台软件 V豆资金系统模块
 *文件: kmoney_sys.class.php
 *copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */

class kmoney_sys  implements fund_factory
{
    const PASSKEY	= '!%$^#^'; //余额检测常量，不能修改!
    const MONEY_FUND= 1; //用户账户交易  已废
    const SUB_FUND  = 2; //科目账户交易  已废
    const TRADE_FUND= 3; //科目映射交易   
    const TAX_FUND  = 4; //税收账户交易
    const USER_FUND  = 5; //用户映射交易
    
    protected $kmoney_db; //数据库操作对象
    protected $uptime = 0; //当前时间
    protected $today  = 0; //本天时间
    private static $instance;

    //获取单例对象
    public static function instance(){
        if(!is_object(self::$instance)){
            $db = domain::main()->GroupDBConn();
            self::$instance = new self($db);
        }
        return self::$instance;
    }
    
    /**
    * 构造函数
    * @param object $kmoney_db 传入数据库操作对象
    */
    private function __construct($kmoney_db){
        if(!is_object($kmoney_db)){
            throw new Exception("kmoney db error");
        }
        $this->uptime = time();
        $this->today = strtotime(date('Y-m-d 00:00:00'));
        $this->kmoney_db = $kmoney_db;
    }

    /**
    * 返回用户余额
    * @param intger $uin 用户ID
    * @return boolen|intger 返回余额如果余额为空返回NULL否则返回FALSE
    */
    public function getLastBalance($info) {
        if(empty($info['uin'])){
            return array('Flag'=>101,'FlagString'=>'uin不正确');
        }
        $sql = "SELECT last_balance FROM ".DB_NAME_KMONEY.".kmoney_balance WHERE uin={$info['uin']}";
        $balance = $this->kmoney_db->get_var($sql);
        return array('Flag'=>100,'FlagString'=>'success','LastBalance'=>$balance);
    }
    
    /*
    * 返回三级科目余额
    * @param intger $bigcaseid 一级科目ID
    * @param intger $caseid 二级科目ID
    * @param intger $parentid 三级科目ID
    * @return boolen|intger 返回余额如果余额为空返回NULL否则返回FALSE
    */
    public function getBusinessBalance($info){
		$bigcaseid = $info['bigcaseid'];
		$caseid = $info['caseid'];
		$parentid = $info['parentid'];
        if($bigcaseid >0 && $caseid>0 &&$parentid >0){
            $sql = "SELECT last_balance FROM ".DB_NAME_KMONEY.".kmoney_parent_balance WHERE bigcase_id={$bigcaseid} AND case_id={$caseid} AND parent_id={$parentid}  ORDER BY id DESC LIMIT 1";
            $balance = $this->kmoney_db->get_var($sql);
            if($balance){
                return array('Flag'=>100,'FlagString'=>'success','LastBalance'=>$balance);
            }else{
                return array('Flag'=>102,'FlagString'=>'游戏正在维护中，请稍等！');
            }
        }else{
            return array('Flag'=>101,'FlagString'=>'参数不正确');
        }
    }
    
    /**
    * V豆资金交易
    * @param array $info 交易信息
    * @return array
    */
    public function trade($info){
        $info['trade_money'] = round($info['trade_money']); //交易金额四舍五入
        /* 检查参数 */
        if($info['trade_money']<1 || $info['parent_id']<1 || $info['child_id']<1 || empty($info['operator'])){
            return array('Flag'=>101,'FlagString'=>'参数不正确');
        }
        /* 检查操作员 */
        $operator = $this->checkOperator($info['operator']);
        if($operator['Flag'] != 100){
            return $operator;
        }
        
        /* 检查业务配置 */
        $child = $this->getTradeInfo($info['parent_id'],$info['child_id']);
        if(empty($child)){
            return array('Flag'=>102,'FlagString'=>'业务不存在');
        }
        if(!$child['bigcase_id']){
            $child['bigcase_id'] = $info['bigcase_id'];
        }
        if(!$child['case_id']){
            $child['case_id'] = $info['case_id'];
        }
        if(!$child['parent_id']){
            $child['parent_id'] = $info['parent_id'];
        }
		$info['uin'] = $info['consume_uin'];
        //trade_type =1 用户支出,三级科目存入
        if($child['trade_type'] == 2 && $child['trade_property'] == self::USER_FUND){
            $info['uin'] = $info['receive_uin'];
        }
        /* 开始记录汇总 */
        
		//如果用用户交易且减币,判断
		if($child['trade_property'] == 5){
			//开始事务
			$this->action();
			$sql = "SELECT uin,last_running_id,last_balance,balance_status,last_balance_check FROM ".DB_NAME_KMONEY.".kmoney_balance WHERE uin={$info['uin']} LIMIT 1 FOR UPDATE ";
			$account = $this->kmoney_db->get_row($sql,'ASSOC'); //余额账户信息
			if(!empty($account)){
				$check = hash('md5',$info['uin'].self::PASSKEY.$account['last_balance']);//echo $check;exit;
				if($account['last_balance_check'] != $check){
					$this->rollback();
					return array('Flag'=>105,'FlagString'=>'该用户账户余额异常，无法获得资金');
				}
				if($account['balance_status'] != 1){
					$this->rollback();
					return array('Flag'=>105,'FlagString'=>'该用户账户已被冻结，无法获得资金');
				}
				if($child['trade_type'] == 1 && $account['last_balance'] < $info['trade_money'] ){
					return array('Flag'=>106,'FlagString'=>'用户余额不足');
				}
			}else{
				if($child['trade_type'] == 1){
					return array('Flag'=>106,'FlagString'=>'用户余额不足');
				}
				$account['last_balance'] = 0 ;
				$account['uin'] =  $info['uin'];
				$check = hash('md5',$info['uin'].self::PASSKEY.$account['last_balance']);
				$account['last_balance_check'] = $check;
				$sql = "INSERT INTO ".DB_NAME_KMONEY.".kmoney_balance (uin,last_balance,last_balance_check,balance_status) VALUES ({$info['uin']},0,'{$check}',1) ";
				$query = $this->kmoney_db->query($sql);
			}
			$info['account'] = $account;
		}
        $child['trade_property'] == 5 ? $info['act'] =true : $info['act'] =false;
		$result = $this->kmoneyTrade($info,$child);
		if($result['Flag'] == 100){
			$this->commit();
		}
        return $result;
    }
    
    /**
    * 一笔业务交易
    * @param array $info 交易信息
    * @return array 接口返回格式
    */
    private function kmoneyTrade($info,$child){
        //参数检查
       /* if($info['consume_uin']<0 || $info['trade_money']<0 || $info['parent_id']<1 || $info['child_id']<1 || empty($info['desc']) || empty($info['operator'])){
            $this->rollback();
            return array('Flag'=>101,'FlagString'=>'参数不正确');
        }
        */
		$info['act'] == true ?  '' : $this->action();
		
        //扩展内容
		// $tax_type = $info['tax_type']>0 ? $info['tax_type']: 0;
        // $info['extra_desc'] = '{"receive_uin":"'.$info['receive_uin'].'","channel_id":"'.$info['channel_id'].'","client":"'.$info['client'].'","tax_type":"'.$tax_type.'"}';

        //科目余额表
		$info['business_daytotal'] = $this->businessDayCount('kmoney_parent_balance',$info['trade_money'],$child);
        if($info['business_daytotal'] < 0){
            $this->rollback();
            return array('Flag'=>106,'FlagString'=>'科目余额不足');
        }
        //操作余额 用户交易
        if($child['trade_property'] == self::MONEY_FUND || $child['trade_property'] == self::USER_FUND){
            //账户平衡 日统计   只操作与用户相关,科目不记录
            $n_child['trade_type'] = $child['trade_type']==1 ? 2 : 1; 
            $trade_result = $this->updateLastBalance($info['account'],$info['trade_money'],$n_child['trade_type']);
        }else{
            $trade_result['Flag'] = 100;
            $trade_result['LastBalance'] = 0;
            $trade_result['FlagString'] = "成功";
        }
        
        if($trade_result['Flag'] == 100){
            //记录流水
            $running = "INSERT INTO ".DB_NAME_KMONEY.".`kmoney_running`(`uin`,`trade_property`,`trade_type`,`income_pay`,`trade_money`,`trade_desc`,`extra_desc`,`parent_balance`,`last_balance`,`bigcase_id`,`case_id`,`parent_id`,`child_id`,`operator_id`,`uptime`) VALUES({$info['uin']},{$child['trade_property']},{$child['trade_type']},{$child['is_income_pay']},{$info['trade_money']},'{$info['desc']}','{$info['extra_desc']}',{$info['business_daytotal']},{$trade_result['LastBalance']},{$child['bigcase_id']},{$child['case_id']},{$child['parent_id']},{$child['child_id']},'{$info['operator']}',{$this->uptime})";
            if(!$this->kmoney_db->query($running)){
                $this->rollback();
                return array('Flag'=>108,'FlagString'=>'记录流水失败');
            }
			$log_array = array('uin'=>$info['uin'],'group_id'=>$info['group_id'],'trade_property'=>$child['trade_property'],'trade_type'=>$child['trade_type'],'income_pay'=>$child['is_income_pay'],'trade_money'=>$info['trade_money'],'trade_desc'=>urlencode($info['desc']),'parent_balance'=>$info['business_daytotal'],'last_balance'=>$trade_result['LastBalance'],'bigcase_id'=>$child['bigcase_id'],'case_id'=>$child['case_id'],'parent_id'=>$child['parent_id'],'child_id'=>$child['child_id'],'uptime'=>$this->uptime,'today'=>$this->today);
			$this->log [] = $log_array;
         //   $this->commit();
        }
        $trade_result['BusinessBalance'] = $info['business_daytotal'];
		$trade_result['fund_type'] = 'Kmoney';
        return $trade_result;
    }
    
    /**
    * 业务日统计
    * @param string $table 需要更新的表
    * @param intger $money 加减金额
    * @param array $conf 业务信息
    * @return boolen
    */
    private function businessDayCount($table,$money,$conf){
        if($conf['bigcase_id'] < 0 ||$conf['case_id'] < 0 ||$conf['parent_id'] < 0){
            return -1;
        }
        $sql = "SELECT * FROM ".DB_NAME_KMONEY.".{$table} WHERE  parent_id={$conf['parent_id']} ORDER BY uptime DESC  LIMIT 1 FOR UPDATE ";
        $today = $this->kmoney_db->get_row($sql,'ASSOC');
        if(empty($today)){
            // $sql = "SELECT * FROM ".DB_NAME_KMONEY.".{$table} WHERE  ORDER BY id DESC  LIMIT 1 FOR UPDATE ";
            // $today = $this->kmoney_db->get_row($sql,'ASSOC');
            $today['deposit_money'] = 0;
            $today['pay_money'] = 0;
            $today['tax_money'] = 0;
        }
        $trade_type = $conf['trade_type'];

        // if($today['uptime'] != $this->today && $today['uptime'] < $this->today){
        // }
		if($trade_type == 1){
			$today['deposit_money'] += $money;
			$today['last_balance']  += $money;
			$SET = "last_balance = {$today['last_balance']}";
		}else{
			$today['pay_money'] += $money;
			$today['last_balance'] -= $money;
			$SET = "last_balance = {$today['last_balance']}";
		}
        if($today['last_balance'] < 0){
           return -1;
        }
		
        if($today['id'] > 0){
            $sql = "UPDATE ".DB_NAME_KMONEY.".{$table} SET last_balance= {$today['last_balance']},uptime = ".$this->uptime." WHERE  id={$today['id']}";
        }else{
            $sql = "INSERT INTO ".DB_NAME_KMONEY.".{$table} (bigcase_id,case_id,parent_id,last_balance,uptime) VALUES ('{$conf['bigcase_id']}','{$conf['case_id']}','{$conf['parent_id']}','{$today['last_balance']}',{$this->uptime})";
        }
        return $this->kmoney_db->query($sql) ? $today['last_balance'] : -1;
    }
    
    /**
    * 支出存入V豆
    * @param intger $uin 用户ID
    * @param intger $money 加减金额
    * @param intger $trade_type 加减类型1为存入2为支出
    * @return array
    */
    private function updateLastBalance($account,$money,$trade_type){
        $uin = $account['uin'];
		if($uin <= 0 || $money <= 0){
            $this->rollback();
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        
        if($trade_type==1){ //加币
            $money += $account['last_balance'];
            $flagstring = '存入成功';
        }elseif($trade_type==2){ //扣币
            if($account['last_balance'] < $money){
                $this->rollback();
                return array('Flag'=>105,'FlagString'=>'余额不足');
            }
            $money = $account['last_balance'] - $money;
            $flagstring = '支付成功';
        }else{
            $this->rollback();
            return array('Flag'=>103,'FlagString'=>'操作错误');
        }
        //更新余额
        $check = hash('md5',$uin.self::PASSKEY.$money);
        $last_running = $account['last_running_id'] + 1;
        $sql = "UPDATE ".DB_NAME_KMONEY.".kmoney_balance SET last_balance={$money} ,last_balance_check='{$check}',uptime={$this->uptime} WHERE uin = {$uin} ";
        if(!$this->kmoney_db->query($sql)){
            $this->rollback();
            return array('Flag'=>103,'FlagString'=>'存入失败');
        }
        return array('Flag'=>100,'FlagString'=>$flagstring,'LastBalance'=>$money);
    }

    /*
    * 获取业务信息
    * @param intger $bigcase_id 一级科目
    * @param intger $case_id 二级科目
    * @param intger $parent_id 三级科目
    * @param intger $child_id 四级科目
    * @return boolen
    */
    private function getTradeInfo($parent_id, $child_id) {
        if(empty($parent_id) || empty($child_id)){
            return false;
        }
        $sql = "SELECT * FROM ".DB_NAME_KMONEY.".config WHERE parent_id={$parent_id} AND child_id={$child_id} AND child_status = 1";
        if($child_id > 900){
            $sql = "SELECT * FROM ".DB_NAME_KMONEY.".common_child_config WHERE  child_id={$child_id}";
        }
        return $this->kmoney_db->get_row($sql,'ASSOC');
    }
    
    private function checkOperator($operator){
        $opt = $this->kmoney_db->get_row("SELECT * FROM ".DB_NAME_KMONEY.".operator WHERE operator_key='{$operator}' AND operator_status=1 LIMIT 1");
        if(empty($opt)){
            return array('Flag'=>101,'FlagString'=>'操作员不正确');
        }
        if(!empty($opt['operator_ip'])){
            $ip = (array)explode(',',$opt['operator_ip']);
            if(!in_array(get_ip(),$ip)){
                if(empty($opt)){
                    return array('Flag'=>102,'FlagString'=>'操作员未授权');
                }
            }
        }
        return array('Flag'=>100,'Result'=>array('operator'=>$operator,'ip'=>get_ip()));
    }
	
	public function addlog(){
		if(!empty($this->log)){
			$log = json_encode($this->log);
			$sql = 'INSERT INTO kkyoo_log.voucher_log (voucher_log) VALUES (\''.$log.'\')';
			$this->kmoney_db->query($sql);
			unset($this->log);
		}
	}
	
	/**
    * 启用事务
    */
	public function action(){
		$this->kmoney_db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
        $this->kmoney_db->start_transaction();
	}
	
    /**
    * 提交事务
    */
    public function commit(){
        $this->kmoney_db->commit();
    }

    /**
    * 回滚事务
    */
    public function rollback(){
        $this->kmoney_db->rollback();
    }
}