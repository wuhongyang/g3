<?php

/*奥点网络媒体互动用户计费管理平台软件
 *模块: 奥点网络媒体互动用户计费管理平台软件 V豆资金系统模块
 *文件: kwealth_sys.class.php
 *copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */

class kwealth_sys implements fund_factory
{
    const PASSKEY	= '!%$^#^'; //余额检测常量，不能修改!
    const TRADE_FUND= 3; //科目映射交易   
    const TAX_FUND  = 4; //税收账户交易
    const USER_FUND  = 5; //用户映射交易
    
    protected $kwealth_db; //数据库操作对象
    protected $uptime = 0; //当前时间
    protected $today  = 0; //本天时间
    private static $instance;

    //获取单例对象
    public static function instance(){
        if(!is_object(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
    * 构造函数
    * @param object $kwealth_db 传入数据库操作对象
    */
    private function __construct(){
		$this->uptime = time();
        $this->today = strtotime(date('Y-m-d 00:00:00'));
        $this->kwealth_db = domain::main()->GroupDBConn();
        $this->plat_db = db::connect(config('database','voucher'));
    }
	
    /**
    * 返回用户余额
    * @param intger $uin 用户ID
    * @return boolen|intger 返回余额如果余额为空返回NULL否则返回FALSE
    */
    public function getLastBalance($info) {
        if(empty($info['uin']) || empty($info['group_id'])){
            return array('Flag'=>101,'FlagString'=>'参数不正确');
        }
        $sql = "SELECT last_balance FROM ".DB_NAME_KWEALTH.".kwealth_balance WHERE uin={$info['uin']} AND group_id = {$info['group_id']} LIMIT 1 FOR UPDATE";
        $balance = $this->kwealth_db->get_var($sql);
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
		$groupid = $info['group_id'];
        if($bigcaseid >0 && $caseid>0 &&$parentid >0 && $groupid >0){
            $sql = "SELECT last_balance FROM ".DB_NAME_KWEALTH.".kwealth_parent_balance WHERE group_id = {$groupid} AND parent_id={$parentid}  ORDER BY uptime DESC LIMIT 1";
            $balance = $this->kwealth_db->get_var($sql);
            if($balance){
                return array('Flag'=>100,'FlagString'=>'success','LastBalance'=>$balance);
            }else{
                return array('Flag'=>102,'FlagString'=>'游戏正在维护中，请稍等！');
            }
        }else{
            return array('Flag'=>101,'FlagString'=>'参数不正确');
        }
    }
    
	private function checkinfo($info){
		$info['trade_money'] = round($info['trade_money']); //交易金额四舍五入
        /* 检查参数 */
        if($info['trade_money']<1 || $info['parent_id']<1 || $info['child_id']<1 || $info['group_id']<1 || empty($info['operator']) ){
            return array('Flag'=>101,'FlagString'=>'参数不正确');
        }
        /* 检查操作员 */
        $operator = $this->checkOperator($info['operator']);
        if($operator['Flag'] != 100){
            return $operator;
        }
        /* 检查业务配置 */
        $child = $this->getTradeInfo($info['parent_id'],$info['child_id']);
        if($child['Flag'] !== 100){
            return array('Flag'=>102,'FlagString'=>'业务不存在'.$info['parent_id'].$info['child_id']);
        }
		$child = $child['Info'];
        if(!$child['bigcase_id']){
            $child['bigcase_id'] = $info['bigcase_id'];
        }
        if(!$child['case_id']){
            $child['case_id'] = $info['case_id'];
        }
        if(!$child['parent_id']){
            $child['parent_id'] = $info['parent_id'];
        }
		return array('Flag'=>100,'Child'=>$child);
	}
	
    /**
    * V豆资金交易
    * @param array $info 交易信息
    * @return array
    */
    public function trade($info){
        $checkinfo = $this->checkinfo($info);
		if($checkinfo['Flag'] != 100){
			return $checkinfo;
		}
        $info['uin'] = $info['consume_uin'];
		$child = $checkinfo['Child'];
        //trade_type =1 用户支出,三级科目存入
        if($child['trade_type'] == 2 && $child['trade_property'] == self::USER_FUND){
            $info['uin'] = $info['receive_uin'];
        }
        /* 开始记录汇总 */
        
        //开始事务
		//如果用用户交易且减币,判断
		if($child['trade_property'] == 5){
			$this->action();
			$sql = "SELECT uin,last_running_id,last_balance,balance_status,last_balance_check FROM ".DB_NAME_KWEALTH.".kwealth_balance WHERE uin={$info['uin']} AND group_id={$info['group_id']} LIMIT 1 FOR UPDATE ";
			$account = $this->kwealth_db->get_row($sql,'ASSOC'); //余额账户信息
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
				$sql = "INSERT INTO ".DB_NAME_KWEALTH.".kwealth_balance (uin,group_id,last_balance,last_balance_check,balance_status) VALUES ({$info['uin']},{$info['group_id']},0,'{$check}',1) ";
				$query = $this->kwealth_db->query($sql);
			}
			$info['account'] = $account;
		}
		
		$child['trade_property'] == 5 ? $info['act'] =true : $info['act'] =false;
		$result = $this->VoucherTrade($info,$child);

		if($result['Flag'] == 100){
			$this->commit();
		}
        return $result;
    }
    
	private function VoucherTrade($info,$child){
        $info['act'] == true ?  '' : $this->action();
		
		//科目余额表
		$info['business_daytotal'] = $this->businessDayCount(DB_NAME_KWEALTH,'kwealth_parent_balance',$info['trade_money'],$child,$info['group_id']);
        if($info['business_daytotal'] < 0){
            $this->rollback();
            return array('Flag'=>106,'FlagString'=>'科目余额不足');
        }
		
        //用户余额表 用户交易
        if($child['trade_property'] == self::USER_FUND){
            //账户平衡 日统计   只操作与用户相关,科目不记录 101 1=>2
            $n_child['trade_type'] = $child['trade_type']==1 ? 2 : 1; 
            $trade_result = $this->updateLastBalance($info['account'],$info['group_id'],$info['trade_money'],$n_child['trade_type']);
        }else{
			$trade_result['Flag'] = 100;
            $trade_result['LastBalance'] = 0;
            $trade_result['FlagString'] = "成功";
		}
		if($trade_result['Flag'] == 100){
			//科目流水表
			$running = "INSERT INTO ".DB_NAME_KWEALTH.".`kwealth_running`(`uin`,`group_id`,`trade_property`,`trade_type`,`income_pay`,`trade_money`,`trade_desc`,`extra_desc`,`parent_balance`,`last_balance`,`bigcase_id`,`case_id`,`parent_id`,`child_id`,`operator_id`,`uptime`) VALUES({$info['uin']},{$info['group_id']},{$child['trade_property']},{$child['trade_type']},{$child['is_income_pay']},{$info['trade_money']},'{$info['desc']}','{$info['extra_desc']}',{$info['business_daytotal']},{$trade_result['LastBalance']},{$child['bigcase_id']},{$child['case_id']},{$child['parent_id']},{$child['child_id']},'{$info['operator']}',{$this->uptime})";
			if(!$this->kwealth_db->query($running)){
				$this->rollback();
				return array('Flag'=>108,'FlagString'=>'记录流水失败');
			}
		}
		$log_array = array('uin'=>$info['uin'],'group_id'=>$info['group_id'],'trade_property'=>$child['trade_property'],'trade_type'=>$child['trade_type'],'fund_type'=>$child['fund_type'],'income_pay'=>$child['is_income_pay'],'trade_money'=>$info['trade_money'],'trade_desc'=>urlencode($info['desc']),'parent_balance'=>$info['business_daytotal'],'last_balance'=>$trade_result['LastBalance'],'bigcase_id'=>$child['bigcase_id'],'case_id'=>$child['case_id'],'parent_id'=>$child['parent_id'],'child_id'=>$child['child_id'],'uptime'=>$this->uptime,'today'=>$this->today);
		$trade_result['BusinessBalance'] = $info['business_daytotal'];
		$trade_result['fund_type'] = 'Voucher';
		$this->log [] = $log_array;
		
        return $trade_result;
	}

	public function TaxTrade($info){
		$checkinfo = $this->checkinfo($info);
		if($checkinfo['Flag'] != 100){
			return $checkinfo;
		}
		$child = $checkinfo['Child'];
		$this->action();
		
		$info['business_daytotal'] = $this->businessDayCount(DB_NAME_TAX,'tax_parent_balance',$info['trade_money'],$child,$info['group_id']);
        if($info['business_daytotal'] < 0){
            $this->rollback();
            return array('Flag'=>106,'FlagString'=>'科目余额不足');
        }
		
		$running = "INSERT INTO ".DB_NAME_TAX.".`tax_running`(`group_id`,`trade_property`,`trade_type`,`income_pay`,`trade_money`,`trade_desc`,`extra_desc`,`parent_balance`,`bigcase_id`,`case_id`,`parent_id`,`child_id`,`operator_id`,`uptime`) VALUES({$info['group_id']},{$child['trade_property']},{$child['trade_type']},{$child['is_income_pay']},{$info['trade_money']},'{$info['desc']}','{$info['extra_desc']}',{$info['business_daytotal']},{$child['bigcase_id']},{$child['case_id']},{$child['parent_id']},{$child['child_id']},'{$info['operator']}',{$this->uptime})";
		if(!$this->kwealth_db->query($running)){
			$this->rollback();
			return array('Flag'=>108,'FlagString'=>'记录流水失败');
		}
		$this->commit();
		$log_array = array('group_id'=>$info['group_id'],'trade_property'=>$child['trade_property'],'trade_type'=>$child['trade_type'],'fund_type'=>$child['fund_type'],'income_pay'=>$child['is_income_pay'],'trade_money'=>$info['trade_money'],'trade_desc'=>urlencode($info['desc']),'parent_balance'=>$info['business_daytotal'],'bigcase_id'=>$child['bigcase_id'],'case_id'=>$child['case_id'],'parent_id'=>$child['parent_id'],'child_id'=>$child['child_id'],'uptime'=>$this->uptime,'today'=>$this->today);
		$this->log [] = $log_array;
		return array('Flag'=>100);
	}

 /**
    * 业务日统计
    * @param string $table 需要更新的表
    * @param intger $money 加减金额
    * @param array $conf 业务信息
    * @param intger $group_id 站ID
    * @return boolen
    */
    private function businessDayCount($database,$table,$money,$conf,$group_id){
        $sql = "SELECT * FROM ".$database.".{$table}  WHERE group_id = {$group_id}  AND parent_id={$conf['parent_id']} ORDER BY uptime DESC  LIMIT 1 FOR UPDATE";
        $today = $this->kwealth_db->get_row($sql,'ASSOC');
        if(empty($today)){
           // $sql = "SELECT * FROM ".$database.".{$table} WHERE group_id = {$group_id} AND parent_id={$conf['parent_id']} ORDER BY uptime DESC  LIMIT 1 FOR UPDATE";
           // $today = $this->voucher_db->get_row($sql,'ASSOC');
            $today['deposit_money'] = 0 ;
            $today['pay_money'] = 0 ;
            $today['tax_money'] = 0 ;
        }
        // if($today['uptime'] != $this->today && $today['uptime'] < $this->today){
        // }
        $trade_type = $conf['trade_type'];
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
            $sql = "UPDATE ".$database.".{$table} SET  last_balance= {$today['last_balance']}, uptime = ".$this->uptime."  WHERE id={$today['id']}";
        }else{
            $sql = "INSERT INTO ".$database.".{$table} (group_id,bigcase_id,case_id,parent_id,last_balance,uptime) VALUES ({$group_id},'{$conf['bigcase_id']}','{$conf['case_id']}','{$conf['parent_id']}','{$today['last_balance']}',{$this->uptime})";
        }
		$query = $this->kwealth_db->query($sql);
        return  $query? $today['last_balance'] : -1;
    }
    
    /**
    * 支出存入V豆
    * @param intger $uin 用户ID
    * @param intger $group_id 站ID
    * @param intger $money 加减金额
    * @param intger $trade_type 加减类型1为存入2为支出
    * @return array
    */
    private function updateLastBalance($account,$group_id,$money,$trade_type){
        $uin = $account['uin'];
		if($uin <= 0 || $money <= 0 || $group_id<=0){
            $this->rollback();
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        if($trade_type==1){ //加币
            $money += $account['last_balance'];
            $flagstring = '存入成功';
        }elseif($trade_type==2){ //扣币
            if($account['last_balance'] < $money){
                $this->rollback();
                return array('Flag'=>105,'FlagString'=>$account['last_balance'].'用户余额不足');
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
		
		$sql = "UPDATE ".DB_NAME_KWEALTH.".kwealth_balance SET last_balance={$money} ,last_balance_check='{$check}',uptime={$this->uptime} WHERE uin = {$uin} AND group_id = {$group_id} ";
        if(!$this->kwealth_db->query($sql)){
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
    public function getTradeInfo($parent_id, $child_id) {
        if(empty($parent_id) || empty($child_id)){
            return array('Flag'=>101,'FlagString'=>'参数有误');
        }
        $sql = "SELECT * FROM ".DB_NAME_KWEALTH_PLAT.".config WHERE parent_id={$parent_id} AND child_id={$child_id} AND child_status = 1";
        if($child_id > 900){
            $sql = "SELECT * FROM ".DB_NAME_KWEALTH_PLAT.".common_child_config WHERE  child_id={$child_id}";
        }
		$row = $this->plat_db->get_row($sql,'ASSOC');
        return empty($row) ? array('Flag'=>101,'FlagString'=>'业务不存在') : array('Flag'=>100,'FlagString'=>'成功','Info'=>$row);
    }
    
    private function checkOperator($operator){
        $opt = $this->plat_db->get_row("SELECT * FROM ".DB_NAME_KWEALTH_PLAT.".operator WHERE operator_key='{$operator}' AND operator_status=1 LIMIT 1");
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
			$query = $this->kwealth_db->query($sql);
			unset($this->log);
		}
	}
	
	/**
    * 启用事务
    */
	public function action(){
		$this->kwealth_db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
        $this->kwealth_db->start_transaction();
	}
	
    /**
    * 提交事务
    */
    public function commit(){
        $this->kwealth_db->commit();
    }

    /**
    * 回滚事务
    */
    public function rollback(){
        $this->kwealth_db->rollback();
    }

}