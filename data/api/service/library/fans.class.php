<?php

/**
 *   粉丝管理
 *   文件: fans.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class fans extends dlhelper
{

	//连接数据库
	public function __construct() 
	{
		$this->db = domain::main()->GroupDBConn("mysql");
		parent::__construct($this->db);
	}

	/*
	 *   粉丝推荐
	 *   @return	array	$return	推荐的粉丝信息
	 */
	public function recommendFans($uin){
		$userMessage = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$uin)));
		$hobby = $userMessage['Hobby'];
		if( $hobby > 0 ){
			$return = array();
			$return = $this->getUserHobby($hobby, $uin);
			$num = count($return);
			if( $num < 2 && $num >= 1){
				$return1 = array();
				$return1 = $this->getUser($hobby, $uin);
				$return = array_merge($return,$return1);
			}else
				$return = $this->getUser($hobby, $uin);
		}
		else
			$return = $this->getUser($hobby, $uin);
		return $return;
	}
	/*
	 *   根据用户兴趣爱好 推荐粉丝
	 *   @param	int	$hobby	用户爱好
	 *   @return	array	$message	用户信息
	 */
	private function getUserHobby($hobby, $uin){
		$table = DB_NAME_IM.'.basic_tbl';
		$sql = "SELECT * FROM ".$table." WHERE hobby_sum='$hobby' AND `uin` not in($uin)";
		$return = array();
		$return = $this->db->get_results($sql);
		$num = count($return);
		if( $num < 2 ){
			$re = array();
			//减少爱好标签再继续匹配
			$re = $this->randHobby($hobby, $uin);
			$return = array_merge($return, $re);
		}
		return $return;
	}
	
	private function randHobby($hobby, $uin){
		$num = $hobby;
		$base = base_convert($num, 10, 2);	//将十进制 转换成二进制
		$length = strlen($base);
		for( $i=0; $i < $length; $i++  ){
			if( $base[$i] == '1' ){
				$l = $length - $i - 1;
				$hobbymessage[] = pow(2,$l);
			}
		}
		$hobbyNum = count($hobbymessage);
		shuffle($hobbymessage);
		//计算要查找的次数
		for( $m=1; $m < $hobbyNum; $m++){
			//计算出sum的值
			for( $j = 0;$j < $m; $j++ )	{
				unset($hobbymessage[$j]);
			}
			$sum = array_sum($hobbymessage);
			$table = DB_NAME_IM.'.basic_tbl';
			if( $sum > 0 ){
				$sql = "SELECT * FROM ".$table." WHERE `hobby_sum` = '{$sum}' AND `uin` not in ($uin) limit 2 ";
				$hobbyResult[] = $this->db->get_results($sql);
			}
			foreach( $hobbyResult as $val ){
				foreach( $val as $value ){
					//判断用户是否已经关注
					$tableFollow = DB_NAME_WEIBO.'.follow';
					$sql = "SELECT * FROM ".$tableFollow." WHERE `follower` = '$uin' AND `following`={$value['uin']} ";
					$num = $this->db->get_col($sql);
					if( $num < 1 ){
						$return[] = $value;
					}
				}
			}
		}
		return $return;
	}
	/*
	 *   获得用户关注数量
	 *   @return	int	$num	粉丝数量
	 */
	public function getFollowNum($uin,$other_uin=0){
		$table = DB_NAME_WEIBO.'.follow';
		if($other_uin > 0){
			$where = " AND following={$other_uin}";
		}
		$sql = "SELECT COUNT(*) FROM ".$table." WHERE `follower`={$uin} {$where}";
		$num = $this->db->get_var($sql);
		return array('Flag'=>100,'FlagString'=>'粉丝数量','Num'=>$num);
	}

	/*
	 *   获得用粉丝注数量
	 *   @return	int	$num	关注数量
	 */
	public function getFansNum($uin){
		$table = DB_NAME_WEIBO.'.follow';
		$sql = "SELECT COUNT(*) FROM ".$table." WHERE `following`={$uin}";
		$num = $this->db->get_var($sql);
		return array('Flag'=>100,'FlagString'=>'粉丝数量','Num'=>$num);
	}
	
	/*
	 *   移除粉丝
	 *   @param	array	$message	移除粉丝的id
	 *   @return	array	$return		操作结果	
	 */
	public function moveFans($message){
		$uin = $message['Uin']; 
		$id = $message['id'];
		$table = DB_NAME_WEIBO.'.follow';
		$sql = "DELETE FROM ".$table."  WHERE `following`={$uin} AND `follower`={$id}";
		$num = $this->db->query($sql);
		if( $num > 0 ){
			$return = array('Flag'=>'100','FlagString'=>'success');
		}else{
			$return = array('Flag'=>'101','FlagString'=>'unsuccess');
		}
		return $return;
	}
	
	/*
	 *   取消关注
	 *   @param	array	$message	id
	 *   @return	array	$return		操作结果	
	 */
	public function moveFollow($uin,$uids){
		$uin = intval($uin);
		$uids = (array)$uids;
		$uids = implode(',',array_map("intval",$uids));
		$sql = "DELETE FROM ".DB_NAME_WEIBO.".`follow` WHERE `follower`={$uin} AND `following` IN({$uids})";
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'删除成功');
		}else{
			return array('Flag'=>102,'FlagString'=>'删除失败');
		}
	}
	
	/*
	 *   添加关注
	 */
	public function addFollow($uin,$uids){
		$uin = intval($uin);
		$uids = array_map("intval",(array)$uids);
		foreach($uids as $f){
			$friends[] = "({$uin},{$f})";
		}
		$friends = implode(',',$friends);
		$sql = "INSERT INTO ".DB_NAME_WEIBO.".`follow` (`follower`,`following`)VALUES{$friends}";
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'关注成功');
		}else{
			return array('Flag'=>102,'FlagString'=>'关注失败');
		}
	}
	
	/*
	 *   获取粉丝信息(对比后)
	 *   @param	array	$message	用户id
	 *   @return	array	$return		粉丝信息
	 */
	public function listFans($uin){
		$uin = intval($uin);
		$tbl_fans = DB_NAME_WEIBO.".follow";
		$lists = $this->findAllPage(
			"{$tbl_fans} AS fs", //table
			"fs.following={$uin}", //where
			"fs.follower DESC",'fs.follower AS `Uin`' //select
		);
		foreach((array)$lists as $key=>$val){
			$userinfo = $this->getUserNick($val['Uin']);
			$lists[$key]['Nick'] = $userinfo['Nick'];
			$lists[$key]['Gender'] = $userinfo['gender'];
		}
		$page = $this->getPage();
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>(array)$lists,'Page'=>$page);
	}
	
	/*
	 *   获取关注信息(组合后)
	 *   @param	array	$message	用户id
	 *   @return	array	$return		关注信息
	 */
	public function listFollow($uin){
		$uin =intval($uin);
		$tbl_fans = DB_NAME_WEIBO.".follow";
		$lists = $this->findAllPage(
			"{$tbl_fans} AS fs", //table
			"fs.follower={$uin} ", //where
			"fs.following DESC",'fs.following AS `Uin`' //select
		);
		foreach((array)$lists as $key=>$val){
			$userinfo = $this->getUserNick($val['Uin']);
			$lists[$key]['Nick'] = $userinfo['Nick'];
			$lists[$key]['Gender'] = $userinfo['gender'];
		}
		$page = $this->getPage();
		return array('Flag'=>100,'FlagString'=>'ok','Result'=>(array)$lists,'Page'=>$page);
	}

	/*
	 *   获得推荐粉丝
	 *   @param	int	$hobby	用户爱好
	 *   @param	int	$uin	当前用户uin
	 *   @return	array	$return	用户信息
	 */
	private function getUser($hobby, $uin){
		$table = DB_NAME_IM.'.basic_tbl';
		$sql = "SELECT `uin` FROM ".$table ;
		$sum = $this->db->get_col($sql);
		$rand = rand(0,$sum-1);
		$fans = $this->getFans($rand);
		$getFansNum = count($fans);
		$loop = 0;
		while( $getFansNum < 2 ){
			$rand = rand(0,$sum-1);
			$fans = $this->getFans($rand);
			$getFansNum = count($fans);
			$loop++;
			if( $loop > 10 )
				break;
		}
		//获取粉丝信息
		foreach( $fans as $val ){
			if( $val != $uin )
				$return[] = $this->getUserBasic($val);
		}
	
		return $return;
	}
	private function getFans($rand){
		$user = $this->isLogin();
		$uin = $user['Uin'];
		$tableFollow = DB_NAME_WEIBO.'.follow';
		$table = DB_NAME_IM.'.basic_tbl';
		$sql = "SELECT `uin` FROM ".$table." limit $rand,2";
		$result = $this->db->get_results($sql);
		$return = array();
		foreach( $result as $val ){
			$sql = "SELECT * FROM ".$tableFollow." WHERE `follower` = '$uin' AND `following`='{$val["uin"]}' ";
			$num = $this->db->get_col($sql);
			if( $num < 1 ){
				$getNum[] = $val['uin'];
			}
		}
		return $getNum;
	}
	
	public function getFollow($uin,$other_uin=0){
		$table = DB_NAME_WEIBO.'.follow';
		if($other_uin > 0){
			$where = " AND following={$other_uin}";
		}
		$sql = "SELECT following FROM ".$table." WHERE `follower`={$uin} {$where}";
		$result = $this->db->get_results($sql,'ASSOC');
		if(empty($result)){
			return array('Flag'=>101,'FlagString'=>'关注0个用户');
		}
		return array('Flag'=>100,'FlagString'=>'关注用户列表','Result'=>$result);
	}
	
	/*
	 *   获取用户昵称
	 *   @param	int	$uid		当前用户id
	 */
	private function getUserNick($uin){	
		$data=array(
			'extparam'=>array(
				'Tag'=>'GetUserBasicForUin',
				'Uin'=>$uin
			)
		);
		$userinfo=httpPOST(SSO_API_PATH,$data);
		$userinfo['baseInfo']['Nick']=$userinfo['baseInfo']['nick'];
		return $userinfo['baseInfo'];
	}
}


