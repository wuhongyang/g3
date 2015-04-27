<?php

/**
 *   个人中心
 *   文件: usercenter.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class usercenter
{

	//构造函数
	public function __construct(){
		$this->db = domain::main()->GroupDBConn('mysql');
        $this->mongo = domain::main()->GroupDBConn('mongo');
	}
	/**
	 *   查看用户是否登录
	 *   @return	array	$return		用户登录信息	
	 */
	public function isLogin(){
		$param = array('extparam'=>array('Tag'=>'GetLogin'));
		$return = httpPOST(SSO_API_PATH, $param, true);
		return $return;
	}

	public function getChannelTax($channelType,$uin){
		$channelType = intval($channelType);
		$uin = intval($uin);
		if($channelType < 1 || $uin < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		$query = array(
			'ChannelType' => (int)$channelType,
			'Uin'=> (int)$uin,
			'Uptime' => (int)date('Ym')
		);
		$table = 'parter_income.new_parter_user_month';

		$sort = array(
			'sort' => array('Uptime'=>-1)
		);
		$result = $this->mongo->get_row($table, $query);

		return array('Flag'=>100,'FlagString'=>'成功','Result'=>$result);
	}

	/**
	 *   保存用户基本资料
	 *   @param	array	$message	要保存的信息
	 *   @return	array	$return		保存结果
	 */
	public function saveBasic($uin,$message){
		if(empty($message['nick'])){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$table = DB_NAME_IM.'.basic_tbl';
		$sql = "SELECT `uin` FROM {$table} WHERE uin='{$uin}'";
		$var = $this->db->get_var($sql);
		if( empty( $var ) ){
			$return = array('Flag'=>101,'FlagString'=>'UIN不存在');
			return $return;
		}
		//$nick = $message['nick'];
		//unset($message['nick']);
		$dl = new dlhelper($this->db);
		//$uin = $message['uin'];
		$rst = $dl->update($table, $message, "uin={$uin}");
		if(!$rst){
			return array('Flag'=>101,'FlagString'=>'更新失败');
		}
		//得到mc中的登录信息
		$userInfo= httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetLogin')));
		if($userInfo['Uin'] == $uin){ //如果修改的和登录的是同一个账号则修改MC中的信息
			//修改UIN的信息
			$userInfo['Nick'] = $message['nick'];
			$userInfo['Gender'] = $message['gender'];
			//保存到mc中
			httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'SetStorage','Userinfo'=>$userInfo)));
		}

		return array('Flag'=>100,'FlagString'=>'更新成功');
	}
	/**
	 *   修改个人联系方式
	 *   @param	array	$message	要保存的信息
	 *   @return	array	$return		保存结果
	 */
	public function saveConnect($message)
	{
		$table = DB_NAME_IM.'.basic_tbl';
		$sql = "SELECT `uin` FROM ". $table. " WHERE uin = '{$message['uin']}'";
		$var = $this->db->get_var($sql);
		if( empty( $var ) )
		{
			$return = array(
				'Flag'   => '101',
				'FlagString'  => 'uin不存在'
			);
			return $return;
		}
		
		$dl = new dlhelper($this->db);
		$uin = $message['uin'];
		$num = $dl->update($table, $message, "uin = '$uin'");
		if( $num > 0 )
		{
			$return = array(
				'Flag'   => '100',
				'FlagString'  => '更新成功'
			);
		}
		else 
		{
			$return = array(
				'Flag'   => '101',
				'FlagString'  => '更新失败'
			);
		}
		return $return;
	}
	/*
	 *   查看基本资料 
	 *   @param	int	$uin	查找的条件
	 *   @return	array	$return		查找结果
	 */
	public function showBasic($uin){
		/*基本资料*/
		//$table = DB_NAME_IM.'.basic_tbl';
		//$sql = "SELECT * FROM ".$table." WHERE uin = '{$uin}' ";
		//$im = $this->db->get_row($sql,"ASSOC");
		
		//$sql = "SELECT a.nick FROM basic_tbl AS a,sso_user_relate AS b WHERE a.uin={$uin} AND b.uin=a.uin AND b.is_use=1";
		//$im['nick'] = $this->db->get_var($sql);
		
		//消费积分/等级		10 8
	/*	$c_array = $this->getLevel($uin,10);
		$level_array['c_level'] = $c_array['Level'];
		$level_array['c_score'] = $c_array['Score'];
		$level_array['c_nextscore'] = $this->getScore(8,$level_array['c_level'],$level_array['c_score']);
		
		//艺人积分/等级		13 10
		$a_array = $this->getLevel($uin,13);
		$level_array['a_level'] = $a_array['Level'];
		$level_array['a_score'] = $a_array['Score'];
		$level_array['a_nextscore'] = $this->getScore(10,$level_array['a_level'],$level_array['a_score']);
		*/
		$return = array(
			'im'     => $im,
			'Level_array'=>$level_array,
			'Flag'   => '100',
			'FlagString'=> 'success'
		);
		return $return;
	}
	
	/*获取用户积分/等级*/
	private function getLevel($uin,$type){
		$uin = intval($uin);
        if($uin <= 0){
            return array('Flag'=>101,'FlagString'=>'参数错误');
        }
        $table_name = 'rank_'.$type.'.total_weight';
        $query_condition = array('UinId'=>$uin);
        $fields = array('Weight');
        $level = $this->mongo->get_var($table_name,$query_condition,$fields);		
        $score = $this->mongo->get_var('kkyoo_integral.total_weight',array('Ruleid'=>$type,'UinId'=>$uin),$fields);
		return array('Flag'=>100,'FlagString'=>'成功','Level'=>intval($level),'Score'=>(int)$score);
	}
	
	/*计算离下一级积分差*/
	private function getScore($type,$level,$score){
		$sql = "SELECT integration FROM ".DB_NAME_TPL.".`business_param_config` WHERE id = ".$type;
        $db = db::connect(config('database','default'));
		$row = $db->get_var($sql,"ASSOC");
		$row = json_decode(urldecode($row),true);
		$nextlevel = $row[$level]['one'];
		$score = $nextlevel - $score;
		return $score;
	}
	
	/**
	 *   保存修改头像
	 *   @param	array	$message	保存信息
	 *   @return	array	$return		操作结果 
	 */
	public function saveHead($message)
	{
		$result = httpPOST(GRIDFS_API_PATH,array('extparam'=>$message));
		return $result;
		
	}

	/**
	 *   查看爱好
	 *   @param	array	$message	空数组 
	 *   @return	array	$return		用户爱好
	 */
	public function showInteres($message)
	{
		$uin = $message['Uin'];
		$table = DB_NAME_IM.'.basic_tbl';
		$sql = "SELECT * FROM ".$table." WHERE uin = '$uin' ";
		$userhobby = $this->db->get_row($sql);

		//查找用户的标签
		if( $userhobby['hobby_sum'] > 0 )
		{
			$num = $userhobby['hobby_sum'];
			$base = base_convert($num, 10, 2);	//将十进制 转换成二进制
			$length = strlen($base);
			for( $i=0; $i < $length; $i++  )
			{
				if( $base[$i] == '1' )
				{
					$l = $length - $i - 1;
					$hobby[] = pow(2,$l);
				}
			}
			$hobbyMessage = array();
			$table = DB_NAME_IM.'.hobby_tbl';
			foreach( $hobby as $val )
			{
				$sql = "SELECT * FROM ".$table." WHERE 	id = '$val' ";
				$hobbyMessage[] = $this->db->get_row($sql);
			}
		}

		//查询所有爱好组合where条件
		$where = '';
		if( count( $hobby ) > 0 )
		{
			$str = '(';
			foreach( $hobby as $val )
			{
				$str .=$val.',';
			}
			$str = trim($str,',').')';
			$where .= " AND `id` NOT IN $str ";
		}
		if(!empty($where)){
			$where = ' WHERE '.ltrim($where,' AND ');
		}
		//获取所有爱好标签
		$table = DB_NAME_IM.'.hobby_tbl';
		$sql = "SELECT `id`,`hobbyname` FROM ".$table.$where;
		$allHobby = $this->db->get_results($sql);

		$return = array(
			'allHobby'    => $allHobby,
			'hobbyMessage'=> $hobbyMessage,
			'hobbyid'     => $userhobby['id'],
			'Flag'	      => '100',
			'FlagString'  => 'success',
		);
		return $return;
	}

	/**
	 *   爱好添加
	 *   @param	array	$message	需要添加的内容
	 *   @return 	array	$return		操作结果
	 */
	public function	addInteres($message)
	{
		$uin = $message['Uin'];
		$table = DB_NAME_IM.'.basic_tbl';
		$dl = new dlhelper($this->db);
		$data = array(
			'uin'   => $uin,
			'hobby_sum'=> $message['hobby_sum'],
		);
		$num = $dl->update($table, $data, "uin = '$uin'");
		if( $num > 0 )
		{
			$return = array(
				'Flag'   => '100',
				'FlagString' => '更新成功',
				'uin'     => $uin,
			);
		}
		return $return;
	}

	/**
	 *   保存扩展信息
	 *   @param	array	$message	保存信息
	 *   @return	array	$return		操作结果
	 */
	public function saveExtend($message)
	{

	}

	public function userlogin($info,$ext){
		// if($info['ChannelId'] > 0){
			// $info['GroupId'] = $this->db->get_var('SELECT `group` FROM kkyoo_new_rooms.rooms WHERE id ='.$info['ChannelId']);
		// }
		$array = array(
			'param' => array(
				'Uin'=>$info['Uin'],
				'SessionKey'=>$info['SessionKey'],
				'GroupId'=>$info['GroupId'],
			),
			'extparam' => array(
				'Tag'	=>  'UserLogin',
				'Remember' => $ext['Remember']
			)
		);
		$result = httpPOST(SSO_API_PATH, $array, false); 
		return  $result;
	}

	public function openidLogin($info){
		return httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'OpenidLogin','Userinfo'=>$info)),false);
	}
	
	/**
	 *   查看扩展信息
	 *   @param	array	$message	查找的条件
	 *   @return	array	$return		查找的信息
	 */
	public function showExtend($message)
	{
	
	}

	/**
	 *   查看头像
	 *   @param	array	$message	查看的条件
	 *   @return	array	$return		查找的头像信息
	 */
	public function showHead($message)
	{
	
	}
}





