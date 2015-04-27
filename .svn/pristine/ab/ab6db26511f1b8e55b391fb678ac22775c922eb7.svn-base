<?php
require_once 'common.php';
$module = $_GET['module'];
$ruin = $user['Uin'];
$Uin = $user['Uin'];
$Nick = $user['Nick'];
$Uid = $user['Uid'];
$serviceType = 'manage_imformation';
switch($module){
	default:
	case 'RMB_details':
		//确认身份
		$roles = getChannelUserInfo($Uin);
		$group_master = $agent = false;
		foreach((array)$roles as $one){
			if($one['type'] == 8){
				$group_master = true;
			}elseif($one['type'] == 16){
				$agent = true;
			}
		}
		if(!($group_master || $agent)){
			alertMsg("请先申请成为站长，才能开通人民币账户！", "/rooms/join.php?module=info&type=1");
		}
		
		$dactype = array(1=>'存入',2=>'支出');
		$detailtype = array(0=>'计算工资',15=>'艺人%s工资支付',7=>'地域负责人%s工资支付',8=>'站长%s工资支付',9=>'室主%s工资支付',10=>'税收兑换人民币',101=>'后台存入',102=>'后台支出',107=>'财务打款成功');
		$channeltype = array(
				0=>array('Typename'=>'请选择','Uin'=>'渠道角色ID','ChannelId'=>'渠道ID','UpChannel'=>'上级渠道角色ID'),
				1=>array('Typename'=>'公司','Uin'=>0,'ChannelId'=>0,'UpChannel'=>0),
				7=>array('Typename'=>'地域负责人','Uin'=>'地域负责人ID','ChannelId'=>'地域ID','UpChannel'=>0),
				8=>array('Typename'=>'站长','Uin'=>'站长ID','ChannelId'=>'群ID','UpChannel'=>'地域负责人ID'),
				9=>array('Typename'=>'室主','Uin'=>'室主ID','ChannelId'=>'房间ID','UpChannel'=>'站长ID'),
				15=>array('Typename'=>'艺人','Uin'=>'艺人ID','ChannelId'=>'房间ID','UpChannel'=>'室主ID'),
		);
		
		$start_time = $_GET['start_date']?$_GET['start_date']:date("Y-m-01");
		$end_time = $_GET['end_date']?$_GET['end_date']:date("Y-m-d");
		$param = array(
				'extparam' => array('Tag'=>'GetRMBDetail','Data'=>array('Uin'=>$user['Uin'],'ChannelType'=>8,'StartTime'=>strtotime($start_time),'EndTime'=>strtotime($end_time))),
				'param' => array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10226,'ChildId'=>105,'Desc'=>"站长人民币查询")
		);
		$result = request($param);
		
		$lists = $result['Result'];
		$cashrecord = (int)$result['CashRecord'];
		$balance = floatval($result['Balance']);
		$page = $result['Page'];
		$file = "RMB_details.html";
		break;
	case 'RMB_pick_up':
		//确认身份
		$roles = getChannelUserInfo($Uin);
		$group_master = $agent = false;
		foreach($roles as $one){
			if($one['type'] == 8){
				$group_master = true;
			}elseif($one['type'] == 16){
				$agent = true;
			}
		}
		
		if($actor){
			alertMsg('您不是站长或代理，无法使用该功能');
		}elseif(!($group_master || $agent)){
			alertMsg("请先申请成为站长，才能开通人民币账户！", "/rooms/join.php?module=info&type=1");
		}
		
		$param = array(
				'extparam' => array('Tag'=>'Account','Uin'=>$user['Uin']),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10234,'ChildId'=>103)
		);
		$result = request($param);
		$channelinfo = $result['Info'];
		
		// $param = array(
				// 'extparam' => array('Tag'=>'Info','Uid'=>$user['Uid']),
				// 'param'    => array('BigCaseId'=>10004,'CaseId'=>10013,'ParentId'=>10247,'ChildId'=>101)
		// );
		// $result = request($param);
		// $channelinfo = array_merge($result['Info'], $channelinfo);
		
		$idcard = substr($channelinfo['idcard'],0,14).'****';
		$bankid = substr($channelinfo['bank_id'],0,9).'****'.substr($channelinfo['bank_id'],13,18);
		
		//提现申请
		if(!empty($_POST['rmb'])){
			$param = array(
					'extparam' => array('Tag'=>'ChannelCash','Name'=>$channelinfo['name'],'IDCard'=>$channelinfo['idcard'],'Bank'=>$channelinfo['bank_address'],'BankName'=>$channelinfo['bankName'],'BankCard'=>$channelinfo['bank_id']),
					'param' => array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10234,'ChildId'=>101,'MoneyWeight'=>$_POST['rmb'],'Desc'=>'提现')
			);
			$result = request($param);
			if( $result['Flag'] != 100) alertMsg($result['FlagString']);
			alertMsg('您的提现申请已提交成功,请耐心等待！',$_SERVER['REQUEST_URI']);
		}
		
		$start_time = $_GET['start_date']?$_GET['start_date']:date("Y-m-01");
		$end_time = $_GET['end_date']?$_GET['end_date']:date("Y-m-d");
		$param = array(
				'extparam' => array('Tag'=>'CashDetail','Uin'=>$user['Uin'],'StartTime'=>strtotime($start_time),'EndTime'=>strtotime($end_time)),
				'param' => array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10234,'ChildId'=>102,'Desc'=>'账户余额')
		);
		$result = request($param);
		$balance = floatval($result['Balance']);
		$freeze = floatval($result['FreezeWeight']);
		$details = $result['Result'];
		$page = $result['Page'];
		
		$states = array(1=>'已经提交申请，等待系统审核',2=>'未通过审核',3=>'审核通过，等待处理',4=>'处理失败，请与我们联系',5=>'处理完成，等等银行打款',6=>'打款失败',7=>'提现成功');
		
		$file = "RMB_pick_up.html";
		break;
	case 'bind_account':
		$uin = $ruin;
		$channelInfo = getChannelUserInfo($uin);
		if(empty($channelInfo)){
			alertMsg('请先申请渠道角色，再绑定提现账户！', 'role_select.php');
		}

		if(isset($_POST) && !empty($_POST)){
			$param = array(
				'extparam' => array('Tag'=>'SaveAccount','Data'=>$_POST),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10234,'ChildId'=>104)
			);
			$rst = request($param);
			if($rst['Flag'] == 100){
				alertMsg($rst['FlagString'],'?module=bind_account');
			}else{
				alertMsg($rst['FlagString'],-1);
			}
		}else{
			$hmid = isset($_GET['hmid']) ? intval($_GET['hmid']) : 0;
			if($hmid > 0){
				//将待办事项设为已读
				$param = array(
					'extparam' => array('Tag'=>'SetRead','Id'=>$hmid),
					'param'    => array('BigCaseId'=>10004,'CaseId'=>10035,'ParentId'=>10250,'ChildId'=>102)
				);
				request($param);
			}
			$banks = array(1=>'中国招商银行',2=>'中国工商银行',3=>'中国建设银行',4=>'中国农业银行');
			$param = array(
				'extparam' => array('Tag'=>'Account','Uin'=>$uin),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10234,'ChildId'=>103)
			);
			$info = request($param);
			$info = (array)$info['Info'];
			$data=array(
				'info'=>isset($info)?$info:'',
				'banks'=>isset($banks)?$banks:''
			);
			$file = 'bind_account.html';
		}
		break;
}
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
include template('group/'.$file,$tpl);