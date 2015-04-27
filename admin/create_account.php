<?php
require_once '../library/global.fun.php';

$module = empty($_GET['module']) ? 'single_create' : trim($_GET['module']);
$link_array = getLevellink(10002, 10004, 10144, 101);


switch($module){
	default:
	case 'single_create':
		if(isset($_POST['uin'])){
			$uin = intval($_POST['uin']);
			if($uin < 10000){
				ShowMsg('账号ID必须大于10000',$link_array[101]['url']);
			}
			$param = array(
					'extparam' => array('Tag'=>"create_single",
										'uin'=>$uin),
					'param' => array('BigCaseId'=>10002,
							'CaseId'=>10004,
							'ParentId'=>10144,
							'ChildId'=>101,
							'Desc'=>"开设账号申请",
							)
			);
			$result = request($param);
			ShowMsg($result['FlagString'],$link_array[101]['url']);
		}
		break;
	case 'range_create':
		if(isset($_POST['start_uin']) || isset($_POST['end_uin'])){
			$start_uin = intval($_POST['start_uin']);
			$end_uin   = intval($_POST['end_uin']);
			if($start_uin >= $end_uin){
				ShowMsg('终止号码必须大于起始号码',$link_array[102]['url']);
			}
			if($start_uin < 10000){
				ShowMsg('号码必须大于10000',$link_array[102]['url']);
			}
			$param = array(
					'extparam' => array('Tag'=>"create_range",
							'range'=>array($start_uin, $end_uin)),
					'param' => array('BigCaseId'=>10002,
							'CaseId'=>10004,
							'ParentId'=>10144,
							'ChildId'=>102,
							'Desc'=>"开设号码段申请",
					)
			);
			$result = request($param);
			ShowMsg($result['FlagString'],$link_array[102]['url']);
		}
		break;
}
	
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('create_account/'.$module.".html",$tpl);