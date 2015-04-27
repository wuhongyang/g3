<?php
require_once 'library/video.class.php';
$video = new video();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch ($json['Tag']) {
	case 'VideoSave':
		echo json_encode($video->save($json['Id'], $json['Name'], $json['Link'], $json['PicMD5'], $json['GroupId'], $json['Uin']));
		break;
	case 'VideoList':
		echo json_encode($video->video_list($json['Id'], $json['GroupId'], $json['Uin']));
		break;
	case 'VideoDel':
		echo json_encode($video->del($json['Id'], $json['GroupId'], $json['Uin']));
		break;
}