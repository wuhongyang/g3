<?php
require_once 'library/recommend.class.php';
$r = new Recommend();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'AddRoomRecommend':
		echo json_encode($r->addRoomRecommend($json['Data']));
		break;
	case 'AddVipRecommend':
		echo json_encode($r->addVipRecommend($json['Data']));
		break;
	case 'AddCommonRecommend':
		echo json_encode($r->addCommonRecommend($json['Data']));
		break;
	case 'RecommendCatList':
		echo json_encode($r->recommendCatList($json['GroupId']));
		break;
	case 'RecommendCatInfo':
		echo json_encode($r->recommendCatInfo($json['Id'],$json['GroupId']));
		break;
	case 'RecommendCatAdd':
		echo json_encode($r->addRecommendCat($json['Data']));
		break;
	case 'RecommendCatEdit':
		echo json_encode($r->recommendCatEdit($json['Id'],$json['GroupId'],$json['Data']));
		break;
	case 'RecommendCatOrder':
		echo json_encode($r->recommendCatOrder($json['Id'],$json['GroupId'],$json['Type']));
		break;
	case 'RecommendCatVisible':
		echo json_encode($r->recommendCatVisible($json['Id'],$json['GroupId']));
		break;
	case 'RecommendSubCatShow':
		echo json_encode($r->recommendSubCatShow($json['GroupId'],$json['ParentId']));
		break;
	case 'RecommendSubCatAdd':
		echo json_encode($r->recommendSubCatAdd($json['Data']));
		break;
	case 'RecommendSubCatArtistAdd':
		echo json_encode($r->recommendSubCatArtistAdd($json['Data']));
		break;
	case 'RecommendSubCatOrder':
		echo json_encode($r->recommendSubCatOrder($json['Id'],$json['GroupId'],$json['ParentId'],$json['Type']));
		break;
	case 'RecommendSubCatInfo':
		echo json_encode($r->recommendSubCatInfo($json['Id'],$json['GroupId']));
		break;
	case 'RecommendSubCatEdit':
		echo json_encode($r->recommendSubCatEdit($json['Id'],$json['GroupId'],$json['Data']));
		break;
	case 'RecommendSubCatArtistEdit':
		echo json_encode($r->recommendSubCatArtistEdit($json['Id'],$json['GroupId'],$json['Data']));
		break;
	case 'RecommendSubCatDel':
		echo json_encode($r->recommendSubCatDel($json['Id'],$json['GroupId']));
		break;
	case 'RecommendSubCatVisible':
		echo json_encode($r->recommendSubCatVisible($json['Id'],$json['GroupId']));
		break;
	case 'RecommendShow':
		echo json_encode($r->recommendShow($json['GroupId'],$json['ParentId']));
		break;
	case 'RecommendOrder':
		echo json_encode($r->recommendOrder($json['Id'],$json['GroupId'],$json['Type']));
		break;
	case 'RecommendDel':
		echo json_encode($r->recommendDel($json['Id'],$json['GroupId']));
		break;
	case 'RecommendCommonDel':
		echo json_encode($r->recommendCommonDel($json['Id'],$json['GroupId']));
		break;
	case 'RecommendCommonInfo':
		echo json_encode($r->recommendCommonInfo($json['Id'],$json['GroupId']));
		break;
	case 'RecommendCommonEdit':
		echo json_encode($r->recommendCommonEdit($json['Id'],$json['GroupId'],$json['Data']));
		break;
	case 'RecommendCommonOrder':
		echo json_encode($r->recommendCommonOrder($json['Id'],$json['GroupId'],$json['Type']));
		break;
}