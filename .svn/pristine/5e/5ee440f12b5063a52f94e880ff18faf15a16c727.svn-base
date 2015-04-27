<?php
require_once 'common.php';

$module=empty($_GET['module'])?'doll':$_GET['module'];

//判断权限
if(!$isArtist){
   ShowMsg('您还不是艺人',-1);
}

switch($module){
	case 'doll':
		$title='艺人后台-娃娃查询';
		$template='doll';
		$type=isset($_GET['type'])?$_GET['type']:1;
		if(!in_array($type,array(1,2,3))){
			header('Location:/404.html');
			exit;
		}
		
		//查询条件
		$time=!empty($_GET['time'])?date('Ymd',strtotime($_GET['time'])):'';
		$roomId=!empty($_GET['roomId'])?intval($_GET['roomId']):$userChannel['ArtistRoom'][0]['id'];
		$search=array(
			'ChannelUin'=>$roomId,
			'UinId'=>$user['Uin'],
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
			}
		}
		//约定底薪
		if(empty($roomId)){
			$slaryRoomId=$userChannel['ArtistRoom'][0]['id'];
		}
		else{
			$slaryRoomId=$roomId;
		}
		$param = array(
				'extparam' => array('Tag'=>'GetArtistSalary','Uin'=>$user['Uin'],'RoomId'=>$slaryRoomId,'GroupId'=>$group_id),
				'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10738,'ChildId'=>102)
		);
		$result = request($param);
		$salary=$result['Salary'];
		$data=array(
			'type'=>$type,
			'salary'=>$salary,
			'time'=>$_GET['time'],
			'roomId'=>$_GET['roomId'],
			'list'=>$list,
			'page'=>$page
		);
	break;
	case 'doll_info':
		$title='艺人后台-娃娃查询';
		$template='doll_info';
		$type=isset($_GET['type'])?$_GET['type']:1;
		if(!in_array($type,array(1,2,3))){
			header('Location:/404.html');
			exit;
		}
		
		//查询条件
		$time=isset($_GET['time'])?intval($_GET['time']):'';
		$roomId=isset($_GET['roomId'])?intval($_GET['roomId']):'';
		if(empty($time)||empty($roomId)){
			ShowMsg('无效的链接', -1);
		}
		$search=array(
			'ChannelUin'=>$roomId,
			'UinId'=>$user['Uin'],
			'Uptime'=>$time
		);
		
		//在麦时长
		$param=array(
			'extparam'=>array('Tag'=>'IntegralSearch','Type'=>$type,'RuleId'=>37,'GroupId'=>$group_id,'Search'=>$search),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10064,'ParentId'=>10735,'ChildId'=>101)
		);
		$result=request($param);
		$onlineTime=0;
		if(isset($result['Data'][0]['Weight'])){
			$onlineTime=round($result['Data'][0]['Weight']/3600,2);
		}
		
		//汇总信息
		$param=array(
			'extparam'=>array('Tag'=>'IntegralSearch','Type'=>$type,'RuleId'=>41,'GroupId'=>$group_id,'Search'=>$search),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10064,'ParentId'=>10735,'ChildId'=>101)
		);
		$result=request($param);
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
			$list=array_values($list);
			foreach($list as $key=>$val){
				$userInfo=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$val['UinId'])));
				$list[$key]['Nick']=$userInfo['baseInfo']['nick'];
			}
		}
		$list=$list[0];
		$list['OnlineTime']=$onlineTime;
		$data=array(
			'info'=>$list,
		);
	break;
	case 'artist_info':
		$title='艺人后台-个人资料';
		$template='artist_info';
		if(isset($_POST) && !empty($_POST)){
			//提交通行证信息
			$data=array(
				'name'=>$_POST['name'],
				'idcard'=>$_POST['idcard']
			);
			$param = array(
				'extparam'=>array('Tag'=>'UserRenzheng','Uin'=>$user['Uin'],'Data'=>$data),
				'param'=>array('BigCaseId'=>'10004','CaseId'=>'10013','ParentId'=>'10087','ChildId'=>'101','Desc'=>'用户认证')
			);
			$result = request($param);
			if( $result['Flag'] != '100' ){
				ShowMsg($result['FlagString'], -1);
			}
			//提交基本资料
			$data = array(
				'Tag'=>'EditNick',
				'GroupId'=>$group_id,
				'Nick'=>$user['Nick'],
				'Gender'=>$_POST['gender'],
				'Qq' => $_POST['qq'],
				'Phone' => $_POST['phone']
			);
			$result = httpPOST(SSO_API_PATH,array('extparam'=>$data));
			if( $result['Flag'] != '100' ){
				ShowMsg($result['FlagString'], -1);
			}
			//提交提现账户
			$data=array(
				'bank_name'=>addslashes($_POST['bank_name']),
				'bank_id'=>addslashes($_POST['bank_id']),
				'bank_address'=>addslashes($_POST['bank_address'])
			);
			$param = array(
				'extparam' => array('Tag'=>'SaveAccount','Data'=>$data),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10234,'ChildId'=>104)
			);
			$result = request($param);
			if( $result['Flag'] != '100' ){
				ShowMsg($result['FlagString'], -1);
			}
			ShowMsg('修改成功', '?module=artist_info');
		}
		//通行证信息
		$passInfo=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','Uid'=>$user['Uin'],'Status'=>1)));
		//基本资料
		$userInfo=httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUserBasicForUin','Uin'=>$user['Uin'])));
		$userInfo=$userInfo['baseInfo'];
		//提现账户
		$banks = array(1=>'中国招商银行',2=>'中国工商银行',3=>'中国建设银行',4=>'中国农业银行');
		$param = array(
			'extparam' => array('Tag'=>'Account','Uin'=>$user['Uin']),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10234,'ChildId'=>103)
		);
		$brankInfo = request($param);
		$brankInfo = (array)$brankInfo['Info'];
		$info=array(
			'name'=>$passInfo['Name'],
			'idcard'=>$passInfo['IdCard'],
			'gender'=>$userInfo['gender'],
			'phone'=>$userInfo['phone'],
			'qq'=>$userInfo['qq'],
			'bank_name'=>$brankInfo['bank_name'],
			'bank_id'=>$brankInfo['bank_id'],
			'bank_address'=>$brankInfo['bank_address']
		);
		$data=array(
			'info'=>isset($info)?$info:'',
			'banks'=>isset($banks)?$banks:''
		);
	break;
}

$serviceType='artist_manage';
$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/service/';
$tmp_config['cache_dir'].=$themes.'/tpl/service/';
$tpl = template::getInstance();
$tpl->setOptions($tmp_config);
include template('artist_manage/'.$template.'.html',$tpl);