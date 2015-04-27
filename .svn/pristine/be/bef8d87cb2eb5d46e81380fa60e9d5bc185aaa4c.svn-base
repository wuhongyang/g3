<?php
require_once 'common.php';

$module = empty($_GET['module']) ? 'room' : $_GET['module'];
// if(empty($user['channel_id'])){
	// ShowMsg('请先申请室主并签约房间，再进入房间管理！','/rooms/join.php?module=info&type=3');
// }

$Uin=$user['Uin'];
$Nick=$user['Nick'];

$param = array(
	'extparam' => array('Tag'=>"GetUserRooms",'Uin'=>$user['Uin']),
	'param' => array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10099,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>"获取用户房间")
);
$roomInfo=request($param);
if($roomInfo['Flag']!=100){
	ShowMsg('您没有权限管理房间',-1);
}
$roomInfo=$roomInfo['Result'];
$roomid=$roomInfo['id'];

//站详情
$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$roomInfo['group'],'IsDetails'=>true),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
);
$userGroupInfo=request($param);
$userGroupInfo=$userGroupInfo['Result'];

//绑定的积分规则表
$param=array(
	'extparam'=>array('Tag'=>'getBusinessRule'),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'GroupId'=>$userGroupInfo['groupid'],'Desc'=>'')
);
$ruleList = request($param);
$ruleList = $ruleList['list'];
switch ($module) {
	case 'room':
		//更新房间信息
		if($_POST){
			//$name = addslashes(trim($_POST['name']));
			$name = addslashes(htmlspecialchars(mb_substr(trim($_POST['name']), 0, 20, 'UTF-8'),ENT_QUOTES));
			$description = addslashes(trim($_POST['description']));
			$salutatory = addslashes(trim($_POST['salutatory']));
			$bgalign = intval($_POST['bgalign']);
			$type = intval($_POST['type']);
			$robot_base_num = intval($_POST['robot_base_num']);
			$robot_num = intval($_POST['robot_num']);
			$param = array(
				'extparam' => array('Tag'=>"saveRoomInfo",'Uin'=>$Uin,'name'=>$name,'description'=>$description,'salutatory'=>$salutatory,'room_id'=>$roomid,'bgalign'=>$bgalign,'type'=>$type,'robot_base_num'=>$robot_base_num,'robot_num'=>$robot_num),
				'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10099,'ChildId'=>101,'Desc'=>"房间信息更新")
			);
			$saveRoomInfo = request($param);
			if($saveRoomInfo['Flag']!=100){
				alertMsg($saveRoomInfo['FlagString']);
			}
			$tmp_cover = dirname(dirname(__FILE__)).$_POST['room_cover'];
			if(file_exists($tmp_cover) && is_file($tmp_cover)){
				$size = json_decode($_POST['room_cover_coords'],true);
				$bytes = file_get_contents($tmp_cover);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'roomid','Index'=>$roomid,'crop'=>$size);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				if($query['rst'] != 100) ShowMsg('房间封面上传失败');
			}
			if(file_exists($_FILES['room_bg']['tmp_name']) && strstr($_FILES['room_bg']['type'],'image')){
				$bytes = file_get_contents($_FILES['room_bg']['tmp_name']);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'roombg','Index'=>$roomid);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				if($query['rst'] != 100) ShowMsg('房间背景上传失败');
			}elseif(isset($_POST['default_roombg']) && is_numeric($_POST['default_roombg'])){
				$bytes = file_get_contents("../pic/roombg/{$_POST['default_roombg']}.jpg");
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'roombg','Index'=>$roomid);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				if($query['rst'] != 100) ShowMsg('房间背景上传失败');
			}
			
			$urls = array(
				PIC_API_PATH."/roomid/{$roomid}/80/60.jpg",//个人中心:80*60
				PIC_API_PATH."/roomid/{$roomid}/260/190.jpg",//大厅大图:260*190
				PIC_API_PATH."/roomid/{$roomid}/150/110.jpg",//大厅小图:150*110
				PIC_API_PATH."/roomid/{$roomid}/200/150.jpg",//推荐位图片:200*150
				PIC_API_PATH."/roombg/{$roomid}/80/60.jpg",//个人中心:80*60
				PIC_API_PATH."/roombg/{$roomid}/0/0.jpg"//房间背景:0*0
			);
			cdn_url($urls, 'CDN_UPLOAD_CLEAR');
		}
		//房间信息
		$param = array(
			'extparam' => array('Tag'=>"getRoomInfo",'Uin'=>$Uin,'RoomId'=>$roomid),
			'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10099,'ChildId'=>101,'Desc'=>"房间信息查询")
		);
		$result = request($param);
		if($result['Flag'] == 100){
			$name 		 = $result['info']['name'];
			$description = $result['info']['description'];
			$salutatory  = $result['info']['salutatory'];
			$bgalign	 = $result['info']['bgalign'];
			$roles = $result['info']['Role'];
			if(empty($roles)){
				$roles=array(8=>'站长');
			}
			$selectRole = $result['info']['SelectRoles'][0]['type'];
			$robotNum = $result['info']['robot_num'];
			$robotNum = $robotNum?$robotNum:300;
			$robotBaseNum = $result['info']['robot_base_num'];
			$robotBaseNum = $robotBaseNum?$robotBaseNum:0;
			$maxuser = $result['info']['maxuser'];
		}else{
			ShowMsg($result['FlagString'],'index.php');
		}
		$title='房间管理-房间信息设置';
		$template = "room";
		break;
	case 'enter':
		$template = "enter";
		$title='房间管理-进入房间设置';
		if($_POST){
			$member_value = $_POST['member_value']? explode(",",addslashes($_POST['member_value'])) : array();
			$deny_value = $_POST['deny_value']? explode(",",addslashes($_POST['deny_value'])) : array();
			$passwd = addslashes($_POST['passwd']);
			$status = intval($_POST['status']);
			intval($_POST['open_status']) == 4? $status = 4 : "";
			$param = array(
				'extparam' => array('Tag'=>"saveEnterInfo",'Uin'=>$Uin,'status'=>$status,'passwd'=>$passwd,'member_value'=>$member_value,'deny_value'=>$deny_value,'room_id'=>$roomid),
				'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10100,'ChildId'=>102,'Desc'=>"房间进入管理更新")
			);
			$result = request($param);
			if($result['Flag'] == 100){
				ShowMsg($result['FlagString'], 'roommanage.php?module=enter');
			}else{
				ShowMsg($result['FlagString'], -1);
			}
		}
		$param = array(
			'extparam' => array('Tag'=>"getEnterInfo",'RoomId'=>$roomid),
			'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10102,'ChildId'=>101,'Desc'=>"房间进入管理查看")
		);
		$result = request($param);
		if($result['Flag'] == 100){
			$status  = $result['info']['status'];
			$passwd  = $result['info']['passwd'];
			$member  = @implode(",", $result['info']['member']);
			$deny	 = @implode(",", $result['info']['deny']);
		}else{
			ShowMsg($result['FlagString'],'index.php');
		}
		break;
	case 'order':
		$template = "order";
		$title='房间管理-排麦规则设置';
		if($_POST){
			$mike_power = intval($_POST['mike_power']);
			$member = $_POST['member']?explode(",",addslashes($_POST['member'])):array();
			$main_video_time = intval($_POST['main_video_time']);
			$param = array(
				'extparam' => array('Tag'=>"saveOrderInfo",'Uin'=>$Uin,'room_id'=>$roomid,'mike_power'=>$mike_power,'member'=>$member,'main_video_time'=>$main_video_time),
				'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10102,'ChildId'=>102,'Desc'=>"房间排麦规则更新")
			);
			$result = request($param);
			if($result['Flag'] == 100){
				ShowMsg($result['FlagString'], 'roommanage.php?module=order');
			}else{
				ShowMsg($result['FlagString'],'index.php');
			}
		}
		$param = array(
			'extparam' => array('Tag'=>"getOrderInfo",'RoomId'=>$roomid),
			'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10102,'ChildId'=>101,'Desc'=>"房间排麦规则查看")
		);
		$result = request($param);
		if($result['Flag'] == 100){
			$mike_power = $result['info']['mike_power'];
			$main_video_time = $result['info']['main_video_time'];
			$member = implode(",", (array)$result['info']['member']);
		}else{
			ShowMsg($result['FlagString'],'index.php');
		}
		break;
	case 'admin':
		$template = "admin";
		$title='房间管理-管理员设置';
		if($_POST){
			//只处理当前选中类型下的内容
			$member = json_decode($_POST['member'], true);
			foreach ($member as $roleid=>$val){
				$member[$roleid] = explode(",", $val);
			}
			foreach ($member as $roleid=>$val){
				foreach ($val as $key1=>$val1){
					if(isset($val1) && $val1 != "")
						$member1[$roleid][] = $val1;
				}
			}
			$role_id = $_POST['role_id'];
			
			$param = array(
				'extparam' => array('Tag'=>"saveManagerInfo",'Uin'=>$Uin,'room_id'=>$roomid,'role_id'=>$role_id,'member'=>$member1[$role_id]),
				'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10101,'ChildId'=>102,'Desc'=>"房间管理员更新")
			);
			$result	 = request($param);
			if($result['Flag'] == 100){
				ShowMsg($result['FlagString'], 'roommanage.php?module=admin');
			}else{
				ShowMsg($result['FlagString'], 'roommanage.php?module=admin');
			}
		}
		
		//站详情
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$roomInfo['group']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
		);
		$userGroupInfo=request($param);
		$userGroupInfo=$userGroupInfo['Result'];
		
		//站下角色
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupRole','RoleShowOne'=>array(2),'RoleShowTwo'=>array(1)),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下角色','GroupId'=>$gruopId)
		);
		$roleList=request($param);
		$roleList=$roleList['list'];
		
		//该房间下的角色及包括的uin
		$param = array(
			'extparam' => array('Tag'=>"getManagerInfo",'RoomId'=>$roomid),
			'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10101,'ChildId'=>101,'Desc'=>"房间管理员查看")
		);
		$result = request($param);
		
		if($result['Flag'] == 100){
			foreach ($result['roles'] as $key=>$val){
				$member[$val['id']][] = $val['uin'];
			}
			foreach ($member as $role_id=>$val){
				$member[$role_id] = implode(",", $val);
			}
		}else{//没有数据
			foreach ($roleList as $val){
				$member[$val['id']] = "";
			}
		}
		$member = json_encode($member);
		break;
	case 'release':
		$template = "release";
		$title='房间管理-释放封杀设置';
		$type = $_GET['type'];
		$is_ip_type = $type=="ip";
		if($_POST){
			$tag = $is_ip_type? "releaseIP" : "releaseID";
			$childid = $is_ip_type? 104 : 102;
			$desc = $is_ip_type? "被封杀IP释放" : "被踢出用户释放";
			$release_ids = $_POST['release_ids'];
			$param = array(
				'extparam' => array('Tag'=>$tag,'Uin'=>$Uin,'id'=>$release_ids,'member'=>$member),
				'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10103,'ChildId'=>$childid,'Desc'=>$desc)
			);
			$result = request($param);
			if($result['Flag'] == 100){
				ShowMsg($result['FlagString'], 'roommanage.php?module=release&type='.$type);
			}else{
				ShowMsg($result['FlagString'], 'roommanage.php?module=release&type='.$type);
			}
		}
		$tag = $is_ip_type? "getReleaseIPInfo" : "getReleaseIDInfo";
		$childid = $is_ip_type? 103 : 101;
		$desc = $is_ip_type? "被封杀IP查看" : "被踢出用户查看";
		$param = array(
			'extparam' => array('Tag'=>$tag,'RoomId'=>$roomid),
			'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10103,'ChildId'=>$childid,'Desc'=>$desc)
		);
		$result = request($param);
		if($result['Flag'] != 100){
			ShowMsg($result['FlagString'],'index.php');
		}
		break;
	case 'roomnotice':
		$title='房间管理-房间轮播公告设置';
		$template = 'roomnotice';
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			if(empty($_FILES)){
				alertMsg('上传图片不能超过2M');
			}
			if(empty($_FILES['cover']['name'])){
				alertMsg('请上传图片');
			}
			if($_FILES['cover']['size']/(1024*1024) > 2){
				alertMsg('上传图片不能超过2M');
			}
			$allow_type = array('gif', 'jpg', 'jpeg', 'png','pjpeg');
			$types = explode('/', $_FILES['cover']['type']);
			$type = $types[count($types)-1];
			if(!in_array($type, $allow_type)){
				alertMsg('请上传JPG、JPEG、GIF和PNG文件，最大2M');
			}
			//取得已有房间公告
			$param = array(
				'extparam' => array('Tag'=>"getRoomNotice",'Roomid'=>$roomid),
				'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10153,'ChildId'=>101,'Desc'=>"获取房间公告")
			);
			$result = request($param);
			$roomNotice = json_decode($result['roomNotice'],true);
			if(count($roomNotice) >= 6){
				showMsg('最多上传6张');
			}

			$imgInfo = pathinfo($_FILES['cover']['name']);
			if(empty($imgInfo['extension'])){
				echo '<script>alert("图片上传失败,请重试");parent.window.location="room_manage.php";</script>';
				exit;
			}
			$bytes = file_get_contents($_FILES['cover']['tmp_name']);
			$index = md5($bytes.time()); //防止上传同一张图md5值相同导致覆盖
			$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
			$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
			$uprst = array('Flag'=>$query['rst'],'File'=>$index);
			if($uprst['Flag'] != 100) ShowMsg('房间公告上传失败');

			//将新加公告与原有公告合并保存
			$file = array('desc'=>$_POST['url'],'img_name'=>$uprst['File']);
			$roomNotice[] = $file;
			$param = array(
				'extparam' => array('Tag'=>"setRoomNotice",'RoomNotice'=>json_encode($roomNotice),'Roomid'=>$roomid),
				'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10153,'ChildId'=>102,'Desc'=>"房间公告管理更新")
			);
			$result = request($param);
			if($result['Flag'] == 100){
				//$file['del_key'] = md5($file['img_name'].$file['desc']); //删除索引
				//$file['img_name'] = PIC_API_PATH."/p/{$file['img_name']}/80/88.jpg";
				//$save_result = json_encode($file);
				alertMsg('上传成功','?module=roomnotice');
			}else{
				alertMsg('上传失败');
			}
		}else{
			$param = array(
				'extparam' => array('Tag'=>"getRoomNotice",'Roomid'=>$roomid),
				'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10153,'ChildId'=>101,'Desc'=>"房间公告管理查看")
			);
			$result = request($param);
			if($result['Flag'] != 100) ShowMsg($result['FlagString'],'index.php');

			$roomNotice = json_decode($result['roomNotice'],true);
			if(isset($_GET['del_roomnotice'])){
				foreach($roomNotice as $key=>$val){
					if(md5($val['img_name'].$val['desc']) == $_GET['del_roomnotice']){
						unset($roomNotice[$key]);
						$roomNotice = array_values($roomNotice);
						$param = array(
							'extparam' => array('Tag'=>"setRoomNotice",'RoomNotice'=>json_encode($roomNotice),'Roomid'=>$roomid),
							'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10153,'ChildId'=>102,'Desc'=>"房间公告管理更新")
						);
						$rst = request($param);
						if($rst['Flag'] != 100){
							ShowMsg('删除失败');
						}else{
							ShowMsg('','?module=roomnotice');
						}
						break;
					}
				}
			}
			foreach($roomNotice as $key=>$val){
				$roomNotice[$key]['img_name'] = cdn_url(PIC_API_PATH."/p/{$val['img_name']}/80/88.jpg");
				$roomNotice[$key]['del_key'] = md5($val['img_name'].$val['desc']); //删除索引
			}
		}
		break;
	case 'recommend':
		if($_POST){
			$param = array(
				'extparam' => array('Tag'=>"PostInfo",'Data'=>$_POST),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10155,'ChildId'=>102,'Desc'=>"提交房间推荐位")
			);
			$rst = request($param);
			alertMsg($rst['FlagString'],'?module=recommend');
		}
		$param = array(
			'extparam' => array('Tag'=>"GetRoomInfo",'Roomid'=>$roomid),
			'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10155,'ChildId'=>101,'Desc'=>"获取房间信息")
		);
		$info = request($param);
		$info = $info['Info'];
		if(!empty($info)){
			$info['worktime'] = json_decode($info['worktime'],true);
		}
		//站详情
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$roomInfo['group']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
		);
		$userGroupInfo=request($param);
		$userGroupInfo=$userGroupInfo['Result'];
		$template = 'recommend';
		$title='房间管理-推荐位申请';
		break;
	case 'reapply':
		$param = array(
			'extparam' => array('Tag'=>"ReApply",'Id'=>intval($_GET['roomid'])),
			'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10155,'ChildId'=>101,'Desc'=>"返回修改")
		);
		$rst = request($param);
		exit;
	break;
	case 'roomip':
		$template="roomip";
		$title='房间管理-在线人数统计';
		
		$startDate=$_GET['start'];
		$endDate=$_GET['end'];
		$data=array(
			'Start'=>$startDate,
			'End'=>$endDate
		);
		$param=array(
			'extparam'=>array('Tag'=>"GetRoomStatistics",'RoomId'=>$roomid,'Data'=>$data),
			'param'=>array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10251,'ChildId'=>101,'Desc'=>"房间在线人数统计")
		);
		$result=request($param);
		$rooms_online=$result['Result'];
		$page=$result['Page'];
		//站详情
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$roomInfo['group']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
		);
		$userGroupInfo=request($param);
		$userGroupInfo=$userGroupInfo['Result'];
		
	break;
	case 'roomranks':
		if($_POST['submited']){
			$_POST['Ruleid'] = $_POST['Ruleid']?$_POST['Ruleid']:array();
			$rule = array();
			foreach ($_POST['Ruleid'] as $key=>$val){
				if(!in_array($val, $rule)){
					$ranks[] = array(
									"Ruleid" => $val,
									"name" => rawurlencode($_POST['name'][$key]),
									"Rows" => $_POST['Rows'][$key],
								);
				}
				$rule[] = $val;
			}
			$param = array(
				'extparam' => array('Tag'=>"SetRoomRanks",'Ranks'=>$ranks),
				'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10101,'ChildId'=>101,'ChannelId'=>$roomid,'Desc'=>"房间排行设置")
			);
			$roomranks = request($param);
			alertMsg($roomranks['FlagString'],$_SERVER['REQUEST_URI']);
		}
		/*$param = array(
			'extparam' => array('Tag'=>"GetRoomRanks"),
			'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10101,'ChildId'=>101,'ChannelId'=>$roomid,'Desc'=>"获取排行设置")
		);
		$roomranks = request($param);*/
		$param = array(
				'extparam' => array('Tag'=>"GetRoomRanks"),
				'param' => array('Uin'=>$Uin,'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10101,'ChildId'=>101,'ChannelId'=>$roomid,'Desc'=>"获取排行设置")
		);
		$roomranks = request($param);
		$rank = array();
		foreach ($roomranks['Ranks'] as $key1=>$val1){
			foreach ($ruleList as $key=>$val){
				if($val['id'] == $val1['Ruleid']){
					$rank[] = array(
							"Ruleid" => $val1['Ruleid'],
							"name" => $val1['name'],
							"Rows" => $val1['Rows'],
							"rule_name" => $val['name'],
							"desc" => $val['desc'],
							"sort" => $val['sort_type']
							);
				}
			}
		}
		$template = 'roomranks';
		$title='房间管理-房间排行榜设置';
	break;
	case 'doll':
		$title='艺人娃娃查询';
		$template='doll';
		$type=isset($_GET['type'])?$_GET['type']:1;
		if(!in_array($type,array(1,2,3))){
			header('Location:/404.html');
			exit;
		}
		
		//查询条件
		$time=!empty($_GET['time'])?date('Ymd',strtotime($_GET['time'])):'';
		$artistId=!empty($_GET['artistId'])?intval($_GET['artistId']):'';
		$search=array(
			'ChannelUin'=>$roomid,
			'UinId'=>$artistId,
			'Uptime'=>$time
		);
		
		//汇总列表
		$param=array(
			'extparam'=>array('Tag'=>'IntegralSearch','Type'=>$type,'RuleId'=>41,'GroupId'=>$group_id,'Search'=>$search),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10064,'ParentId'=>10735,'ChildId'=>101)
		);
		$result=request($param);
		$page=$result['Page'];
		$list=array();
		if($result['Data']){
			foreach($result['Data'] as $val){
				if(isset($list[$val['UinId'].'_'.$val['ChannelUin'].'_'.$val['Uptime']])){
					if($val['FourthId']==10667){//歌舞娃娃
						$list[$val['UinId'].'_'.$val['ChannelUin'].'_'.$val['Uptime']]['Doll']=$val['Weight'];
					}
					else{//歌舞皇后
						$list[$val['UinId'].'_'.$val['ChannelUin'].'_'.$val['Uptime']]['Empress']=$val['Weight'];
					}
				}
				else{
					if($val['FourthId']==10667){//歌舞娃娃
						$val['Doll']=$val['Weight'];
					}
					else{//歌舞皇后
						$val['Empress']=$val['Weight'];
					}
					$list[$val['UinId'].'_'.$val['ChannelUin'].'_'.$val['Uptime']]=$val;
				}
			}
			foreach($list as $key=>$val){
				$userInfo=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$val['UinId'])));
				$list[$key]['Nick']=$userInfo['baseInfo']['nick'];
				$param = array(
						'extparam' => array('Tag'=>'GetArtistSalary','Uin'=>$val['UinId'],'RoomId'=>$val['ChannelUin'],'GroupId'=>$group_id),
						'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10738,'ChildId'=>102)
				);
				$result = request($param);
				$list[$key]['Salary']=$result['Salary'];
			}
		}
		$data=array(
			'type'=>$type,
			'time'=>$_GET['time'],
			'list'=>$list,
			'page'=>$page
		);
	break;
}

$serviceType='room_manage';
if($themes=='default'){
	$tpl = template::getInstance();
	$tpl->setOptions(get_config('template','service'));
}
else{
	$tmp_config=get_config('template','group_site');
	$tmp_config['template_dir'].=$themes.'/tpl/service/';
	$tmp_config['cache_dir'].=$themes.'/tpl/service/';
	$tpl = template::getInstance();
	$tpl->setOptions($tmp_config);
}
include template("roommanage/".$template.".html",$tpl);
