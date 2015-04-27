<?php
require_once dirname(__FILE__).'/library/goods.class.php';

$goods = new GoodsConf();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'SaveCate':
		echo json_encode($goods->saveCate($json['Id'], $json['CateName'], $json['CateId'], $json['ImgPath'], $json['ImgSrc'], $json['State'], $json['GroupId']));
		break;
	case 'CateList':
		echo json_encode($goods->cateList($json['GroupId']));
		break;
	case 'CateMove':
		echo json_encode($goods->cateMove($json['CateId'], $json['Option'], $json['GroupId']));
		break;
	case 'GoodsMove':
		echo json_encode($goods->goodsMove($json['Id'], $json['Option'], $json['GroupId'], $json['CateId'], $json['SubCateId']));
		break;
	case 'CateDetail':
		echo json_encode($goods->cateDetail($json['Id'], $json['GroupId']));
		break;
	case 'GoodsList':
		echo json_encode($goods->goodsList($json['CateId'], $json['SubCateId'], $json['GroupId']));
		break;
	case 'AllCateGoods':
		echo json_encode($goods->allCateGoods($json['GroupId'], $json['GoodsId']));
		break;
	case 'PackageGoods':
		echo json_encode($goods->packageGoods($json['CateId'], $json['GroupId']));
		break;
	case 'ScopeGoods':
		echo json_encode($goods->scopeGoods($json['GroupId']));
		break;
	case 'SavePackage':
		echo json_encode($goods->savePackage($json['CateId'], $json['GroupId'], $json['Data']));
		break;
	case 'SaveGoods':
		echo json_encode($goods->saveGoods($json['CateId'], $json['GroupId'], $json['Data']));
		break;
	case 'GoodsDetials':
		echo json_encode($goods->goodsDetails($json['Id'], $json['GroupId']));
		break;
	case 'PackageDetail':
		echo json_encode($goods->packageDetail($json['Id'], $json['GroupId']));
		break;
	case 'SubCateList':
		echo json_encode($goods->subCateList($json['GroupId'],$json['CategoryId']));
		break;
	case 'SubCateInfo':
		echo json_encode($goods->subCateInfo($json['Id'],$json['GroupId']));
		break;
	case 'SubCateAdd':
		echo json_encode($goods->subCateAdd($json['Data']));
		break;
	case 'SubCateEdit':
		echo json_encode($goods->subCateEdit($json['Id'],$json['Data']));
		break;
	case 'SubCateMove':
		echo json_encode($goods->subCateMove($json['Data']));
		break;
}