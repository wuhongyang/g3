<?php
/*
* 
* 查询排行 积分数据
*
* @author 
* @date 2010-12-20
* @copyright 杭州奥点科技有限公司
*/
class rule
{
	public $mongo;

	/**
	* 构造函数
	* 初始化DB类
	*/
	public function __construct($mongo){
		if(is_object($mongo)){
			$this->mongo = $mongo;
		}else{
			die('No DB Object');
		}
	}

	/**
	* 获取排行数据结果集
	*
	* @parame UinId 对应于业务规则定义主键1
	* @parame ChannelUin 对应于业务规则定义主键2
	* @parame ExtendUin 对应于业务规则定义主键3
	* @parame ruleid 规则id
	* @parame order 传入一个排序键值
	* @return imresult 传入表的查询结果
	*/
	public function getRuleRank($UinId,$ChannelUin,$ExtendUin,$ParentId,$ruleid,$rows=10,$period=array(),$time=0){
		$cycle_array = array('day','week','month','year','total');
		$rows = $rows >0 ? $rows : 10;
		if(!empty($period) && is_array($period)){
			$cycle_array = (array)$period;
		}
		if($ruleid > 0){
			$query_condition['Ruleid'] = (int)$ruleid;
		}
		if($UinId > 0){
			$query_condition['UinId'] = (int)$UinId;
		}
		if($ChannelUin > 0){
			$query_condition['ChannelUin'] = (int)$ChannelUin;
		}
		if($ExtendUin > 0){
			$query_condition['ExtendUin'] = (int)$ExtendUin;
		}
		if($ParentId > 0){
			$query_condition['FourthId'] = (int)$ParentId;
		}
		if(empty($query_condition)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		foreach($cycle_array as $key=>$type){
			$table = $type.'_weight';
			$uptime = $this->get_period($type,$time);
			unset($query_condition['Uptime']);
			if($uptime > 0){
				$query_condition['Uptime'] = $uptime;
			}
			$result[$type] = $this->mongo->get_results('kkyoo_integral.'.$table,$query_condition,array("sort"=>array("Weight"=>-1),'limit'=>array('rows'=>$rows)));
		}
		return $result;
	}
	
	/**
	* 根据条件查询等级值单条记录
	*
	* @parame UinId 对应于业务规则定义主键1
	* @parame ChannelUin 对应于业务规则定义主键2
	* @parame ExtendUin 对应于业务规则定义主键3
	* @parame ruleid 业务规则id
	* @parame period 查询周期
	* @return array 等级值
	*/
	public function getRuleLevel($UinId,$ChannelUin,$ExtendUin,$ruleid,$period){
		if($ruleid <1 ){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		if($UinId > 0){
			$query_condition['UinId'] = (int)$UinId;
		}
		if($ChannelUin > 0){
			$query_condition['ChannelUin'] = (int)$ChannelUin;
		}
		if($ExtendUin > 0){
			$query_condition['ExtendUin'] = (int)$ExtendUin;
		}
		if(empty($query_condition)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if($period != 'total'){
			$query_condition['Uptime'] = $this->get_period($period);
		}
        $fields = array('Weight');
        $level = $this->mongo->get_var('rank_'.$ruleid.'.'.$period.'_weight',$query_condition,$fields);
		return intval($level);
	}
	
	/**
	* 根据条件查询积分值单条记录
	*
	* @parame UinId 对应于业务规则定义主键1
	* @parame ChannelUin 对应于业务规则定义主键2
	* @parame ExtendUin 对应于业务规则定义主键3
	* @parame ruleid 业务规则id
	* @parame period 查询周期
	* @return array 积分值
	*/
	public function getRuleWeight($UinId,$ChannelUin,$ExtendUin,$ruleid,$period){
		if($ruleid > 0){
			$query_condition['Ruleid'] = (int)$ruleid;
		}
		if($UinId > 0){
			$query_condition['UinId'] = (int)$UinId;
		}
		if($ChannelUin > 0){
			$query_condition['ChannelUin'] = (int)$ChannelUin;
		}
		if($ExtendUin > 0){
			$query_condition['ExtendUin'] = (int)$ExtendUin;
		}
		if(empty($query_condition)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		if($period != 'total'){
			$query_condition['Uptime'] = $this->get_period($period);
		}
        $fields = array('Weight');
        $weight = $this->mongo->get_var('kkyoo_integral.'.$period.'_weight',$query_condition,$fields);
		return intval($weight);
	}
	
	/**
	* 获取等级数据 结果集
	*
	* @parame UinId 对应于业务规则定义主键1
	* @parame ChannelUin 对应于业务规则定义主键2
	* @parame ExtendUin 对应于业务规则定义主键3
	* @parame ruleid 规则id
	* @parame order 传入一个排序键值
	* @return imresult 传入表的查询结果
	*/
	public function getRuleLevelResult($UinId,$ChannelUin,$ExtendUin,$ruleid,$rows=10,$period=array(),$time=0){
		$cycle_array = array('day','week','month','year','total');
		$rows = $rows >0 ? $rows : 10;
		if(!empty($period) && is_array($period)){
			$cycle_array = (array)$period;
		}
		if($ruleid <1 ){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		if($UinId > 0){
			$query_condition['UinId'] = (int)$UinId;
		}
		if($ChannelUin > 0){
			$query_condition['ChannelUin'] = (int)$ChannelUin;
		}
		if($ExtendUin > 0){
			$query_condition['ExtendUin'] = (int)$ExtendUin;
		}
		if(empty($query_condition)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		foreach($cycle_array as $key=>$type){
			$uptime = $this->get_period($type,$time);
			unset($query_condition['Uptime']);
			if($uptime > 0){
				$query_condition['Uptime'] = $uptime;
			}
			$result[$type] = $this->mongo->get_results('rank_'.$ruleid.'.'.$type.'_weight',$query_condition,array("sort"=>array("Weight"=>-1),'limit'=>array('rows'=>$rows)));
		}
		return $result;
	}
	
	protected function get_period($type,$time=0){
		switch ($type){
			case 'day':
				$uptime = $time > 0 ? date('Ymd',$time) : date('Ymd');
				break;
			case 'week':
				$uptime = $time > 0 ? date('oW',$time) : date('oW');
				break;
			case 'month':
				$uptime = $time > 0 ? date('Ym',$time) : date('Ym');
				break;
			case 'year':
				$uptime = $time > 0 ? date('Y',$time) : date('Y');
				break;
			case 'total':
				$uptime = 0;
				break;
			default :
				$uptime = date('Ymd');
		}
		return (int)$uptime;
	}

}
