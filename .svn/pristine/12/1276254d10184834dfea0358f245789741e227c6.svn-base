<?php

/*奥点网络媒体互动用户计费管理平台软件
 *模块: 奥点网络媒体互动用户计费管理平台软件 V豆资金系统模块
 *文件: kwealth.class.php
 *copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */

class kwealth_sys
{
    const PASSKEY	= '!%$^#^'; //余额检测常量，不能修改!
    const MONEY_FUND= 1; //用户账户交易  已废
    const SUB_FUND  = 2; //科目账户交易  已废
    const TRADE_FUND= 3; //科目映射交易   
    const TAX_FUND  = 4; //税收账户交易
    const USER_FUND  = 5; //用户映射交易
    
    protected $kwealth_db; //数据库操作对象
    protected $uptime = 0; //当前时间
    protected $today  = 0; //本天时间
    private   static $instance;

    //获取单例对象
    function instance(){
        if(!is_object(self::$instance)){
            $db	= db::connect(config('database','kwealth'));
            self::$instance = new self($db);
        }
        return self::$instance;
    }
    
    /**
    * 构造函数
    * @param object $kwealth_db 传入数据库操作对象
    */
    private function __construct($kwealth_db){
        if(!is_object($kwealth_db)){
            throw new Exception("kwealth db error");
        }
        $this->uptime = time();
        $this->today = strtotime(date('Y-m-d 00:00:00'));
        $this->kwealth_db = $kwealth_db;
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

    /**
    * 返回用户余额
    * @param intger $uin 用户ID
    * @return boolen|intger 返回余额如果余额为空返回NULL否则返回FALSE
    */
    public function getLastBalance($uin) {
        if(empty($uin)){
            return array('Flag'=>101,'FlagString'=>'uin不正确');
        }
        $sql = "SELECT last_balance FROM ".DB_NAME_KWEALTH.".kwealth_balance WHERE uin={$uin}";
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
    public function getBusinessBalance($bigcaseid,$caseid,$parentid){
        if($bigcaseid >0 && $caseid>0 &&$parentid >0){
            $sql = "SELECT last_balance FROM ".DB_NAME_KWEALTH.".kwealth_business_day_total WHERE bigcase_id={$bigcaseid} AND case_id={$caseid} AND parent_id={$parentid} ORDER BY id DESC LIMIT 1";
            $balance = $this->kwealth_db->get_var($sql);
            return array('Flag'=>100,'FlagString'=>'success','LastBalance'=>$balance);
        }else{
            return array('Flag'=>101,'FlagString'=>'参数不正确 #getBusinessBalance');
        }
    }

    /**
    * V宝资金交易
    * @param array $info 交易信息
    * @return array
    */
    public function trade($info){
        //$info['trade_money'] = round($info['trade_money']); //交易金额四舍五入
        /* 检查参数 */
        if($info['trade_money']<0.01 || $info['parent_id']<1 || $info['child_id']<1  || empty($info['operator'])){
            return array('Flag'=>101,'FlagString'=>'参数不正确 #trade');
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
        /* 开始记录汇总 */
        
        //开始事务
        $this->action();
        
        //净账平衡 存入/支出汇总
        if($child['is_income_pay']==1){
            if(!$this->KwealthParentCount('kwealth_deposit_pay',$info['trade_money'],$child['trade_type'])){
                $this->rollback();
                return array('Flag'=>104,'FlagString'=>'净收支日统计失败');
            }
        }
        
        //三级科目日汇总/业务余额日汇总 业务统计
        $info['business_daytotal'] = $this->businessDayCount('kwealth_business_day_total',$info['trade_money'],$child);
        if($info['business_daytotal'] < 0){
            $this->rollback();
            return array('Flag'=>105,'FlagString'=>'余额不足');
        }
        //四级科目交易汇总
        //if(! $this->childDayCount('kwealth_child_day_total',$info['trade_money'],$child))
        //	return array('Flag'=>106,'FlagString'=>'四级科目统计失败');
        
        //系统总账日汇总
        if(!$this->KwealthParentCount('kwealth_parent_total',$info['trade_money'],$child['trade_type'])){
            $this->rollback();
            return array('Flag'=>103,'FlagString'=>'系统总账统计失败');
        }
        $trade_result = $this->kwealthTrade($info,$child);
        return $trade_result;
    }
    
    /**
    * 一笔业务交易
    * @param array $info 交易信息
    * @return array 接口返回格式
    */
    private function kwealthTrade($info,$child){
        //参数检查
        if($info['consume_uin']<0 || $info['receive_uin']<0 || $info['trade_money']<0.01 || $info['parent_id']<1 || $info['child_id']<1 || empty($info['desc']) || empty($info['operator'])){
            $this->rollback();
            return array('Flag'=>101,'FlagString'=>'参数不正确 #kwealthTrade');
        }
        
        //扩展内容
        $info['extra_desc'] = '{"receive_uin":"'.$info['receive_uin'].'","channel_id":"'.$info['channel_id'].'","client":"'.$info['client'].'"}';
        
        $info['uin'] = $info['consume_uin'];
        //trade_type =1 用户支出,三级科目存入
        if($child['trade_type'] == 1 && $child['trade_property'] == self::USER_FUND){
            $info['uin'] = $info['consume_uin'];
        }else  if($child['trade_type'] == 2 && $child['trade_property'] == self::USER_FUND){
            $info['uin'] = $info['receive_uin'];
        }
        
        //操作余额 用户交易
        if($child['trade_property'] == self::MONEY_FUND || $child['trade_property'] == self::USER_FUND){
            //账户平衡 日统计   只操作与用户相关,科目不记录
            $n_child['trade_type'] = $child['trade_type']==1 ? 2 : 1; 
            if(!$this->KwealthParentCount('kwealth_day_total',$info['trade_money'],$n_child['trade_type'])){
                $this->rollback();
                return array('Flag'=>109,'FlagString'=>'日统计失败');
            }
            $trade_result = $this->updateLastBalance($info['uin'],$info['trade_money'],$n_child['trade_type']);
        }else{
            $trade_result['Flag'] = 100;
            $trade_result['LastBalance'] = 0;
            $trade_result['FlagString'] = "成功";
        }
        
        if($trade_result['Flag'] == 100){
            //记录流水
            if(empty($info['business_daytotal'])){
                $info['business_daytotal']= intval($info['business_daytotal']);
            }
            $running = "INSERT INTO ".DB_NAME_KWEALTH.".`kwealth_running`(`uin`,`trade_property`,`trade_type`,`income_pay`,`trade_money`,`trade_desc`,`extra_desc`,`parent_balance`,`last_balance`,`bigcase_id`,`case_id`,`parent_id`,`child_id`,`operator_id`,`uptime`) VALUES({$info['uin']},{$child['trade_property']},{$child['trade_type']},{$child['is_income_pay']},{$info['trade_money']},'{$info['desc']}','{$info['extra_desc']}',{$info['business_daytotal']},{$trade_result['LastBalance']},{$child['bigcase_id']},{$child['case_id']},{$child['parent_id']},{$child['child_id']},'{$info['operator']}',{$this->uptime})";
            if(!$this->kwealth_db->query($running)){
                $this->rollback();
                return array('Flag'=>108,'FlagString'=>'记录流水失败');
            }
            $this->commit();
        }
        $trade_result['BusinessBalance'] = $info['business_daytotal'];
        return $trade_result;
    }
    
    private function KwealthParentCount($table , $money , $trade_type){
        $sql = "SELECT * FROM ".DB_NAME_KWEALTH.".{$table} WHERE uptime = ".$this->today."  LIMIT 1 FOR UPDATE ";
        $today = $this->kwealth_db->get_row($sql , "ASSOC");
        if(empty($today)){
            $sql = "SELECT * FROM ".DB_NAME_KWEALTH.".{$table}  ORDER BY uptime DESC  LIMIT 1 FOR UPDATE ";
            $today = $this->kwealth_db->get_row($sql , "ASSOC");
        }
        if($today['uptime'] != $this->today && $today['uptime'] < $this->today){
            $today['deposit_money'] = 0 ;
            $today['pay_money'] = 0 ;
        }
        //1存入 2支出
        if($trade_type==1){
            $today['deposit_money'] += $money;
            $today['last_balance']  += $money;
        }elseif($trade_type==2){
            $today['pay_money']    += $money;
            $today['last_balance'] -= $money;
        }
        if($today['id'] > 0 && $today['uptime'] == $this->today){
            $sql = "UPDATE ".DB_NAME_KWEALTH.".{$table} SET pay_money = {$today['pay_money']} , deposit_money={$today['deposit_money']} , last_balance= {$today['last_balance']} ,uptime = {$this->today} WHERE id = {$today['id']}";	
        }else{
            $sql = "INSERT INTO ".DB_NAME_KWEALTH.".{$table} (pay_money,deposit_money,last_balance,uptime)VALUES('{$today['pay_money']}','{$today['deposit_money']}','{$today['last_balance']}',{$this->today})";
        }
        return $this->kwealth_db->query($sql);
    }
    
    /**
    * 业务日统计
    * @param string $table 需要更新的表
    * @param intger $money 加减金额
    * @param array $conf 业务信息
    * @return boolen
    */
    private function childDayCount($table,$money,$conf){
        $sql = "SELECT id ,trade_money FROM ".DB_NAME_KWEALTH.".{$table} WHERE bigcase_id={$conf['bigcase_id']} AND case_id={$conf['case_id']} AND parent_id={$conf['parent_id']} AND child_id = {$conf['child_id']} AND uptime={$this->today} LIMIT 1";
        $today = $this->kwealth_db->get_row($sql,'ASSOC');
        if(empty($today)){
            $today['trade_money'] = $money;
            $sql = "INSERT INTO ".DB_NAME_KWEALTH.".{$table} (bigcase_id,case_id,parent_id,child_id,trade_money,uptime)
                VALUES('{$conf['bigcase_id']}','{$conf['case_id']}','{$conf['parent_id']}','{$conf['child_id']}','{$today['trade_money']}',{$this->today})";
        }else{
            $today['trade_money'] += $money;
            $sql = "UPDATE ".DB_NAME_KWEALTH.".{$table} SET trade_money = '{$today['trade_money']}' WHERE id = {$today['id']}";
        }
        return $this->kwealth_db->query($sql);
    }
    
    /**
    * 业务日统计
    * @param string $table 需要更新的表
    * @param intger $money 加减金额
    * @param array $conf 业务信息
    * @return boolen
    */
    private function businessDayCount($table,$money,$conf){
        if($conf['bigcase_id'] < 0 ||$conf['case_id'] < 0 ||$conf['parent_id'] < 0 ){
            return -1;
        }
        $sql = "SELECT * FROM ".DB_NAME_KWEALTH.".{$table} WHERE bigcase_id={$conf['bigcase_id']} AND case_id={$conf['case_id']} AND parent_id={$conf['parent_id']}  AND uptime = ".$this->today." LIMIT 1 FOR UPDATE";
        $today = $this->kwealth_db->get_row($sql,'ASSOC');
        if(empty($today)){
            $sql = "SELECT * FROM ".DB_NAME_KWEALTH.".{$table} WHERE bigcase_id={$conf['bigcase_id']} AND case_id={$conf['case_id']} AND parent_id={$conf['parent_id']} ORDER BY id DESC  LIMIT 1 FOR UPDATE ";
            $today = $this->kwealth_db->get_row($sql,'ASSOC');
        }
        $trade_type = $conf['trade_type'];
        if($conf['trade_property'] == self::USER_FUND && $conf['trade_property'] == self::TRADE_FUND){
            return $today['last_balance'] ;
        }
        if($today['uptime'] != $this->today && $today['uptime'] < $this->today){
            $today['deposit_money'] = 0;
            $today['pay_money'] = 0;
            $today['tax_money'] = 0;
        }
        
        if($conf['is_income_pay'] == 1){ //净账户
            if($conf['trade_property'] == self::MONEY_FUND){ //V豆账户
                $today['deposit_money'] += $money;
                $today['pay_money'] += $money;
                $SET = "pay_money = {$today['pay_money']},deposit_money = {$today['deposit_money']}";
            }elseif($conf['trade_property'] == self::SUB_FUND || $conf['trade_property'] == self::TRADE_FUND){ //科目交易/业务交易
                if($trade_type == 2){
                    $today['pay_money'] += $money;
                    $today['last_balance'] -= $money;
                    $SET = "pay_money = {$today['pay_money']},last_balance = {$today['last_balance']}";
                }else{
                    $today['deposit_money'] += $money;
                    $today['last_balance']  += $money;
                    $SET = "deposit_money = {$today['deposit_money']},last_balance = {$today['last_balance']}";
                }
            }else{ //税金交易
                $today['tax_money'] += $money;
                $today['last_balance'] -= $money;
                $SET = "tax_money = {$today['tax_money']},last_balance = {$today['last_balance']}";
            }
        }else{ //非净账户
            if($trade_type == 1){
                $today['deposit_money'] += $money;
                $today['last_balance']  += $money;
                $SET = "deposit_money = {$today['deposit_money']},last_balance = {$today['last_balance']}";
            }else{
                $today['pay_money'] += $money;
                $today['last_balance'] -= $money;
                $SET = "pay_money = {$today['pay_money']},last_balance = {$today['last_balance']}";
            }
        }
        
        if($today['last_balance'] < 0){
            return -1;
        }
        if($today['id'] > 0 && $today['uptime'] == $this->today){
            $sql = "UPDATE ".DB_NAME_KWEALTH.".{$table} SET {$SET} WHERE id = {$today['id']}";
        }else{
            $sql = "INSERT INTO ".DB_NAME_KWEALTH.".{$table} (bigcase_id,case_id,parent_id,pay_money,deposit_money,tax_money,last_balance,uptime) VALUES ('{$conf['bigcase_id']}','{$conf['case_id']}','{$conf['parent_id']}','{$today['pay_money']}','{$today['deposit_money']}','{$today['tax_money']}','{$today['last_balance']}',{$this->today})";
        }
        return $this->kwealth_db->query($sql) ? $today['last_balance'] : -1;
    }
    
    /**
    * 支出存入V豆
    * @param intger $uin 用户ID
    * @param intger $money 加减金额
    * @param intger $trade_type 加减类型1为存入2为支出
    * @return array
    */
    private function updateLastBalance($uin,$money,$trade_type){
        if($uin <= 0 || $money <= 0){
            $this->rollback();
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        $sql = "SELECT last_running_id,last_balance,balance_status FROM ".DB_NAME_KWEALTH.".kwealth_balance WHERE uin={$uin} LIMIT 1 FOR UPDATE";
        $account = $this->kwealth_db->get_row($sql,'ASSOC'); //余额账户信息
        if(!empty($account)){ //检查账户余额
            $check = hash('md5',$uin.self::PASSKEY.(float)$account['last_balance']);
            $sql = "SELECT last_balance FROM ".DB_NAME_KWEALTH.".kwealth_balance WHERE uin={$uin} AND last_balance_check='{$check}' LIMIT 1 FOR UPDATE";
            $check_account = $this->kwealth_db->get_var($sql);
            if($account['last_balance'] != $check_account){
                $this->rollback();
                return array('Flag'=>102,'FlagString'=>'该用户账户余额异常，无法获得资金');
            }
            if($account['balance_status'] != 1){
                $this->rollback();
                return array('Flag'=>102,'FlagString'=>'该用户账户已被冻结，无法获得资金');
            }
        }
        if($trade_type==1){ //加币
            $money += $account['last_balance'];
            $flagstring = '存入成功';
        }elseif($trade_type==2){ //扣币
            if($account['last_balance'] < $money){
                $this->rollback();
                return array('Flag'=>103,'FlagString'=>'余额不足');
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
        $sql = "REPLACE INTO ".DB_NAME_KWEALTH.".kwealth_balance(uin,last_running_id,last_balance,last_balance_check,balance_status,uptime)
                VALUES ({$uin},{$last_running},{$money},'{$check}',1,{$this->uptime})";
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
    private function getTradeInfo($parent_id, $child_id) {
        if(empty($parent_id) || empty($child_id)){
            return false;
        }
        $sql = "SELECT * FROM ".DB_NAME_KWEALTH.".config WHERE parent_id={$parent_id} AND child_id={$child_id}";
        if($child_id > 900){
            $sql = "SELECT * FROM ".DB_NAME_KWEALTH.".common_child_config WHERE  child_id={$child_id}";
        }
        return $this->kwealth_db->get_row($sql,'ASSOC');
    }
    
    private function checkOperator($operator){
        $opt = $this->kwealth_db->get_row("SELECT * FROM ".DB_NAME_KWEALTH.".operator WHERE operator_key='{$operator}' AND operator_status=1 LIMIT 1");
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
}