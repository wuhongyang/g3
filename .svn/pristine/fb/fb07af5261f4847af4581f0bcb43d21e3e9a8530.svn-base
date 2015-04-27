<?php
/**
 *   开放型渠道用户操作接口
 *   copyright: (C) 2006-2011 by 杭州奥点科技有限公司
 */
require_once 'library/partner_channel.class.php';
require_once __ROOT__.'/api/group/library/group_manage.class.php';
require_once 'library/pass_manager.class.php';
require_once 'library/handle_matter.class.php';
require_once __ROOT__.'/api/rooms/library/room_common.class.php';

$partner_channel=new PartnerChannel();
$json=$_POST['extparam'];
$param=$_POST['param'];

switch($json['Tag']){
	case 'SetGuardian'://已签约列表
		echo json_encode($partner_channel->SetGuardian($param['ChannelId'],$json['Guardians']));
		break;
	case 'GetSignedList'://已签约列表
		echo json_encode($partner_channel->getSignedList($json['Data']));
		break;
	case 'PartnerSigned'://房间签约室主或艺人
		echo json_encode($partner_channel->partnerSigned($json['Data']));
		break;
	case 'GetUserSignedList'://用户签约的渠道
		echo json_encode($partner_channel->getUserSignedList($json['Data']));
		break;
	case 'GetChannelUser'://开放型渠道用户查询
		echo json_encode($partner_channel->getUserSignedList($json['Data']));
		break;	
	case 'GetTerminationRecordsList'://解约历史
		echo json_encode($partner_channel->getTerminationRecordsList($json['Data']));
		break;
	case 'GetTerminationRecordsInfo'://解约历史详情
		echo json_encode($partner_channel->getTerminationRecordsInfo($json['Id']));
		break;
	case 'PartnerTermination'://房间解约室主或艺人
		echo json_encode($partner_channel->partnerTermination($json['Data']));
		break;
	case 'GetApplyList':
		echo json_encode($partner_channel->getApplyList($json['GroupId'],$json['SearchData']));
		break;
}