<?php
include_once 'common.php';

$module=empty($_GET['module'])?'group_info':$_GET['module'];
if($module == "get_message"){
	$param = array(
			'extparam' => array('Tag'=>'GetMsg', 'LastId'=>$_GET['LastId'], 'GroupId'=>$_GET['GroupId']),
			'param'    => array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10619,'ChildId'=>102)
	);
	$info = request($param);
	if($info['Data']['readid'])
		echo "var rsp = ".json_encode($info['Data']);
	exit;
}
//验证是否登陆
$user=checkDpLogin();
$Uin=$user['Uin'];
$Nick=$user['Nick'];
$Uid=$user['Uid'];
$serviceType = 'manage_imformation';
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
if($userGroupInfo['Flag']!=100){
	alertMsg($userGroupInfo['FlagString'],'/');
}
$userGroupInfo=$userGroupInfo['Result'];

switch($module){
	default:
	case 'manage_imformation':
		//权限判断
		if(!checkGroupPermission(10259,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title = "站点信息-经营概况";
		$room_imformation = array();
		$roomid = array();
		$flow_time = $_GET['flow_time']?$_GET['flow_time']:date("Y-m-d");
		$count_time = $_GET['count_time']?$_GET['count_time']:date("Y-m-d");
		
		//获取roomid
		// $param=array(
			// 'extparam'=>array('Tag'=>'GetGroupRooms','GroupId'=>$userGroupInfo['groupid']),
			// 'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>'获取站下房间')
		// );		
		// $userRooms=request($param);

		// foreach((array)$userRooms['roomList'] as $one_room){
			// $roomid[] = $one_room['id'];
		// }
		//获取峰值
		$param = array(
				'extparam' => array('Tag'=>"GroupFlow",'Data' => array('RoomId' => $roomid, 'Format' => true, 'Time'=>$flow_time,'GroupId'=>$userGroupInfo['groupid'])),
				'param'=> array('BigCaseId'	=> 10004,'CaseId'=> 10038,'ParentId'=> 10244,'ChildId'=> 102,'Uin'=> $userGroupInfo['uin'],'Client'=> 'WEB ADMIN','Desc'=> '查询站下属房间的税收和收入')
		);
		$top = request($param);
		//数据整理
		// foreach((array)$userRooms['roomList'] as $key=>$one_room){
			// $room_imformation[$key]['room_id'] = $one_room['id'];
			// $room_imformation[$key]['time'] = $flow_time;
			// $room_imformation[$key]['curuser'] = $top['Result'][$one_room['id']]['curuser']?$top['Result'][$one_room['id']]['curuser']:0;
			// $room_imformation[$key]['curip'] = $top['Result'][$one_room['id']]['curip']?$top['Result'][$one_room['id']]['curip']:0;
		// }
		
		// //获取税收
		// $tax_arr = array();
		$type = $_GET['type']?$_GET['type']:1;
		$time = $count_time;
		// if($_GET['count_time']){
			// $time = ;
		// }
		$param = array(
				'extparam' => array('Tag'=>"GroupIncome", 'Data'=>array('Type'=>$type,'RoomId'=>$roomid,'GroupId'=>$groupId,'Time'=>$time)),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10259,'ChildId'=>101,'Uin'=>$userGroupInfo['uin'],'Desc'=>"查询站下属房间的税收和收入")
		);
		$tax = request($param);
		
		// //数据整理
		// foreach($tax['Result'] as $key=>$one_tax){
			// $tax_arr[$key]['time'] = $one_tax['Uptime'];
			// $tax_arr[$key]['roomid'] = $one_tax['ChannelId'];
			// $tax_arr[$key]['tax'] = $one_tax['Weight'];
			// $tax_arr[$key]['uin'] = $one_tax['Uin'];
			// $tax_arr[$key]['RMB'] = $one_tax['Weight']*0.9/10000;
		// }
		
		//是否拥有站管理权限
		if(checkGroupPermission(10258,$permission)){
			$groupManage=true;
		}
		//是否拥有站内配置查看权限
		if(checkGroupPermission(10315,$permission)){
			$groupConfig=true;
		}
		
		$serviceType='group_manage';
		$file = "manage_imformation.html";
		break;
	case 'tax_detail':
	case 'tax_exchange':
	case 'tax_signatory':
		if($module == 'tax_detail'){
			$title = '税收管理-税收流水查询';
		}elseif($module == 'tax_exchange'){
			$title = '税收管理-税收兑换记录';
		}else{
			$title = '税收管理-签约人员税收汇总';
		}
		//确认身份
		$roles = getChannelUserInfo($userGroupInfo['uin'],8);
		
		//是否有税收流水查询的权限
		if(checkGroupPermission(10263,$permission)){
			$taxDetail=true;
		}
		//是否有税收兑换记录的权限
		if(checkGroupPermission(10264,$permission)){
			$taxExchange=true;
		}
		//是否有签约人员税收汇总的权限
		if(checkGroupPermission(10265,$permission)){
			$taxSignatory=true;
		}
		
		//获取用户余额
		$param = array(
			'extparam' => array('Tag'=>"GetTaxBalance",'GroupId'=>$groupId),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10263,'ChildId'=>102)
		);
		$r_balance = httpPOST(KMONEY_API_PATH,$param);
		$balance = (int)$r_balance['LastBalance'];
		//$to_money = $balance/10000;
		//$to_wealth = $balance*0.9/10000;

		$start_time = $_GET['start_date']?$_GET['start_date']:date("Y-m-01");
		$end_time = $_GET['end_date']?$_GET['end_date']:date("Y-m-d");
		
		if($module == 'tax_detail'){
			//权限判断
			if(!$taxDetail){
				alertMsg('无权访问','group.php?module=group_info');
			}
			error_reporting(e_all);
			//获得流水
			$param = array(
					'extparam' => array('Tag'=>"TaxDetials", 'Data'=>array('StartDate'=>$start_time,'EndDate'=>$end_time, "GroupId"=>$groupId)),
					'param' => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10264,'ChildId'=>104,'Uin'=>$userGroupInfo['uin'],'Desc'=>"账户税收查询")
			);
			$result = request($param);
			$lists = $result['List'];
			$page = $result['Page'];
			$pay_total = $lists['pay_total'];
			$deposit_total = $lists['deposit_total'];
			unset($result['Page']);
			unset($lists['pay_total']);
			unset($lists['deposit_total']);
			/*$page = $result['Page'];
			$count = count($result['Result']);
			$details = array();
			for($i=0;$i<$count;$i++){
				$details[$i]['no'] = $i+1;
				$details[$i]['uptime'] = date("Y-m-d H:i:s", $result['Result'][$i]['Uptime']);
				$details[$i]['weight'] = $result['Result'][$i]['Weight'];
				$details[$i]['balance'] = $result['Result'][$i]['Balance'];
				$details[$i]['desc'] = $result['Result'][$i]['Desc'];
				switch($result['Result'][$i]['ChannelType']){
					case 8:
						$details[$i]['role'] = "站长";
						break;
					case 9:
						$details[$i]['role'] = "室主";
						break;
					case 15:
						$details[$i]['role'] = "艺人";
						break;
				}
			}*/
			
			$file = "tax_detail.html";
		}elseif($module == 'tax_exchange'){
			//权限判断
			if(!$taxExchange){
				alertMsg('无权访问','group.php?module=group_info');
			}
			$param = array(
					'extparam' => array('Tag'=>'ExchangeDetails','Data'=>array('StartDate'=>$start_time,'EndDate'=>$end_time,'Uin'=>$userGroupInfo['uin'])),
					'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10264,'ChildId'=>104)
			);
			$info = request($param);
			$counter = count($info['Result']);
			$page = $info['Page'];
			$list = array();
			for($i=0;$i<$counter;$i++){
				$list[$i]['no'] = $i+1;
				$list[$i]['role'] = '站长';
				$list[$i]['uin'] = $info['Result'][$i]['Uin'];
				switch($info['Result'][$i]['Type']){
					case 1:
						$list[$i]['type'] = "V宝";
						break;
					case 2:
						$list[$i]['type'] = "人民币";
						break;
					case 3:
						$list[$i]['type'] = "V点";
						break;
				}
				$list[$i]['toWeight'] = $info['Result'][$i]['ToWeight'];
				$list[$i]['weight'] = $info['Result'][$i]['Weight'];
				$list[$i]['desc'] = $info['Result'][$i]['Desc'];
				$list[$i]['time'] = date("Y-m-d H:i:s", $info['Result'][$i]['Uptime']);
			}
			$file = "tax_exchange.html";
		}elseif($module == 'tax_signatory'){
			//权限判断
			if(!$taxSignatory){
				alertMsg('无权访问','group.php?module=group_info');
			}
			$type = $_GET['type']?$_GET['type']:1;
			$role = $_GET['role']?$_GET['role']:3;
			
			$param = array(
					'extparam' => array('Tag'=>'SignatoryDetails','Data'=>array('Uin'=>$userGroupInfo['uin'],'Type'=>$type,'Role'=>$role)),
					'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10265,'ChildId'=>101)
			);
			$info = request($param);
			//处理数据
			$page = $info['Page'];
			$list = array();
			$counter = count($info['Result']);
			for($i=0;$i<$counter;$i++){
				$list[$i]['no'] = $i+1;
				$list[$i]['uptime'] = $info['Result'][$i]['Uptime'];
				$list[$i]['role'] = $info['Result'][$i]['ChannelType'] == 9?"室主":"艺人";
				$list[$i]['uin'] = $info['Result'][$i]['Uin'];
				$list[$i]['weight'] = $info['Result'][$i]['Weight'];
			}
			$file = "tax_signatory.html";
		}
		break;
	/*
	case 'artist_tax_detail':
		//获取站下艺人
		$artistList = getGroupChannelUser($groupId);
		foreach ($artistList as $key => $val) {
			$userInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$val['uid'])));
			$artistList[$key]['nick'] = empty($userInfo['baseInfo']['nick']) ? $val['uid'] : $userInfo['baseInfo']['nick'];
		}
		$artistList = (array)$artistList;

		$info['stime'] = !empty($_GET['stime']) ? $_GET['stime'] : date('Y-m-d');
		$info['etime'] = !empty($_GET['etime']) ? $_GET['etime'] : date('Y-m-d');
		$info['uin'] = !empty($_GET['uin']) ? $_GET['uin'] : $artistList[0]['uid'];
		$info['group_id'] = intval($groupId);
		$info['package_id'] = intval($userGroupInfo['package_id']);

		$list = httpPOST('api/service/artist_tax_api.php',array('extparam'=>array('Tag'=>'GetArtistTax','Data'=>$info)));
		$totalTax = (int)$list['TotalTax'];
		$currentMonthTax = (int)$list['CurrentMonthTax'];
		$list = $list['Result'];
		$page = $list['page'];
		unset($list['page']);
		$list = (array)$list['list'];
		$file = 'artist_tax_detail.html';
		break;*/
	case 'artist_tax_exchange':
		$themes=$GroupData['Template']!=''?$GroupData['Template']:'default';
		if(!in_array($themes,array('cc51','sixroom'))){
			echo '您的站还没有该功能';exit;
		}
		if(!checkGroupPermission(10583,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title = '艺人积分兑换';
		
		$param = array(
				'extparam' => array('Tag'=>'GetBalance','GroupId'=>$groupId),
				'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10583,'ChildId'=>102)
		);
		$list = request($param);
		$page = $list['Page'];
		unset($list['Page']);
		$list = (array)$list['Result'];
		foreach ($list as $key => $value) {
			$result = httpPOST(KBASIC_API_PATH,array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$value['UinId'])));
			$list[$key]['nick'] = empty($result['Nick']) ? $value['UinId'] : $result['Nick'];
		}

		$file = 'artist_tax_exchange.html';
		break;
	case 'artist_tax_exchange_submit':
		$themes=$GroupData['Template']!=''?$GroupData['Template']:'default';
		if(!in_array($themes,array('cc51','sixroom'))){
			echo '您的站还没有该功能';exit;
		}
		if(!checkGroupPermission(10583,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$userId=$_GET['uin']?intval($_GET['uin']):intval($_POST['uin']);
		if($_POST){
			$kmoney=$_POST['kmoney'];
			$money=$_POST['money'];
			$param = array(
					'extparam' => array('Tag'=>'Exchange','RMBWeight'=>$money),
					'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10583,'ChildId'=>103,'GroupId'=>$groupId,'Uin'=>$Uin,'TargetUin'=>$userId,'DoingWeight'=>1,'MoneyWeight'=>$kmoney,'Desc'=>$Uin.'给'.$userId.'兑换积分'.$kmoney.'发放人民币'.$money.'元')
			);
			$result = request($param);
			exit(json_encode($result));
		}
		$userInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$userId)));
		if($userInfo['Flag']!=100){
			alertMsg('没有这个艺人','manage_imformation.php?module=artist_tax_exchange');
		}
		$userInfo=$userInfo['baseInfo'];
		$param = array(
				'extparam' => array('Tag'=>'GetBalance','Uin'=>$userId,'GroupId'=>$groupId),
				'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10583,'ChildId'=>102)
		);
		$userBalance = request($param);
		$userBalance=intval($userBalance['Result']['Weight']);
		$title = '艺人积分兑换';
		$file = 'artist_tax_exchange_submit.html';
		break;	
	case 'artist_tax_exchange_history':
		$themes=$GroupData['Template']!=''?$GroupData['Template']:'default';
		if(!in_array($themes,array('cc51','sixroom'))){
			echo '您的站还没有该功能';exit;
		}
		if(!checkGroupPermission(10583,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title = '艺人积分兑换历史';
		//获取站下艺人
		$artistList = getGroupChannelUser($groupId);
		foreach ($artistList as $key => $val) {
			$userInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$val['uid'])));
			$artistList[$key]['nick'] = empty($userInfo['baseInfo']['nick']) ? $val['uid'] : $userInfo['baseInfo']['nick'];
		}
		$artistList = (array)$artistList;
		
		$stime=0;
		$etime=0;
		if($_GET['stime']){
			$stime=strtotime($_GET['stime']);
		}
		if($_GET['etime']){
			$etime=strtotime($_GET['etime'].' 23:59:59');
		}
		$param = array(
				'extparam' => array('Tag'=>'TaxDetail','GroupId'=>$groupId,'Uin'=>intval($_GET['uin']),'CaseId'=>10047,'ParentId'=>10583,'StartTime'=>$stime,'EndTime'=>$etime),
				'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10583,'ChildId'=>102)
		);
		$list = request($param);
		$page = $list['Page'];
		unset($list['Page']);
		$list = (array)$list['Result'];
		
		$info['stime'] = !empty($_GET['stime']) ? $_GET['stime'] : date('Y-m-d');
		$info['etime'] = !empty($_GET['etime']) ? $_GET['etime'] : date('Y-m-d');
		$info['uin'] = !empty($_GET['uin']) ? $_GET['uin'] : $artistList[0]['uid'];
		
		$file = 'artist_tax_exchange_history.html';
		break;
	case 'artist_doll':
		if(!checkGroupPermission(10738,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title = '艺人娃娃兑换';
		
		$type=isset($_GET['type'])?$_GET['type']:1;
		if(!in_array($type,array(1,2,3))){
			$type=1;
		}
		
		//查询条件
		$time=!empty($_GET['time'])?date('Ymd',strtotime($_GET['time'])):'';
		$roomId=!empty($_GET['roomId'])?intval($_GET['roomId']):'';
		$artistId=!empty($_GET['artistId'])?intval($_GET['artistId']):'';
		$search=array(
			'ChannelUin'=>$roomId,
			'UinId'=>$artistId,
			'Uptime'=>$time
		);
		
		//汇总列表
		$param=array(
			'extparam'=>array('Tag'=>'IntegralSearch','Type'=>$type,'RuleId'=>41,'GroupId'=>$groupId,'Search'=>$search),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10064,'ParentId'=>10735,'ChildId'=>101)
		);;
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
						'extparam' => array('Tag'=>'GetArtistSalary','Uin'=>$val['UinId'],'RoomId'=>$val['ChannelUin'],'GroupId'=>$groupId),
						'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10738,'ChildId'=>102)
				);
				$result = request($param);
				$list[$key]['Salary']=$result['Salary'];
				$channelInfo=getChannelInfo($val['UinId'],$val['ChannelUin']);
				if($channelInfo['Flag']==100){
					$list[$key]['IsSigned']=true;
				}
			}
		}
		$file = 'artist_doll.html';
		break;
	case 'artist_salary':
		if(!checkGroupPermission(10738,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title = '修改艺人底薪';
		
		if(isset($_POST) && !empty($_POST)){
			$salary = intval($_POST['salary']);
			$artistId = intval($_POST['artistId']);
			$roomId = intval($_POST['roomId']);
			$param = array(
					'extparam' => array('Tag'=>'EditArtistSalary','Salary'=>$salary,'Uin'=>$artistId,'RoomId'=>$roomId,'GroupId'=>$groupId),
					'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10738,'ChildId'=>102)
			);
			$result = request($param);
			if( $result['Flag'] != '100' ){
				ShowMsg($result['FlagString'], -1);
			}
			ShowMsg('修改成功', '?module=artist_salary&artistId='.$artistId.'&roomId='.$roomId);
		}
		$artistId = intval($_GET['artistId']);
		$roomId = intval($_GET['roomId']);
		$param = array(
				'extparam' => array('Tag'=>'GetArtistSalary','Uin'=>$artistId,'RoomId'=>$roomId,'GroupId'=>$groupId),
				'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10738,'ChildId'=>102)
		);
		$result = request($param);
		if( $result['Flag'] != '100' ){
			ShowMsg($result['FlagString'], -1);
		}
		$salary=$result['Salary'];
		
		$artistNick=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$artistId)));
		$artistNick=$artistNick['baseInfo']['nick'];
		
		$file = 'artist_salary.html';
		break;	
	case 'integral_search':
		if(!checkGroupPermission(10577,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title = '积分查询';
		$param = array(
				'extparam' => array('Tag'=>'IntegralSearch','Search'=>array_map('intval', (array)$_GET['search']),'Type'=>intval($_GET['type']),'RuleId'=>intval($_GET['rule_id']), 'GroupId'=>$groupId),
				'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10577,'ChildId'=>101)
		);
		$info = request($param);
		if($info['Flag'] != 100){
			ShowMsg($info['FlagString'], -1);
		}
		$input = $info['LabelName'];
		foreach($input as $k1=>$v1){
			foreach($v1 as $k2=>$v2){
				if($v2 == "站id"){
					unset($input[$k1][$k2]);
				}
			}
		}
		$input_json = json_encode($input);
		$file = "integral_search.html";
		break;
	case 'send_message':
		if(!checkGroupPermission(10619,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		if($_POST){
			$post = array_map("htmlspecialchars", array_map("addslashes", $_POST));
			$title = $post['title'];
			$content = $post['content'];
			if(mb_strlen($title, "utf-8") > 20){
				ShowMsg("标题不能超过20个字符", -1);
			}
			if(mb_strlen($content, "utf-8") > 100){
				ShowMsg("内容不能超过100个字符", -1);
			}
			$param = array(
					'extparam' => array('Tag'=>'SendMsg', 'Title'=>$title, 'Content'=>$content, 'GroupId'=>$groupId),
					'param'    => array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10619,'ChildId'=>101)
			);
			$info = request($param);
			if($info['Flag'] != 100){
				ShowMsg($info['FlagString'], -1);
			}else{
				ShowMsg($info['FlagString'], "?module=send_message");
			}
		}
		$file = "send_message.html";
		break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('imformation/'.$file,$tpl);