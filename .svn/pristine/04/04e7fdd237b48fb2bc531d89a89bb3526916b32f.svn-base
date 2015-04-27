<?php
require_once 'common.php';

$module=empty($_GET['module'])?'signed_list':$_GET['module'];

$Uin=$user['Uin'];
$Nick=$user['Nick'];

//判断权限	
// if(empty($user['channel_id'])){
	// ShowMsg('请先申请成为渠道角色',-1);
// }

switch($module){
	case 'user_signed_list':
		$title='我的签约-签约房间';
		$template='usersignedlist';
		
		$param=array(
			'extparam'=>array('Tag'=>'GetUserSignedList','Data'=>array('Uin'=>$Uin)),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10246,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'用户签约的渠道')
		);
		$signedList=request($param);
	break;
	case 'user_termination':
		$title='签约管理-解约房间';
		$template='usertermination';
		
		$id=intval($_REQUEST['id']);
		if($id<=0){
			ShowMsg('error','index.php');
		}
		$param=array(
			'extparam'=>array('Tag'=>'GetUserSignedList','Data'=>array('Uin'=>$Uin,'Id'=>$id)),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10246,'ChildId'=>101,'Uin'=>$Uin,'Desc'=>'用户签约的渠道')
		);
		$signedInfo=request($param);
		if(empty($signedInfo['total'])){
			ShowMsg('没有这个房间','index.php');
		}
		$signedInfo=$signedInfo['userList'][0];
	break;
	case 'user_termination_submit':
		$id=intval($_REQUEST['id']);
		if($id<=0){
			ShowMsg('error','index.php');
		}
		$data=array(
			'PartnerUin'=>$Uin,
			'ChannelId'=>$id,
			'Type'=>2
		);
		$param=array(
			'extparam'=>array('Tag'=>'PartnerTermination','Data'=>$data),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10046,'ParentId'=>10261,'ChildId'=>102,'Uin'=>$Uin,'Desc'=>'解约室主或艺人')
		);
		$result=request($param);
		if($result['Flag']!=100){
			ShowMsg($result['FlagString'],'room_channel_user.php?module=user_signed_list');
		}
		else{
			echo '<script>window.location="room_channel_user.php?module=user_termination_success&id='.$result['recordsId'].'"</script>';
			exit;
		}
	break;
	case 'user_termination_success':
		$title='我的签约-解除签约成功';
		$template='userterminationsuccess';
		
		$id=intval($_REQUEST['id']);
		if($id<=0){
			ShowMsg('error','index.php');
		}
		
		$param=array(
			'extparam'=>array('Tag'=>'GetTerminationRecordsInfo','Id'=>$id),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10246,'ChildId'=>103,'Uin'=>$Uin,'Desc'=>'查看解约历史详情')
		);
		$result=request($param);
		if($result['Flag']!=100){
			ShowMsg($result['FlagString'],'room_channel_user.php?module=user_signed_list');
		}
		$records=$result['Records'];
		if($records['uin']!=$Uin){
			ShowMsg('error','room_channel_user.php?module=user_signed_list');
		}
	break;
	case 'user_termination_list':
		$title='我的签约-查看解约历史';
		$template='userterminationlist';
		
		$data=array(
			'Uin'=>$Uin
		);
		$param=array(
			'extparam'=>array('Tag'=>'GetTerminationRecordsList','Data'=>$data),
			'param'=>array('BigCaseId'=>10004,'CaseId'=>10038,'ParentId'=>10246,'ChildId'=>103,'Uin'=>$Uin,'Desc'=>'查看解约历史')
		);
		$signedList=request($param);
	break;
}

$serviceType='room_channel_user';
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
include template('roommanage/'.$template.'.html',$tpl);