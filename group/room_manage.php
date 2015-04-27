<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'room_list':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
$Uin=$user['Uin'];
$Nick=$user['Nick'];
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
$groupId=(int)$permisssions['groupId'];
$permission=(array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];

//站详情
$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$groupId,'IsDetails'=>true),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'获取站信息')
);
$userGroupInfo=request($param);
$userGroupInfo=$userGroupInfo['Result'];

//绑定的积分规则表
$param=array(
	'extparam'=>array('Tag'=>'getBusinessRule'),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'GroupId'=>$groupId,'Desc'=>'')
);
$ruleList = request($param);
$ruleList = $ruleList['list'];

switch($module){
	case 'room_list':
		//权限判断
		if($menuPermissions['roomManage']<=0){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='房间管理-房间列表';
		$template='room_list';
		
		//站下房间
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupRoomsList','GroupId'=>$userGroupInfo['groupid'],'ChannelId'=>$_GET['channel_id']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下房间')
		);		
		$userRooms=request($param);
		
		//是否拥有开设房间权限
		if(checkGroupPermission(10260,$permission)){
			$openRoom=true;
		}
		
		//如果拥有房间管理的一个权限则显示房间管理连接
		$parent=array(10099,10100,10101,10102,10103,10153,10155,10223,10251,10257);
		$intersect=array_intersect($permission,$parent);
		if(!empty($intersect)){
			$roomManage=true;
		}
		
		//如果没有更新房间信息模块的权限，将默认模块转入拥有权限的模块
		$roomManageModule='room_info';
		if(checkGroupPermission(10099,$permission)){
			$roomManageModule='room_info';
		}
		elseif(checkGroupPermission(10100,$permission)){
			$roomManageModule='enter';
		}
		elseif(checkGroupPermission(10101,$permission)){
			$roomManageModule='admin';
		}
		elseif(checkGroupPermission(10102,$permission)){
			$roomManageModule='order';
		}
		elseif(checkGroupPermission(10103,$permission)){
			$roomManageModule='release';
		}
		elseif(checkGroupPermission(10153,$permission)){
			$roomManageModule='roomnotice';
		}
		elseif(checkGroupPermission(10155,$permission)){
			$roomManageModule='recommend';
		}
		elseif(checkGroupPermission(10251,$permission)){
			$roomManageModule='roomip';
		}
		elseif(checkGroupPermission(10257,$permission)){
			$roomManageModule='roomranks';
		}
	break;
	case 'open_room':
		//是否拥有开设房间权限
		if(!checkGroupPermission(10260,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='房间管理-开设房间';

		//站所有房间界面
		$rooms_ui = json_decode($userGroupInfo['room_ui']);
		//模板界面
		$templates_ui = empty($userGroupInfo['ktv_template']) ? array() : json_decode($userGroupInfo['ktv_template'], true);
		$param = array(
				'extparam' => array('Tag'=>"GetRoomUi", 'RoomsUi'=>$rooms_ui),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10398,'ChildId'=>101,'Desc'=>"查看房间界面")
		);
		$rst = request($param);
		$package_list = (array)$rst['Results'];
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupRoomsList','GroupId'=>$userGroupInfo['groupid'],'ChannelId'=>$_GET['channel_id']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下房间')
		);		
		$userRooms=request($param);
		$userRoomsTotal=$userRooms['total'];
		$roomsTotal=$userGroupInfo['open_num']-$userRoomsTotal>0?$userGroupInfo['open_num']:$userRoomsTotal;
		$roomsFreezeTotal=$userRooms['freezeTotal'];
		$roomsSurplusTotal=$roomsTotal-$userRoomsTotal;
		$template='open_room';
	break;
	case 'open_room_submit':
		//是否拥有开设房间权限
		if(!checkGroupPermission(10260,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		// $_POST['room_ui'] = (!empty($_POST['template_ui']) && $_POST['template_ui'] !=-1) ? 38 : $_POST['room_ui'];
		$data=array(
			//'ProvinceId'=>$userGroupInfo['province'],
			//'CityId'=>$userGroupInfo['city'],
			//'AreaId'=>$userGroupInfo['area'],
			//'RegionId'=>$userGroupInfo['region_id'],
			'GroupId'=>$userGroupInfo['groupid'],
			'OpenNum'=>$userGroupInfo['open_num'],
			'GroupUin'=>$userGroupInfo['uin'],
			'RoomUi'=>$_POST['room_ui'],
			'TemplateUi'=>$_POST['template_ui']
		);
		$param=array(
			'extparam'=>array('Tag'=>'OpenRoom','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>102,'Uin'=>$userGroupInfo['uin'],'GroupId'=>$groupId,'Desc'=>'开设新房间')
		);
		$result=request($param);
		if($result['Flag']==100){
			ShowMsg($result['FlagString'],'room_manage.php?module=open_room_success&room_id='.$result['roomId']);
		}
		else{
			ShowMsg($result['FlagString'],'room_manage.php?module=room_list');
		}
		exit;	
	break;
	case 'open_room_success':
		//是否拥有开设房间权限
		if(!checkGroupPermission(10260,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title='房间管理-开设房间';
		$template='open_room_success';
		$serviceType='room_manage';
		
		$roomId=intval($_GET['room_id']);
		if($roomId<=0){
			ShowMsg('出错啦!','room_manage.php?module=room_list');
		}
		$param=array(
			'extparam'=>array('Tag'=>"getRoomInfo",'RoomId'=>$roomId,'Uin'=>$userGroupInfo['uin']),
			'param'=>array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10099,'ChildId'=>101,'Desc'=>"房间信息查询")
		);
		$roomInfo=request($param);
		if(empty($roomInfo['info'])){
			ShowMsg('出错啦!','room_manage.php?module=room_list');
		}
		$roomInfo=$roomInfo['info'];
		$channelInfo=getChannelInfo($userGroupInfo['uin'],$roomInfo['id'],9);
		
		//是否拥有签约室主权限
		if(checkGroupPermission(10261,$permission)){
			$signedManage=true;
		}
	break;
	//以下为房间管理
	default:
		//站下房间
		$param=array(
			'extparam'=>array('Tag'=>'GetGroupRooms','GroupId'=>$userGroupInfo['groupid']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下房间')
		);		
		$userRooms=request($param);
		//切房间
		if(isset($_GET['roomid'])&&$_GET['roomid']>0){
			setcookie('roomid',$_GET['roomid'],0,'/');
			$roomid=$_GET['roomid'];
		}elseif(isset($_COOKIE['roomid'])&&$_COOKIE['roomid']>0){
			$roomid=$_COOKIE['roomid'];
		}else{
			$roomid=$userRooms['roomList'][0]['id'];
		}
		$roomid=intval($roomid);
		//房间详情
		foreach($userRooms['roomList'] as $val){
			if($val['id']==$roomid){
				$roomInfo=$val;
				break;
			}
		}
		if(!isset($roomInfo)){
			ShowMsg('你没有这个房间的管理权限!','room_manage.php?module=room_list');
		}
		
		//房间管理左部导航栏
		if(checkGroupPermission(10099,$permission)){
			$roomManageMenu['room_info']=true;
		}
		if(checkGroupPermission(10100,$permission)){
			$roomManageMenu['enter']=true;
		}
		if(checkGroupPermission(10101,$permission)){
			$roomManageMenu['admin']=true;
		}
		if(checkGroupPermission(10102,$permission)){
			$roomManageMenu['order']=true;
		}
		if(checkGroupPermission(10103,$permission)){
			$roomManageMenu['release']=true;
		}
		if(checkGroupPermission(10153,$permission)){
			$roomManageMenu['roomnotice']=true;
		}
		if(checkGroupPermission(10155,$permission)){
			$roomManageMenu['recommend']=true;
		}
		if(checkGroupPermission(10251,$permission)){
			$roomManageMenu['roomip']=true;
		}
		if(checkGroupPermission(10257,$permission)){
			$roomManageMenu['roomranks']=true;
		}
		if(checkGroupPermission(10398,$permission)){
			$roomManageMenu['set_ui_package']=true;
		}
		//房间管理模块
		switch($module){
			case 'room_info':
			//权限判断
			if(!checkGroupPermission(10099,$permission)){
				alertMsg('无权访问','group.php?module=group_info');
			}
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
					'extparam' => array('Tag'=>"saveRoomInfo",'Uin'=>$userGroupInfo['uin'],'name'=>$name,'description'=>$description,'salutatory'=>$salutatory,'room_id'=>$roomid,'bgalign'=>$bgalign,'type'=>$type,'robot_base_num'=>$robot_base_num,'robot_num'=>$robot_num),
					'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10099,'ChildId'=>101,'Desc'=>"房间信息更新")
				);
				$saveRoomInfo = request($param);
				if($saveRoomInfo['Flag']!=100){
					alertMsg($saveRoomInfo['FlagString']);
				}
				$tmp_cover = dirname(dirname(__FILE__)).$_POST['room_cover'];
				if(file_exists($tmp_cover) && is_file($tmp_cover)){
					$size = json_decode($_POST['room_cover_coords'],true);
					$bytes = file_get_contents($tmp_cover);
					//$thumb = new thumb($bytes);
					//$thumb->crop($size['w'],$size['h'],$size['x'],$size['y']);
					//if($thumb->save($tmp_cover)){
						$bytes = file_get_contents($tmp_cover);
						$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'roomid','Index'=>$roomid,'crop'=>$size);
						$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
						$roombg = array('Flag'=>$query['rst'],'File'=>$roomid);
						if($roombg['Flag'] != 100) ShowMsg('房间封面上传失败');
					//}else{
					//	ShowMsg('房间封面上传失败');
					//}
				}
				if(file_exists($_FILES['room_bg']['tmp_name']) && strstr($_FILES['room_bg']['type'],'image')){
					$bytes = file_get_contents($_FILES['room_bg']['tmp_name']);
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'roombg','Index'=>$roomid);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
					$roombg = array('Flag'=>$query['rst'],'File'=>$roomid);
					if($roombg['Flag'] != 100) ShowMsg('房间背景上传失败');
				}elseif(isset($_POST['default_roombg']) && is_numeric($_POST['default_roombg'])){
					$bytes = file_get_contents("../pic/roombg/{$_POST['default_roombg']}.jpg");
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'roombg','Index'=>$roomid);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
					$roombg = array('Flag'=>$query['rst'],'File'=>$roomid);
					if($roombg['Flag'] != 100) ShowMsg('房间背景上传失败');
				}
				
				$urls = array(
					PIC_API_PATH."/roomid/{$roomid}/80/60.jpg",//个人中心:80*60
					PIC_API_PATH."/roomid/{$roomid}/260/190.jpg",//大厅大图:260*190
					PIC_API_PATH."/roomid/{$roomid}/150/110.jpg",//大厅小图:150*110
					PIC_API_PATH."/roomid/{$roomid}/200/150.jpg",//推荐位图片:200*150
					PIC_API_PATH."/roomid/{$roomid}/0/0.jpg",
					PIC_API_PATH."/roombg/{$roomid}/80/60.jpg",//个人中心:80*60
					PIC_API_PATH."/roombg/{$roomid}/0/0.jpg"//房间背景:0*0
				);
				cdn_url($urls, 'CDN_UPLOAD_CLEAR');
			}
			//房间信息
			$param = array(
				'extparam' => array('Tag'=>"getRoomInfo",'Uin'=>$userGroupInfo['uin'],'RoomId'=>$roomid),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10099,'ChildId'=>101,'Desc'=>"房间信息查询")
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
			$template = "room_info";
			break;
		case 'enter':
			//权限判断
			if(!checkGroupPermission(10100,$permission)){
				alertMsg('无权访问','group.php?module=group_info');
			}
			$template = "enter";
			$title='房间管理-进入房间设置';
			if($_POST){
				$member_value = $_POST['member_value']? explode(",",addslashes($_POST['member_value'])) : array();
				$deny_value = $_POST['deny_value']? explode(",",addslashes($_POST['deny_value'])) : array();
				$passwd = addslashes($_POST['passwd']);
				$status = intval($_POST['status']);
				intval($_POST['open_status']) == 4? $status = 4 : "";
				$param = array(
					'extparam' => array('Tag'=>"saveEnterInfo",'Uin'=>$userGroupInfo['uin'],'status'=>$status,'passwd'=>$passwd,'member_value'=>$member_value,'deny_value'=>$deny_value,'room_id'=>$roomid),
					'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10100,'ChildId'=>102,'Desc'=>"房间进入管理更新")
				);
				$result = request($param);
				if($result['Flag'] == 100){
					ShowMsg($result['FlagString'], 'room_manage.php?module=enter');
				}else{
					ShowMsg($result['FlagString'], -1);
				}
			}
			$param = array(
				'extparam' => array('Tag'=>"getEnterInfo",'RoomId'=>$roomid),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10102,'ChildId'=>101,'Desc'=>"房间进入管理查看")
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
			//权限判断
			if(!checkGroupPermission(10102,$permission)){
				alertMsg('无权访问','group.php?module=group_info');
			}
			$template = "order";
			$title='房间管理-排麦规则设置';
			if($_POST){
				$mike_power = intval($_POST['mike_power']);
				$member = $_POST['member']?explode(",",addslashes($_POST['member'])):array();
				$main_video_time = intval($_POST['main_video_time']);
				if($main_video_time>500 || $main_video_time<5){
					alertMsg('上麦时长必须为5-500');
				}
				$param = array(
					'extparam' => array('Tag'=>"saveOrderInfo",'Uin'=>$userGroupInfo['uin'],'room_id'=>$roomid,'mike_power'=>$mike_power,'member'=>$member,'main_video_time'=>$main_video_time),
					'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10102,'ChildId'=>102,'Desc'=>"房间排麦规则更新")
				);
				$result = request($param);
				if($result['Flag'] == 100){
					ShowMsg($result['FlagString'], 'room_manage.php?module=order');
				}else{
					ShowMsg($result['FlagString'],'index.php');
				}
			}
			$param = array(
				'extparam' => array('Tag'=>"getOrderInfo",'RoomId'=>$roomid),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10102,'ChildId'=>101,'Desc'=>"房间排麦规则查看")
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
			$member = array();
			//权限判断
			if(!checkGroupPermission(10101,$permission)){
				alertMsg('无权访问','group.php?module=group_info');
			}
			$template = "admin";
			$title='房间管理-管理员设置';
			if($_POST){
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
					ShowMsg($result['FlagString'], 'room_manage.php?module=admin');
				}else{
					ShowMsg($result['FlagString'], 'room_manage.php?module=admin');
				}
			}
			
			//站下角色
			$param=array(
				'extparam'=>array('Tag'=>'GetGroupRole','RoleShowOne'=>array(2),'RoleShowTwo'=>array(1)),
				'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下角色','GroupId'=>$groupId)
			);
			$roleList=request($param);
			$roleList=$roleList['list'];
			
			$param = array(
				'extparam' => array('Tag'=>"getManagerInfo",'RoomId'=>$roomid),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10101,'ChildId'=>101,'Desc'=>"房间管理员查看")
			);
			$result = request($param);
			/*if($result['Flag'] == 100){
				$member  = @implode(",", $result['info']['member']);
			}else{
				ShowMsg($result['FlagString'],'index.php');
			}*/
			
			
			if($result['Flag'] == 100){
				foreach ($result['roles'] as $key=>$val){
					$member[$val['id']][] = $val['uin'];
				}
				foreach ($member as $role_id=>$val){
					$member[$role_id] = implode(",", $val);
				}
			}else{
				foreach ($roleList as $val){
					$member[$val['id']] = "";
				}
			}
			$member = json_encode($member);
			break;
		case 'release':
			//权限判断
			if(!checkGroupPermission(10103,$permission)){
				alertMsg('无权访问','group.php?module=group_info');
			}
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
					'extparam' => array('Tag'=>$tag,'Uin'=>$userGroupInfo['uin'],'id'=>$release_ids,'member'=>$member),
					'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10103,'ChildId'=>$childid,'Desc'=>$desc)
				);
				$result = request($param);
				if($result['Flag'] == 100){
					ShowMsg($result['FlagString'], 'room_manage.php?module=release&type='.$type);
				}else{
					ShowMsg($result['FlagString'], 'room_manage.php?module=release&type='.$type);
				}
			}
			$tag = $is_ip_type? "getReleaseIPInfo" : "getReleaseIDInfo";
			$childid = $is_ip_type? 103 : 101;
			$desc = $is_ip_type? "被封杀IP查看" : "被踢出用户查看";
			$param = array(
				'extparam' => array('Tag'=>$tag,'RoomId'=>$roomid),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10103,'ChildId'=>$childid,'Desc'=>$desc)
			);
			$result = request($param);
			if($result['Flag'] != 100){
				ShowMsg($result['FlagString'],'index.php');
			}
			break;
		case 'roomnotice':
			//权限判断
			if(!checkGroupPermission(10153,$permission)){
				alertMsg('无权访问','group.php?module=group_info');
			}
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
					'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10153,'ChildId'=>101,'Desc'=>"获取房间公告")
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
					'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10153,'ChildId'=>102,'Desc'=>"房间公告管理更新")
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
					'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10153,'ChildId'=>101,'Desc'=>"房间公告管理查看")
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
								'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10153,'ChildId'=>102,'Desc'=>"房间公告管理更新")
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
			//权限判断
			if(!checkGroupPermission(10155,$permission)){
				alertMsg('无权访问','group.php?module=group_info');
			}
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
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10155,'ChildId'=>101,'Desc'=>"获取房间信息")
			);
			$info = request($param);
			$info = $info['Info'];
			if(!empty($info)){
				$info['worktime'] = json_decode($info['worktime'],true);
			}
			$template = 'recommend';
			$title='房间管理-推荐位申请';
			break;
		case 'reapply':
			$param = array(
				'extparam' => array('Tag'=>"ReApply",'Id'=>intval($_GET['roomid'])),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10155,'ChildId'=>101,'Desc'=>"返回修改")
			);
			$rst = request($param);
			exit;
		break;
		case 'set_ui_package':
			//权限判断
			if(!checkGroupPermission(10398,$permission)){
				alertMsg('无权访问','group.php?module=group_info');
			}
			if(!empty($_POST['room_ui']) || !empty($_POST['template_ui'])){
				// $_POST['room_ui'] = (!empty($_POST['template_ui']) && $_POST['template_ui'] !=-1) ? 38 : $_POST['room_ui'];
				$param = array(
					'extparam' => array('Tag'=>"SetRoomUi",'RoomUi'=>(int)$_POST['room_ui'],'Roomid'=>$roomid,'TemplateUi'=>$_POST['template_ui']),
					'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10398,'ChildId'=>102,'GroupId'=>$groupId,'Desc'=>"修改房间界面")
				);
				$rst = request($param);
				alertMsg($rst['FlagString'],'?module=set_ui_package');
			}			
			
			//站所有房间界面
			$param = array(
				'extparam' => array('Tag'=>"GetRoomUi",'RoomsUi'=>json_decode($userGroupInfo['room_ui'], true)),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10398,'ChildId'=>101,'GroupId'=>$groupId,'Desc'=>"查看方案")
			);
			$rst = request($param);
			$uilist = (array)$rst['Results'];
			$roomsUi = array();
			foreach ($uilist as $val) {
				$roomsUi[$val['id']] = $val;
			}
			//当前房间界面
			$param = array(
				'extparam' => array('Tag'=>"RoomInfo",'RoomId'=>$roomid),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10398,'ChildId'=>101,'GroupId'=>$groupId,'Desc'=>"查看方案")
			);
			$rst = request($param);
			if($rst['Flag'] != 100){
				alertMsg('房间不存在', -1);
			}
			$templates_ui = empty($userGroupInfo['ktv_template']) ? array() : json_decode($userGroupInfo['ktv_template'], true);
			if(!empty($rst['RoomInfo']['template_ui'])){
				$curui = $rst['RoomInfo']['template_ui'];
				$tempui=1;
				$curuiName = $curui;
			}else{
				$curui = $rst['RoomInfo']['ui_version'];
				$roomui=1;
				$curuiName = $roomsUi[$curui]['name'];
			}

			$template = 'set_ui_package';
		break;
		case 'roomip':
			//权限判断
			if(!checkGroupPermission(10251,$permission)){
				alertMsg('无权访问','group.php?module=group_info');
			}
			$template="roomip";
			$title='房间管理-在线人数统计';
			$roomid=intval($_GET['searchroomid'])>0?$_GET['searchroomid']:$roomid;
			//判断是否拥有该房间管理
			$isManage=false;
			foreach($userRooms['roomList'] as $val){
				if($val['id']==$roomid){
					$isManage=true;
					break;
				}
			}
			if($isManage!==true){
				setcookie('roomid','',-1,'/');
				ShowMsg('您没有该房间的管理权限',-1);
			}
			
			$startDate=$_GET['start'];
			$endDate=$_GET['end'];
			$data=array(
				'Start'=>$startDate,
				'End'=>$endDate
			);
			$param=array(
				'extparam'=>array('Tag'=>"GetRoomStatistics",'RoomId'=>$roomid,'Data'=>$data),
				'param'=>array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10251,'ChildId'=>101,'Desc'=>"房间在线人数统计")
			);
			$result=request($param);
			$rooms_online=$result['Result'];
			$page=$result['Page'];
			
		break;
		case 'roomranks':
			//权限判断
			if(!checkGroupPermission(10257,$permission)){
				alertMsg('无权访问','group.php?module=group_info');
			}
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
					'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10101,'ChildId'=>101,'ChannelId'=>$roomid,'Desc'=>"房间排行设置")
				);
				$roomranks = request($param);
				alertMsg($roomranks['FlagString'],$_SERVER['REQUEST_URI']);
			}
			$param = array(
				'extparam' => array('Tag'=>"GetRoomRanks"),
				'param' => array('Uin'=>$userGroupInfo['uin'],'BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10101,'ChildId'=>101,'ChannelId'=>$roomid,'Desc'=>"获取排行设置")
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
								//"type" => $val1['type'],
								"sort" => $val['sort_type']
								);
					}
				}
			}
			$template = 'roomranks';
			$title='房间管理-房间排行榜设置';
		break;
		}
	break;
}

$serviceType='room_manage';
$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('room_manage/'.$template.'.html',$tpl);