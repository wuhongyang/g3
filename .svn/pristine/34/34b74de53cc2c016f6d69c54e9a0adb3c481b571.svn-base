<?php

/**
 *   用户信息管理
 *   文件: usermanage.class.php
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
 
class usermanage
{
	protected $group_id;

	//构造函数
	public function __construct($group_id) 
	{
		$this->group_id = $group_id;
        $this->db = $group_id?domain::main()->GroupDBConn("mysql", $group_id):db::connect(config('database','default'));
	}

	/**
	 *   用户信息管理
	 *   @param	array	$message	查找条件
	 *   @return	array	$return		查询结果
	 */
	public function showUserMessage($message)
	{
		extract($message);
        
		$where = ' 1';
		
        if($data_group_id){
            $where .= " AND a.group_id='{$data_group_id}'";
        }
        
		//关键字搜索
		if( $vtype > 0 && $gjz != ''){
			$where .= $vtype==1? " AND a.uin like '{$gjz}%' " : " AND a.nick like '{$gjz}%' ";
		}

		//性别
		if( $gender > 0 ){
			$where .= $gender==1? " AND a.gender=1" : " AND a.gender=2";
		}

		//地区搜索
		if($province > 0 ){
			$where .= " AND a.province='{$province}'";
		}
		if($city >0){
			$where .= " AND a.city='{$city}'";
		}
		if($area > 0){
			$where .= " AND a.area='{$area}'";
		}
		
		//日期
		if( ! empty($time_start)){
			$where .= " AND a.birthday >= '{$time_start}'";
		}
		if( ! empty($time_end)){
			$where .= " AND a.birthday <= '{$time_end}'";
		}
		
		$sql = "SELECT a.*,c.vip_grade,b.uid,c.use_expire,c.uptime FROM ".DB_NAME_IM.".basic_tbl AS a LEFT JOIN ".DB_NAME_IM.".sso_user_relate AS b ON a.uin=b.uin LEFT JOIN ".DB_NAME_COMMON.".tbl_vip_info AS c ON a.uin=c.vip_uin WHERE";
		
		if($binded == 1){//绑定
			if($vip > 0){
				$sql_count = "SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl AS a LEFT JOIN ".DB_NAME_IM.".sso_user_relate AS b ON a.uin=b.uin LEFT JOIN ".DB_NAME_COMMON.".tbl_vip_info AS c ON a.uin=c.vip_uin WHERE $where AND b.is_use=1 AND c.vip_grade={$vip} AND c.use_expire * 86400 + c.uptime > UNIX_TIMESTAMP()";
				$where .= " AND b.is_use=1 AND c.vip_grade={$vip} AND c.use_expire * 86400 + c.uptime > UNIX_TIMESTAMP()";
			}elseif($vip == 0){
				$sql_count = "SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl AS a LEFT JOIN ".DB_NAME_IM.".sso_user_relate AS b ON a.uin=b.uin LEFT JOIN ".DB_NAME_COMMON.".tbl_vip_info AS c ON a.uin=c.vip_uin WHERE $where AND b.is_use=1 AND (c.vip_uin IS NULL OR c.use_expire * 86400 + c.uptime <= UNIX_TIMESTAMP())";
				$where .= " AND b.is_use=1 AND (c.vip_uin IS NULL OR c.use_expire * 86400 + c.uptime <= UNIX_TIMESTAMP())";
			}else{
				$sql_count = "SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl AS a LEFT JOIN ".DB_NAME_IM.".sso_user_relate AS b ON a.uin=b.uin WHERE $where AND b.is_use=1";
				$where .= " AND b.is_use=1";
			}
		}elseif($binded == 2){//未绑定
			if($vip > 0){//不存在未绑定的会员
				return array('Flag'=>101,'FlagString'=>'用户列表');
			}else{
				$sql_count = "SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl AS a LEFT JOIN ".DB_NAME_IM.".sso_user_relate AS b ON a.uin=b.uin WHERE $where AND b.uin IS NULL";
				$where .= " AND b.uin IS NULL";
			}
		}else{//全部
			if($vip > 0){
				$sql_count = "SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl AS a LEFT JOIN ".DB_NAME_COMMON.".tbl_vip_info AS c ON a.uin=c.vip_uin WHERE $where AND c.vip_grade={$vip} AND c.use_expire * 86400 + c.uptime > UNIX_TIMESTAMP()";
				$where .= " AND c.vip_grade={$vip} AND c.use_expire * 86400 + c.uptime > UNIX_TIMESTAMP()";
			}elseif($vip == 0){
				$sql_count = "SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl AS a LEFT JOIN ".DB_NAME_COMMON.".tbl_vip_info AS c ON a.uin=c.vip_uin WHERE $where AND (c.vip_uin IS NULL OR c.use_expire * 86400 + c.uptime <= UNIX_TIMESTAMP())";
				$where .= " AND (c.vip_uin IS NULL OR c.use_expire * 86400 + c.uptime <= UNIX_TIMESTAMP())";
			}else{
				$sql_count = "SELECT COUNT(*) FROM ".DB_NAME_IM.".basic_tbl AS a WHERE $where";
			}
		}
		$sql .= $where;
		
		$count = $this->db->get_var($sql_count);
		if($count<=0){
			return array('Flag'=>100,'FlagString'=>'用户列表');
		}
		
		$pageArr = $this->showPage($count);
		$sql .= " LIMIT ".$pageArr['limit'];
		$lists = $this->db->get_results($sql,'ASSOC');
		foreach($lists as $key => $val ){
			if(($val['use_expire'] *86400 + $val['uptime']) > time()){
				if($val['vip_grade'] == 2){
					$lists[$key]['vipname'] = "高级会员";
				}elseif($val['vip_grade'] == 1){
					$lists[$key]['vipname'] = "普通会员";
				}else{
					$lists[$key]['vipname'] = "普通用户";
				}
			}else{
				$lists[$key]['vipname'] = "普通用户";
			}
			
			if( $val['hobby_sum'] > 0){
				$lists[$key]['hobby_sum'] = $this->getHobby($val['hobby_sum']);
			}
			if($val['uid'] > 0 ){
				$lists[$key]['binded'] = '<span style="color:red">已绑定(<a href="passport.php?uin='.$val['uin'].'">查看</a>)</span>';
			}else{
				$lists[$key]['binded'] = '<span style="color:green">未绑定</span>';
			}
			$lists[$key]['gender'] = $val['gender'] ==1 ? "男" : "女";
			
			//取得通行证身份证号码
			$sql = "SELECT idcard FROM ".DB_NAME_IM.".username WHERE uid={$val['uid']}";
			$lists[$key]['idcard'] = $this->db->get_var($sql);
		}

		$return = array(
			'Flag'       =>  100,
			'FlagString' => '用户列表',
			'lists'	     =>  $lists,
			'binded'     =>  $binded,
			'page'       =>  $pageArr['page'],
		);
		return $return;
	}
	
	//获取用户详细信息
	public function getUserDetail($param){
		$uin = $param['uin'];
		if(!$uin){
			return array("FlagString"=>"需要uin", "Flag"=>102);
		}
		$sql = "SELECT nick,uin,gender,group_id FROM ".DB_NAME_IM.".`basic_tbl` WHERE uin = '".$uin."'";
		$res = $this->db->get_row($sql, "ASSOC");
		if( !$res ){
			return array("FlagString"=>"无该用户", "Flag"=>102);
		}
		//通行证信息
		//$sql = "SELECT s.is_use,u.phone,u.email,u.uid FROM ".DB_NAME_IM.".sso_user_relate s LEFT JOIN ".DB_NAME_IM.".username u USING(uid) WHERE s.uin={$uin}";
		//$passInfo = $this->db->get_row($sql,ASSOC);
        $passInfo = httpPOST(SSO_API_PATH,array('param'=>array('GroupId'=>$this->group_id),'extparam'=>array('Tag'=>'GetUser','Uid'=>$uin,'Status'=>1)));
		$res = array_merge($res,$passInfo);
		//会员等级
		$userInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$uin)));
		if($userInfo['VipSpare'] < 1){//VIP过期
			$vipName = '普通用户';
		}else{
			if($userInfo['Vip'] == 2){
				$vipName = '高级会员';
			}elseif($userInfo['Vip'] == 1){
				$vipName = '普通会员';
			}else{
				$vipName = '普通用户';
			}
		}
		$res['VipName'] = $vipName;
		//消费等级
		//$c_array = $this->getLevel($uin,10);
		//$res['c_level'] = $c_array['Level'];
		//艺人等级
		//$a_array = $this->getLevel($uin,13);
		//$res['a_level'] = $a_array['Level'];

		$result = array("FlagString"=>"查询成功", "Flag"=>100, "info"=>$res);
		return $result;
	}

	public function getUserBankInfo($uin){
		$uin = intval($uin);
		if($uin < 1){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql = "SELECT bank_id,bank_name,bank_address FROM ".DB_NAME_PARTNER.".`account` WHERE uin={$uin}";
		$bankInfo = $this->db->get_row($sql,ASSOC);
		if(empty($bankInfo)){
			return array('Flag'=>102,'FlagString'=>'用户银行信息');
		}
		return array('Flag'=>100,'FlagString'=>'用户银行信息','BankInfo'=>$bankInfo);
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
	
	//根据兴趣数字综合获取具体兴趣文字
	private function getHobby($hobby_sum){
		$base = base_convert($hobby_sum, 10, 2);
		$length = strlen($base);
		for($i=0; $i < $length; $i++)
		{
			if( $base[$i] > 0 )
			{
				$l = $length - $i - 1;
				$hobby[] = pow(2,$l);
			}
		}
		$table = DB_NAME_IM.'.hobby_tbl';
		$sql = "SELECT hobbyname FROM ".DB_NAME_IM.".hobby_tbl WHERE id IN(".implode($hobby,',').")";
		$result = $this->db->get_results($sql,'ASSOC');
		foreach((array)$result as $key => $val){
			$hobbyMessage .= $val['hobbyname'].',';
		}
		return rtrim($hobbyMessage,',');
	}
	
	//分页
	private function showPage($total,$perpage=20){
		if($total>0){
			$page=new extpage(array (
				'total'=>$total,
				'perpage'=>$perpage
			));
			$pageArr['page']=$page->show();
			$pageArr['limit']=$page->limit();
			unset($page);
		}
		return $pageArr;
	}
}