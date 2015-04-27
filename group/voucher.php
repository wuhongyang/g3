<?php
require_once dirname(dirname(__FILE__)).'/library/global.fun.php';

$module=empty($_GET['module'])?'voucher_account':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
$Uid=$user['Uid'];
$Uin=$user['Uin'];
$Nick=$user['Nick'];
$serviceType='voucher';
$title = '资金管理';
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$isDz=(int)$permisssions['isDz'];
$group_id=(int)$permisssions['groupId'];
$groupId= $group_id;
$permission=(array)$permisssions['permission'];
$menuPermissions=(array)$permisssions['menuPermissions'];

$param=array(
	'extparam'=>array('Tag'=>'GetGroupInfo','GroupId'=>$group_id),
	'param'=>array('BigCaseId'=>10006,'CaseId'=>10045,'ParentId'=>10258,'ChildId'=>101)
);
$groupinfo=request($param);
$Ginfo=$groupinfo['Result'];
if($groupinfo['Flag']!=100){
	ShowMsg('站不存在','/');
}

//是否有金币充值的权限
if(checkGroupPermission(10269,$permission)){
	$voucherAccount=true;
}
//是否有金币明细的权限
if(checkGroupPermission(10270,$permission)){
	$voucherBalance=true;
}

switch($module){
	default:
	case 'voucher_account':
		//权限判断
		if(!$voucherAccount){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$param=array(
			'extparam'=>array('Tag'=>'GetBusinessBalance','GroupId'=>$group_id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>101)
		);
		$result = httpPOST(KMONEY_API_PATH,$param);
		$voucher = $result['LastBalance'];
		$file = "voucher_account.html";
		break;
	case 'voucher_balance' :
		//权限判断
		if(!$voucherBalance){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title .= ' - 资金明细管理';
		$param=array(
			'extparam'=>array('Tag'=>'VoucherBalance','Uin'=>$_GET['Uin'],'GroupId'=>$group_id,'status'=>$_GET['status']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10270,'ChildId'=>101)
		);
		$result = request($param);
		$status_array = array('请选择','冻结','正常');
		$lists = $result['List'];
		$page = $result['Page'];
		$user_balance = $lists['user_balance'];
		unset($lists['user_balance']);
		unset($result['Page']);
		$file = "voucher_balance.html";
		break;
	case 'voucher_running' :
		//权限判断
		if(!$voucherBalance){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title .= ' - 资金明细管理';
		$_GET['endDate'] = $_GET['endDate'] ? $_GET['endDate'] : date('Y-m-d H:i:s');
		$_GET['startDate'] = $_GET['startDate'] ? $_GET['startDate'] : date('Y-m-d 00:00:00');
		$EndDate = $_GET['endDate'];
		$StartDate = $_GET['startDate'];
		$param=array(
			'extparam'=>array('Tag'=>'VoucherRunning','Uin'=>$_GET['Uin'],'GroupId'=>$group_id,'StartDate'=>$StartDate,'EndDate'=>$EndDate,'BigCaseId'=>$_GET['bigcase_id'],'CaseId'=>$_GET['case_id'],'ParentId'=>$_GET['parent_id']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10270,'ChildId'=>102)
		);
		$result = request($param);
		$lists = $result['List'];
		$page = $result['Page'];
		$pay_total = $lists['pay_total'];
		$deposit_total = $lists['deposit_total'];
		unset($result['Page']);
		unset($lists['pay_total']);
		unset($lists['deposit_total']);
		
		$file = "voucher_running.html";
		break;
	case 'voucher_parent' :
		//权限判断
		if(!$voucherBalance){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title .= ' - 资金明细管理';
		$_GET['endDate'] = $_GET['endDate'] ? $_GET['endDate'] : date('Y-m-d');
		$_GET['startDate'] = $_GET['startDate'] ? $_GET['startDate'] : date('Y-m-01');
		$EndDate = $_GET['endDate'];
		$StartDate = $_GET['startDate'];
		$param=array(
			'extparam'=>array('Tag'=>'VoucherParent','Uin'=>$Ginfo['uin'],'GroupId'=>$group_id,'StartDate'=>$StartDate,'EndDate'=>$EndDate),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10270,'ChildId'=>103)
		);
		$result = request($param);
		$lists = $result['List'];
		$page = $result['Page'];
		$file = "voucher_parent.html";
		break;
	case 'voucher_recharge' :exit;
		//权限判断
		if(!$voucherAccount){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title .= ' - 资金充值';
		$param = array(
			'extparam' => array('Tag'=>"GetTaxBalance",'GroupId'=>$group_id),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>102)
		);
		$taxInfo = httpPOST(KMONEY_API_PATH,$param);
		$param = array(
			'extparam' => array('Tag'=>"GetBusinessBalance",'GroupId'=>$group_id),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>102)
		);
		$result = httpPOST(KMONEY_API_PATH,$param);
		$voucher = $result['LastBalance'] >0?$result['LastBalance'] :0;
		$file = "voucher_recharge.html";
		break;
	case 'tax_recharge' :
		//权限判断
		if(!$voucherAccount){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$roles = getChannelUserInfo($Ginfo['uin'],8);
		$param=array(
			'extparam'=>array('Tag'=>'TaxRecharge','Uin'=>$Ginfo['uin'],'GroupId'=>$group_id,'Weight'=>$_POST['Weight'],'ChannelId'=>$roles[0]['room_id'],'RoomId'=>$roles[0]['up_uid']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>102,'GroupId'=>$group_id)
		);
		$result = request($param);
		if($result['Flag'] == 106){
			alertMsg('税收余额不足');
		}elseif($result['Flag'] != 100){
			alertMsg($result['FlagString']);
		}
		header("Location:?module=voucher_recharge");
		break;
	case 'voucher_to_vip' :
		//权限判断
		if(!$voucherAccount){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title .= ' - 资金充值';
		$param=array(
			'extparam'=>array('Tag'=>'GetBusinessBalance','GroupId'=>$group_id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>101)
		);
		$result = httpPOST(KMONEY_API_PATH,$param);
		$voucher = $result['LastBalance'] >0?$result['LastBalance'] :0;
		$file = "voucher_tovip.html";
		break;
		/*
	case 'voucher_payment' :
		//权限判断
		if(!$voucherAccount){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$other = $_POST;
		unset($_POST);
		$other['weight'] = empty($other['weight']) ? $other['money'] : $other['weight'];
		$other['uin'] = $Ginfo['uin'];
		$other['group_id'] = $group_id;
		$file = "voucher_payment.html";
		break;
		*/
	case 'vip_recharge' :
		//权限判断
		if(!$voucherAccount){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$param=array(
			'extparam'=>array('Tag'=>'VipRecharge','Uin'=>$Ginfo['uin'],'TargetUin'=>$_POST['targetuin'],'GroupId'=>$group_id,'Weight'=>$_POST['Weight']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>103,'GroupId'=>$group_id,'Desc'=>'站长给用户'.$_POST['targetuin']."充值金额".$_POST['Weight'],'Uin'=>$Ginfo['uin'],'TargetUin'=>$_POST['targetuin'])
		);
		$result = request($param);
		if($result['Flag'] != 100) alertMsg($result['FlagString']);
		header("Location:?module=voucher_running");
		break;
	case 'vip_deduct':
		//权限判断
		if(!$voucherAccount){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$param=array(
			'extparam'=>array('Tag'=>'VipDeduct','Uin'=>$Ginfo['uin'],'TargetUin'=>$_POST['targetuin'],'GroupId'=>$group_id,'Weight'=>$_POST['Weight']),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>110,'GroupId'=>$group_id, 'Desc'=>'站长扣除用户'.$_POST['targetuin']."金额".$_POST['Weight'], 'Uin'=>$Ginfo['uin'], 'TargetUin'=>$_POST['targetuin'])
		);
		$result = request($param);
		if($result['Flag'] != 100) alertMsg($result['FlagString']);
		header("Location:?module=voucher_running");
		break;
	case 'payment_save' :
		//权限判断
		if(!$voucherAccount){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$post = $_POST;
		if(!empty($post['pay_type']) && $post['pay_expense'] > 0){
			if ($post['pay_type'] == 'CHINABANK'){
				$ParentId = '10094';
			}elseif ($post['pay_type'] == 'TENPAY'){
				$ParentId = '10095';
			}
			$post['type'] = 'Voucher';
			$post['notOpenAgent'] = 2;
			$post['GroupId'] = $group_id;
			$param = array(
				'extparam' => array('Tag'=>'SubmitTrade', 'Rebate' => '1','GroupId'=>$post['GroupId']),
				'param' => array('BigCaseId'=>10005,'CaseId'=>10024,'ParentId'=>$ParentId,'ChildId'=>103,'Uin'=>$Ginfo['uin'],'TargetUin'=>$Ginfo['uin'],'MoneyWeight'=>$post['pay_expense']),
			);
			$result = request($param);
			
			if($result['Flag'] == 100) {
				$post['trade_id'] = $result['TradeId'];
				$param = array(
					'extparam' => array('Tag'=>'WebPay','mypost' => $post),
					'param' => array('BigCaseId'=>10005,'CaseId'=>10024,'ParentId'=>$ParentId,'ChildId'=>103,'Uin'=>$Ginfo['uin'],'TargetUin'=>$Ginfo['uin'],'MoneyWeight'=>$post['pay_expense'],'Desc'=>'站内虚拟币充值','DoingWeight'=>1,'GroupId'=>$post['GroupId'])
				);
				$res = request($param);
				echo $res['Result'];
				exit;
			} else {
				header('Location:./');
			}
		}else{
			exit('参数错误');
		}
		break;
	case 'getUserInfo' :	//获取用户信息
		$uin = $_GET['uin'];
		/*$param = array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$uin));
		$result = httpPOST(KBASIC_API_PATH,$param);
		$return = $result['Flag'] == 100 ? $uin.'|'.$result['Nick'] : '|';
		exit($return);*/
		$result = getGroupVip($uin, $group_id);
		if(empty($result)){
			$return = '|';
		}else{
			$return = $uin.'|'.$result['nick'];
		}
		exit($return);
		break;
	case 'account_balance'://冻结,解冻账号
		//权限判断
		if(!$voucherBalance){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$Uin = $_GET['uin'];
		$Status = $_GET['status'];
		$param = array(
			'extparam' => array('Tag'=>'Account_balacne','Uin'=>$Uin,'GroupId'=>$group_id,'Status'=>$Status),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10270,'ChildId'=>104,'Desc'=>'冻结,解冻账号')
		);//
		$list_array = request($param);
		exit(json_encode($list_array));
		break;
	case 'success' :	//获取用户信息
		$file = "success.html";
		break;
	case 'voucher_config':
		//权限判断
		if(!checkGroupPermission(10314,$permission)){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title = '业务配置 - 业务资金库配置';
		if($_POST['turn'] == 'on'){
			//开启
			$param = array(
					'extparam' => array('Tag'=>'Charge', 'GroupId'=>$group_id, 'BigCaseId'=>$_POST['bigcase_id'], 
							'CaseId'=>$_POST['case_id'], 'ParentId'=>$_POST['parent_id'], 'Balance'=>$_POST['Balance'], 'Turn'=>'on'),
					'param' => array()
			);
			$result = httpPOST(CCS_API_PATH, $param);
			alertMsg($result['FlagString'], "voucher.php?module=voucher_config");
		}elseif($_POST['turn'] == 'off'){
			//关闭
			$param = array(
					'extparam' => array('Tag'=>'Close', 'GroupId'=>$group_id, 'BigCaseId'=>$_POST['bigcase_id'],
							'CaseId'=>$_POST['case_id'], 'ParentId'=>$_POST['parent_id']),
					'param' => array()
			);
			$result = httpPOST(CCS_API_PATH, $param);
			alertMsg($result['FlagString'], "voucher.php?module=voucher_config");
		}elseif($_POST['btype'] == 'ab'){
			//补充余额
			$param = array(
					'extparam' => array('Tag'=>'SCBalanceAdd', 'Data'=>array('GroupId'=>$group_id, 'BigCaseId'=>$_POST['ab_bigcase_id'], 
							'CaseId'=>$_POST['ab_case_id'], 'ParentId'=>$_POST['ab_parent_id'], 'Value'=>$_POST['ab_value'])),
					'param' => array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10314,'ChildId'=>102,'Desc'=>'补充余额')
			);
			$result = request($param);
			alertMsg($result['FlagString'], "voucher.php?module=voucher_config");
		}elseif($_POST['btype'] == 'gb'){
			//获取余额
			$param = array(
					'extparam' => array('Tag'=>'SCBalanceGet', 'Data'=>array('GroupId'=>$group_id, 'BigCaseId'=>$_POST['gb_bigcase_id'], 
							'CaseId'=>$_POST['gb_case_id'], 'ParentId'=>$_POST['gb_parent_id'])),
					'param' => array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10314,'ChildId'=>103,'Desc'=>'提取余额')
			);
			$result = request($param);
			alertMsg($result['FlagString'], "voucher.php?module=voucher_config");
		}
		$param = array(
			'extparam' => array('Tag'=>"GetBusinessBalance",'GroupId'=>$group_id),
			'param' => array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>102)
		);
		$result = httpPOST(KMONEY_API_PATH,$param);
		$v_balance = $result['LastBalance'] >0?$result['LastBalance'] :0;
		$param = array(
				'extparam' => array('Tag'=>'SCList2', 'GroupId'=>$group_id),
				'param' => array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10314,'ChildId'=>101,'Desc'=>'查看业务资金库配置')
		);
		$result = request($param);
		$list = $result['Data']['Result'];
		$page = $result['Data']['Page'];
		$file = "voucher_config.html";
		break;
	case "voucher_deduct"://给用户扣款
		//权限判断
		if(!$voucherAccount){
			alertMsg('无权访问','group.php?module=group_info');
		}
		$title .= ' - 用户扣款';
		$param=array(
			'extparam'=>array('Tag'=>'GetBusinessBalance','GroupId'=>$group_id),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10049,'ParentId'=>10269,'ChildId'=>101)
		);
		$result = httpPOST(KMONEY_API_PATH,$param);
		$voucher = $result['LastBalance'] >0?$result['LastBalance'] :0;
		$file = "voucher_deduct.html";
		break;
		
}
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('voucher_manage/'.$file,$tpl);