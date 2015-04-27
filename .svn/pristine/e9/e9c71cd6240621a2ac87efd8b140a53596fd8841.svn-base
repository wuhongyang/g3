<?php
require_once '../../library/global.fun.php';

$param = array(
		'extparam' => array('Tag'=>'ChinabankReceive','notOpenAgent'=>intval($_GET['notOpenAgent'])),
		'param'    => array('BigCaseId'=>10005,'CaseId'=>10024,'ParentId'=>10094,'ChildId'=>101,'Type'=>$_GET['type'],'GroupId'=>intval($_GET['GroupId']))
	);
$result = request($param);

if($result['Flag'] == 100){
	if($_GET['type'] == 'agency'){
		if(intval($_GET['notOpenAgent']) == 1){
			ShowMsg('','http://'.$_SERVER['HTTP_HOST'].'/service/agent_region.php?module=success&uin='.$result['Uin'].'&recharge_money='.$result['Money']);
		}else{
			ShowMsg('','http://'.$_SERVER['HTTP_HOST'].'/rooms/agency.php?module=success');
		}
	}elseif($_GET['type'] == 'Voucher'){
		if(intval($_GET['notOpenAgent']) == 2){
			echo ShowMsg('','http://'.$_SERVER['HTTP_HOST'].'/group/voucher.php?module=success&balance='.$result['BusinessBalance']);
		}
	}else{
		if(empty($result['Callback'])){
			ShowMsg('','http://'.$_SERVER['HTTP_HOST'].'/shop/recharge_success.php?style=chinabank&recharge_money='.$result['Money'].'&PayId='.$result['PayId'].'&ChannelId='.$result['ChannelId'].'&uin='.$result['Uin'].'&group_id='.intval($_GET['GroupId']));
		}else{
			ShowMsg('','http://'.$_SERVER['HTTP_HOST'].'/vip.php?module=buy_success');
		}
	}
}elseif($result['Flag'] == 103){
	ShowMsg('','http://'.$_SERVER['HTTP_HOST'].'/shop/recharge_fail.php?style=chinabank&recharge_money='.$result['Money'].'&PayId='.$result['PayId'].'&ChannelId='.$result['ChannelId'].'&uin='.$result['Uin'].'&group_id='.intval($_GET['GroupId']).'&trade_id='.$result['TradeId']);
}else{
	echo $result['Result'];
}