<?php
require_once 'common.php';
if(!in_array($themes,array('cc51','sixroom','tt1758'))){
	echo '该站还没有该功能';exit;
}

$module=empty($_GET['module'])?'detail':$_GET['module'];

$Uin=$user['Uin'];
$Nick=$user['Nick'];

//判断权限	
if(!$isArtist){
   ShowMsg('您还不是艺人',-1);
}

switch($module){
	case 'detail':
		$title='艺人后台-收入详单';
		$template='artist_tax_exchange';
		
		//当前收入
		$param=array(
			'extparam'=>array('Tag'=>'GetPointVaule','UinId'=>$Uin,'ExtendUin'=>$group_id,'Ruleid'=>$groupExtInfo['artistTaxId']['value'],"Period"=>"month"),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10064,'ParentId'=>10580,'ChildId'=>101)
		);
		$taxTotalMonth=request($param);
		$taxTotalMonth=intval($taxTotalMonth['Weight']);
		
		//税收余额
		$param = array(
				'extparam' => array('Tag'=>'GetBalance','Uin'=>$Uin,'GroupId'=>$group_id),
				'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10583,'ChildId'=>102)
		);
		$userBalance = request($param);
		$userBalance=intval($userBalance['Result']['Weight']);
		
		//在线时长本月
		$param=array(
			'extparam'=>array('Tag'=>'GetPointVaule','UinId'=>$Uin,'ExtendUin'=>$group_id,'Ruleid'=>$groupExtInfo['onlineRuleId']['value'],"Period"=>"month"),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10064,'ParentId'=>10580,'ChildId'=>101)
		);
		$onLineTimeMonth=request($param);
		$onLineTimeMonth=intval($onLineTimeMonth['Weight']);
		$onLineTimeMonth=round($onLineTimeMonth/3600,2);
		
		//在线时长本周
		$param=array(
			'extparam'=>array('Tag'=>'GetPointVaule','UinId'=>$Uin,'ExtendUin'=>$group_id,'Ruleid'=>$groupExtInfo['onlineRuleId']['value'],"Period"=>"week"),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10064,'ParentId'=>10580,'ChildId'=>101)
		);
		$onLineTimeWeek=request($param);
		$onLineTimeWeek=intval($onLineTimeWeek['Weight']);
		$onLineTimeWeek=round($onLineTimeWeek/3600,2);
	break;
	case 'exchange':
		$title='艺人后台-收入详单';
		$template='artist_tax_exchange_submit';	
		//税收余额
		$param = array(
				'extparam' => array('Tag'=>'GetBalance','Uin'=>$Uin,'GroupId'=>$group_id),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10064,'ParentId'=>10581,'ChildId'=>101)
		);
		$userBalance = request($param);
		$userBalance=intval($userBalance['Result']['Weight']);
	break;
	case 'exchange_submit':
		$kmoney=$_POST['kmoney'];
		$param = array(
				'extparam' => array('Tag'=>'KmoneyExchange'),
				'param'    => array('BigCaseId'=>10006,'CaseId'=>10047,'ParentId'=>10583,'ChildId'=>104,'GroupId'=>$group_id,'Uin'=>$Uin,'TargetUin'=>$Uin,'DoingWeight'=>1,'MoneyWeight'=>$kmoney,'Desc'=>$Uin.'兑换税收积分'.$kmoney.'获取金币'.$kmoney)
		);
		$result = request($param);
		if($result['Flag']!=100){
			ShowMsg($result['FlagString'],-1);
		}
		else{
			ShowMsg($result['FlagString'],'/service/artist_tax_exchange.php?module=history');
		}
	break;
	case 'history':
		$title='艺人后台-兑换记录';
		$template='artist_tax_exchange_history';
		
		$stime=0;
		$etime=0;
		if($_GET['stime']){
			$stime=strtotime($_GET['stime']);
		}
		if($_GET['etime']){
			$etime=strtotime($_GET['etime'].' 23:59:59');
		}
		$param = array(
				'extparam' => array('Tag'=>'TaxDetail','GroupId'=>$group_id,'Uin'=>$Uin,'CaseId'=>10047,'ParentId'=>10583,'StartTime'=>$stime,'EndTime'=>$etime),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10064,'ParentId'=>10582,'ChildId'=>101)
		);
		$list = request($param);
		$page = $list['Page'];
		unset($list['Page']);
		$list = (array)$list['Result'];
	break;
	case 'tax_history':
		$title='艺人后台-收礼记录';
		$template='artist_tax_history';
		
		$stime=0;
		$etime=0;
		if($_GET['stime']){
			$stime=strtotime($_GET['stime']);
		}
		if($_GET['etime']){
			$etime=strtotime($_GET['etime'].' 23:59:59');
		}
		$param = array(
				'extparam' => array('Tag'=>'TaxDetail','GroupId'=>$group_id,'Uin'=>$Uin,'Ruleid'=>19,'StartTime'=>$stime,'EndTime'=>$etime),
				'param'    => array('BigCaseId'=>10004,'CaseId'=>10064,'ParentId'=>10582,'ChildId'=>101)
		);
		$list = request($param);
		$page = $list['Page'];
		unset($list['Page']);
		$list = (array)$list['Result'];
		foreach($list as $key=>$val){
			$param = array(
				'extparam'=>array(
					"Tag" => "GetParent",
					"ParentId" => $val['ParentId']
				)
			);
			$ccsInfo = httpPOST(CCS_API_PATH,$param);
			$list[$key]['name']=$ccsInfo['Result']['parent_name'];
		}
	break;
}

$serviceType='artist_tax_exchange';
$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/service/';
$tmp_config['cache_dir'].=$themes.'/tpl/service/';
$tpl = template::getInstance();
$tpl->setOptions($tmp_config);
include template('artist/'.$template.'.html',$tpl);