<?php
require_once 'library/decoration.class.php';
$d = new Decoration();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'UpdateGroupInfo': 
		echo json_encode($d->updateGroupInfo($json['GroupId'],$json['Data']));
		break;	
	case 'AddCarousel':
		echo json_encode($d->addCarousel($json['Data']));
		break;	
	case 'CarouselList':
		echo json_encode($d->carouselList($json['GroupId']));
		break;	
	case 'CarouselUp':
		echo json_encode($d->carouselUp($json['Id'],$json['GroupId']));
		break;	
	case 'CarouselDown':
		echo json_encode($d->carouselDown($json['Id'],$json['GroupId']));
		break;	
	case 'CarouselDel':
		echo json_encode($d->carouselDel($json['Id'],$json['GroupId']));
		break;	
	case 'GroupStyle':
		echo json_encode($d->groupStyle($json['GroupId']));
		break;	
	case 'StyleList':
		echo json_encode($d->styleList($json['CatId']));
		break;	
	case 'StyleCatList':
		echo json_encode($d->styleCatList());
		break;	
	case 'GroupStyleSetting':
		echo json_encode($d->groupStyleSetting($json['GroupId'],$json['StyleId']));
		break;	
	case 'StyleInfo':
		echo json_encode($d->styleInfo($json['StyleId']));
		break;	
	case 'Init':
		echo json_encode($d->init($json['GroupId']));
		break;
	case 'UpdateGroupStyle':
		echo json_encode($d->updateGroupStyle($json['GroupId'],$json['Data']));
		break;
}