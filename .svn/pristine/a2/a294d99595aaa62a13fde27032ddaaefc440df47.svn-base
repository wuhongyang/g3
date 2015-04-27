<?php
require_once 'library/gridfs.class.php';
require_once 'library/pic_category.class.php';
$json = $_POST['extparam'];

//强制转换类型
foreach((array)$json['options'] as $key=>$val){
	if(is_numeric($val)) $json['options'][$key] = intval($val);
}
foreach((array)$json['extra'] as $key=>$val){
	if(is_numeric($val)) $json['extra'][$key] = intval($val);
}

$gridfs = new gridfs();
switch($json['Tag']){
// 	case 'Delete':
// 		echo json_encode($gridfs->delete($json['where'],$json['options']));
// 		break;
// 	case 'Save':
// 		echo json_encode($gridfs->save($json['path'],$json['extra']));
// 		break;
// 	case 'SaveUnique':
// 		echo json_encode($gridfs->saveUnique($json['path'],$json['unique'],$json['extra']));
// 		break;
// 	case 'SaveBytes':
// 		echo json_encode($gridfs->saveBytes(base64_decode($json['bytes']),$json['extra']));
// 		break;
// 	case 'SaveBytesUnique':
// 		echo json_encode($gridfs->saveBytesUnique(base64_decode($json['bytes']),$json['unique'],$json['extra']));
// 		break;
	case 'GetFile':
		echo json_encode($gridfs->getFile($json['where']));
		break;
// 	case 'GetFileByMd5':
// 		echo json_encode($gridfs->getByMd5($json['md5']));
// 		break;

	case 'CatList' :  	//图片分类列表显示
		$pic = new pic_category();
		echo json_encode($pic->catList( $json['mypost'] ));
		break;
	case 'CatName' :  	//图片分类名称
		$pic = new pic_category();
		echo json_encode($pic->getCatName( $json['mypost'] ));
		break;
	case 'AddPicCat':	//图片分类添加,编辑
		$pic = new pic_category();
		echo json_encode($pic->addPicCat( $json['mypost'] ));
		break;

	case 'PicList' :  	//图片列表显示
		$pic = new pic_category();
		echo json_encode($pic->picList( $json['mypost'] ));
		break;

	case 'PicAdd'  :	//图片添加,编辑
		$pic = new pic_category();
		echo json_encode($pic->picAdd( $json['mypost'] ));
		break;

	case 'ShowOriPic':	//查看原图
		$pic = new pic_category();
		echo json_encode($pic->showOriPic( $json['mypost'] ));
		break;
	case 'DelPicCat' :	//图片分类删除
		$pic = new pic_category();
		echo json_encode($pic->delPicCat( $json['mypost'] ));
		break;
}
