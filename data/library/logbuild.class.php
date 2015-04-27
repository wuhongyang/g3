<?php
/*
* 日志辅助类
* 该类封装了一些常用的表查询操作与分页功能
*
* @author 
* @date 
* @copyright 杭州奥点科技有限公司
*/

class logbuild
{
	/**
    * 构造函数
    */
    public function __construct(){
	}
	
	public function __destruct(){
	}
	
	public function getLogData($param,$extparam){
		if($param['GroupId'] > 0){
			$group_param=array(
				'extparam'=>array('Tag'=>'GetGroupInfo','Id'=>$param['GroupId']),
				'param'=>array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101,'Desc'=>'获取站信息')
			);
			$group=httpPOST('api/rooms/rooms_api.php',$group_param);
			$business_array = $group['Result']['business_array'];
		}
		$roomid = $param['ChannelId'];
		// if($param['GroupId'] < 1){
			// $channel_relation = getChannelRelation($roomid);
			// $extparam['RegionId'] = (int)$channel_relation['RegionId'];
			// $param['GroupId'] = $channel_relation['GroupId'];
		// }
		$log_array = array(
			'param'=>array(
				'Uin'=>(int)$param['Uin'],'TargetUin'=>(int)$param['TargetUin'],'ChannelId'=>(int)$param['ChannelId'],'BigCaseId'=>(int)$param['BigCaseId'],'CaseId'=>(int)$param['CaseId'],'ParentId'=>(int)$param['ParentId'],'ChildId'=>(int)$param['ChildId'],'MoneyWeight'=>(int)$param['MoneyWeight'],'DoingWeight'=>(int)$param['DoingWeight'],'TaxType'=>(int)$param['TaxType'],'Desc'=>$param['Desc'],'GroupId'=>(int)$param['GroupId']
			),
			'extparam'=>$extparam
		);
		foreach($business_array as $key=>$value){
			$uin = $value[2]==1 ? $param['TargetUin'] : $param['Uin'];
			if($value[1] > 1 && $uin >0){//需要通过角色来确定id
				$roles_info = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'GetRole','Uin'=>$uin,'GroupId'=>$param['GroupId'],'ChannelId'=>$roomid,'RoleId'=>$value[1])));
				if($roles_info['Flag'] == 100){
					$log_array['param'][$key] = (int)$uin;
				}
			}
		}
		return $log_array;
	}
	
	public function checkWork($param,$extparam){
		if($param['BigCaseId']<1 ||$param['CaseId']<1 ||$param['ParentId']<1 ||$param['GroupId'] <1){
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
		$parent_balance = get_parent_money($param['BigCaseId'],$param['CaseId'],$param['ParentId'],$param['GroupId']);
		
		$request = array(
			'extparam' => array('Tag'=>'Charge', 'GroupId'=>$param['GroupId'], 'BigCaseId'=>$param['BigCaseId'], 'CaseId'=>$param['CaseId'], 'ParentId'=>$param['ParentId'], 'Balance'=>$parent_balance),
		//	'param' => array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10314,'ChildId'=>102)
		);
		$money = httpPOST(CCS_API_PATH,$request);
		return $money;
	}
	
	public function setlog($info){
		$param = $info['param'];
		$request = array(
			'extparam' => array('Tag'=>'setlog','GroupId'=>$param['GroupId']),
			'param' => array('BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>$param['ChildId'])
		);
		// $money = httpPOST(KMONEY_API_PATH,$request);
	}
}
