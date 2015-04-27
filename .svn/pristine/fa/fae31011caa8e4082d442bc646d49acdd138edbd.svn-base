<?php
require_once 'library/image_manager.class.php';
$im = new ImageManager();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'StampCateList':
		echo json_encode($im->cate_list("tbl_stamptype", $json['NoPage']));
		break;
	case 'ExpressionCateList':
		echo json_encode($im->cate_list("tbl_expressiontype", $json['NoPage']));
		break;
	case 'StampCateGet':
		echo json_encode($im->stamp_cate_get($json['ParentId'])); 
		break;
	case 'ExpressionCateGet':
		echo json_encode($im->expression_cate_get($json['CateId']));
		break;
	case 'StampList':
		echo json_encode($im->stamp_list($json['ParentId']));
		break;
	case 'ExpressionList':
		echo json_encode($im->expression_list($json['CateId']));
		break;
	case 'StampGet':
		echo json_encode($im->stamp_get($json['StampId']));
		break;
	case 'ExpressionGet':
		echo json_encode($im->expression_get($json['ExpressionId']));
		break;
	case 'StampSave':
		echo json_encode($im->stamp_save($json['StampId'], $json['StampName'], $json['StampImgPath']));
		break;
	case 'ExpressionSave':
		echo json_encode($im->expression_save($json['Id'], $json['ImgName'], $json['ImgPath']));
		break;
	case 'StampDel':
		echo json_encode($im->stamp_del($json['StampId']));
		break;
	case 'ExpressionDel':
		echo json_encode($im->expression_del($json['Id']));
		break;
	case 'StampCateSave':
		echo json_encode($im->stamp_cate_save($json['Data']));
		break;
	case 'ExpressionCateSave':
		echo json_encode($im->expression_cate_save($json['Data']));
		break;
	case 'StampAdd':
		echo json_encode($im->stamp_add($json['ParentId'], $json['Data']));
		break;
	case 'ExpressionAdd':
		echo json_encode($im->expression_add($json['CateId'], $json['Data']));
		break;
}