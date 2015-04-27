<?php
require_once 'library/image.class.php';
//require_once dirname(dirname(dirname(dirname(__FILE__)))).'/library/global.fun.php';
$i = new Image();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'ImageList':
		echo json_encode($i->imageList($json['GroupId']));
		break;
	case 'ImageInfo':
		echo json_encode($i->imageInfo($json['Id'],$json['GroupId']));
		break;
	case 'ImageAdd':
		echo json_encode($i->imageAdd($json['Data']));
		break;
	case 'ImageEdit':
		echo json_encode($i->imageEdit($json['Id'],$json['Data']));
		break;
	case 'ImageDel':
		echo json_encode($i->imageDel($json['Id'],$json['GroupId']));
		break;
	case 'Visible':
		echo json_encode($i->visible($json['Id'],$json['GroupId']));
		break;
	case 'Order':
		echo json_encode($i->order($json['Id'],$json['GroupId'],$json['Type']));
		break;
}