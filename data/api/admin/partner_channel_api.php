<?php
include 'library/partner_channel.class.php';
include 'library/room.class.php';

$json = $_POST['extparam'];
$partner_channel = new partner_channel($json['GroupId']);

switch($json['Tag']){
	case 'PartnerList':
		echo json_encode($partner_channel->partnerList($json['SearchData']));
		break;
	case 'ChannelList':
		echo json_encode($partner_channel->channelList($json['SearchData']));
		break;
	case 'ChannelInfoList':
		echo json_encode($partner_channel->getChannelInfo($json['Data']));
		break;
	case 'ChannelAdd':
		echo json_encode($partner_channel->channelAdd($json['Data']));
		break;
	case 'ChannelUpdate':
		echo json_encode($partner_channel->channelUpdate($json['Data'],$json['Id']));
		break;
	case 'PartnerInfoList':
		echo json_encode($partner_channel->getPartnerInfo($json['Id']));
		break;
	case 'ChannelsInPartner':
		echo json_encode($partner_channel->getChannelsByPartnerId($json['PartnerId']));
		break;
	case 'PartnerAdd':
		echo json_encode($partner_channel->partnerAdd($json['Data']));
		break;
	case 'PartnerUpdate':
		echo json_encode($partner_channel->partnerUpdate($json['Data'],$json['Id']));
		break;
	case 'SetSalaryAndReward':
		echo json_encode($partner_channel->setSalaryAndReward($json['Id']));
		break;
	case 'SaveSalaryAndReward':
		echo json_encode($partner_channel->saveSalaryAndReward($json['Data']));
		break;
	case 'ProxyAdd':
		echo json_encode($partner_channel->proxyAdd($json['Data']));
		break;;
	case 'ChannelSync':
		echo json_encode($partner_channel->channelSync($json['Ids']));
		break;
}