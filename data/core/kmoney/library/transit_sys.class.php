<?php

/*奥点网络媒体互动用户计费管理平台软件
 *模块: 奥点网络媒体互动用户计费管理平台软件 V豆资金系统模块
 *文件: kmoney.class.php
 *copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */

class transit_sys
{   
    private $transit = null;
	protected $method;
	protected $kmoney;
	protected $child_id;
	
	/**
    * 构造函数
    */
    public function __construct(){
	}
	
	public function __destruct(){
	}
	
	public function trade($info){
		if($info['group_id'] > 0){
			switch ($info['tag']){
				case 'Kwealth'://现金操作
					$this->transit = kwealth_sys::instance();
					break;
				case 'Kincome'://礼金操作
				case 'Kownincome'://礼金操作
					$this->transit = kincome_sys::instance();
					$this->transit->table_pre = $info['tag']== 'Kincome'? 'uin' : 'ownuin';
					break;
				default ://V点操作
					$this->transit = voucher_sys::instance();
					break;
			}
		}else{//V豆操作
			$this->transit = kmoney_sys::instance();
		}
		//$this->transit->action();
		$result = $this->transit->$info['method']($info);
		if($result['Flag'] == 100){
			// if($this->kmoney == true){
				// $result['KmoneyBalance'] = $kmoney_result['LastBalance'];
			// }
			if($info['child_id'] == 901 || $info['child_id'] == 908){
				$info['bigcase_id'] = 10006;
				$info['case_id'] = 10047;
				$info['parent_id'] = 10264;
				$this->child_id = $info['child_id'] == 901 ? 101 : 102;
				$info['child_id'] = $this->child_id;
				$a = $this->transit->TaxTrade($info);
				$info['child_id'] = 103;
				$this->child_id == 101 ? $this->transit->TaxTrade($info) : '';
			}
			$this->transit->addlog();
		//	$this->transit->commit();
		}else{
		//	$this->transit->rollback();
		}
		return $result;
	}
}