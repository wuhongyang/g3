<?php
class new_sso
{
	//数据库指针
	protected $db;

	//存储器指针
	protected $storage;

	//唯一令牌
	protected $token;
	protected $token_name = 'USER_LOGIN_TOKEN';
	
	//构造函数
	function __construct($group_id){
		/* 默认值 */
		$this->token = $_COOKIE[$this->token_name];
		
		/* memcache连接创建 */
		$this->storage = cache::connect(config('cache','memcache'));
		
		/* 站配置数据读取 */
		if($group_id != 10000){
			if($group_id > 0){
				$GroupData = domain::main()->GroupKeyVal($group_id);
			}else{
				$GroupData = domain::main()->GroupData();
			}
		}
		
		/* 当前访问站ID */
		if($group_id > 0){
			$this->group_id = $group_id;
		}else{
			$this->group_id = $GroupData['groupid'];
		}
		
		/* 数据库连接创建 */
		if($GroupData['groupid'] > 0){
			$this->db = domain::main()->GroupDBConn("mysql",$GroupData['groupid']);
		}else{
			$this->db = db::connect(config('database','default'));
		}
        $this->platform = array(1=>"email", 2=>"username", 3=>"phone", 4=>"qq");
	}
    
    public function openidLogin($info){
		$sql = "SELECT uin AS Uid,login AS Openid, uptime AS Created, state FROM ".DB_NAME_IM.".new_username WHERE `login`='{$info['openid']}' AND group_id = '{$this->group_id}' LIMIT 1";
		$userinfo = $this->db->get_row($sql,'ASSOC');
        if(empty($userinfo)){ //注册ID
        	return array("Flag"=>101, "FlagString"=>"用户不存在");
        }
		elseif($userinfo['state'] != 1){
            return array('Flag'=>102,'FlagString'=>'用户已被冻结');
        }
        unset($userinfo['state']);
        $userinfo['GroupId'] = $this->group_id;
        $userinfo = $this->getBindUinInfo($userinfo);
        
        $sql = "SELECT platform,login FROM ".DB_NAME_IM.".`new_username` WHERE uin = '".$userinfo['Uid']."' AND group_id = '".$userinfo['GroupId']."'";
        $res = $this->db->get_results($sql, "ASSOC");
        foreach($res as $one){
            if($one['platform'] == 1){
                $userinfo['Email'] = $one['login'];
            }elseif($one['platform'] == 2){
                $userinfo['UserName'] = $one['login'];
            }elseif($one['platform'] == 3){
                $userinfo['Phone'] = $one['login'];
            }
        }

        if($userinfo['Flag'] != 100){
        	return $userinfo; 
        }
        $this->token = strtoupper(md5(uniqid(time()).$userinfo['Uid'].$this->group_id));
		if(!$this->set_storage($userinfo)){
			return array('Flag'=>103,'FlagString'=>'Storage Error');
		}
		$ip = get_ip();
		$sql = "UPDATE ".DB_NAME_IM.".`new_username` set `load_ip` = '".$ip."', `load_time` = '".time()."' WHERE `uin` = '".$userinfo['Uid']."'";
		$this->db->query($sql);

        $userinfo['Token'] = $this->token;
		$userinfo['SessionKey'] = $this->token;
		$userinfo['Flag'] = 100;
		$userinfo['FlagString'] = '登录成功';
		$expire = $info['remember']>0? time()+86400 * 30 : 0;
		//$token = 'USER_LOGIN_TOKEN';
		$this->set_cookie($this->token_name ,$this->token,$expire);
        return $userinfo;
    }

	//获取当前登录状态
	public function getLogin($token = '',$groupid=0,$reset=1){
		$this->group_id = $groupid ? $groupid : $this->group_id;
		if(!empty($token)){
			$this->token = $token;
			$userinfo = $this->get_storage($this->token);
			if(!empty($userinfo) && $reset){
				$this->set_cookie($this->token_name ,$this->token,time()+86400*30);
			}
		}
		else{
			if($this->group_id > 0){
			 	$cookie_arr = $this->http_cookie($this->token_name);
				foreach((array)$cookie_arr as $cookie){
					$userinfo = $this->get_storage($cookie);
					if($userinfo['GroupId'] == $this->group_id){
						$this->token = $cookie;
						break;
					}else{
					   unset($userinfo);
					}
				}
			}else{
			 	$userinfo = $this->get_storage($this->token);
			}
		}
		if(empty($userinfo)){
			return array('Flag'=>103,'FlagString'=>'您还未登录');
		}
		if($userinfo['GroupId'] != $this->group_id && $this->group_id >0 && $userinfo['GroupId'] !=10000){
			return array('Flag'=>102,'FlagString'=>'登录失败');
		}
		// if($this->checkSethonor($userinfo)){
			// $this->del_storage($this->token);
			// return array('Flag'=>103,'FlagString'=>'账号已冻结');
		// }
		$userinfo['Token'] = $this->token;
		$userinfo['Flag'] = 100;
		return $userinfo;
	}

    private function deny_check($deny_list){
        $deny_arr = array();
        foreach((array)$deny_list as $one){
            if(!$one){
                continue;
            }
            $deny_arr[] = $one;
        }
        if(!$deny_arr){
            return false;
        }
        $deny_str = join(",", $deny_arr);
        $sql   = "SELECT COUNT(*) FROM ".DB_NAME_IM.".`uin_filter` WHERE content IN (".$deny_str.")";
        $exist = $this->db->get_var($sql);
        if($exist){
            return true;
        }else{
            return false;
        }
    }

	//用户登录验证
	public function userLogin($user,$password,$remember=0,$groupid=0,$is_uin=0,$deny_list){
	    if($this->deny_check($deny_list)){
           return array('Flag'=>101, 'FlagString'=>'用户被封杀');
	    }
		$this->group_id = $groupid ? $groupid : $this->group_id;
		$pass = $this->password($password);
        if($is_uin){
            $sql = "SELECT a.uin AS Uid, a.login as Login, a.uptime AS Created, b.passwd AS pass,b.msg_id AS lastMsgID,b.pic FROM ".DB_NAME_IM.".new_username a LEFT JOIN ".DB_NAME_IM.".basic_tbl b ON a.uin = b.uin WHERE a.`uin`='{$user}' AND a.group_id = '".$this->group_id."' AND a.state=1 LIMIT 1";
        }else{
            $sql = "SELECT a.uin AS Uid, a.login as Login, a.uptime AS Created, b.passwd AS pass,b.pic FROM ".DB_NAME_IM.".new_username a LEFT JOIN ".DB_NAME_IM.".basic_tbl b ON a.uin = b.uin WHERE a.`login`='{$user}' AND a.group_id = '".$this->group_id."' AND a.state=1 LIMIT 1";
        }
		$info = $this->db->get_row($sql,'ASSOC');
		if(empty($info)){
			return array('Flag'=>103,'FlagString'=>'用户不存在或被冻结');
		}
		if($pass != $info['pass']){
			return array('Flag'=>102,'FlagString'=>'用户名或密码错误');
		}
		unset($info['pass']);
		$info['Openid'] = $user;
		$info['GroupId'] = $this->group_id;

		$userinfo = $this->getBindUinInfo($info);
		if($userinfo['Flag'] != 100){
			return $userinfo;
		}
		//if($this->checkSethonor($userinfo)){
		//	return array('Flag'=>104,'FlagString'=>'账号已冻结');
		//}
		//设置登录信息
        $this->token = strtoupper(md5(uniqid(time()).$userinfo['Uid'].$this->group_id));
        $res = $this->getUser('',$userinfo['Uin']);
        if($res['Flag'] != 100){
        	return array("Flag"=>105, "FlagString"=>"系统错误");
        }
        $userinfo['Email'] = $res['Email'];
        $userinfo['Phone'] = $res['Phone'];
        $userinfo['UserName'] = $res['UserName'];
        $userinfo['QQCount'] = $res['QQCount'];
		if(!$this->set_storage($userinfo)){
			return array('Flag'=>103,'FlagString'=>'Storage Error');
		}
		// 更新合作区, 最后登录IP, IP所在城市, 最后登录时间
		$ip   = get_ip();
// 		$result = httpPOST(REGION_API_PATH, array("extparam"=>array("ip"=>$ip, "Tag"=>"Address")));
// 		$city = $result['province'].$result['city'];
// 		$comm = 1;
		$sql = "UPDATE ".DB_NAME_IM.".`new_username` set `load_ip` = '".$ip."', `load_time` = '".time()."' WHERE `uin` = '".$userinfo['Uid']."'";
		$this->db->query($sql);
		
		$userinfo['Token'] = $this->token;
		$userinfo['SessionKey'] = $this->token;
		$userinfo['Flag'] = 100;
		$userinfo['FlagString'] = '登录成功';
		$expire = $remember>0? time()+86400 * 30 : 0;
		//$token = 'USER_LOGIN_TOKEN';
		$this->set_cookie($this->token_name ,$this->token,$expire);
		return $userinfo;
	}
	
	//发送验证码
	public function sendCode($phone,$module){
		if(empty($phone) || !preg_match('/^(13|14|15|18)\d{9}$/',$phone)){
			return array('Flag'=>101,'FlagString'=>'参数不正确');
		}
		//检测手机是否已经被验证
		$userInfo = $this->getUser($phone);
		if($userInfo['Flag'] == 100){
			return array('Flag'=>102,'FlagString'=>'该手机号已经被绑定，请换其他手机认证！');
		}
		/*
		$sql = "SELECT COUNT(1) FROM ".DB_NAME_IM.".username WHERE `phone` = '{$phone}'";
		$count = $this->db->get_var($sql);
		if($count > 0){
			return array('Flag'=>102,'FlagString'=>'该手机号码已经通过认证，不能重复申请认证！');
		}*/
		$sql = "SELECT `val`,`uptime` FROM ".DB_NAME_IM.".user_tmp WHERE `get`='{$phone}' AND group_id='{$this->group_id}'";
		$codeInfo = $this->db->get_row($sql);
		$uptime = time()+3600;
		$code   = rand(100000,999999);
		//写入验证码
		if(empty($codeInfo)){
			$sql = "INSERT INTO ".DB_NAME_IM.".user_tmp VALUES('{$phone}','{$code}',{$uptime},'{$this->group_id}')";
		}else{
			$sql = "UPDATE ".DB_NAME_IM.".user_tmp SET `val`='{$code}',uptime={$uptime} WHERE `get`='{$phone}' AND group_id='{$this->group_id}'";
		}
		$rst = $this->db->query($sql);
		if(!$rst){
			return array('Flag'=>103,'FlagString'=>'服务器异常');
		}
		$msg = "您的校验码是：{$code}，有效时间1小时，如非本人操作，请不要将此校验码告诉他人【VV酷通行证】";
		//发送短信
		$rst = sendSMS($phone,$msg,$module);
		return $rst;
		/*
		if($rst != 100){
			return array('Flag'=>104,'FlagString'=>'服务器忙，请稍后再试！');
		}
		return array('Flag'=>100,'FlagString'=>'发送成功');
		*/
	}
	
	//绑定手机
	public function bindPhone($email,$phone,$bindcode,$uid,$openid){
		//检测手机是否已经被验证
		$userInfo = $this->getUser($phone);
		if($userInfo['Flag'] == 100){
			return array('Flag'=>102,'FlagString'=>'该手机号码已经通过认证，不能重复申请认证！');
		}
		//验证
		$sql = "SELECT `val`,`uptime` FROM ".DB_NAME_IM.".user_tmp WHERE `get`='{$phone}' AND group_id='{$this->group_id}'";
		$codeInfo = $this->db->get_row($sql);
		if(!$codeInfo){
			return array('Flag'=>102,'FlagString'=>'请点击获取验证码');
		}
		if($codeInfo['uptime'] < time()){
			return array('Flag'=>102,'FlagString'=>'验证码已过期，请重新获取');
		} 
		if($codeInfo['val'] != $bindcode){
			return array('Flag'=>102,'FlagString'=>'验证码不正确');
		}
		//手机绑定
		$rst = $this->bindUser($email,$phone,$uid,$openid,3);
		//删除临时表记录
		$sql = "DELETE FROM ".DB_NAME_IM.".user_tmp WHERE `get`='{$phone}' AND group_id='{$this->group_id}'";
		$this->db->query($sql);
		return $rst;
	}

	//绑定帐号
	public function bindUser($user,$bind,$uid,$openid,$platform){
		if(!$uid){ 
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		
		$userinfo = $this->getUser($user,$uid);
		if($userinfo['Flag'] != 100){
			return array('Flag'=>104,'FlagString'=>'帐号不存在');
		}
		$sql = "SELECT `login` FROM ".DB_NAME_IM.".new_username WHERE `login` = '".$bind."' AND group_id = '".$this->group_id."' LIMIT 1";
		$exist = $this->db->get_var($sql);
		if($exist){
			return array("Flag"=>102, "FlagString"=>$bind."已经被绑定");
		}
		$sql = "SELECT `id` FROM ".DB_NAME_IM.".new_username WHERE uin = '".$userinfo['Uid']."' AND platform = '".$platform."' AND group_id = '".$this->group_id."'";
		$id = $this->db->get_var($sql);
		if($id){
			$sql = "UPDATE ".DB_NAME_IM.".new_username SET `login` = '".$bind."' WHERE id = '".$id."'";
			$rst = $this->db->query($sql);
		}else{
			$this->db->start_transaction();
			$col = "`uin`,`platform`,`login`,`group_id`,`name`,`qq`,`idcard`,`uptime`,`state`,`load_ip`,`load_time`";
			$sql = "INSERT INTO ".DB_NAME_IM.".new_username(".$col.") (SELECT ".$col." FROM ".DB_NAME_IM.".new_username WHERE uin = '".$uid."' AND group_id = '".$this->group_id."' LIMIT 1)";
			$rst1 = $this->db->query($sql);
			$sql = "UPDATE ".DB_NAME_IM.".new_username SET `login` = '".$bind."',`platform` = '".$platform."' WHERE uin = '".$uid."' AND  group_id = '".$this->group_id."' LIMIT 1";
			$rst2 = $this->db->query($sql);
			if(!$rst1 || !$rst2){
				$this->db->rollback();
				return array('Flag'=>106,'FlagString'=>'系统错误');
			}else{
				$this->db->commit();
			}
		}
		
		$userinfo = $this->getLogin();
		
		$userinfo[ucfirst($this->platform[$platform])] = $bind;
		$this->set_storage($userinfo);
		return array('Flag'=>100,'FlagString'=>'成功');
	}

	//注册通行证帐号
	public function regPassport($user,$pass,$platform,$age=0,$nick='',$gender=1,$uid=0,$province='',$city='',$area=''){
		
        if(!in_array($platform, array_keys($this->platform))){
			return array("Flag"=>102, "FlagString"=>"参数错误");
		}
		//if(empty($nick)) $nick = $user;
		if($uid < 1 ){
			$res = $this->getLastuin();
			$uid = $res['LastUin'];
		}
        if($user){
    		$userinfo = $this->getUser($user,$uid);
    		if($userinfo['Flag'] == 100){
    			return array('Flag'=>102,'FlagString'=>'用户已存在');
    		}
		}else{
            $user = "IM_".$uid;
		}
        
		$data = array('platform'=>$platform,'uin'=>$uid,'login'=>$user,'uptime'=>time(),'group_id'=>$this->group_id);
		$key = implode('`,`',array_keys($data));
		$val = implode("','",array_values($data));
		$sql = "INSERT INTO ".DB_NAME_IM.".new_username (`{$key}`)VALUES('{$val}')";
		if(!$this->db->query($sql)){
			return array('Flag'=>103,'FlagString'=>'系统错误');
		}
        $userinfo = array(
			'Nick'=>$nick,
			'Gender'=>$gender,
			'Uid'=>$uid,
			'GroupId'=>$this->group_id,
            'Age'=>$age,
            'Province'=>$province,
            'City'=>$city,
            'Area'=>$area,
		);
		$userinfo = $this->getBindUinInfo($userinfo, $pass);
		return $userinfo;
		//return array('Flag'=>100,'FlagString'=>'成功');
	}

	public function openidReg($data){
		if(empty($data['openid'])){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}

		//是否存在
		$sql = "SELECT id FROM ".DB_NAME_IM.".`new_username` WHERE login='{$data['openid']}' AND group_id={$this->group_id} AND platform=4";
		if($this->db->get_var($sql) > 0){
			return array('Flag'=>102,'FlagString'=>'QQ已经绑定');
		}

		//获取UIN  getLastuin
		$res = $this->getLastuin();
		$uin = $res['LastUin'];

		//插入new_username
		$this->db->start_transaction();
		$time = time();
		$sql = "INSERT INTO ".DB_NAME_IM.".new_username(`uin`,`login`,`platform`,`group_id`,`uptime`) VALUES({$uin},'{$data['openid']}',4,{$this->group_id},{$time})";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'注册QQ失败');
		}

		//插入basic_tbl
		$data['nick'] = htmlspecialchars(urldecode($data['nick']));
		$rst = $this->registerUin($uin,$this->group_id,$data['nick'],$password);
		if($rst['Flag'] != 100){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'注册QQ失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'OK','Uin'=>$uin);
	}

	public function qqBind($data){
		if(empty($data['openid'])){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$username_pattern = '/^[a-zA-Z0-9_]{2,15}$/';
		if(preg_match($username_pattern, $data['username']) === 0){
			return array('Flag'=>101,'FlagString'=>'用户名错误');
		}
		if(is_numeric($data['username'])){
			return array('Flag'=>101,'FlagString'=>'用户名不能是纯数字');
		}
		if(empty($data['password']) || $data['password'] != $data['repassword']){
			return array('Flag'=>101,'FlagString'=>'密码错误');
		}
		$data['password'] = $this->password(md5($data['password']));

		if(empty($_SESSION)){
			session_start();
		}
		if($data['checkcode'] != $_SESSION['captcha']){
			return array('Flag'=>101,'FlagString'=>'验证码错误');
		}
		$userinfo = $this->getUser($data['username']);
		if($userinfo['Flag'] == 100){
			return array('Flag'=>102,'FlagString'=>'用户已存在');
		}
		$userinfo = $this->getUser($data['openid']);
		if($userinfo['Flag'] != 100){
			return array('Flag'=>103,'FlagString'=>'无法绑定QQ，缺少必要参数');
		}
		$sql = "SELECT id FROM ".DB_NAME_IM.".new_username WHERE group_id={$this->group_id} AND uin={$userinfo['Uid']} AND `platform`=2";
		if($this->db->get_var($sql) > 0){
			return array('Flag'=>104,'FlagString'=>'该已有绑定账号，不能绑定');
		}
		$this->db->start_transaction();
		$time = time();
		$sql = "INSERT INTO ".DB_NAME_IM.".new_username(`uin`,`login`,`platform`,`group_id`,`uptime`) VALUES({$userinfo['Uid']},'{$data['username']}',2,{$this->group_id},{$time})";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'绑定失败');
		}
		$sql = "UPDATE ".DB_NAME_IM.".basic_tbl SET passwd='{$data['password']}' WHERE uin={$userinfo['Uid']} LIMIT 1";
		if(!$this->db->query($sql)){
			$this->db->rollback();
			return array('Flag'=>103,'FlagString'=>'绑定失败');
		}
		$this->db->commit();
		return array('Flag'=>100,'FlagString'=>'ok','Uin'=>$userinfo['Uid']);
	}
	/*
	function openidReg($user,$pass,$platform,$openid,$picurl=''){
		if(!$openid || $platform == 4 || !in_array($platform, array_keys($this->platform))){
			return array("Flag"=>101, "FlagString"=>"参数错误");
		}
		$sql = "SELECT `uin` FROM ".DB_NAME_IM.".`new_username` WHERE login = '".$user."' AND group_id = ".$this->group_id." AND platform = '".$platform."'";
		$uin = $this->db->get_var($sql);
		
		if($uin){
			$f_uin = $uin;
			$userinfo = $this->getuser('',$uin);
			if(!empty($userinfo['QQCount'])){
				return array('Flag'=>102,'FlagString'=>'已被其他QQ绑定');
			}
		}else{
			$res = $this->getLastuin();
			$f_uin = $res['LastUin'];
		}
		//添加QQ账号
		$this->db->start_transaction();
		$data = array('platform'=>4,'uin'=>$f_uin,'login'=>$openid,'uptime'=>time(),'group_id'=>$this->group_id);
		$key = implode('`,`',array_keys($data));
		$val = implode("','",array_values($data));
		$sql = "INSERT INTO ".DB_NAME_IM.".new_username (`{$key}`)VALUES('{$val}')";
		$done = $this->db->query($sql);
		
		if($uin){
			//存在账号，直接绑定
			$sql = "SELECT passwd FROM ".DB_NAME_IM.".`basic_tbl` WHERE uin = '".$f_uin."'";
			$passwd = $this->db->get_var($sql);
			if($passwd == $this->password($pass)){
				if($done){
					$this->db->commit();
					$userinfo = array(
						'Uid'=>$f_uin,
						'GroupId'=>$this->group_id,
					);
					return $this->getBindUinInfo($userinfo);
				}else{
					$this->db->rollback();
					return array("Flag"=>102, "FlagString"=>"绑定失败");
				}
			}else{
				$this->db->rollback();
				return array("Flag"=>103, "FlagString"=>"绑定账号输入密码错误");
			}
		}else{
			//不存在账号的情况，注册UIN
			$done2 = $this->registerUin($f_uin, $this->group_id, '', $pass);
			//添加用户账号
			$data = array('platform'=>$platform,'uin'=>$f_uin,'login'=>$user,'uptime'=>time(),'group_id'=>$this->group_id);
			$key = implode('`,`',array_keys($data));
			$val = implode("','",array_values($data));
			$sql = "INSERT INTO ".DB_NAME_IM.".new_username (`{$key}`)VALUES('{$val}')";
			$done3 = $this->db->query($sql);
			
			if($done && $done2['Flag']==100 && $done3){
				$this->db->commit();
				if($picurl){
					$bytes = socket_request(urldecode($picurl));
					if(strlen($bytes) > 10){
						$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'uin','Index'=>$f_uin['Uin']);
						socket_request(UPLOAD_API_PATH,$opt,true,600);
					}	
				}
				$userinfo = array(
					'Uid'=>$f_uin,
					'GroupId'=>$this->group_id,
				);
				return $this->getBindUinInfo($userinfo);
			}else{
				$this->db->rollback();
				return array("Flag"=>102, "FlagString"=>"注册失败");
			}
		}
	}*/
	
	//重置密码
	public function resetPassword($user, $oldpass ,$pass, $is_uin){
// 		if( ! $usertype = getUserType($user)) return array('Flag'=>101,'FlagString'=>'用户名类型错误');
		$oldpass = $this->password($oldpass);
        if($is_uin){
            $sql = "SELECT uin,passwd FROM ".DB_NAME_IM.".basic_tbl where uin = '".$user."' AND group_id = '".$this->group_id."'";
        }else{
            $sql = "SELECT uin,passwd FROM ".DB_NAME_IM.".basic_tbl where uin = (SELECT uin FROM ".DB_NAME_IM.".new_username WHERE `login` = '{$user}' AND `group_id` = '{$this->group_id}' LIMIT 1)";
        }
        $res = $this->db->get_row($sql);
		$passwd = $res['passwd'];
		$uin = $res['uin'];
		if(!$passwd){
			return array('Flag'=>103,'FlagString'=>'不存在该用户');
		}
		if($oldpass != $passwd){
			return array('Flag'=>103,'FlagString'=>'原密码错误');
		}
		$pass = $this->password($pass);
		$sql = "UPDATE ".DB_NAME_IM.".basic_tbl SET passwd = '".$pass."' WHERE uin = '".$uin."'";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'系统错误');
		}
		return array('Flag'=>100,'FlagString'=>"修改密码成功");
	}

	//编辑密码
	public function editPassword($uid, $pass){
		$pass = $this->password($pass);
		$sql = "UPDATE ".DB_NAME_IM.".basic_tbl SET `passwd`='{$pass}' WHERE `uin`='{$uid}'";
		if(!$this->db->query($sql)){
			return array('Flag'=>102,'FlagString'=>'系统错误');
		}
		return array('Flag'=>100);
	}
	
	//UIN是否存在
	public function uinExist($uin){
		$uin = intval($uin);
		$sql = "SELECT uin FROM ".DB_NAME_IM.".basic_tbl WHERE uin={$uin} LIMIT 1";
		if($this->db->get_var($sql) > 0){
			return array('Flag'=>100,'FlagString'=>'用户ID存在');
		}
		return array('Flag'=>101,'FlagString'=>'用户ID已被绑定','Uin'=>$uin);
	}

	//用户出退
	public function userLogout(){
		$this->set_cookie($this->token_name ,'',-1);
		//站退出
		if(!empty($_COOKIE['DP_LOGIN_TOKEN'])){
			$this->set_cookie('DP_LOGIN_TOKEN','',-1);
		}
		return array('Flag'=>100);
	}

	//注册UIN
	public function registerUin($uin,$groupid,$nick,$password,$age,$province,$city,$area){
		if(empty($uin)){
			$uin = $this->getLastuin();
			if($uin['Flag'] != 100){
				return array('Flag'=>102,'FlagString'=>'无可用UIN');
			}
			$uin = $uin['LastUin'];
		}
		if(empty($nick)){
			$nick = $uin;
		}
		$sql = 'INSERT INTO '.DB_NAME_IM.'.basic_tbl (uin,nick,group_id,`passwd`,`age`,`province`,`city`,`area`,`msg_id`)
         VALUES ("'.$uin.'","'.$nick.'","'.$groupid.'","'.$this->password($password).'",
         "'.intval($age).'","'.$province.'","'.$city.'","'.$area.'",IFNULL((SELECT MAX(id) FROM '.DB_NAME_NEW_ROOMS.'.broadcast), 0));';
		$query1 = $this->db->query($sql);
		if($query1){
			$result = array('Flag'=>100,'FlagString'=>'注册成功','Uin'=>$uin,'Nick'=>$nick);
		}else{
			$result = array('Flag'=>101,'FlagString'=>'注册失败','Uin'=>0,'Nick'=>'');
		}
		return $result;
	}

	//获取最后UIN
	public function getLastuin(){
		$uin = $this->db->get_var('SELECT MAX(uin) FROM '.DB_NAME_IM.'.basic_tbl');
		$uin = $uin < 10000? 10000 : $uin +1;
		$array = array('Flag'=>100,'FlagString'=>'成功','LastUin'=>$uin);
		return $array;
	}

	/*
	 *  获取用户数量
	 */
	public function countUser(){
		$table = DB_NAME_IM.'.new_username';
		$sql = "SELECT count(DISTINCT `uin`) FROM {$table}";
		return array('Flag'=>100,'FlagString'=>'成功','count'=>$this->db->get_var($sql));
	}
	
	//用户是否存在
	public function getUser($user,$uid=0,$state=true,$group_id=''){
		$where = "`login`='{$user}'";
		if($uid > 0){
			$where = " uin = {$uid} ";
		}
		if($state !== true){
			$where .= " AND `state`={$state}";
		}
		if(!$group_id){
			$group_id = $this->group_id;
		}
		$where .= " AND group_id = '{$group_id}'";
		$sql = "SELECT * FROM ".DB_NAME_IM.".`new_username` WHERE {$where}";
		$info = $this->db->get_results($sql,'ASSOC');
		if(empty($info)){
			return array('Flag'=>101,'FlagString'=>'用户不存在');
		}else{
			if($uid > 0){
				$email = "";
				$phone = "";
				$username = "";
				$qq = "";
				foreach($info as $one){
					switch($this->platform[$one['platform']]){
						case 'email':
							$email = $one['login'];
							break;
						case 'username':
							$username = $one['login'];
							break;
						case 'phone':
							$phone = $one['login'];
							break;
						case 'qq':
							$qq = $one['login'];
							break;
					}
				}
				return array(
					'Flag'=>100,
					'FlagString'=>'ok',
					'ID'=>$info[0]['id'],
					'Uid'=>$info[0]['uin'],
					'Email'=>$email,
					'Phone'=>$phone,
					'UserName'=>$username,
					'Name'=>$info[0]['name'],
					'IdCard'=>$info[0]['idcard'],
					'QQCount'=>$qq,
					'qq'=>$info[0]['qq'],
					'uptime'=>$info[0]['uptime'],
					'load_time'=>$info[0]['load_time'],
					'permanent_city'=>$info[0]['permanent_city']
				);
			}else{
				return array(
					'Flag'=>100,
					'FlagString'=>'ok',
					'ID'=>$info[0]['id'],
					'Uid'=>$info[0]['uin'],
					'Name'=>$info[0]['name'],
					'IdCard'=>$info[0]['idcard'],
					'qq'=>$info[0]['qq'],
					'Platform'=>$info[0]['platform'],
					'uptime'=>$info[0]['uptime'],
					'load_time'=>$info[0]['load_time'],
					'permanent_city'=>$info[0]['permanent_city']
				);
			}
			
		}
	}
	
	//取得通行证绑定的UIN
	public function getUserInfo($uid){
		if($uid > 0){
			$result['Uin'] = $uid;
			$result['IsUse'] = 1;
		}
		return $result;
	}
	
	//根据uin获得基本资料
	public function getUserBasicForUin($uin){
		if((!is_numeric($uin)||$uin<=0)){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$sql="SELECT * FROM ".DB_NAME_IM.".basic_tbl WHERE uin=".$uin;
		$info=$this->db->get_row($sql,'ASSOC');
		if(empty($info)){
			return array('Flag'=>102,'FlagString'=>'没有这个用户');
		}
		return array('Flag'=>100,'FlagString'=>'用户信息','baseInfo'=>$info);
	}
	
	//设置用户登录信息
	private function getBindUinInfo($userinfo, $password=''){
		$sql = "SELECT nick AS Nick,gender AS Gender,group_id AS GroupId,uin AS Uin,pic AS face,nick AS nickname,
        country,province,city,region AS basicregion,college AS basiccollege,branch AS basicbranch,department AS basicdepartment,
        gender,auth,age,email,address,zip_code AS zipcode,phone AS tel,`name`,blood_type AS blood,college,profession,homepage,introduction AS intro
        From ".DB_NAME_IM.".basic_tbl WHERE uin = '".$userinfo['Uid']."' AND group_id = '".$userinfo['GroupId']."'";
        $uininfo = $this->db->get_row($sql,ASSOC);
        if(empty($uininfo) && $password){//登陆为空,注册账号并绑定关系
			$uininfo = $this->registerUin($userinfo['Uid'],$userinfo['GroupId'],$userinfo['Nick'],$password,$userinfo['Age'],$userinfo['Province'],$userinfo['City'],$userinfo['Area']);
		}
		elseif(empty($uininfo) && !$password){
			return array("Flag"=>102, "FlagString"=>"系统错误");
		}
		$userinfo = array_merge(array('Flag'=>100,'FlagString'=>'ok'),$userinfo,$uininfo);
		return $userinfo;
	}

	//检查用户是否被禁止
	//private function checkSethonor($userinfo){
	//	$sql = "SELECT COUNT(*) FROM ".DB_NAME_IM.".uin_filter WHERE content='{$userinfo['Email']}' OR content='{$userinfo['Phone']}' OR content='{$userinfo['Uin']}' OR content='".get_ip()."'";
	//	return $this->db->get_var($sql);
	//}

	//昵称修改
	public function editNick($param,$info){
		$nick = addslashes(htmlspecialchars($info['Nick']));
		$group_id = $info['GroupId']?(int)$info['GroupId']:$this->group_id;
		$gender = (int)$info['Gender'];
		$name = addslashes(htmlspecialchars($info['Name']));
		$birthday = $info['Birthday'];
		$country = (int)$info['Country'];
		$province = (int)$info['Province'];
		$city = (int)$info['City'];
		$area = (int)$info['Area'];
		$height = (int)$info['Height'];
		$qq = $info['Qq'];
		$phone = $info['Phone'];
		$introduction = addslashes(htmlspecialchars($info['Introduction']));
		$age = (int)$info['Age'];
        
        $pic = (int)$info['Pic'];
        $email = addslashes(htmlspecialchars($info['Email']));
        $address = addslashes(htmlspecialchars($info['Address']));
        $zip_code = addslashes(htmlspecialchars($info['ZipCode']));
        $blood_type = addslashes(htmlspecialchars($info['BloodType']));
        $college = addslashes(htmlspecialchars($info['College']));
        $profession = addslashes(htmlspecialchars($info['Profession']));
        $homepage = addslashes(htmlspecialchars($info['Homepage']));
        $auth = (int)$info['Auth'];
        
		if(empty($nick)){
			return array('Flag'=>101,'FlagString'=>'昵称不能为空');
		}
		if(isset($param['SessionKey'])){
            $userinfo = $this->get_storage($param['SessionKey']);
		}else{
		    $userinfo = $this->getLogin();  
		}
		if(empty($userinfo)){
			return array('Flag'=>102,'FlagString'=>'修改错误');
		}
		$rst = true;
		if($userinfo['Nick'] != $nick){
			$userinfo['Nick'] = $nick;
			$update_col = "  nick = '{$nick}' ,";
		}
		if($gender > 0){
			$userinfo['Gender'] = $gender;
			$update_col .= " gender = {$gender}  ,";
			$role_id = ($gender == 1) ? 10261 : ($gender == 2 ? 10262 : 0);
		}
		if(!empty($birthday)){
			$update_col .= " birthday = '{$birthday}' ,";
		}
		if(!empty($country)){
		    $userinfo['country'] = $country;
			$update_col .= " country = '{$country}' ,";
		}
		if(!empty($province)){
		    $userinfo['province'] = $province;
			$update_col .= " province = '{$province}' ,";
		}
		if(!empty($city)){
		    $userinfo['city'] = $city;
			$update_col .= " city = '{$city}' ,";
		}
		if(!empty($area)){
		    $userinfo['area'] = $area;
			$update_col .= " area = '{$area}'  ,";
		}
		if($height > 0){
			$update_col .= " height={$height} ,";
		}
		if($qq > 0){
			$update_col .= " qq={$qq}  ,";
		}
		if($phone > 0){
		    $userinfo['phone'] = $phone;
			$update_col .= " phone={$phone}  ,";
		}
		if($age > 0){
		    $userinfo['age'] = $age;
			$update_col .= " age={$age}  ,";
		}
		if(!empty($introduction)){
		    $userinfo['intro'] = $introduction;
			$update_col .= " introduction='{$introduction}' ,";
		}
		if(!empty($name)){
			$update_col .= " name='{$name}' ,";
		}
        if(!empty($pic)){
            $userinfo['face'] = $pic;
            $update_col .= " pic='{$pic}' ,";
        }
        if(!empty($email)){
            $userinfo['email'] = $email;
            $update_col .= " email='{$email}' ,";
        }
        if(!empty($address)){
            $userinfo['address'] = $address;
            $update_col .= " address='{$address}' ,";
        }
        if(!empty($zip_code)){
            $userinfo['zipcode'] = $zip_code;
            $update_col .= " zip_code='{$zip_code}' ,";
        }
        if(!empty($blood_type)){
            $userinfo['blood'] = $blood_type;
            $update_col .= " blood_type='{$blood_type}' ,";
        }
        if(!empty($college)){
            $userinfo['basiccollege'] = $college;
            $update_col .= " college='{$college}' ,";
        }
        if(!empty($profession)){
            $userinfo['profession'] = $profession;
            $update_col .= " profession='{$profession}' ,";
        }
        if(!empty($homepage)){
            $userinfo['homepage'] = $homepage;
            $update_col .= " homepage='{$homepage}' ,";
        }
        if(!empty($auth)){
            $userinfo['auth'] = $auth;
            $update_col .= " auth='{$auth}' ,";
        }
		if($userinfo['Uin'] > 0){
			$update_where = " uin = {$userinfo['Uin']}";
		}
		if(empty($update_col) || empty($update_where)){
			return array('Flag'=>102,'FlagString'=>'参数错误');
		}
		$update_col = rtrim($update_col,' ,');
		$sql = "UPDATE ".DB_NAME_IM.".basic_tbl SET {$update_col} WHERE {$update_where} ";
		$rst = $this->db->query($sql);
		if(!$rst){
			return array('Flag'=>102,'Uin'=>$userinfo['Uin'],'FlagString'=>'修改失败');
		}
		
		if($group_id > 0 && $userinfo['GroupId'] < 1){
			$sql = "UPDATE ".DB_NAME_IM.".`basic_tbl` SET `group_id` = '".$userinfo['GroupId']."' WHERE `uin` = '".$userinfo['Uin']."';";
			$rst1 = $this->db->query($sql);
			if(!$rst1){
				return array('Flag'=>102,'Uin'=>$userinfo['Uin'],'FlagString'=>'修改失败');
			}
		}
		if($role_id > 0){
			//添加角色
			$roleData=array(
				'extparam'=>array(
					'Tag'=>'AddGroupRole',
					'GroupId'=>$group_id,
					'Uin'=>$userinfo['Uin'],
					'RoleId'=>$role_id,
					'Ruleid'=>10261
				)
			);
			httpPOST(ROLE_API_PATH,$roleData);
		}
		$this->set_storage($userinfo);
		return array('Flag'=>100,'Uin'=>$userinfo['Uin'],'FlagString'=>'修改成功');
	}

	//修改通行证信息
	public function passPortModify($uid,$fields){
		if(empty($uid)){
			return array('Flag'=>101,'FlagString'=>'Uid不能为空');
		}
		$userinfo = $this->getLogin();
		if(empty($userinfo)){
			return array('Flag'=>102,'FlagString'=>'修改错误');
		}

		$rst = $this->editPassport($uid,$fields);
		if($rst['Flag'] == 100){
			$this->set_storage($userinfo);
			return array('Flag'=>100,'FlagString'=>'修改成功');
		}
		return array('Flag'=>103,'FlagString'=>'修改失败');
	}

	public function editPassport($uin,$fields){
		$set = '';
		foreach($fields as $key => $value){
			$set .= ',`'.$key.'`=\''.$value.'\'';
			$userinfo[$key] = $value;
		}
		$set = ltrim($set,',');
		$sql = "UPDATE ".DB_NAME_IM.".new_username SET ".$set." WHERE uin = ".$uin;
		if(!$this->db->query($sql)){
			return array('Flag'=>101,'FlagString'=>'failed');
		}
		if($fields['group_id'] > 0){
			$sql = "UPDATE ".DB_NAME_IM.".basic_tbl SET group_id={$fields['group_id']} WHERE uin = ".$uin;
			if(!$this->db->query($sql)){
				return array('Flag'=>102,'FlagString'=>'failed');
			}
		}
		return array('Flag'=>100,'FlagString'=>'Ok','userinfo'=>$userinfo);
	}
    
    public function searchUserByUin($uin){
        $sql = "SELECT `pic`,`nick`,`gender`,`age`,`uin` FROM ".DB_NAME_IM.".`basic_tbl` WHERE uin = ".$uin." AND group_id = ".$this->group_id;
        $row = $this->db->get_row($sql, "ASSOC");
        
        $keys       = array_map("ucfirst", array_keys($row));
        $values     = array_values($row);
        $user_data  = array_combine($keys, $values);
        $flag_data  = array("Flag"=>100, "FlagString"=>"success");
        
        return array_merge($flag_data, $user_data);
    }
    
    public function searchUserByInfo($email, $nick){
        if(!$email && !$nick){
            return array("Flag"=>101, "FlagString"=>"参数不正确");
        }elseif($email){
            $sql  = "SELECT `pic`,`nick`,`gender`,`age`,`uin` FROM ".DB_NAME_IM.".`basic_tbl` 
            WHERE group_id = ".$this->group_id." AND email = '".$email."'";
        }elseif($nick){
            $sql  = "SELECT `pic`,`nick`,`gender`,`age`,`uin` FROM ".DB_NAME_IM.".`basic_tbl` 
            WHERE group_id = ".$this->group_id." AND nick = '".$nick."'";
        }else{
            $sql  = "SELECT `pic`,`nick`,`gender`,`age`,`uin` FROM ".DB_NAME_IM.".`basic_tbl` 
            WHERE group_id = ".$this->group_id." AND email = '".$email."' AND nick = '".$nick."'";    
        }
        $rows = $this->db->get_results($sql, "ASSOC");
        
        $user_data = array();
        foreach($rows as $row){
            $keys       = array_map("ucfirst", array_keys($row));
            $values     = array_values($row);
            $user_data[]= array_combine($keys, $values);
        }
        $flag_data = array("Flag"=>100, "FlagString"=>"success", "Count"=>count($user_data)); 
        return array_merge($flag_data, array("Detail"=>$user_data));
    }
    
    function hasPwdProtection($uin){
        $sql   = "SELECT COUNT(*) FROM ".DB_NAME_IM.".`pwd_protection`
                  WHERE group_id = '".$this->group_id."' AND uin = '".$uin."'";
        $exist = $this->db->get_var($sql);
        
        if($exist){
            return array("Flag"=>100, "FlagString"=>"该用户存在密码保护");
        }else{
            return array("Flag"=>102, "FlagString"=>"该用户不存在密码保护");
        }
    }
    
    function addPwdProtection($uin, $question, $answer){
        $sql   = "SELECT COUNT(*) FROM ".DB_NAME_IM.".`pwd_protection`
                  WHERE group_id = '".$this->group_id."' AND uin = '".$uin."'";
        $exist = $this->db->get_var($sql);
        if($exist){
            return array("Flag"=>102, "FlagString"=>"该用户存在密码保护");
        }
        
        $sql  = "INSERT INTO `kexoo_im`.`pwd_protection` (`uin`, `group_id`, `question`, `answer`)
                 VALUES ('".$uin."', '".$this->group_id."', '".$question."', '".$answer."'); ";
        $done = $this->db->query($sql);
        if($done){
            return array("Flag"=>100, "FlagString"=>"操作成功");
        }else{
            return array("Flag"=>102, "FlagString"=>"操作失败");
        }
    }
    
    function editPwdProtection($uin, $question, $answer, $old_answer){
        $sql   = "SELECT id FROM ".DB_NAME_IM.".`pwd_protection`
                  WHERE group_id = '".$this->group_id."' AND uin = '".$uin."' 
                  AND answer = '".$old_answer."'";
        $id = $this->db->get_var($sql);
        if(!$id){
            return array("Flag"=>102, "FlagString"=>"验证资料错误");
        }
        
        $sql = "UPDATE ".DB_NAME_IM.".`pwd_protection` SET 
                `question` = '".$question."' ,`answer` = '".$answer."'
                 WHERE `id` = '".$id."'; ";
        $done = $this->db->query($sql);
        if($done){
            return array("Flag"=>100, "FlagString"=>"操作成功");
        }else{
            return array("Flag"=>102, "FlagString"=>"操作失败");
        }
    }
    
    function getQuestion($uin){
        $sql        = "SELECT question FROM ".DB_NAME_IM.".pwd_protection
                       WHERE group_id = '".$this->group_id."' AND uin = '".$uin."'";
        $question   = $this->db->get_var($sql);
        
        return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$question);
    }
	
    function resetPwdByQuestion($uin, $answer){
        $sql         = "SELECT answer FROM ".DB_NAME_IM.".pwd_protection
                       WHERE group_id = '".$this->group_id."' AND uin = '".$uin."'";
        $true_answer = $this->db->get_var($sql);
        if($true_answer != $answer){
            return array("Flag"=>102, "FlagString"=>"问题答案错误");
        }
        
        $new_pass = "";
        for($i=0;$i<6;$i++){
            $new_pass .= mt_rand(0, 9);
        }
        $res = $this->editPassword($uin, md5($new_pass));
        if($res['Flag'] == 100){
            return array("Flag"=>100, "FlagString"=>"操作成功", "Password"=>$new_pass);
        }else{
            return array("Flag"=>102, "FlagString"=>"操作失败");
        }
    }
    
	//修改用户高级信息
	public function editUserAdvanced($uin,$groupId,$advancedInfo){
		$uin=intval($uin);
		$groupId=intval($groupId);
		if($uin<=0||$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数为空');
		}
		$info=$this->getUserAdvanced($uin,$groupId);
		if($info['Flag']!=100){
			return array('Flag'=>102,'FlagString'=>'查询用户高级信息出现错误');
		}
		
		//为空则插入
		if(empty($info['advanced'])){
			$advancedInfo=array($advancedInfo['key']=>$advancedInfo['info']);
			$advancedInfo=addslashes(serialize($advancedInfo));
			$sql="INSERT INTO ".DB_NAME_IM.".advanced_tbl(uin,group_id,info) VALUES('{$uin}','{$groupId}','{$advancedInfo}')";
		}
		//不为空则更新
		else{
			$info['advanced']['info'][$advancedInfo['key']]=$advancedInfo['info'];
			$advancedInfo=addslashes(serialize($info['advanced']['info']));
			$sql="UPDATE ".DB_NAME_IM.".advanced_tbl SET info='{$advancedInfo}' WHERE uin={$uin} AND group_id={$groupId}";
		}
		if($this->db->query($sql)){
			return array('Flag'=>100,'FlagString'=>'操作成功');
		}else{
			return array('Flag'=>103,'FlagString'=>'操作失败');
		}
	}
	
	//查询用户高级信息
	public function getUserAdvanced($uin,$groupId){
		$uin=intval($uin);
		$groupId=intval($groupId);
		if($uin<=0||$groupId<=0){
			return array('Flag'=>101,'FlagString'=>'参数为空');
		}
		$sql="SELECT * FROM ".DB_NAME_IM.".advanced_tbl WHERE uin={$uin} AND group_id={$groupId}";
		$info=$this->db->get_row($sql,'ASSOC');
		if(!empty($info)){
			$info['info']=unserialize($info['info']);
		}
		return array('Flag'=>100,'FlagString'=>'成功','advanced'=>$info);
	}

	public function getInfoByIdcard($idcard,$isNeedGroup=true){
		$len = strlen($idcard);
		if($len != 15 && $len != 18){
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
		$where = " idcard='{$idcard}'";
		if($isNeedGroup > 0){
			$where .= " AND group_id={$this->group_id}";
		}

		$sql = "SELECT * FROM ".DB_NAME_IM.".new_username WHERE  {$where}";
		$row = $this->db->get_row($sql,ASSOC);
		return array('Flag'=>100,'FlagString'=>'ok','Info'=>$row);
	}
    
    function getNickArray($data){
        $uins_arr = array();
        foreach($data as $one){
            $uins_arr[] = $one['uin'];
        }
        
        $sql = "SELECT uin,nick FROM ".DB_NAME_IM.".`basic_tbl` WHERE uin IN (".join(",", $uins_arr).")";
        $res = $this->db->get_results($sql, "ASSOC");
        return array("Flag"=>100, "FlagString"=>"查询成功", "Data"=>$res);
    }
    
	//获取登录用户信息
	private function get_storage($token=''){
		if(empty($token)){
			return false;
		}
		return $this->storage->get($token);
	}

	//存储登录用户信息
	public function set_storage($userinfo,$time=604800){
		$result = $this->storage->set($this->token,$userinfo,$time);
		if($result){
			return array('Flag'=>100,'FlagString'=>'ok');
		}else{
			return array('Flag'=>101,'FlagString'=>'fail');
		}
	}

	//删除登录用户信息
	private function del_storage($token){
		return $this->storage->delete(array($token));
	}
	
	//用户密码加密
	private function password($char){
		return md5('88'.$char.'(+_+)');
	}
	
	//同一cookie不同domain获取
	private function http_cookie($name){
		$http_cookie = explode(";",$_SERVER["HTTP_COOKIE"]);
		foreach((array)$http_cookie as $cookie){
			$cookie = trim($cookie);
			$len = strlen($name);
			if(substr($cookie,0,$len) == $name){
				$result[] = substr($cookie,$len+1);
			}
		}
		return $result;
	}
	
	private function set_cookie($token_name,$token_value,$expire){
		return @setcookie($token_name,$token_value,$expire,'/',$_SERVER['HTTP_HOST']);
	}
}
