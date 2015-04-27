<?php

/**
 *   渠道操作接口
 *   文件: partner_channel.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class groupProxy
{
	//数据库指针
	protected $db = null;

	//构造函数
	public function __construct() {
		$this->db = domain::main()->GroupDBConn();
	}
	
	//划账流水列表
	public function getRemitAccountList($data){
		$uin=intval($data['Uin']);
		$targetUin=intval($data['TargetUin']);
		$start=strtotime($data['StartDate']);
		$end=strtotime($data['EndDate']);
		if($uin<=0){
			return array('Flag'=>101,'FlagString'=>'用户ID错误');
		}
		$where="uin=$uin";
		if($targetUin>0){
			$where.=" AND target_uin=$targetUin";
		}
		if($start>0){
			$where.=" AND uptime>=$start";
		}
		if($end>0){
			$where.=" AND uptime<=$end";
		}
		$sql="SELECT COUNT(*) FROM ".DB_NAME_PARTNER.".proxy_remit_account WHERE ".$where;
		$count=$this->db->get_var($sql);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'没有数据');
		}

		$pageArr=$this->showPage($count);
		
		$sql="SELECT * FROM ".DB_NAME_PARTNER.".proxy_remit_account WHERE ".$where." ORDER BY id DESC LIMIT ".$pageArr['limit'];
		$list=$this->db->get_results($sql,'ASSOC');
		foreach($list as $key=>$val){
			$data=array(
				'extparam'=>array(
					'Tag'=>'GetUserBasicForUin',
					'Uin'=>$val['target_uin']
				)
			);
			$userInfo=httpPOST(SSO_API_PATH,$data);
			if(!empty($userInfo['baseInfo'])){
				$list[$key]['nick']=$userInfo['baseInfo']['nick'];
			}
		}
		
		return array('Flag'=>100,'FlagString'=>'划账流水列表','list'=>$list,'page'=>$pageArr['page'],'total'=>$count);
	}
	
	//划账流水总计
	public function getRemitAccountCount($data){
		$uin=intval($data['Uin']);
		$targetUin=intval($data['TargetUin']);
		$start=strtotime($data['StartDate']);
		$end=strtotime($data['EndDate']);
		if($uin<=0){
			return array('Flag'=>101,'FlagString'=>'用户ID错误');
		}
		$where="uin=$uin";
		if($targetUin>0){
			$where.=" AND target_uin=$targetUin";
		}
		if($start>0){
			$where.=" AND uptime>=$start";
		}
		if($end>0){
			$where.=" AND uptime<=$end";
		}
		$sql="SELECT SUM(vmoney) FROM ".DB_NAME_PARTNER.".proxy_remit_account WHERE ".$where;
		$total=$this->db->get_var($sql);
		$total=intval($total);
		
		return array('Flag'=>100,'FlagString'=>'划账流水总计','total'=>$total);
	}
	
	//划账
	public function proxyRecharge($groupId,$uin,$targetUin,$money){
		$groupId=intval($groupId);
		$uin=intval($uin);
		$targetUin=intval($targetUin);
		$money=intval($money);
		if($groupId<=0||$uin<=0||$targetUin<=0||$money<=0){
			return array('Flag'=>101,'FlagString'=>'必要参数错误');
		}
		if($uin==$targetUin){
			return array('Flag'=>102,'FlagString'=>'不可以给自己充值');
		}
		
		//include_once('pass_manager.class.php');
		//$PassManager=new PassManager();
		//$ssoInfo=$PassManager->ssoInfo($targetUin);
		$ssoInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$targetUin)));
		if($ssoInfo['Flag'] != 100){
			return array('Flag'=>103,'FlagString'=>'该用户不存在');
		}
		
		//代理扣钱
		$param=array(
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10471,'ChildId'=>102,'Uin'=>$uin,'TargetUin'=>$targetUin,'MoneyWeight'=>$money,'Desc'=>'渠道划账'),
			'extparam'=>array('Tag'=>'Kmoney','Operator'=>'574B9AEC5E7BB01C96730C1B9E05C0E2','GroupId'=>$groupId),
		);
		$result=httpPOST(KMONEY_API_PATH,$param);
		if($result['Flag']!=100){
			return $result;
		}
		
		//用户充钱
		$param=array(
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10471,'ChildId'=>103,'Uin'=>$uin,'TargetUin'=>$targetUin,'MoneyWeight'=>$money,'Desc'=>'渠道划账'),
			'extparam'=>array('Tag'=>'Kmoney','Operator'=>'574B9AEC5E7BB01C96730C1B9E05C0E2','GroupId'=>$groupId),
		);
		$result=httpPOST(KMONEY_API_PATH,$param);
		if($result['Flag']!=100){
			return $result;
		}
		
		//记录
		$sql="INSERT INTO ".DB_NAME_PARTNER.".proxy_remit_account (uin,target_uin,vmoney,uptime) VALUES ('$uin','$targetUin','$money','".time()."')";
		$result=$this->db->query($sql);
		if($result){
			return array('Flag'=>100,'FlagString'=>'划账成功');
		}
		else{
			return array('Flag'=>1041,'FlagString'=>'划账流水记录失败');
		}
	}
	
	//分页
	private function showPage($total,$perpage=20){
		if($total>0){
			$page=new extpage(array (
				'total'=>$total,
				'perpage'=>$perpage
			));
			$page_arr['page']=$page->show();
			$page_arr['limit']=$page->limit();
			unset($page);
		}
		return $page_arr;
	}
}


