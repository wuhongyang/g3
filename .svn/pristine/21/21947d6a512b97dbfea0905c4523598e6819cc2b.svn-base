<?php
class kbasic {
	
	/*
	 * 数据库指针
	 * 
	 */
	protected $db = null;

	/*
	 * 构造函数
	 * 
	 */
	public function __construct() {
		$this->db = domain::main()->GroupDBConn('mysql');
	}
	
// 	public function getLuckStarBalance($uin){
// 		$sql = "SELECT luckstar FROM kkyoo_common.luckstar_balance WHERE uin={$uin}";
// 		$starnum = intval($this->db->get_var($sql));
// 		return array('Flag'=>100,'Result'=>$starnum);
// 	}
	/*
	public function addLuckStarBalance($uin,$num){
		$sql = "SELECT luckstar FROM kkyoo_common.luckstar_balance WHERE uin={$uin}";
		$starnum = intval($this->db->get_var($sql)) + $num;
		$sql = "REPLACE INTO kkyoo_common.luckstar_balance(uin,luckstar)VALUES({$uin},{$starnum})";
		$rst = $this->db->query($sql);
		if($rst){
			return array('Flag'=>100,'FlagString'=>'ok');
		}else{
			return array('Flag'=>101,'FlagString'=>'fail');
		}
	}*/
	
// 	public function cutLuckStarBalance($uin,$num){
// 		$sql = "SELECT luckstar FROM kkyoo_common.luckstar_balance WHERE uin={$uin}";
// 		$starnum = intval($this->db->get_var($sql));
// 		if($starnum < $num){
// 			return array('Flag'=>101,'FlagString'=>'幸运星余额不足！');
// 		}
// 		$starnum -= $num;
// 		$sql = "REPLACE INTO kkyoo_common.luckstar_balance(uin,luckstar)VALUES({$uin},{$starnum})";
// 		$rst = $this->db->query($sql);
// 		if($rst){
// 			return array('Flag'=>100,'FlagString'=>'ok');
// 		}else{
// 			return array('Flag'=>101,'FlagString'=>'fail');
// 		}
// 	}
	
	/*
	 * 获取用户信息
	 * 
	 */
	public function getUserInfo($uin) {
		$uin = intval($uin);
		$sql = "SELECT `name` AS `Name`,nick AS Nick,gender AS Gender,hobby_sum AS Hobby,birthday AS Birthday,cityname AS Cityname,city AS City FROM ".DB_NAME_IM.".basic_tbl WHERE uin={$uin}";
		$array = $this->db->get_row($sql,'ASSOC');
		if(empty($array)) return array('Flag'=>102,'FlagString'=>'获取用户信息失败');
		//$sql = "SELECT * FROM ".DB_NAME_IM.".sso_user_relate WHERE uin={$uin}";
		//$uid = $this->db->get_var($sql);
		//if(empty($uid)) return array('Flag'=>102,'FlagString'=>'获取用户信息失败');
		//$sql = "SELECT nick AS Nick FROM ".DB_NAME_IM.".username WHERE uid={$uid}";
		//$userinfo = $this->db->get_row($sql,'ASSOC');

	//	$vip_info				   = $this->get_vipinfo($uin);
	//	$grade_info				   = $this->get_gradeinfo($uin);
	//	$array['Nick']			   = $userinfo['Nick'];
		$array['Uin']			   = $uin;
	/*	$array['Vip']			   = $vip_info['VipGrade'];
		$array['VipBuyTime']       = $vip_info['VipBuyTime'];
		$array['UseExpire']		   = $vip_info['UseExpire'];
		$array['VipExpire']		   = $vip_info['VipExpire'];
		$array['VipSpare']		   = $vip_info['VipSpare'];
		$array['UinSurplus']	   = $grade_info['UinSurplus'];
		$array['UinGradeSecond']   = $grade_info['UinGradeSecond'];
		$array['CurGradeSecond']   = $grade_info['CurGradeSecond'];
		$array['NextGradeSecond']  = $grade_info['NextGradeSecond'];
		$array['NowGradeSecond']   = $grade_info['NowGradeSecond'];*/
		$userinfo = array('Flag'=>100,'FlagString'=>'获取用户信息成功');
		$userino = array_merge($userinfo,$array);
		return $userino;
	}
	
	/*
	 * 更新用户信息
	 *
	 */
	/*
	public function set_uininfo($array) {
		if(is_array($array) && array_key_exists('Uin',$array)) {
			foreach((array)$array as $key => $val) {
				if(strtolower($key) == 'level') {
					if(empty($val)) {
						$val = $this->get_gradeinfo($array['Uin'],'UinGrade');
					}
					$val = intval($val);
				}
				if(strtolower($key) == 'vip') {
					if(empty($val)) {
						$val = $this->get_vipinfo($array['Uin'],'VipGrade');
					}
					$val = intval($val);
				}
				if($key != 'Uin' && !empty($key) && $val != '') {
					$body .= '`'.strtolower($key).'` = "'.$val.'",';
				}
			}
			$query = $this->db->query('UPDATE right_tbl SET '.substr($body,0,-1).' WHERE uin = '.$array['Uin'].';');
			if($query) {
				$array = array(
					'Flag'=>100,
					'FlagString'=>'用户信息更新成功'
				);
			} else {
				$array = array(
					'Flag'=>101,
					'FlagString'=>'用户信息更新失败'
				);
			}
		} else {
			$array = array(
				'Flag'=>102,
				'FlagString'=>'参数有误'
			);
		}
		return $array;
	}*/
	
	/*
     * 更新用户等级
     * 
     * @access public
     * @param string $uin 用户ID
	 * @param string $grade_date 等级时间戳
     * @return string
     */
    /*
	public function update_grade($uin,$grade_date,$begin_date,$end_date) {
		if($uin > 0 && $grade_date > 0 && $begin_date > 0 && $end_date > 0) {
			$this->db->start_transaction();
			$cur_grade = $this->db->get_var('SELECT gradeDate FROM kkyoo_common.tbl_grade_gradedate WHERE uin = "'.$uin.'";');
			$uin_grade = $this->db->get_var('SELECT grade FROM kkyoo_common.tbl_grade_gradeconfig WHERE '.intval($cur_grade + $grade_date).' BETWEEN beginTotal AND endTotal LIMIT 1;');
			if($cur_grade > 0) {
				$sql = 'UPDATE kkyoo_common.tbl_grade_gradedate SET gradeDate = gradeDate + '.$grade_date.',date = NOW() WHERE uin = "'.$uin.'";';
			} else {
				$sql = 'INSERT INTO kkyoo_common.tbl_grade_gradedate(uin,gradeDate,date) VALUES("'.$uin.'","'.$grade_date.'",NOW());';
			}
			$query1 = $this->db->query($sql);
			$mongo_array = array(
				'db'=>ENV_GET('ENV_THEME_PATH').'_kbasic',
				'table'=>'UserGrade',
				'record'=>array(
					'uin'=> (string)$uin,
					'grade'=> (string)$uin_grade,
					'grade_date'=>(string) $grade_date,
					'begin_date'=>(string) $begin_date,
					'end_date'=> (string)$end_date,
					'uptime'=> time()
				)
			);
			$json_obj = json_decode(socket_request('http://'.ENV_GET('ENV_NODEJS_IP').'/?cmd='.json_encode($mongo_array),'',true,ENV_GET('ENV_SOCKET_TIME')));
			
			if($query1) {
				$this->db->commit();
				$this->set_uininfo(array('Uin'=>$uin,'Level'=>$uin_grade));
				$array = array(
					'Flag'=>'100',
					'FlagString'=>'更新用户等级成功',
					'Uin'=>$uin,
					'UinGrade'=>$uin_grade
				);
			} else {
				$this->db->rollback();
				$array = array(
					'Flag'=>'101',
					'FlagString'=>'更新用户等级失败',
					'Uin'=>$uin,
					'UinGrade'=>$uin_grade
				);
			}
		} else {
			$array = array(
				'Flag'=>'102',
				'FlagString'=>'参数有误',
				'Uin'=>0,
				'UinGrade'=>0
			);
		}
		return $array;
	}*/
	/*
	public function checkVip($vip_uin,$vip_grade){
		$hasGrade = $this->get_vipinfo($vip_uin,'VipGrade','count');
		if($hasGrade > 0) {
			//是否过期
			$haveGrade = $this->get_vipinfo($vip_uin,'VipGrade');
			//不过期就加天数不更新时间
			if($haveGrade > 0){
				//判断等级是否一样
				if($haveGrade != $vip_grade){
					$status = 4;//等级不一样
				}else{
					$status = 3;//等级一样
				}
			}else{
				//已经过期
				$status = 2;
			}
			
		} else {
			//没有会员
			$status = 1;
		}
		return array('Flag'=>100,'FlagString'=>'检测是否可以买会员','Status'=>$status);
	}
	*/
	/*
	 * 开通/升级/续费会员
	 *
	 */
	
	// public function set_uinvip($uin,$vip_uin,$vip_grade,$use_expire,$buy_expense,$pay_expense) {
	// 	if($uin > 0 && $vip_uin > 0 && $vip_grade > 0 && $use_expire > 0 && $buy_expense >=0 && $pay_expense >= 0) {
	// 		$rst = $this->checkVip($vip_uin,$vip_grade);
	// 		$status = $rst['Status'];
	// 		if($status == 1){
	// 			//不是会员就添加
	// 			$sql1 = 'INSERT INTO kkyoo_common.tbl_vip_info(uin,vip_uin,vip_grade,use_expire,buy_expense,pay_expense,uptime) VALUES("'.$uin.'","'.$vip_uin.'","'.$vip_grade.'","'.$use_expire.'","'.$buy_expense.'","'.$pay_expense.'","'.time().'");';
	// 			$flagstring = '会员开设成功';
	// 		}elseif($status == 2){
	// 			//已经过期，更新时间
	// 			$sql1 = 'UPDATE kkyoo_common.tbl_vip_info SET uin="'.$uin.'",vip_grade ="'.$vip_grade.'",use_expire = '.$use_expire.',buy_expense = "'.$buy_expense.'",pay_expense = "'.$pay_expense.'",uptime="'.time().'" WHERE vip_uin = "'.$vip_uin.'";';
	// 			$flagstring = '会员续费成功';
	// 		}elseif($status == 3){
	// 			//不过期就加天数不更新时间
	// 			$sql1 = 'UPDATE kkyoo_common.tbl_vip_info SET uin="'.$uin.'",vip_grade ="'.$vip_grade.'",use_expire = use_expire+'.$use_expire.',buy_expense = "'.$buy_expense.'",pay_expense = "'.$pay_expense.'" WHERE vip_uin = "'.$vip_uin.'";';
	// 			$flagstring = '会员续费成功';
	// 		}elseif($status == 4){
	// 			//跨会员等级，更新等级，更新时间
	// 			$sql1 = 'UPDATE kkyoo_common.tbl_vip_info SET uin="'.$uin.'",vip_grade ="'.$vip_grade.'",use_expire = '.$use_expire.',buy_expense = "'.$buy_expense.'",pay_expense = "'.$pay_expense.'",uptime="'.time().'" WHERE vip_uin = "'.$vip_uin.'";';
	// 			$flagstring = '转会员成功';
	// 		}
			/*
			$hasGrade = $this->get_vipinfo($vip_uin,'VipGrade','count');
			if($hasGrade > 0) {
				//是否过期
				$haveGrade = $this->get_vipinfo($vip_uin,'VipGrade');
				//不过期就加天数不更新时间
				if($haveGrade > 0){
					//判断等级是否一样
					if($haveGrade != $vip_grade){
						return array('Flag'=>103,'FlagString'=>'会员没有过期，不能进行该操作!');
					}
					$sql1 = 'UPDATE kkyoo_common.tbl_vip_info SET uin="'.$uin.'",vip_grade ="'.$vip_grade.'",use_expire = use_expire+'.$use_expire.',buy_expense = "'.$buy_expense.'",pay_expense = "'.$pay_expense.'" WHERE vip_uin = "'.$vip_uin.'";';
				}else{
					//已经过期，更新时间
					//if($hasGrade != $vip_grade){
					//	return array('Flag'=>103,'FlagString'=>'会员等级不正确!');
					//}
					$sql1 = 'UPDATE kkyoo_common.tbl_vip_info SET uin="'.$uin.'",vip_grade ="'.$vip_grade.'",use_expire = '.$use_expire.',buy_expense = "'.$buy_expense.'",pay_expense = "'.$pay_expense.'",uptime="'.time().'" WHERE vip_uin = "'.$vip_uin.'";';
				}
				
			} else {
				$sql1 = 'INSERT INTO kkyoo_common.tbl_vip_info(uin,vip_uin,vip_grade,use_expire,buy_expense,pay_expense,uptime) VALUES("'.$uin.'","'.$vip_uin.'","'.$vip_grade.'","'.$use_expire.'","'.$buy_expense.'","'.$pay_expense.'","'.time().'");';
			}*/
			//$sql2 = 'INSERT INTO kkyoo_common.tbl_buyvip_detail(uin,vip_uin,vip_grade,use_expire,buy_expense,pay_expense,uptime) VALUES("'.$uin.'","'.$vip_uin.'","'.$vip_grade.'","'.$use_expire.'","'.$buy_expense.'","'.$pay_expense.'","'.time().'");';
			//$this->db->start_transaction();
			//$query1 = $this->db->query($sql1);
			//$query2 = $this->db->query($sql2);
			//if($query1/* && $query2*/) {
				//$this->db->commit();
				//更新VIP标志
	// 			$this->set_uininfo(array('Uin'=>$vip_uin,'Vip'=>$vip_grade));
	// 			$array = array(
	// 				'Flag'=>100,
	// 				'FlagString'=>$flagstring,
	// 				'Uin'=>$uin,
	// 				'VipUin'=>$vip_uin
	// 			);
	// 		} else {
	// 			//$this->db->rollback();
	// 			$array = array(
	// 				'Flag'=>101,
	// 				'FlagString'=>'会员操作失败',
	// 				'Uin'=>0,
	// 				'VipUin'=>0
	// 			);
	// 		}
	// 	} else {
	// 		$array = array(
	// 			'Flag'=>102,
	// 			'FlagString'=>'参数有误',
	// 			'Uin'=>0,
	// 			'VipUin'=>0
	// 		);
	// 	}
	// 	return $array;
	// }
	
	/*
     * 获取用户等级
     * 
     * @access protected
     * @param string $uin 用户ID
     * @return string
     */
    /*
	protected function get_gradeinfo($uin,$key = '') {
		if($uin > 0) {
			$cur_grade_second = (int)$this->db->get_var('SELECT gradeDate FROM kkyoo_common.tbl_grade_gradedate WHERE uin = "'.$uin.'" LIMIT 1;');
			$uin_grade_second = $this->db->get_var('SELECT grade FROM kkyoo_common.tbl_grade_gradeconfig WHERE '.$cur_grade_second.' BETWEEN beginTotal AND endTotal LIMIT 1;');
			$now_grade_second = $this->db->get_var('SELECT beginTotal FROM kkyoo_common.tbl_grade_gradeconfig WHERE grade = '.intval($uin_grade_second).' LIMIT 1;');
			$next_grade_second = $this->db->get_var('SELECT beginTotal FROM kkyoo_common.tbl_grade_gradeconfig WHERE grade = '.intval($uin_grade_second).'+1 LIMIT 1;');
			if($next_grade_second < $now_grade_second) {
				$uin_surplus = 0;
			} else {
				$uin_surplus = ($next_grade_second - $cur_grade_second) / 3600;
			}
			$array = array(
				'UinGradeSecond'=>$uin_grade_second,
				'UinSurplus'=>$uin_surplus,
				'CurGradeSecond'=>$cur_grade_second,
				'NextGradeSecond'=>$next_grade_second,
				'NowGradeSecond'=>$now_grade_second
			);
			if(!empty($key)) {
				$result = $array[$key];
			} else {
				$result = $array;
			}
			return $result;
		}
	}*/
	
	/*
     * 获取会员信息
     * 
     * @access protected
     * @param string $uin 用户ID
     * @return string
     */
    /*
    protected function get_vipinfo($uin,$key = '',$type = '') {
		if($uin > 0) {
			if(empty($type)) {
				$where = ' AND use_expire * 86400 + uptime >=UNIX_TIMESTAMP()';
			}
			$row = $this->db->get_row('SELECT vip_uin,vip_grade,uptime,use_expire FROM kkyoo_common.tbl_vip_info WHERE vip_uin = "'.$uin.'"'.$where.' LIMIT 1;');
			if(is_array($row) && !empty($row)) {
				$array = array(
					'VipUin'=>$row['vip_uin'],
					'VipGrade'=>$row['vip_grade'],
					'VipBuyTime'=>$row['uptime'],
					'UseExpire'=>$row['use_expire'],
					'VipExpire'=>$row['use_expire'] * 86400 + $row['uptime'],
					'VipSpare'=>ceil(($row['use_expire'] * 86400 + $row['uptime'] - time()) / 86400)
				);
			}
			if(!empty($key)) {
				$result = $array[$key];
			} else {
				$result = $array;
			}
			return $result;
		}
    }
	*/
	public function getRolesIcon($uin){
		$uin = intval($uin);
		if($uin <= 0) return array('Flag'=>101,'FlagString'=>'参数错误');
		$roles = array();
		//站长
		$isDZ = getChannelType($uin,0,8);
		if($isDZ > 0){
			$roles[] = 10009;
		}
		//室主
		$isRoomer = getChannelType($uin,0,9);
		//$sql = "SELECT COUNT(1) FROM ".DB_NAME_NEW_ROOMS.".rooms  WHERE ownuin={$uin} AND `status`>0";
		//$isRoomer = $this->db->get_var($sql);
		if($isRoomer > 0){
			$roles[] = 10001;
		}
		//艺人
		$isArtist = getChannelType($uin);
		if($isArtist > 0){
			$roles[] = 10005;
		}
		//超管
		/*$sql = "SELECT level FROM ".DB_NAME_NEW_ROOMS.".chatmanager_tbl WHERE uin={$uin} AND flag=1";
		$level = $this->db->get_var($sql);
		if($level > 0){
			$roles[] = ($level==1) ? 10000 : 10008;
		}
		//管理
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_NEW_ROOMS.".roommanager_tbl WHERE uin={$uin}";
		$isAdmin = $this->db->get_var($sql);
		if($isAdmin > 0){
			$roles[] = 10002;
		}
		//vip
		$vip_info = $this->getUserInfo($uin);
		//$vip_info = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$uin)));
		if($vip_info['VipSpare'] > 0){
			if($vip_info['Vip'] == 1){
				$roles[] = 10003;
			}elseif($vip_info['Vip'] == 2){
				$roles[] = 10004;
			}
		}*/
		foreach($roles as $key => $val){
			if($val > 0){
				$roles[$key] = "http://{$_SERVER['HTTP_HOST']}/pic/roleicon/{$val}.png";
			}
		}
		return array('Flag'=>100,'FlagString'=>'成功','RolesIcon'=>$roles);
	}
}
