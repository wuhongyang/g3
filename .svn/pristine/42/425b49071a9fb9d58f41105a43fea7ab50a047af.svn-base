<?php

/**
 *   代理账户操作接口
 *   文件: proxy_sys.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class proxy_sys
{
	//数据库指针
	protected $db = null;
	protected $uptime = 0; //当前时间
    protected $today  = 0; //本天时间
	private static $instance;
	
	//获取单例对象
    function instance(){
        if(!is_object(self::$instance)){
            $db	= domain::main()->GroupDBConn();
            self::$instance = new self($db);
        }
        return self::$instance;
    }
	
	//构造函数
	public function __construct($db) {
		if(!is_object($db)){
            throw new Exception("db error");
        }
        $this->uptime = time();
        $this->today = strtotime(date('Y-m-d 00:00:00'));
        $this->db = $db;
	}
	
	/**
    * 启用事务
    */
	public function action(){
        $this->db->query('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');
        $this->db->start_transaction();
    }
	
	/**
    * 提交事务
    */
	public function commit(){
        $this->db->commit();
    }
	
	/**
    * 回滚事务
    */
    public function rollback(){
        $this->db->rollback();
    }

	public function proxyBalance($uin){
		if($uin > 0){
			$sql = "SELECT * FROM ".DB_NAME_PARTNER.".proxy_balance WHERE uin =".$uin;
			$row = $this->db->get_row($sql,"ASSOC");
			return empty($row) ? array('Flag'=>101,'FlagString'=>'不存在') : array('Flag'=>100,'FlagString'=>'存在','Data'=>$row);
		}else{
			return array('Flag'=>101,'FlagString'=>'uin不正确');
		}
	}
	
	/*
		代理账户操作
		Uin 操作用户
		Confirm_uin 确认操作用户
		MoneyWeight 操作金额
		TradeDesc 操作描述
		TradeType 操作类型 1 加 2减
	*/
	public function proxyAccount($info){
		if($info['Uin'] <0 || $info['MoneyWeight'] < 0 || $info['Desc'] == '' || $info['TradeType'] <=0 || $info['Child_type'] <=0) return array('Flag'=>101,'FlagString'=>'参数有误1');
		$userinfo = $this->checkUser($info['Uin'],16);
		if($userinfo['Flag'] !== 100) return array('Flag'=>102,'FlagString'=>'用户不存在');
		$this->action();
		$result = $this->updateBalance($info['Uin'],$info['MoneyWeight'],$info['TradeType']);
		if($result['Flag'] ==100){
			$sql = "INSERT INTO ".DB_NAME_PARTNER.".proxy_running (uin,trade_money,trade_type,trade_desc,child_type,balance,uptime) VALUES ({$info['Uin']},{$info['MoneyWeight']},{$info['TradeType']},'{$info['Desc']}',{$info['Child_type']},{$result['Balance']},{$this->uptime})";
			if(!$this->db->query($sql)){
				$this->rollback();
                return array('Flag'=>103,'FlagString'=>'记录流水失败');
			}
			$this->commit();
			return array('Flag'=>100,'FlagString'=>'成功','Balance'=>$result['Balance']);
		}
		return $result;
	}
	
	private function checkUser($uin,$type){
		if($uin > 0){
			$sql = 'SELECT * FROM '.DB_NAME_PARTNER.'.channel_user WHERE uid = '.$uin.' AND `type` = '.$type.' AND flag = 1';
			$row = $this->db->get_row($sql,'ASSOC');
			return empty($row) ? array('Flag'=>101,'FlagString'=>'不存在') : array('Flag'=>100,'FlagString'=>'存在','Data'=>$row);
		}
	}
	
	private function updateBalance($uin,$money,$trade_type){
		if($uin <=0 || $money <=0){
			$this->rollback();
			return array('Flag'=>101,'FlagString'=>'参数错误2');
		}
		$sql = "SELECT * FROM ".DB_NAME_PARTNER.".proxy_balance WHERE uin =".$uin.' LIMIT 1 FOR UPDATE';
		$row = $this->db->get_row($sql,"ASSOC");
		if($trade_type == 1){//加
			$money += $row['balance'];
		}elseif($trade_type == 2){//减
			if($row['balance'] < $money){
				$this->rollback();
				return array('Flag'=>101,'FlagString'=>'余额不足');
			}
			$money = $row['balance']-$money;
		}
		$sql = "REPLACE INTO ".DB_NAME_PARTNER.".proxy_balance (`uin`, `balance`) VALUES ({$uin},{$money})";
		if(!$this->db->query($sql)){
			$this->rollback();
			return array('Flag'=>102,'FlagString'=>'操作失败');
		}
		return array('Flag'=>100,'FlagString'=>'操作成功','Balance'=>$money);
	}

}
