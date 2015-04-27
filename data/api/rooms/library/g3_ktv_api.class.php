<?php

/*奥点网络媒体互动用户计费管理平台软件
 *模块: 奥点网络媒体互动用户计费管理平台软件 KTV业务资金模块
 *文件: g3_ktv_api.class.php
 *copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
include_once 'room_common.class.php';
class g3_ktv_api extends RoomCommon
{
	protected $rank_expire = 180;
    protected $uptime = 0; //当前时间
    protected $today  = 0; //本天时间
	
	//构造函数
	public function __construct() {
		parent::__construct();
		$this->db  = db::connect(config('database','default'));
		$this->cache = cache::connect(config('cache','memcache'));
		$this->mongo = domain::main()->GroupDBConn('mongo');
		$this->uptime = time();
        $this->today = strtotime(date('Y-m-d 00:00:00'));
        $this->groupMysql = domain::main()->GroupDBConn();
	}
	
	public function setMikePlayTime($param,$extparam){
		//判断被抱下人是否是艺人
		// $uin_type = getChannelType($param['TargetUin'],$param['ChannelId']);
		// $extparam['IsArtist'] = $uin_type > 0 ? 1 : 0;
		// $logs[] = array('param'=>array('Uin'=>$param['Uin'],'TargetUin'=>$param['TargetUin'],'ChannelId'=>$param['ChannelId'],'BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>$param['ChildId'],'MoneyWeight'=>$param['MoneyWeight'],'DoingWeight'=>$param['DoingWeight'],'Desc'=>$param['Desc'],'GroupId'=>$param['GroupId']),'extparam'=>$extparam);
		return array('Flag'=>100,'FlagString'=>'抱下麦成功','LogData'=>array(getLogData($param,$extparam)));
	}

	//验证用户登录
	public function roomlogin($param,$pwd){
		$uin = $param['Uin'];
		$sessionkey = $param['SessionKey'];
		$roomid = $param['ChannelId'];
		$time = time();
		$roomdb = DB_NAME_NEW_ROOMS;
		if($roomid <= 0) return array('Flag'=>101,'FlagString'=>'参数错误!');
		//-------------------------------房间状态------------------------------
		$roominfo = $this->get_roominfo($roomid); //房间信息
		if(empty($roominfo)){
			return array('Flag'=>103,'FlagString'=>'该房间不存在!');
		}
		if(empty($roominfo['status'])){
			return array('Flag'=>103,'FlagString'=>'该房间已冻结!');
		}
		$roominfo['name'] = htmlspecialchars_decode($roominfo['name'],ENT_QUOTES); //特殊字符转码
		
		if(is_numeric($uin) && $uin > GUEST_UIN_START && $uin < GUEST_UIN_END){
			$is_guest = 1;
			$guest_info = httpPOST(GUEST_API_PATH,array('param'=>array('Uin'=>$uin,'SessionKey'=>$sessionkey),'extparam'=>array('Tag'=>'GuestLogin')));
			if($guest_info['Flag'] != 100){
				$uininfo = false;
			}else{
				$uininfo = array(
					'Uin'    => $guest_info['Uin'],
					'nick'   => $guest_info['Nick'],
					'kmoney_balance' => 0,
					'gender' => (int)$guest_info['Gender'],
					'vip'    => 0,
					'pic'    => "http://static.vvku.com/robotimage/".rand(1,110).".jpg",///robotimage/1.jpg
				//	'MaxRole' => 10006   //游客角色
				);
			}
		}else{
			//判断用户登录
			$parame = array('param'=>array('SessionKey'=>$sessionkey,'GroupId'=>$roominfo['group']),'extparam'=>array('Tag'=>'GetLogin'));
			$loginuser = httpPOST(SSO_API_PATH,$parame);
			if($loginuser['Flag'] !== 100) return array('Flag'=>102,'FlagString'=>'登录失败!');
			$loginuser['Nick'] = stripcslashes(htmlspecialchars_decode($loginuser['Nick'],ENT_QUOTES)); //特殊字符转码
			$uin = $loginuser['Uin'];
			//登录成功写入历史访问
			$this->historyAccess($uin,$roomid);
			$uininfo = array(
				'Uin'    => $uin,
				'nick'   => $loginuser['Nick'],
			//	'kmoney_balance' => $money,
				'gender' => (int)$loginuser['Gender'],
				// 'vip'    => (int)$userinfo['Vip'], 
				'pic'    => cdn_url(PIC_API_PATH."/uin/{$uin}/40/40.jpg"),
			);
			$l_num = $this->getParentNum($uin,10058,10428);//靓号
			if($l_num['Flag'] == 100){
				$uininfo['LNUM'] = (int)$l_num['other_id'];
			}
		}
		if( ! $uininfo){
			return array('Flag' => 108,'FlagString'=>'游客登录失败！');
		}

		$money = get_money($uin,$roominfo['group']);
		$uininfo['VoucherBalance'] = round($money);
		//if($roominfo['expiretime'] < $time){
		//	return array('Flag'=>103,'FlagString'=>'该房间已到期!');
		//}
		$get_superadmin = $this->get_member("chatmanager_tbl", null, $uin); //超管
		$get_superadmin = $get_superadmin['level']; // 1为超管，2为巡管
		$get_admin  = $this->get_member("roommanager_tbl", $roomid, $uin); //管理
		$get_admin = $get_admin['uin'];
		$get_member = $this->get_member("roommember_tbl", $roomid, $uin); //成员
		$get_member = $get_member['uin'];
		
		//房间设置密码(室主 管理 超管可不输入密码)
		if($roominfo['status'] == 1){
			if($pwd != $roominfo['passwd'] && $roominfo['ownuin'] != $uin && empty($get_admin) && empty($get_superadmin)){
				return array('Flag'=>105,'FlagString'=>'登录失败，房间密码错误!'); //Flag=105不能改
			}
		//是否是成员
		}elseif($roominfo['status'] == 2){
			if($roominfo['ownuin'] != $uin && empty($get_superadmin) && empty($get_admin) && empty($get_member)){
				return array('Flag'=>106,'FlagString'=>'该房间只允许成员进入，您不是该房间成员,登录失败!');
			}
		//黑名单
		}elseif($roominfo['status'] == 3){
			$get_deny = $this->get_member("roomdeny_tbl", $roomid, $uin);
			$get_deny = $get_deny['uin'];
			if($get_deny > 0 && $roominfo['ownuin'] != $uin && empty($get_superadmin) && empty($get_admin)){
				return array('Flag'=>106,'FlagString'=>'登录失败，您已被该房间列入黑名单！');
			}
		//房间关闭(室主 管理 超管可进)
		}elseif($roominfo['status'] == 4){
			if($roominfo['ownuin'] != $uin && empty($get_admin) && empty($get_superadmin)){
				return array('Flag'=>104,'FlagString'=>'登录失败，房间已关闭！');
			}
		}
		//检查是否在踢出名单内
		$is_kick = $this->get_kick($roomid, array("id"=>$uin, "ip"=>""));
		if($is_kick){
			return array('Flag'=>106,'FlagString'=>'您已经被踢出该房间，无法进入！');
		}

		//VIP图标
		$groupvip = getGroupVip($uin,$roominfo['group']);
		if(!empty($groupvip)){
			$rule = new rule($this->mongo);
			$uininfo['GroupVipLevel'] = $rule->getRuleLevel($uin,0,$roominfo['group'],31,'total');
		}
		
		$roles_info = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'UserRole','Uin'=>$uin,'GroupId'=>$roominfo['group'],'ChannelId'=>$roomid)));
		$roles = $roles_info['Roles'];
        /************组合图片路径************/
        $roominfo['roomnotice'] = json_decode($roominfo['roomnotice'],true);
        foreach($roominfo['roomnotice'] as $key=>$item){
            $item['img_name'] = cdn_url(PIC_API_PATH."/p/{$item['img_name']}/0/0.jpg");
            $roominfo['roomnotice'][$key] = $item;
        }
        $roominfo['roomnotice'] = json_encode($roominfo['roomnotice']);
        /************组合图片路径 END************/
		//房间代理图标
		//$sql="SELECT room_id FROM ".DB_NAME_GROUP.".groups_proxy WHERE group_id=".$roominfo['group']." AND uin=".$uin;
		//$proxyRoom=$this->groupMysql->get_var($sql);
		//if($proxyRoom==='0'||$proxyRoom==$roomid){
		//	$uininfo['proxy'] = "http://{$_SERVER['HTTP_HOST']}/pic/roleicon/10010.png";
		//}
		
		//站信息
		$sql="SELECT * FROM ".DB_NAME_GROUP.".tbl_groups WHERE groupid=".$roominfo['group'];
		$groupinfo=$this->db->get_row($sql,"ASSOC");
		
		
		//富豪等级
		require_once dirname(__FILE__).'/rooms.class.php';
		$rooms = new rooms();
		$regalLevel = $rooms->regalLevel($uin);
		if($regalLevel['Flag'] == 100){
			$uininfo['Regal'] = $regalLevel['Regal'];
		}
		
		//艺人等级
		$actlevel = $rooms->regalLevel($uin,13);
		if($actlevel['Flag'] == 100){
			$uininfo['ActorRegal'] = $actlevel['Regal'];
		}
		
		//全站用户等级
		$userlevel = $rooms->regalLevel($uin,28);
		if($userlevel['Flag'] == 100){
			$uininfo['UserLevel'] = $userlevel['Regal'];
		}
		
		//进场道具
		$entryPropsInfo = $this->getParentNum($uin,10041,0,$roomid);
		if($entryPropsInfo['Flag'] == 100){
			$pic_url = !empty($entryPropsInfo['room_image_md5']) ? $entryPropsInfo['room_image_md5'] : $entryPropsInfo['image_md5'];
			$uininfo['EntryProps'] = array('pic_url'=>$pic_url,'swf_url'=>$entryPropsInfo['flash_md5'],'name'=>$entryPropsInfo['name'],'price'=>$entryPropsInfo['price']);
		}
		/*房间与服务器信息*/
		$roominfo['start'] = GUEST_UIN_START;
		$roominfo['end']   = GUEST_UIN_END;
		$roominfo['bgUrl'] = cdn_url(PIC_API_PATH.'/roombg/'.$roomid.'/0/0.jpg');
		$roominfo['UserList'] = USER_LIST;
		$roominfo['robot_num'] = $roominfo['robot_num'] > 0 ? $roominfo['robot_num'] : 300;
		$sql = "SELECT * FROM ".DB_NAME_NEW_ROOMS.".otherserverinfo WHERE `status`=1";
		$serverinfo = array();
		$serverinfo['ServerInfo'] = $this->groupMysql->get_results($sql,'ASSOC');
		$serverinfo['RoomInfo'] = $roominfo;
		
		//用户信息
		$arr['Uin'] = $uin;
		$arr['SessionKey'] = $loginuser['Token'];
		$arr['UinMoneyWeight'] = $param['MoneyWeight'];
		$arr['UserInfo'] = $uininfo;
		$arr['AllInfo'] = $serverinfo;
		$arr['RegionId'] = $roominfo['region_id'];
		$arr['RoleInfo'] = $roles;
		$arr['RoleOrderType'] = $groupinfo['role_order_type'];
		
		$arr['Flag'] = 100;
		$arr['FlagString'] = '成功';
		return $arr;
	}
	
	private function historyAccess($uin,$roomid){
		require_once 'footprint.class.php';
		$footprint = new FootPrint();
		$footprint->historyAccess($uin,$roomid);
	}
	
	
	//用户退出
	public function roomlogout($param,$extparam){
		$uin = $param['Uin'];
		$roomid = $param['ChannelId'];
		if($uin > 0 && $roomid > 0){
			// $channel_relation = getChannelRelation($roomid);
			// $extparam['RegionId'] = (int)$channel_relation['RegionId'];
			// $extparam['GroupId'] = (int)$channel_relation['GroupId'];
			
			$res = $this->setRoomCurUser($roomid,$uin,0);
			// $log[] = array('param'=>array('Uin'=>$param['Uin'],'TargetUin'=>$param['TargetUin'],'OwnUin'=>(int)$channel_relation['Ownuin'],'ActorUin'=>0,'ChannelId'=>$param['ChannelId'],'BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>$param['ChildId'],'MoneyWeight'=>$param['MoneyWeight'],'DoingWeight'=>$param['DoingWeight'],'Desc'=>$param['Desc'],'GroupId'=>$extparam['GroupId']),'extparam'=>$extparam);
			$log[] = getLogData($param,$extparam);
			$res['LogData'] = $log;
			
			$array = array(
				'db'=>'Advertise',
				'table'=>'details',
				'record'=>array('$set'=>array('RoomLogout'=>time())),
				'where'=>array('Uin'=>(int)$uin),
			);
			socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($array)));
			return $res;
		}else{
			return array('Flag'=>101,'FlagString'=>'参数错误');
		}
	}
	
	//初始化在线人数
	public function serverinit(){
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET curuser=0,hasplay=0";
		$rst1 = $this->groupMysql->query($sql);
		$sql = "UPDATE ".DB_NAME_PARTNER.".channel_user SET is_online=0 ";
		$rst2 = $this->groupMysql->query($sql);
		$rst3 = $this->groupMysql->query('UPDATE '.DB_NAME_IM.'.basic_tbl SET is_online = 0,room_id=0 ');
		if($rst1 && $rst2 && $rst3){
			return array('Flag'=>100,'FlagString'=>'ok');
		}else{
			return array('Flag'=>101,'FlagString'=>'fail');
		}
	}

	//房间初始化
	private function roominit($roomid){
		if($roomid > 0){
			$roomdb = DB_NAME_NEW_ROOMS;
			$sql = "SELECT * FROM ".DB_NAME_NEW_ROOMS.".otherserverinfo WHERE `status`=1";
			$server = $this->groupMysql->get_results($sql,'ASSOC');
				$arr = array();
				$arr['start'] = GUEST_UIN_START;
				$arr['end']   = GUEST_UIN_END;
				$arr['bgUrl'] = cdn_url(PIC_API_PATH.'/roombg/'.$roomid.'/0/0.jpg');
				$arr['UserList'] = USER_LIST;
				//$arr['RoomInfo'] = $rst;
				$arr['ServerInfo'] = $server;
				//$arr['MikeMember'] = $mikeMember;
			//}
			return $arr;
		}
	}

	//机器人登陆
	public function robotLogin($param){
		$roomid = $param['ChannelId'];
		$roomdb = DB_NAME_NEW_ROOMS;
		if($roomid <= 0) return array('Flag'=>101,'FlagString'=>'参数错误');
		$guest_info = httpPOST(GUEST_API_PATH,array('extparam'=>array('Tag'=>'GetRobot','Gender'=>rand(0,1))));
		if($guest_info['Flag'] != 100) return $guest_info;
		$uininfo = array(
			'Uin'    => $guest_info['Uin'],
			'nick'   => 'aaa',//$guest_info['Nick'],
			'kmoney_balance' => 0,
			'gender' => 1,//$guest_info['Gender'],
			'level'  => 0,
			'vip'    => 0,
			'pic'    => '1.jpg',//PIC_API_PATH."/uin/0/40/40.jpg",
		);
		//----------------------返回信息--------------------
		$arr['Flag'] = 100;
		$arr['FlagString'] = '登陆成功';
		$arr['Uin'] = $guest_info['Uin'];
		$arr['Order'] = 0;
		$arr['UserInfo'] = $uininfo;
		return $arr;
	}

	//设置房间信息
	public function setRoomInfo($param,$json){
		$set = '';
		$roomid = $param['ChannelId'];

		if(isset($json['MainVideoTime']))
			$set.="`main_video_time`='{$json['MainVideoTime']}',";
		if(isset($json['DiceStatus']))
			$set.="`dicestatus`='{$json['DiceStatus']}',";
		if(isset($json['DiceRange']))
			$set.="`dicerange`='{$json['DiceRange']}',";
		if(isset($json['PublicTalkStat']))
			$set.="`publictalkstat`='{$json['PublicTalkStat']}',";
		if(isset($json['PrivateTalkStat']))
			$set.="`privatetalkstat`='{$json['PrivateTalkStat']}',";
		if(isset($json['AllPublicTalkStat']))
			$set.="`allpublictalkstat`='{$json['AllPublicTalkStat']}',";
		if(isset($json['AllPrivateTalkStat']))
			$set.="`allprivatetalkstat`='{$json['AllPrivateTalkStat']}',";
		if(isset($json['SendStat']))
			$set.="`sendstat`='{$json['SendStat']}',";
		if(isset($json['SendTime']))
			$set.="`sendtime`='{$json['SendTime']}',";
		if(!empty($json['VideoNum']))
			$set.="`video_num`='{$json['VideoNum']}',";
		if(isset($json['DiceTime']))
			$set.="`dicetime`='{$json['DiceTime']}',";

		$set = trim($set,',');
		if(!empty($set)){
			$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET {$set} WHERE id='{$roomid}'";
			$rst = $this->groupMysql->query($sql);
			if ($rst) {
				$result = array('Flag'=>100,'FlagString'=>'更新成功!');
			}else{
				$result = array('Flag'=>101,'FlagString'=>'更新失败!');
			}
		}else{
			$result = array('Flag'=>102,'FlagString'=>'参数错误!');
		}
		return $result;
	}

	//设置用户上麦
	public function setMikePlaying($param){
		$roomid = $param['ChannelId'];
		$GroupId = $param['GroupId'];

		$main_video_time = $this->groupMysql->get_var("SELECT main_video_time FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid} LIMIT 1",'ASSOC'); //房间信息

		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET hasplay=1 WHERE id={$roomid}";
		$rst = $this->groupMysql->query($sql);
		$sql = "UPDATE ".DB_NAME_PARTNER.".channel_user SET is_online=1 WHERE uid={$param['Uin']} AND room_id={$roomid}";
		$rst1 = $this->groupMysql->query($sql);
        $rst2 = $this->groupMysql->query('UPDATE '.DB_NAME_IM.'.basic_tbl SET is_online = 1,room_id = '.$roomid.' WHERE uin = '.$param['Uin'].' AND group_id = '.$GroupId);

		if($rst && $rst1 && $rst2){
			$result = array('Flag'=>100,'FlagString'=>'成功','MianVideoTime'=>$main_video_time);
		}else{
			$result = array('Flag'=>101,'FlagString'=>'失败');
		}
		return $result;
	}
	
	//设置用户下表演麦
	public function setMikePlayEnd($param,$extparam){
		$roomid = $param['ChannelId'];
		$uin = $param['Uin'];
		$target_uin = $param['TargetUin'];
		
		// $channel_relation = getChannelRelation($roomid);
		// $extparam['RegionId'] = (int)$channel_relation['RegionId'];
		// $extparam['GroupId'] = (int)$channel_relation['GroupId'];
		
		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET hasplay=0 WHERE id={$roomid}";
		$rst = $this->groupMysql->query($sql);
		$sql = "UPDATE ".DB_NAME_PARTNER.".channel_user SET is_online=0 WHERE uid={$uin} AND room_id={$roomid}";
		$rst1 = $this->groupMysql->query($sql);
		$rst2 = $this->groupMysql->query('UPDATE '.DB_NAME_IM.'.basic_tbl SET is_online = 0,room_id=0 WHERE uin = '.$uin.' AND group_id = '.$param['GroupId']);
		if($rst && $rst1 && $rst2){
			//判断收礼人是否是艺人
			// $uin_type = getChannelType($param['TargetUin'],$roomid);
			// $param['ActorUin'] = $uin_type > 0 ? (int)$param['TargetUin'] : 0;
			// $logs[] = array('param'=>array('Uin'=>$param['Uin'],'TargetUin'=>$param['TargetUin'],'OwnUin'=>(int)$channel_relation['Ownuin'],'ActorUin'=>$param['ActorUin'],'ChannelId'=>$param['ChannelId'],'BigCaseId'=>$param['BigCaseId'],'CaseId'=>$param['CaseId'],'ParentId'=>$param['ParentId'],'ChildId'=>$param['ChildId'],'MoneyWeight'=>$param['MoneyWeight'],'DoingWeight'=>$param['DoingWeight'],'Desc'=>$param['Desc'],'GroupId'=>$extparam['GroupId']),'extparam'=>$extparam);
			$logs[] = getLogData($param,$extparam);
			$result = array('Flag'=>100,'FlagString'=>'成功','LogData'=>$logs);
		}else{
			$result = array('Flag'=>101,'FlagString'=>'失败');
		}
		return $result;
	}


	//用户排麦
	public function queueMike($param){
		$roomid = $param['ChannelId'];
		$uin = $param['Uin'];

		$mike_power = $this->groupMysql->get_var("SELECT mike_power FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid} LIMIT 1",'ASSOC'); //房间信息
		if($mike_power == 2){
			$isArtist = getChannelType($uin,$roomid );
			if($isArtist > 0){
				$result = array('Flag'=>100,'FlagString'=>'成功');
			}else{
				$result = array('Flag'=>102,'FlagString'=>'当前规则下,只允许艺人排麦');
			}
		}
		elseif($mike_power == 3){
			$mike_members = $this->get_member('mike_members', $roomid, $uin);
			$mike_members = $mike_members['uin'];
			if($mike_members > 0){
				$result = array('Flag'=>100,'FlagString'=>'成功');
			}else{
				$result = array('Flag'=>102,'FlagString'=>'当前规则下,只允许指定成员排麦');
			}
		}else{
			$result = array('Flag'=>100,'FlagString'=>'成功');
		}
		return $result;
	}

	//发送公聊
	public function sendPubChat($param){
		$roomid = $param['ChannelId'];
		$uin = $param['Uin'];

		$get_superadmin = $this->get_member("chatmanager_tbl", null, $uin); //超管
		$get_superadmin = $get_superadmin['level'];
		$get_admin  = $this->get_member("roommanager_tbl", $roomid, $uin); //管理
		$get_admin = $get_admin['uin'];

		$get_ownuin = $this->groupMysql->get_var("SELECT ownuin FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid} AND ownuin={$uin} LIMIT 1"); //房间信息
		$chatinfo = $this->groupMysql->get_row("SELECT allpublictalkstat,publictalkstat,sendtime FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid} LIMIT 1","ASSOC");//所有
		
		if(!$get_superadmin && !$get_admin && !$get_ownuin){
			//所有用户公聊设置
			if(!$chatinfo['allpublictalkstat']){
				//游客用户公聊设置
				if(!($uin < GUEST_UIN_START || $uin > GUEST_UIN_END)){
					if(!$chatinfo['publictalkstat']){
						return array('Flag'=>'100','FlagString'=>'允许游客公聊','SendTime'=>$chatinfo['sendtime']);
					}else{
						return array('Flag'=>'101','FlagString'=>'不允许游客公聊');
					}
				}
				return array('Flag'=>'100','FlagString'=>'允许公聊','SendTime'=>$chatinfo['sendtime']);
			}else{
				return array('Flag'=>'101','FlagString'=>'不允许公聊');
			}
		}else{
			return array('Flag'=>'100','FlagString'=>'允许公聊','SendTime'=>$chatinfo['sendtime']);
		}
	}

	//发送私聊
	public function sendPrivChat($param){
		$roomid = $param['ChannelId'];
		$uin = $param['Uin'];

		$get_superadmin = $this->get_member("chatmanager_tbl", null, $uin); //超管
		$get_superadmin = $get_superadmin['level'];
		$get_admin  = $this->get_member("roommanager_tbl", $roomid, $uin); //管理
		$get_admin = $get_admin['uin'];

		$get_ownuin = $this->groupMysql->get_var("SELECT ownuin FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid} AND ownuin={$uin} LIMIT 1"); //房间信息

		if(!$get_superadmin && !$get_admin && !$get_ownuin){
			$chatinfo = $this->groupMysql->get_row("SELECT allprivatetalkstat,privatetalkstat FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid} LIMIT 1");//所有
			//所有用户公聊设置
			if(!$chatinfo['allprivatetalkstat']){
				//游客用户公聊设置
				if(!($uin < GUEST_UIN_START || $uin > GUEST_UIN_END)){
					if(!$chatinfo['privatetalkstat']){
						return array('Flag'=>'100','FlagString'=>'允许游客私聊','SendTime'=>0);
					}else{
						return array('Flag'=>'101','FlagString'=>'不允许游客私聊');
					}
				}
				return array('Flag'=>'100','FlagString'=>'允许私聊','SendTime'=>0);
			}else{
				return array('Flag'=>'101','FlagString'=>'不允许私聊');
			}
		}else{
			return array('Flag'=>'100','FlagString'=>'允许私聊','SendTime'=>0);
		}
	}

	//发送骰子聊天
	public function sendDice($param){
		$roomid = $param['ChannelId'];
		$dicetime = $this->groupMysql->get_var("SELECT dicetime FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid} LIMIT 1");
		return array('Flag'=>'100','FlagString'=>'成功','DiceTime'=>$dicetime);
	}

	//发送彩条文件
	public function SendColourBar(){
		//$roomid = $param['ChannelId'];
		$colourbartime = 5;
		return array('Flag'=>'100','FlagString'=>'成功','ColourBarTime'=>$colourbartime);
	}
	
	//发广播
	public function UseBroadcast($param,$extparam){
		$BigCase = $param['BigCaseId'];
		$Case = $param['CaseId'];
		$Parent = $param['ParentId'];
		$Uin = $param['Uin'];
		$RoomId = $param['ChannelId'];
		$Client = $param['Client'];
		$TradeMoney = $param['MoneyWeight'];
		if($Uin < 0 && $RoomId < 0 && $TradeMoney < 0) {
			return array('Flag'=>106,'FlagString'=>'参数错误!');
		}
		/*
		$shelter = $this->groupMysql->get_var('SELECT CONVERT((uptime + shelter * 60) - UNIX_TIMESTAMP() ,SIGNED) FROM '.DB_NAME_NEW_ROOMS.'.tbl_roomsbroadcast_config WHERE uin = '.$Uin.' AND status = 1;');
		if($shelter > 0) {
			return array ('Flag' => 105,'FlagString' => '发广播失败，您已被管理员屏蔽','UinBalance'=>0);
		}
		*/
		$TradeDesc = '发广播ID:'.$Uin.',金额:'.$TradeMoney;
		
		//扣除送礼者V豆
		$param['TaxType'] = 2;
		$kmoneyTrade = $this->trade($param,101,$TradeMoney,$TradeDesc,true);
		if($kmoneyTrade['Flag'] != 100){
			return $kmoneyTrade;
		}
		$kmoneyTrade['log']['extparam'] = $extparam;
		$log[] = $kmoneyTrade['log'];
		unset($kmoneyTrade['log']);
		unset($param['TaxType']);
		//税收存入
		$incomeTrade = $this->trade($param,901,$TradeMoney,$TradeDesc);
		if($incomeTrade['Flag'] != 100){
			return $incomeTrade;
		}
		$result = array('Flag'=>100,'FlagString'=>'广播发布成功！','LogData'=>$log);
		isset($kmoneyTrade['balance']) ? $result['UinBalance'] =$kmoneyTrade['balance'] : '';
		isset($kmoneyTrade['VoucherBalance']) ? $result['VoucherBalance'] =$kmoneyTrade['VoucherBalance'] : '';
		return $result;
	}
	
	//地区聊天
	/*public function regionChat($param,$extparam){
		$BigCase = $param['BigCaseId'];
		$Case = $param['CaseId'];
		$Parent = $param['ParentId'];
		$Uin = $param['Uin'];
		$RoomId = $param['ChannelId'];
		$Client = $param['Client'];
		$TradeMoney = 50000;
		if($Uin < 0 && $RoomId < 0 && $TradeMoney < 0) {
			return array('Flag'=>106,'FlagString'=>'参数错误!');
		}
		
		$TradeDesc = '地区聊天ID:'.$Uin.',金额:'.$TradeMoney;
		
		//扣除送礼者V豆
		$param['TaxType'] = 2;
		$kmoneyTrade = $this->trade($param,$param['ChildId'],$TradeMoney,$TradeDesc,true);
		if($kmoneyTrade['Flag'] != 100){
			return $kmoneyTrade;
		}
		$kmoneyTrade['log']['extparam'] = $extparam;
		$log[] = $kmoneyTrade['log'];
		unset($kmoneyTrade['log']);
		unset($param['TaxType']);
		//税收存入
		$incomeTrade = $this->trade($param,901,$TradeMoney,$TradeDesc);
		if($incomeTrade['Flag'] != 100){
			return $incomeTrade;
		}
		$result = array('Flag'=>100,'FlagString'=>'地区聊天成功！','LogData'=>$log);
		isset($kmoneyTrade['balance']) ? $result['UinBalance'] =$kmoneyTrade['balance'] : '';
		isset($kmoneyTrade['VoucherBalance']) ? $result['VoucherBalance'] =$kmoneyTrade['VoucherBalance'] : '';
		return $result;
	}*/

	//图章
	public function Stamp($param,$stampid,$extparam) {
		$BigCase = $param['BigCaseId'];
		$Case = $param['CaseId'];
		$Parent = $param['ParentId'];
		$Uin = $param['Uin'];
		$ActUin = $param['TargetUin'];
		$RoomId = $param['ChannelId'];
		$Client = $param['Client'];
		if($Uin <= 0 && $ActUin <= 0 && $RoomId <= 0 && $stampid <= 0) {
			return $array = array('Flag' => 103,'FlagString' => "系统错误");
		}
		include_once('stamp.class.php');
		$stamp = new stamp();
		$stamp_res = $stamp->stamp_arr($stampid);
		if(empty($stamp_res)){
			return array('Flag'=>104,'FlagString'=>"盖章不存在");
		}
		$price = $stamp_res['price'];
		$stampName = $stamp_res['stamp_name'];
		// $stampExpire = 30;//$stamp_res['stamp_expire'];注释无效值
		$TradeDesc = '盖章用户:'.$Uin.',被盖章用户:'.$ActUin.',盖章名称：'.$stampName.',支出:'.$price;
		
		//扣除送礼者V豆
		$param['TaxType'] = 2;
		$kmoneyTrade = $this->trade($param,101,$price,$TradeDesc,true);
		if($kmoneyTrade['Flag'] != 100){
			return $kmoneyTrade;
		}
		$kmoneyTrade['log']['extparam'] = $extparam;
		$log[] = $kmoneyTrade['log'];
		unset($kmoneyTrade['log']);
		unset($param['TaxType']);
		//税收存入
		$incomeTrade = $this->trade($param,901,$price,$TradeDesc);
		if($incomeTrade['Flag'] != 100){
			return $incomeTrade;
		}
		$result = array('Flag'=>100,'FlagString'=>'操作成功','LogData'=>$log);
		$result['Uin'] = $Uin;
		$result['ActUin'] = $ActUin;
		$result['RoomId'] = $RoomId;
		$result['StampName'] = $stampName;
		$result['LastTime'] = 0;
		$result['StampExpire'] = $stampExpire;
		isset($kmoneyTrade['balance']) ? $result['UinBalance'] =$kmoneyTrade['balance'] : '';
		isset($kmoneyTrade['VoucherBalance']) ? $result['VoucherBalance'] =$kmoneyTrade['VoucherBalance'] : '';
		
		return $result;
	}
	
	//礼物列表
	public function PropsList($param,$json){
		$roomid = $param['ChannelId'];
		$imttype = $json['ImgType'];

		$roomdb = DB_NAME_NEW_ROOMS;
		
		/*礼物道具*/
		$module_id = domain::main()->GroupKeyVal($param['GroupId'],'module_id');
		$sql = 'SELECT cate_id,cate_name,is_tricky FROM '.DB_NAME_TPL.'.tbl_gift_cate WHERE tpl_id='.$module_id.' AND `status`=1 ORDER BY `order` DESC';
		$ui_arr = $this->db->get_results($sql,'ASSOC');
		// $ui_info = $this->groupMysql->get_row('SELECT p.* FROM '.DB_NAME_NEW_ROOMS.'.rooms AS r,'.DB_NAME_SYSTEM_CONFIG.'.tbl_ui_package AS p WHERE p.id=r.ui_version AND r.id='.$roomid,'ASSOC');
		// $ui_arr = json_decode($ui_info['gifts'],true);
		if(!empty($ui_arr)){
			$props_info_cate = array();
			foreach($ui_arr as $kk=>$vv){
				// $gifs_arr = $vv['ids'];
				$cate_name = $vv['cate_name'];
				$props_arr = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'PropsList','Data'=>array('cate_id'=>$vv['cate_id'],'props_status'=>1))));
				foreach((array)$props_arr['list'] as $key => $row){
					$big_props_ico = '';
					if($imttype=='swf'){
						$props_ico = $row['swf_img_path'];
						if($row['big_swf_img_path']){
							$big_props_ico = $row['big_swf_img_path'];
						}
					}else{
						$props_ico = $row['gif_img_path'];
					}
					if($vv['is_tricky'] >= 1){
						$props_key = 10000+$vv['is_tricky'];
						$props_info[$props_key][] = array(
							'CaseId'=>$row['case_id'],
							'ParentId'=>$row['parent_id'],
							'Cmd'=>$row['cmd'],
							'CmdPath'=>$row['cmd_path'],
							'PropsIco'=>$props_ico,
							'BigPropsIco'=>$big_props_ico,
							'PropsName'=>$row['props_name'],
							'PropsSize'=>$row['props_size'],
							'PropsMoney'=>$row['props_money'],
							'PropsTax'=>$row['tax_percent'],
							'PropsReceive'=>$row['receive_percent'],
							'PropsTooltip'=>$row['props_desc'],
							'ScreenSize'=>$row['screen_size'],
							'ID'=>$row['id'],
						);
					}else{
						$props_info[$row['case_id']][$kk]['CateName'] = $cate_name;
						$props_info[$row['case_id']][$kk]['List'][] = array(
							'CaseId'=>$row['case_id'],
							'ParentId'=>$row['parent_id'],
							'Cmd'=>$row['cmd'],
							'CmdPath'=>$row['cmd_path'],
							'PropsIco'=>$props_ico,
							'BigPropsIco'=>$big_props_ico,
							'PropsName'=>$row['props_name'],
							'PropsSize'=>$row['props_size'],
							'PropsMoney'=>$row['props_money'],
							'PropsTax'=>$row['tax_percent'],
							'PropsReceive'=>$row['receive_percent'],
							'PropsTooltip'=>$row['props_desc'],
							'ScreenSize'=>$row['screen_size'],
							'ID'=>$row['id'],
						);
					}
					
				}
			}
		}
		
		/*功能-互动道具*/		
		$props_arr = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'FunctionPropsList','Data'=>array('tpl_id'=>$module_id,'props_status'=>1))));
		foreach((array)$props_arr['list'] as $key => $row){
			if($imttype=='swf'){
				$props_ico = $row['swf_img_path'];
			}else{
				$props_ico = $row['gif_img_path'];
			}
			$props_info[$row['case_id']][] = array(
				'CaseId'=>$row['case_id'],
				'ParentId'=>$row['parent_id'],
				'Cmd'=>$row['cmd'],
				'CmdPath'=>$row['cmd_path'],
				'PropsIco'=>$props_ico,
				'PropsName'=>$row['props_name'],
				'PropsSize'=>$row['props_size'],
				'PropsMoney'=>$row['props_money'],
				'PropsTax'=>$row['tax_percent'],
				'PropsReceive'=>$row['receive_percent'],
				'PropsTooltip'=>$row['props_desc'],
                'SelectUser'=>$row['select_user'],
			);
		}
		
		/*互动游戏*/
		$gameProps_arr = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'GamePropsList','Data'=>array('tpl_id'=>$module_id,'props_status'=>1))));
		foreach((array)$gameProps_arr['list'] as $key => $row){
			$props_info[$row['case_id']][] = array(
				'CaseId'=>$row['case_id'],
				'ParentId'=>$row['parent_id'],
				'Cmd'=>$row['cmd'],
				'CmdPath'=>$row['cmd_path'],
				'PropsIco'=>$row['img_path'],
				'PropsName'=>$row['props_name'],
				'PropsSize'=>$row['props_size'],
				'PropsTooltip'=>$row['props_desc'],
			);
		}
		return array('Flag'=>100,'FlagString'=>'获取礼物列表成功','Result'=>$props_info);
	}
	
	//互动游戏列表 接口已废
	public function InteractList($param){
		$roomid = $param['ChannelId'];
		
		$ui_version = $this->groupMysql->get_var('SELECT ui_version FROM '.DB_NAME_NEW_ROOMS.'.rooms WHERE id='.$roomid);

		$ui_info = $this->db->get_row('SELECT flash_games FROM '.DB_NAME_SYSTEM_CONFIG.'.tbl_ui_package WHERE id='.$ui_version,'ASSOC');
		$interact_arr = httpPOST(REGION_API_PATH,array('extparam'=>array('Tag'=>'InteractList','Data'=>array('idin'=>$ui_info['flash_games'],'interact_status'=>1))));
		foreach((array)$interact_arr['list'] as $key => $row){
			$interact_info[$key]['CaseId'] = $row['case_id'];
			$interact_info[$key]['ParentId'] = $row['parent_id'];
			$interact_info[$key]['Cmd'] = $row['cmd'];
			$interact_info[$key]['CmdPath'] = $row['cmd_path'];
			$interact_info[$key]['InteractIco'] = $row['interact_img'];
			$interact_info[$key]['Category'] = $row['category'];
			$interact_info[$key]['CategoryId'] = $row['category_id'];
			$interact_info[$key]['InteractName'] = $row['interact_name'];
			$interact_info[$key]['StatusIco'] = $row['status_img'];
			$interact_info[$key]['RoomSpan'] = $row['room_span'];
		}
		$category = array(1=>'棋牌游戏',2=>'竞猜游戏',3=>'休闲游戏');
		return array('Flag'=>100,'FlagString'=>'获取礼物列表成功','Result'=>$interact_info,'CategoryName'=>$category);
	}

	//获取房间信息
	public function getRoomInfo($param){
		$roomid = $param['ChannelId'];
		$info = $this->get_roominfo($roomid);
		return array('Flag'=>100,'FlagString'=>'获取房间列表成功','Result'=>$info);
	}

	//用户配置保存
	public function setUserInfo($uin,$user_info){
		if($uin > 0 && !empty($user_info)){
			$array = array(
				'db'=>'userinfo',
				'table'=>'extUserSetting',
				'record'=>array(
					'Uin'=>(int)$uin,
					'UserInfo'=>$user_info
				)
			);
			$info = $this->getUserInfo($uin);
			if($info['UserInfo']){
				$where = array(
					'where'=>array(
						'Uin'=>(int)$uin
					)
				);
				$array = array_merge($array,$where);
			}
			$result = json_decode(socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($array))),true);
			if($result['success'] == 100){
				$array = array(
					'Flag' => '100',
					'FlagString' => '信息保存成功'
				);
			}else{
				$array = array(
					'Flag' => '101',
					'FlagString' => '信息保存失败'
				);
			}
		}else{
			$array = array(
				'Flag' => '102',
				'FlagString' => '参数错误'
			);
		}
		return $array;
	}

	//获取用户配置
	public function getUserInfo($uin){
		if($uin > 0){
			$array = array(
				'db'=>'userinfo',
				'table'=>'extUserSetting',
				'fields'=>array(
					'UserInfo'=>''
				),
				'where'=>array(
					'Uin'=>(int)$uin,
				),
				'option'=>array(
					'limit'=>1
				)
			);
			$result = json_decode(socket_request(MONGO_API_PATH.'/?cmd='.urlencode(json_encode($array))),true);
			return array('Flag'=>100,'FlagString'=>'用户自定义配置','UserInfo'=>$result['record'][0]['UserInfo']);
		}
	}
	
	//房间主题存储信息保存
	public function setRoomTheme($roomid,$room_theme){
		if($roomid > 0 && !empty($room_theme)){
			$row = $this->getRoomTheme($roomid);
			if(!empty($row['RoomTheme'])){
				$result = $this->mongo->query('kkyoo_new_rooms.rooms_theme',array('$set'=>array('RoomId'=>$roomid,'RoomTheme'=>$room_theme)),array('_id'=>$row['RoomTheme']['_id']));
			}else{
				$result = $this->mongo->query('kkyoo_new_rooms.rooms_theme',array('RoomId'=>$roomid,'RoomTheme'=>$room_theme));
			}
			if($result){
				$array = array('Flag'=>100,'FlagString'=>'信息保存成功');
			}else{
				$array = array('Flag'=>101,'FlagString'=>'信息保存失败');
			}
		}else{
			$array = array('Flag'=>102,'FlagString'=>'参数错误');
		}
		return $array;
	}
	
	//获取房间主题存储信息
	public function getRoomTheme($roomid){
		if($roomid > 0){
			$row = $this->mongo->get_row('kkyoo_new_rooms.rooms_theme',array('RoomId'=>$roomid));
			return array('Flag'=>100,'FlagString'=>'房间主题存储信息','RoomTheme'=>$row);
		}
		return array('Flag'=>102,'FlagString'=>'参数错误');
	}
	
	private function getRuleConfig(){
		//得到配置的时间
		$sql = "SELECT rule FROM ".DB_NAME_TPL.".business_rule WHERE id=6";
		$rules = $this->db->get_var($sql);
		$rules = json_decode(urldecode($rules),true);
		return $rules;
	}
	
	private function setRoomCurUser($roomid,$uin,$num){
		$time = time();
		$sql = "SELECT a.curuser FROM ".DB_NAME_NEW_ROOMS.".rooms AS a WHERE a.id={$roomid}";
		$roominfo = $this->groupMysql->get_row($sql,'ASSOC');
		if($num > 0){
			$roominfo['curuser'] += 1;
			$this->groupMysql->query("UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET curuser={$roominfo['curuser']} WHERE id={$roomid};");
			// $this->groupMysql->query("REPLACE INTO ".DB_NAME_NEW_ROOMS.".tbl_rooms_online(room_id,uin,times)VALUES({$roomid},{$uin},{$time})");
		}else{
			$roominfo['curuser'] -= 1;
			if($roominfo['curuser'] < 0) $roominfo['curuser'] = 0;
			$this->groupMysql->query("UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET curuser={$roominfo['curuser']} WHERE id={$roomid}");
			// $this->groupMysql->query("DELETE FROM ".DB_NAME_NEW_ROOMS.".tbl_rooms_online WHERE room_id={$roomid} AND uin={$uin}");
		}
		return array('Flag'=>100,'FlagString'=>'成功');
	}

	//冻结房间
	public function freezeRoom($param){
		$get_superadmin = $this->get_member("chatmanager_tbl", null, $param['Uin']); //超管
		$get_superadmin = $get_superadmin['level'];
		if($get_superadmin != 1){
			return array('Flag'=>101,'FlagString'=>'权限不足，操作失败！');
		}

		$sql = "UPDATE ".DB_NAME_NEW_ROOMS.".rooms SET status=-1 WHERE id='{$param['ChannelId']}'";
		if($this->groupMysql->query($sql)){
			return array('Flag'=>100,'FlagString'=>'冻结成功');
		}else{
			return array('Flag'=>102,'FlagString'=>'冻结失败');
		}
	}
	
	//设置房间人数IP数,明细
	public function RoomCuruserIpNum($roomid,$curuser,$ipnum,$reguser){
		if(empty($roomid)) return array('Flag'=>101,'FlagString'=>'参数错误');
		$roomdb = DB_NAME_NEW_ROOMS;
		if(is_numeric($roomid) && is_numeric($curuser) && is_numeric($reguser)){
			$sql = "SELECT r.group,r.robot_num FROM {$roomdb}.rooms AS r WHERE r.id={$roomid}";
			$roominfo = $this->groupMysql->get_row($sql,'ASSOC');
			if(empty($roominfo))  return array('Flag'=>102,'FlagString'=>'房间不存在');
			$region_id = (int)$roominfo['group'];
			$sql = "UPDATE {$roomdb}.rooms SET curip={$ipnum},curuser={$curuser} WHERE id={$roomid}";
			if( ! $this->groupMysql->query($sql)) return array('Flag'=>103,'FlagString'=>'更新房间人数失败');
			//使用mongod存储明细,汇总
			$this->mongo = domain::main()->GroupDBConn('mongo',$region_id);
			$this->mongo->query(DB_NAME_NEW_ROOMS.'.tbl_roomsuser_history',array('roomid'=>$roomid,'region_id'=>$region_id,'curuser'=>$curuser,'curip'=>$ipnum,'reguser'=>$reguser,'createtime'=>$this->uptime));
			$row = $this->mongo->get_row(DB_NAME_NEW_ROOMS.'.tbl_roomsuser_total',array('roomid'=>$roomid,'region_id'=>$region_id,'createtime'=>$this->today));
			if(empty($row)){
				$this->mongo->query(DB_NAME_NEW_ROOMS.'.tbl_roomsuser_total',array('roomid'=>$roomid,'region_id'=>$region_id,'curuser'=>$curuser,'curip'=>$ipnum,'reguser'=>$reguser,'createtime'=>$this->today));
			}else{
				if($row['curuser'] < $curuser){
					$row['curuser'] = $curuser;
				}
				if($row['curip'] < $ipnum){
					$row['curip'] = $ipnum;
				}
				if($row['reguser'] < $reguser){
					$row['reguser'] = $reguser;
				}
				$this->mongo->query(DB_NAME_NEW_ROOMS.'.tbl_roomsuser_total',array('$set'=>array('curuser'=>$row['curuser'],'curip'=>$row['curip'],'reguser'=>$row['reguser'])),array('_id'=>$row['_id']));
			}
			$arr['Flag'] = 100;
			$arr['FlagString'] = '成功';
			$arr['robot_num'] = $roominfo['robot_num'];
		}else{
			$arr['Flag'] = 101;
			$arr['FlagString'] = '参数错误!';
		}
		return $arr;
	}
	
	//分站在线人数汇总,明细
	public function systemUserNumAndIp($region_id,$curuser,$ipnum,$reguser){
		$region_id = intval($region_id);
		if($region_id <= 0 ){
			return array('Flag'=>101,'FlagString'=>'region_id = '.$region_id);
		}
		$this->mongo = domain::main()->GroupDBConn('mongo',$region_id);
		$this->mongo->query(DB_NAME_NEW_ROOMS.'.tbl_rooms_usertotal_history',array('region_id'=>$region_id,'curusertotal'=>$curuser,'curiptotal'=>$ipnum,'curregusertotal'=>$reguser,'createtime'=>$this->uptime));
		$row = $this->mongo->get_row(DB_NAME_NEW_ROOMS.'.tbl_rooms_usertotal',array('region_id'=>$region_id,'createtime'=>$this->today));
		if(empty($row)){
			$this->mongo->query(DB_NAME_NEW_ROOMS.'.tbl_rooms_usertotal',array('region_id'=>$region_id,'maxcuruser'=>$curuser,'maxcurip'=>$ipnum,'maxcurreguser'=>$reguser,'curusersum'=>$curuser,'curipsum'=>$ipnum,'curregusersum'=>$reguser,'createtime'=>$this->today,'count'=>1));
		}else{
			if($row['maxcuruser'] < $curuser){
				$row['maxcuruser'] = $curuser;
			}
			if($row['maxcurip'] < $ipnum){
				$row['maxcurip'] = $ipnum;
			}
			if($row['maxcurreguser'] < $reguser){
				$row['maxcurreguser'] = $reguser;
			}
			$this->mongo->query(DB_NAME_NEW_ROOMS.'.tbl_rooms_usertotal',array('$set'=>array('maxcuruser'=>$row['maxcuruser'],'maxcurip'=>$row['maxcurip'],'maxcurreguser'=>$row['maxcurreguser']),'$inc'=>array('curusersum'=>$curuser,'curipsum'=>$ipnum,'curregusersum'=>$reguser,'count'=>1)),array('_id'=>$row['_id']));
		}
		return array('Flag'=>100);
	}
	
	//获取总在线人数汇总,明细
	public function userTotalNum($user,$ip,$reguser){
		$this->plat_mongo = db::connect(config('mongodb','channel'),'mongo');
		$this->plat_mongo->query(DB_NAME_NEW_ROOMS.'.tbl_total_userhistory',array('curuser'=>$user,'curip'=>$ip,'reguser'=>$reguser,'createtime'=>$this->uptime));
		$row = $this->plat_mongo->get_row(DB_NAME_NEW_ROOMS.'.tbl_total_usertotal',array('createtime'=>$this->today));
		if(empty($row)){
			$this->plat_mongo->query(DB_NAME_NEW_ROOMS.'.tbl_total_usertotal',array('maxcuruser'=>$user,'maxcurip'=>$ip,'maxcurreguser'=>$reguser,'createtime'=>$this->today,'curusersum'=>$user,'curipsum'=>$ip,'curregusersum'=>$reguser,'count'=>1));
		}else{
			if($row['maxcuruser'] < $user){
				$row['maxcuruser'] = $user;
			}
			if($row['maxcurip'] < $ip){
				$row['maxcurip'] = $ip;
			}
			if($row['maxcurreguser'] < $reguser){
				$row['maxcurreguser'] = $reguser;
			}
			$this->plat_mongo->query(DB_NAME_NEW_ROOMS.'.tbl_total_usertotal',array('$set'=>array('maxcuruser'=>$row['maxcuruser'],'maxcurip'=>$row['maxcurip'],'maxcurreguser'=>$row['maxcurreguser']),'$inc'=>array('curusersum'=>$user,'curipsum'=>$ip,'curregusersum'=>$reguser,'count'=>1)),array('_id'=>$row['_id']));
		}
		
		return array('Flag'=>100);
	}

	public function getRoomConfig($roomid){
		if($roomid > 0){
			$sql = "SELECT ownuin,region_id FROM ".DB_NAME_NEW_ROOMS.".rooms WHERE id={$roomid}";
			$room_row = $this->groupMysql->get_row($sql,"ASSOC");
			if(empty($room_row)) return array('Flag'=>102,'FlagString'=>'房间不存在');
			return array('Flag'=>100,'FlagString'=>'成功','Ownuin'=>(int)$room_row['ownuin'],'RegionId'=>(int)$room_row['region_id']);
		}else{
			return array('Flag'=>101,'FlagString'=>'参数有误');
		}
	}
	
	/*
	*房内消费排行
	*$roomid 房间id
	*$period 排行类型(day,week,month,total)
	*单位
	*/
	public function getRoomConsume($roomid,$rows=10,$period,$unit){
		if($roomid > 0){
			$cycle_array = array('day','week','month','year','total');
			if(!empty($period)){
				$cycle_array = array($period);
			}
			$cachename = "ROOMCONSUMERANK_{$roomid}{$rows}{$period}";
			$result = $this->cache->get($cachename);
			if(empty($result)){
				$long_info = $this->cache->long_get($cachename);
				$rule = new rule($this->mongo);
				$result = $rule->getRuleRank(0,$roomid,0,12,10,$cycle_array);
				foreach($result as $kk=>$rank){
					if(empty($rank)) continue;
					foreach($rank as $key=>$value){
						$sInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$value['UinId'])));
						if($sInfo['Flag'] == 100){
							$result[$kk][$key]['Nick'] = $sInfo['Nick'];
						}else{
							$result[$kk][$key]['Nick'] = $value['UinId'];
						}
						if($unit){
							$result[$kk][$key]['Weight'] = number_format($result[$kk][$key]['Weight']/10000, 2, '.', '');
						}
					}
				}
				
				$this->cache->set($cachename,$result,$this->rank_expire);
			}
			return array('Flag'=>100,'FlagSting'=>'房内消费排行','Result'=>$result);
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	/*
	*房内幸运排行榜
	*$roomid 房间id
	*/
	public function getRoomLucky($uin,$channeluin,$extuin,$ruleid,$period=array(),$rows=10,$time=0){
		if($ruleid >0){
			$result = $this->cache->get("ROOMLUCKRANK_{$uin}{$channeluin}{$extuin}{$ruleid}{$rows}{$time}");
			if(empty($result)){
				$long_info = $this->cache->long_get("ROOMLUCKRANK_{$uin}{$channeluin}{$extuin}{$ruleid}{$rows}{$time}");
				$this->cache->set("ROOMLUCKRANK_{$uin}{$channeluin}{$extuin}{$ruleid}{$rows}{$time}",$long_info,$this->rank_expire);
				$rule = new rule($this->mongo);
				$result = $rule->getRuleRank($uin,$channeluin,$extuin,0,(int)$ruleid,$rows,$period,$time);
				foreach($result as $kk=>$rank){
					if(empty($rank)) continue;
					foreach($rank as $key=>$value){
						if($value['UinId'] > 0){
							$sInfo = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$value['UinId'])));
							if($sInfo['Flag'] == 100){
								$result[$kk][$key]['Nick'] = $sInfo['Nick'];
							}else{
								$result[$kk][$key]['Nick'] = $value['UinId'];
							}
						}
					}
				}
				$this->cache->set("ROOMLUCKRANK_{$uin}{$channeluin}{$extuin}{$ruleid}{$rows}{$time}",$result,$this->rank_expire);
			}
			return array('Flag'=>100,'FlagSting'=>'房内幸运排行榜','Result'=>$result);
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	/*
		获得等级值
		索引值1 索引值2 索引值3(用户id,房间id,群id等) 规则id 周期
	*/
	public function getLevel($uin,$channeluin,$extuin,$ruleid,$period='week'){
		$rule = new rule($this->mongo);
		$level = $rule->getRuleLevel($uin,$channeluin,$extuin,$ruleid,$period);
		return array('Flag'=>100,'FlagString'=>'成功','Level'=>intval($level));
	}
	
	/*
		获得积分值
		索引值1 索引值2 索引值3(用户id,房间id,群id等) 规则id 周期
	*/	
	public function getWeight($uin,$channeluin,$extuin,$ruleid,$period='week'){
		$rule = new rule($this->mongo);
		$wight = $rule->getRuleWeight($uin,$channeluin,$extuin,$ruleid,$period);
		return array('Flag'=>100,'FlagString'=>'成功','Weight'=>intval($wight));
	}
	
	/*
		获得等级值 结果集
		索引值1 索引值2 索引值3(用户id,房间id,群id等) 规则id 周期 记录数 时间
	*/
	public function getLevelResult($uin,$channeluin,$extuin,$ruleid,$period=array(),$rows=10,$time=0){
		if($ruleid >0){
		//	$result = $this->cache->get("ROOMLEVELRESUL_{$uin}{$channeluin}{$ruleid}{$rows}{$time}");
			if(empty($result)){
				$long_info = $this->cache->long_get("ROOMLEVELRESUL_{$uin}{$channeluin}{$ruleid}{$rows}{$time}");
				$this->cache->set("ROOMLEVELRESUL_{$uin}{$channeluin}{$ruleid}{$rows}{$time}",$long_info,$this->rank_expire);
				$rule = new rule($this->mongo);
				$result = $rule->getRuleLevelResult($uin,$channeluin,$extuin,(int)$ruleid,$rows,$period,$time);
				$this->cache->set("ROOMLEVELRESUL_{$uin}{$channeluin}{$ruleid}{$rows}{$time}",$result,$this->rank_expire);
			}
			return array('Flag'=>100,'FlagString'=>'成功','Result'=>$result);
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	/*
	获取商城功能牌
	*/
	public function FunCardList($group_id,$uin){
		$categories = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'Categories','GroupId'=>$group_id,'Category'=>3,'State'=>0)));
		if($categories['Flag'] != 100){
			return array('Flag'=>101,'FlagString'=>'没有功能牌');
		}
		$categories = (array)$categories['Category'];
		$cardlist = array();
		foreach ($categories as $category) {
			$goods = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'GoodsOnCategory','GroupId'=>$group_id,'Category'=>3,'CategoryId'=>$category['id'],'State'=>0)));
			if($goods['Flag'] != 100){
				continue;
			}
			$goods = (array)$goods['Goods'];
			foreach ($goods as $k => $v) {
				$commodityInfo = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'GetCommodityInfo','CommodityId'=>$v['commodity_id'],'Category'=>3)));
				if($commodityInfo['Flag'] == 100){
					$commodityInfo = (array)$commodityInfo['CommodityInfo'];
					$commodityInfo['name'] = $v['commodity_name'];
					$cardlist[$v['id']] = $commodityInfo;
				}
			}
		}
		$funs = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'GetStock','Category'=>3,'Data'=>array('uin'=>$uin,'case_id'=>10060,'group_id'=>$group_id))));
		$funs = (array)$funs['Stocks'];
		$commodities = array_keys($cardlist);
		foreach ((array)$funs as $val) {
			if(in_array($val['commodity'], $commodities)){
				$cardlist[$val['commodity']]['Num'] = intval($val['quality']);
			}
		}
		$rst['Flag'] = 100;
		$rst['FlagString'] = '成功';
		$rst['Commodities'] = $cardlist;
		return $rst;
	}

	public function daemonList($group_id,$room_id,$artist){
		$roles_info = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'GetRole','GroupId'=>$group_id,'ChannelId'=>$room_id,'RoleId'=>array(10390,10391),'Category'=>5,'Artist'=>$artist)));
		if($roles_info['Flag'] != 100){
			return $roles_info;
		}
		foreach ((array)$roles_info['Roles'] as $key => $val) {
			$uinInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$val['uin'])));
			$roles_info['Roles'][$key]['nick'] = $uinInfo['baseInfo']['nick'];
			$roles_info['Roles'][$key]['uin_link'] = cdn_url(PIC_API_PATH."/uin/{$val['uin']}/40/40.jpg");
		}
		return $roles_info;
	}
	
	/*
		人气票赠送
	*/
	public function PopularVote($param,$extparam){
		$logbuild = new logbuild();
		$checkWork = $logbuild->checkWork($param,$extparam);
		$moneyweight = $checkWork['Data']['MoneyWeight'];
		$targetuin = $param['TargetUin'];
		if($checkWork['Flag'] == 100 &&$moneyweight > 0){
			$TradeDesc = '用户'.$param['Uin'].'在房间'.$param['ChannelId'].'赠送人气票获得'.$moneyweight;
			$param['TargetUin'] = $param['Uin'];
			$kmoneyTrade = $this->trade($param,101,$moneyweight,$TradeDesc,true);
			if($kmoneyTrade['Flag'] != 100){
				return $kmoneyTrade;
			}
			$kmoneyTrade['log']['extparam'] = $extparam;
			$log[] = $kmoneyTrade['log'];
			unset($kmoneyTrade['log']);
		}else{
			$log[] = getLogData($param,$extparam);
		}
		$uin_type = getChannelType($param['TargetUin'],$param['ChannelId']);
		$param['ActorUin'] = $uin_type > 0 ? (int)$param['TargetUin'] : 0;
		$param['TargetUin'] = $targetuin;
		$result = array('Flag'=>100,'FlagString'=>'操作成功','LogData'=>$log);
		isset($kmoneyTrade['balance']) ? $result['UinBalance'] =$kmoneyTrade['balance'] : '';
		isset($kmoneyTrade['VoucherBalance']) ? $result['VoucherBalance'] =$kmoneyTrade['VoucherBalance'] : '';
		$moneyweight > 0 ? $result['VoteWeight']=$moneyweight : ''; 
		return $result;
	}
	
	//获取距离下一等级积分差
	public function scoreDiff($uin,$channeluin,$extuin,$ruleid,$period='week'){
		$rule = new rule($this->mongo);
		$weight = $rule->getRuleWeight($uin,$channeluin,$extuin,$ruleid,$period);
		$sql = 'SELECT integration FROM '.DB_NAME_TPL.'.business_param_config WHERE rule_id = '.$ruleid;
		$var = $this->db->get_var($sql,'ASSOC');
		$int_obj = json_decode(urldecode($var),true);
		foreach($int_obj as $key=>$value){
			if($weight >=$value['one'] && $weight < $value['two']){
				$diff = $value['two'] - $weight +1;
				$level = $value['value'];
				$thisLevelWeight=$value['one'];
				$nextLevelWeight=$value['two'];
				break;
			}
		}
		$roleData=array(
			'extparam'=>array(
				'Tag'=>'GetRole',
				'GroupId'=>$extuin,
				'Uin'=>$uin,
				'Ruleid'=>$ruleid
			)
		);
		$roleInfo=httpPOST(ROLE_API_PATH,$roleData);
		return array('Flag'=>100,'FlagString'=>'成功','Weight'=>$weight,'Level'=>$level,'Diff'=>$diff,'ThisLevelWeight'=>$thisLevelWeight,'NextLevelWeight'=>$nextLevelWeight,'RolesImg'=>$roleInfo['Roles'][0]['role_small_icon'],'RolesName'=>$roleInfo['Roles'][0]['name']);
	}
	
	public function getConfig($group_id){
		//获取广播嗽叭价格
		$sql = "SELECT * FROM ".DB_NAME_NEW_ROOMS.".tbl_roomsbroadcast_config WHERE group_id = ".$group_id;
		$row = $this->groupMysql->get_row($sql,"ASSOC");
		$row['room_bc_price'] = $row['room_bc_price'] > 0 ? $row['room_bc_price'] : 5000;//房间喇叭价格
		$row['site_bc_price'] = $row['site_bc_price'] > 0 ? $row['site_bc_price'] : 20000;//全站广播价格
		$row['signet_times'] = $row['signet_times'] > 0 ? $row['signet_times'] : 30;//印章时效
		$row['runway_price'] = $row['runway_price'] > 0 ? $row['runway_price'] : 200000;//跑道礼物价格
		$row['pick_price'] = $row['pick_price'] == '' ? 100000 : $row['pick_price'];//点歌价格
		
		$sql = "SELECT ext FROM ".DB_NAME_GROUP.".footer WHERE group_id = ".$group_id;
		$var = json_decode($this->db->get_var($sql,"ASSOC"),true);
		// $var['robotNum']['value'] = (int)$var['robotNum']['value'] > 0 ? (int)$var['robotNum']['value'] : 300;

		//守护列表
		$daemonCategory = 5;
		$daemonList = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'GetGoodsOnCat','Category'=>$daemonCategory,'GroupId'=>$group_id)));
		$daemonList = (array)$daemonList['Lists'];

		return array('Flag'=>100,'FlagString'=>'成功','RoomPrice'=>$row['room_bc_price'],'SitePrice'=>$row['site_bc_price'],'ActorRuleid'=>(int)$var['artistRankRuleId']['value'],'RichRuleid'=>(int)$var['richRankRuleId']['value'],'GetGiftRuleid'=>(int)$var['getGiftRuleId']['value'],'PvoteRuleid'=>(int)$var['PvoteRuleId']['value'],'StampExpire'=>$row['signet_times'],'RunwayPrice'=>$row['runway_price'],'PickPrice'=>$row['pick_price'],'ActPercentage'=>$row['act_percentage'], 'TaxPercentage'=>$row['tax_percentage'],'DaemonList'=>$daemonList);
	}
	
	function setState($param, $json){
		$json['Tag'] = "SetState";
		return httpPOST("api/group/vip_api.php", array("param"=>$param, "extparam"=>$json));
	}
	
	public function GetUserRole($param,$json){
		if($json['Roleid'] > 0 && $param['Uin']>0 && $param['GroupId'] > 0 && $json['Ruleid'] > 0){
			$roles_info = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'GetRole','Uin'=>$param['Uin'],'GroupId'=>$param['GroupId'],'ChannelId'=>$param['ChannelId'],'Ruleid'=>$json['Ruleid'],'RoleId'=>$json['Roleid'])));
			return $roles_info;
		}
		return array('Flag'=>101,'FlagString'=>'参数有误');
	}
	
	//根据三级科目查询用户所在站商品库存
	/*
		用户id	站id	三级id	二级id	是否进场动画	是否需要返回商品详情
	*/
	private function getParentNum($uin,$caseid,$parentid=0,$room_id=0){
		if($uin > 0 && $caseid > 0){
			$info = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'GetCategoryByCaseId','CaseId'=>$caseid)));
			$category = $info['Flag'] == 100 ? $info['Category'] : 4;
			$result = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'GetStock','Category'=>$category,'Data'=>array('uin'=>$uin,'parent_id'=>$parentid,'case_id'=>$caseid,'room_id'=>$room_id))));
			return $result;
		}
		return array('Flag'=>101,'FlagString'=>'参数错误');
	}
	
	/**
	* 交易金额操作
	* @parent interge 一级业务
	* @child interge 二级业务
	* @uin interge 用户id
	* @money floot 交易金额
	* @desc string 描述
	*/
	private function trade($param,$child,$money,$desc,$islog=false){
		$param['ChildId'] = $child;
		$param['MoneyWeight'] = $money;
		$param['Desc'] = $desc;
		$log = getLogData($param,$extparam);
		$request = array('param'=>$param,'extparam'=>array('Tag'=>'Kmoney','Operator'=>'67CB9A8B12FC827EF5C008EE4F1B2E0F','GroupId'=>$param['GroupId']));
		$rst = httpPOST(KMONEY_API_PATH,$request);
		if($rst['Flag'] != 100) return $rst;
		$array = array('Flag'=>100,'balance'=>$rst['LastBalance']);
		$islog ? $array['log'] = $log :'';
		if($rst['fund_type'] == 'Kmoney'){
			return $array;
		}else{
			$array['VoucherBalance'] = $rst['LastBalance'];
			unset($array['balance']);
			isset($rst['KmoneyBalance']) ? $array['balance'] =$rst['KmoneyBalance'] : '';
		}
		return $array;
	}
}