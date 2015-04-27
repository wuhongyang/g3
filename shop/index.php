<?php
require_once 'common.php';
$module = isset($_GET['module']) ? $_GET['module'] : 'index';

$rebate = json_encode(array('index'=>10000,'tenpay'=>10000,'mobilecard'=>9000,'gamecard'=>8000,'vdouexchange'=>1, 'alipay'=>10000));
$recharge_style = array('index'=>'网银','tenpay'=>'财付通','mobilecard'=>'手机充值卡','gamecard'=>'游戏卡','vdouexchange'=>'金豆兑换','alipay'=>'支付宝');

switch ($module) {
	case 'index':
		if(!isset($_GET['type']) && $GroupData['EXT']['chinabank_id']['value']){
			$type = "index";
		}elseif(!isset($_GET['type']) && $GroupData['EXT']['tenpay_id']['value']){
			$type = "tenpay";
		}elseif(!isset($_GET['type']) && $GroupData['EXT']['baofoo_id']['value']){
			$type = "mobilecard";
		}elseif(!isset($_GET['type']) && $GroupData['EXT']['alipay_id']['value']){
			$type = "alipay";
		}elseif(!isset($_GET['type'])){
			header('Location:/shop/shop.php?category_id='.$category[0]['cate_id']);
			exit;
		}else{
			$type = $_GET['type'];
		}
		$roomid = isset($_GET['roomid']) ? intval($_GET['roomid']) : 0;
		
		if($groupId < 1){
			//参数错误 退出
			alertMsg('非法链接',-1);
		}
		if($roomid > 0){
			//得到房间名称，站（群）ID
			$param = array(
				'extparam' => array('Tag'=>'GetRoomInfo', 'Roomid'=>$roomid),
				'param' => array('BigCaseId'=>10001,'CaseId'=>10001,'ParentId'=>10001,'ChildId'=>101)
			);
			$roomInfo = request($param);
			$roomName = empty($roomInfo['name']) ? $roomid : $roomInfo['name'];
		}
		$balance = get_parent_money(10006, 10049, 10269, $groupId)/10000;
		if($type == 'vdouexchange'){
			//得到金豆余额 金豆兑换页面
			$vdou = (int)get_money($user['Uin']);
		}
		$tem = "{$type}.html";
		break;
	case 'getUserInfo' :	//获取用户信息
		$uin = $_GET['uin'];
		$row = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'GetActualUin','Uin'=>$uin,'GroupId'=>$GroupData['groupid'])));
		if($row['Flag'] != 100){
			exit('|'.$row['FlagString']);
		}
		$uin = $row['Uin'];
		$param = array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$uin));
		$result = httpPOST(KBASIC_API_PATH,$param);
		$return = $result['Flag'] == 100? intval($_GET['uin']).'|'.$result['Nick'] : '|';
		exit($return);
		break;
	case 'recharge_new_window':
		$tem = 'recharge_new_window.html';
		break;
	case 'payment_save' :	//提交充值信息
		$post = $_POST;
		$recharge_type = array('ALIPAY','CHINABANK','TENPAY','gamecard','mobilecard');
		
		if($groupId < 1 || $post['pay_expense'] < 1 || $post['pay_uin'] < 1 || !in_array($post['pay_type'],$recharge_type)){
			alertMsg('参数错误');
		}
		$row = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'GetActualUin','Uin'=>$post['pay_uin'],'GroupId'=>$groupId)));
		if($row['Flag'] != 100){
			alertMsg($row['FlagString'], '?group_id='.$groupId);
		}
		$post['pay_uin'] = $row['Uin'];
		$uinInfo = getGroupVip($post['pay_uin'],$groupId);
		if($uinInfo['uin'] != $post['pay_uin']){
			alertMsg($post['pay_uin'].'不是本站会员，不能充值', '?group_id='.$groupId);
		}
		if($post['type'] == 'agency'){
			if($post['notOpenAgent'] != 1){//开通代理
				//是否有绑定的渠道ID
				$openInfo = httpPOST(SSO_API_PATH,array('extparam'=>array('Tag'=>'GetUser','UserName'=>$user['Openid'])));
				if($openInfo['ChannelId'] > 0){
					$Uin = $openInfo['ChannelId'];
				}
				$post['pay_uin'] = $Uin;
				if($post['pay_expense'] < LOWEST_WEIGHT){
					exit('参数错误');
				}
			}
		}
		if( !empty($post['pay_type']) && $post['pay_expense'] > 0 && $post['pay_uin'] > 0 )
		{
			$rebate = 1;
			if($post['pay_type'] == 'ALIPAY'){
				$ParentId = '10064';
				if(!$GroupData['EXT']['alipay_id']['value']){
					ShowMsg("未开通的支付方式", '?group_id='.$groupId);
				}
			}elseif ($post['pay_type'] == 'CHINABANK'){
				$ParentId = '10094';
				if(!$GroupData['EXT']['chinabank_id']['value']){
					ShowMsg("未开通的支付方式", '?group_id='.$groupId);
				}
			}elseif ($post['pay_type'] == 'TENPAY'){
				$ParentId = '10095';
				if(!$GroupData['EXT']['tenpay_id']['value']){
					ShowMsg("未开通的支付方式", '?group_id='.$groupId);
				}
			}elseif($post['pay_type'] == 'gamecard'){
				$ParentId = 10320;
				$rebate = 0.8;
				$post['pay_type'] = 'BAOFOO';
				if(!$GroupData['EXT']['baofoo_id']['value']){
					ShowMsg("未开通的支付方式", '?group_id='.$groupId);
				}
			}elseif($post['pay_type'] == 'mobilecard'){
				$ParentId = 10320;
				$rebate = 0.9;
				$post['pay_type'] = 'BAOFOO';
				if(!$GroupData['EXT']['baofoo_id']['value']){
					ShowMsg("未开通的支付方式", '?group_id='.$groupId);
				}
			}
			$balance = get_parent_money(10006, 10049, 10269, $groupId);
			$need_pay = $post['pay_expense']*10000*$rebate;
			if($need_pay > $balance){
				//商家的余额不足
				ShowMsg("充值功能正在维护中！", '?group_id='.$groupId);
			}
			//生成订单
			$param = array(
				'extparam' => array('Tag'=>'SubmitTrade', 'Rebate' => $rebate, 'GroupId'=>$groupId, 'PayId'=>$post['pay_id'],'ChannelId'=>intval($post['roomid']),'Callback'=>$json['Callback'],'Element'=>$json['Element']),
				'param' => array('BigCaseId'=>10005,'CaseId'=>10024,'ParentId'=>$ParentId,'ChildId'=>101,'Uin'=>$user['Uin'],'TargetUin'=>$post['pay_uin'],'MoneyWeight'=>$post['pay_expense']),
			);
			$result = request($param);
			if($result['Flag'] == 100) {
				$post['trade_id'] = $result['TradeId'];
				$param = array(
					'extparam' => array('Tag'=>'WebPay','mypost' => $post),
					'param' => array('BigCaseId'=>10005,'CaseId'=>10024,'ParentId'=>$ParentId,'ChildId'=>101,'Uin'=>$user['Uin'],'TargetUin'=>$post['pay_uin'],'MoneyWeight'=>$post['pay_expense'],'Desc'=>'V点充值','DoingWeight'=>1)
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
	case 'vdouexchange': //金豆兑换
		$money = intval($_POST['pay_expense']);
		$vdou = (int)get_money($user['Uin']);
		if($money > $vdou){
			alertMsg("兑换金额不能大于金豆余额");
		}
		$param = array(
			'extparam' => array('Tag'=>'VChange','Group_id'=>$groupId,'Weight'=>$money,'Uin'=>$user['Uin']),
			'param'    => array('BigCaseId'=>10004,'CaseId'=>10042,'ParentId'=>10317,'ChildId'=>104)
		);
		$result = request($param);
		if($result['Flag'] == 100){
			exit('<script>parent.location.href="/shop/index.php?module=exchange_success&group_id='.$groupId.'&vdian='.$money.'"</script>');
			//header("Location: /shop/index.php?module=exchange_success&group_id={$groupId}&vdian={$money}");
		}else{
			alertMsg($result['FlagString']);
		}
		break;
	case 'exchange_success':
		$vdian = intval($_GET['vdian']);
		$type = 'vdouexchange';
		$tem = 'exchange_success.html';
		break;
}

$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/shop/';
$tmp_config['cache_dir'].=$themes.'/tpl/shop/';
$tpl = template::getInstance();
$tpl->setOptions($tmp_config);
include template('index/'.$tem,$tpl);