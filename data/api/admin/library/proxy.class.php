<?php

/**
 *   代理账户操作接口
 *   文件: proxy.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class proxy
{
	//数据库指针
	protected $db = null;
	private static $instance;
	
	//获取单例对象
    function instance(){
        if(!is_object(self::$instance)){
            $db	= db::connect(config('database','default'));
            self::$instance = new self($db);
        }
        return self::$instance;
    }
	
	//构造函数
	public function __construct($db) {
		if(!is_object($db)){
            throw new Exception("db error");
        }
        $this->db = $db;
	}
	
	public function lowestWeightSet($info){
		$env = new env();
		$result = $env->env_write_config($info);
		if($result['Flag'] == 100){
			return array('Flag'=>100,'FlagString'=>'更新成功');
		}else{
			return array('Flag'=>101,'FlagString'=>'更新失败');
		}
	}
}
