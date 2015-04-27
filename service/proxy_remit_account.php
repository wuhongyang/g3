<?php
require_once 'common.php';

$module=empty($_GET['module'])?'list':$_GET['module'];

$Uin=$user['Uin'];
$Nick=$user['Nick'];

if($group_id<=0){
	ShowMsg('请从正确域名登陆!',-1);
}
if($GroupData['module_id']==0){
	$RoleId=10011;
}
elseif($GroupData['module_id']==1){
	$RoleId=10545;
}
elseif($GroupData['module_id']==2){
	$RoleId=10011;
}
elseif($GroupData['module_id']==3){
	$RoleId=10541;
}
$roles_info = httpPOST(ROLE_API_PATH,array('extparam'=>array('Tag'=>'GetRole','Uin'=>$Uin,'GroupId'=>$group_id,'RoleId'=>$RoleId)));
$roles = $roles_info['Roles'];
if(empty($roles)){
	$module='no_proxy';
}

//V点
$vmoney = get_money($user['Uin'],$user['GroupId']);
$serviceType='proxy_remit_account';

switch($module){
	case 'list':
		$title='代理划账';
		$template='list';
		//流水
		$param=array(
			'extparam'=>array('Tag'=>'GetRemitAccountList','Data'=>array('Uin'=>$Uin,'TargetUin'=>$_GET['TargetUin'],'StartDate'=>$_GET['StartDate'],'EndDate'=>$_GET['EndDate'])),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10471,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'划账流水列表')
		);
		$list=request($param);
		//发生总计
		$param=array(
			'extparam'=>array('Tag'=>'GetRemitAccountCount','Data'=>array('Uin'=>$Uin,'TargetUin'=>$_GET['TargetUin'],'StartDate'=>$_GET['StartDate'],'EndDate'=>$_GET['EndDate'])),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10471,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'划账流水总计')
		);
		$total=request($param);
		$total=$total['total'];
		
		$StartDate=$_GET['StartDate']?$_GET['StartDate']:date('Y-m').'-01 00:00';
		$EndDate=$_GET['EndDate']?$_GET['EndDate']:date('Y-m-d H:i');
	break;
	case 'recharge':
		$template='recharge';
		//发生总计
		$param=array(
			'extparam'=>array('Tag'=>'GetRemitAccountCount','Data'=>array('Uin'=>$Uin,'StartDate'=>$_GET['StartDate'],'EndDate'=>$_GET['EndDate'])),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10471,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'划账流水总计')
		);
		$total=request($param);
	break;
	case 'recharge_submit':
		$param=array(
			'extparam'=>array('Tag'=>'ProxyRecharge','GroupId'=>$group_id),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10471,'ChildId'=>102,'Uin'=>$Uin,'TargetUin'=>$_POST['uin'],'Desc'=>$Uin.'给'.$_POST['uin'].'划账金额'.$_POST['money'],'MoneyWeight'=>$_POST['money'],'DoingWeight'=>1)
		);
		$result=request($param);
		
		if($result['Flag']!=100){
			ShowMsg($result['FlagString'],'proxy_remit_account.php?module=recharge');
		}
		else{
			ShowMsg($result['FlagString'],'proxy_remit_account.php?module=list');
		}
	break;
	case 'no_proxy':
		$title='代理划账';
		$template='no_proxy';
	break;
	case 'integration':
		$stime = isset($_GET['stime']) ? $_GET['stime'] : date('Y-m-01');
		$etime = isset($_GET['etime']) ? $_GET['etime'] : date('Y-m-d');
		$_GET['stime'] = $stime;
		$_GET['etime'] = $etime;
		$param=array(
			'extparam'=>array('Tag'=>'ProxyIntegrationDetail','GroupId'=>$group_id,'Search'=>$_GET),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10061,'ParentId'=>10467,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'代理税收')
		);
		$list = request($param);
		$msg = $list['Flag'] != 100 ? $list['FlagString'] : '';
		$detail = (array)$list['Detail'];
		$page = $detail['page'];
		$detail = (array)$detail['list'];
		$dayCollect = empty($list['DayCollect']) ? 0 : $list['DayCollect'];
		$monthCollect = empty($list['MonthCollect']) ? 0 : $list['MonthCollect'];
		$totalCollect = empty($list['TotalCollect']) ? 0 : $list['TotalCollect'];
		unset($list);
		$serviceType='proxy_integration';
		$template = 'integration';
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
include template('remit_account/'.$template.'.html',$tpl);