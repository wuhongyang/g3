<?php
require_once '../../library/global.fun.php';
function doShow($show_url) {
	$strHtml = "<html><head>\r\n" .
	"<meta name=\"TENCENT_ONLINE_PAYMENT\" content=\"China TENCENT\">" .
	"<script language=\"javascript\">\r\n" .
	"document.location.replace('" . $show_url . "');\r\n" .
	"</script>\r\n" .
	"</head><body></body></html>";
	return $strHtml;
}

$param = array(
	'extparam' => array('Tag'=>'Tenpay','notOpenAgent'=>intval($_GET['notOpenAgent'])),
	'param'    => array('BigCaseId'=>10005,'CaseId'=>10024,'ParentId'=>10095,'ChildId'=>101,'Type'=>$_GET['type'],'GroupId'=>intval($_GET['GroupId']))
);
	
$result = request($param);

if($result['Flag'] == 100){
	if($_GET['type'] == 'agency'){
		if(intval($_GET['notOpenAgent']) == 1){
			echo doShow('http://'.$_SERVER['HTTP_HOST'].'/service/agent_region.php?module=success&uin='.$result['Uin'].'&recharge_money='.$result['Money']);
		}else{
			echo doShow('http://'.$_SERVER['HTTP_HOST'].'/rooms/agency.php?module=success');
		}
	}elseif($_GET['type'] == 'Voucher'){
		if(intval($_GET['notOpenAgent']) == 2){
			echo doShow('http://'.$_SERVER['HTTP_HOST'].'/group/voucher.php?module=success&balance='.$result['BusinessBalance']);
		}
	}else{
		if(empty($result['Callback'])){
			ShowMsg('','http://'.$_SERVER['HTTP_HOST'].'/shop/recharge_success.php?style=tenpay&recharge_money='.$result['Money'].'&PayId='.$result['PayId'].'&ChannelId='.$result['ChannelId'].'&uin='.$result['Uin'].'&group_id='.intval($_GET['GroupId']));
		}else{
			ShowMsg('','http://'.$_SERVER['HTTP_HOST'].'/vip.php?module=buy_success');
		}
		//echo doShow('http://'.$_SERVER['HTTP_HOST'].'/shop/recharge_success.php?style=tenpay&recharge_money='.$result['Money'].'&PayId='.$result['PayId'].'&ChannelId='.$result['ChannelId'].'&uin='.$result['Uin'].'&group_id='.intval($_GET['GroupId']));
		//echo doShow('http://'.$_SERVER['HTTP_HOST'].'/shop/recharge_success.php?style=tenpay&recharge_money='.$result['Money'].'PayId='.$result['PayId'].'&ChannelId='.$result['ChannelId']);
	}
}elseif($result['Flag'] == 103){
	ShowMsg('','http://'.$_SERVER['HTTP_HOST'].'/shop/recharge_fail.php?style=tenpay&recharge_money='.$result['Money'].'&PayId='.$result['PayId'].'&ChannelId='.$result['ChannelId'].'&uin='.$result['Uin'].'&group_id='.intval($_GET['GroupId']).'&trade_id='.$result['TradeId']);
}else{
	echo $result['Result'];
}
