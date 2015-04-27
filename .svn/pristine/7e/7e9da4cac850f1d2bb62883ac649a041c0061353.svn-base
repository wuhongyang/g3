<?php
include 'library/channel_category.class.php';

$ChannelCategory = new ChannelCategory();
$json = $_POST['extparam'];

switch($json['Tag']){
	case 'List':
		echo json_encode($ChannelCategory->lists($json['SearchData']));
		break;
	case 'Info':
		echo json_encode($ChannelCategory->getInfoById($json['Id']));
		break;
	case 'Add':
		echo json_encode($ChannelCategory->add($json['Data']));
		break;
	case 'Update':
		echo json_encode($ChannelCategory->update($json['Data'],$json['Id']));
		break;
}