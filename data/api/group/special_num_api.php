<?php
require_once 'library/special_num.class.php';
$sn = new SpecialNum();
$json = $_POST['extparam'];
$param = $_POST['param'];

switch($json['Tag']){
	case 'CateList':
		echo json_encode($sn->cate_list($json['GroupId']));
		break;
	case 'AddCate':
		echo json_encode($sn->add_cate($json['GroupName'], $json['GroupId'],$json['ShopStaus'], $json['Words']));
		break;
	case 'UpdateCate':
		echo json_encode($sn->update_cate($json['GroupName'], $json['GroupId'], $json['CateId'],$json['ShopStaus'], $json['Words']));
		break;
	case 'DeleteCate':
		echo json_encode($sn->delete_cate($json['GroupId'], $json['CateId']));
		break;
	case 'GetCateName':
		echo json_encode($sn->get_cate_name($json['GroupId'], $json['CateId']));
		break;
	case 'UpdateOrder':
		echo json_encode($sn->update_order($json['CateId'], $json['GroupId'], $json['Option']));
		break;
	case 'NumList':
		echo json_encode($sn->num_list($json['CateId'], $json['GroupId']));
		break;
	case 'AddNum':
		echo json_encode($sn->add_num($json['Name'], $json['Category'], $json['Price'], $json['GroupId'], $json['Options']));
		break;
	case 'DeleteNum':
		echo json_encode($sn->delete_num($json['GroupId'], $json['Id']));
		break;
	case 'GiftNum'://赠送靓号
		echo json_encode($sn->giftNum($json['Data']));
		break;
	case 'NumRecord':
		echo json_encode($sn->numRecord($json['GroupId'],$json['Data'],$json['no_page']));
		break;
	case 'RecycleNum':
		echo json_encode($sn->recycleNum($json['GroupId'],$json['StockId']));
		break;
	case 'BannerImg':
		echo json_encode($sn->bannerImg($json['GroupId'],$json['ImgPath'],$json['Src'],$json['DelImg']));
		break;
	case 'GetBannerImg':
		echo json_encode($sn->getBannerImg($json['GroupId']));
		break;
	case 'UseNum'://启用靓号
		echo json_encode($sn->useNum($json['GroupId'], $json['Uin'], $json['Num']));
		break;
}