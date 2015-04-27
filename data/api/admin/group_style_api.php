<?php
include_once 'library/group_style.class.php';
$gr = new GroupStyle();
$json = $_POST['extparam'];

switch($json['Tag']){
	case 'StyleList':
		echo json_encode($gr->styleList($json['GroupId']));
		break;
	case 'StyleSave':
		echo json_encode($gr->styleSave($json['Data']));
		break;
	case 'StyleSettingList':
		echo json_encode($gr->styleSettingList($json['Id']));
		break;
	case 'StyleSettingSave':
		echo json_encode($gr->styleSettingSave($json['Data']));
		break;
	case 'StyleCategoryList':
		echo json_encode($gr->styleCategoryList());
		break;
	case 'StyleCategorySave':
		echo json_encode($gr->styleCategorySave($json['Data']));
		break;
}