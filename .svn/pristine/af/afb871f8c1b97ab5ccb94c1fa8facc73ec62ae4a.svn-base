<?php
include_once( '../library/global.fun.php' );

$module = empty( $_GET['module'] ) ? 'safeguard' : trim( $_GET['module'] );
$link_array = getLevellink( 10002, 10007, 10217, 101 );

$money = defined('LOWEST_WEIGHT') ? LOWEST_WEIGHT : '';

if($module == 'safeguard'){
	if(isset($_POST) && !empty($_POST)){
		$param = array(
			'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10217,'ChildId'=>101),
			'extparam' => array('Tag'=>'LowestWeightSet','Data'=>array('LOWEST_WEIGHT'=>intval($_POST['money'])))
		);
		$rst = request($param);
		alertMsg($rst['FlagString'],'?module=safeguard');
		exit;					
	}else{
		$link_array = getLevellink( 10002, 10007, 10217, 101 );
		$temp = 'safeguard';
	}
}elseif($module == 'in'){
	if(isset($_POST) && !empty($_POST)){
		$data = $_POST;
		$data['TradeType'] = 1;
		$data['Child_type'] = 5;
		$param = array(
			'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10217,'ChildId'=>102),
			'extparam' => array('Tag'=>'ProxyAccount','Data'=>$data)
		);
		$rst = request($param);
		alertMsg($rst['FlagString'],'?module=in');
		exit;
	}else{
		$link_array = getLevellink( 10002, 10007, 10217, 102 );
		$temp = 'in';
	}
}elseif($module == 'out'){
	if(isset($_POST) && !empty($_POST)){
		$data = $_POST;
		$data['TradeType'] = 2;
		$data['Child_type'] = 6;
		$param = array(
			'param' => array('BigCaseId'=>10002,'CaseId'=>10007,'ParentId'=>10217,'ChildId'=>103),
			'extparam' => array('Tag'=>'ProxyAccount','Data'=>$data)
		);
		$rst = request($param);
		alertMsg($rst['FlagString'],'?module=out');
		exit;
	}else{
		$link_array = getLevellink( 10002, 10007, 10217, 103 );
		$temp = 'out';
	}
}

$tpl = template::getInstance();
$tpl->setOptions( get_config( 'template','admin' ) );
include template( 'agent_adjust_account/'.$temp.".html",$tpl );