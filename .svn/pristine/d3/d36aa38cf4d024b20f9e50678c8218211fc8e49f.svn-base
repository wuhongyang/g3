<?php
require_once 'library/regions.class.php';
require_once 'library/interact_manage.class.php';
require_once 'library/props_manage.class.php';
require_once 'library/site.class.php';
require_once 'library/site_category.class.php';
require_once 'library/dialect.class.php';
require_once 'library/outstation_ad.class.php';
require_once 'library/game_props.class.php';
require_once 'library/function_props.class.php';

$region = new Regions();
$interact_manage = new interact_manage();
$props_manage = new props_manage();
$site_category = new site_category();
$site = new site();
$dialect = new dialect();
$outstation_ad = new outstation_ad();
$game_props = new GameProps();
$function_props = new FunctionProps();

$json = $_POST['extparam'];

switch($json['Tag']){
	case 'Address':
		echo json_encode($region->getCity($json['Ip']));
		break;
	case 'GetOpenCity':
		echo json_encode($region->getOpenCity($json['Status']));
		break;
	// case 'GetSiteList':
		// echo json_encode($region->getSiteList());
		// break;
	// case 'GetRoomsCase':
		// echo json_encode($region->getRoomsCase($json['RegionId']));
		// break;
// 	case 'SetCurUser':
// 		echo json_encode($region->SetCurUser($json['CaseId'],$json['CurUser']));
// 		break;
	// case 'GetHotSites':
		// echo json_encode($region->getHotSites());
		// break;
	// case 'GetSite':
		// echo json_encode($region->getSite($json['Domain']));
		// break;
// 	case 'GetSites':
// 		echo json_encode($region->getSites());
// 		break;
	// case 'PositionSite':
		// echo json_encode($region->positionSite($json['Ip']));
		// break;
	case 'GetParentId':
		echo json_encode($region->getParentId($json['SortId']));
		break;
	case 'GetAllClassify':
		echo json_encode($region->getAllClassify());
		break;
	case 'GetProvinceName':
		echo json_encode($region->getProvinceName($json['ProvinceId']));
		break;
	case 'GetCityName':
		echo json_encode($region->getCityName($json['CityId']));
		break;
	case 'GetAreaName':
		echo json_encode($region->getAreaName($json['AreaId']));
		break;
	case 'GetAllProvince':
		echo json_encode($region->getAllProvince());
		break;
	case 'GetCities':
		echo json_encode($region->getCities($json['ProvinceId']));
		break;
// 	case 'GetCycleImg':
// 		echo json_encode($region->getCycleImg($json['RegionId']));
// 		break;
	//游戏
	case 'GetInteractGropuConfig':
		echo json_encode($interact_manage->getInteractGropuConfig($json['Interact'],$json['Groupid']));
		break;
	case 'SetInteractGropuConfig':
		echo json_encode($interact_manage->setInteractGropuConfig($json['Interact'],$json['Groupid'],$json['Robot']));
		break;
	case 'InteractList':
		$id = $json['Id'];
		echo json_encode($interact_manage->interact_list($json['Data'],$id));
		break;
	case 'InteractAdd': 
		echo json_encode($interact_manage->interact_add($json));
		break;
	case 'InteractUpdate':
		echo json_encode($interact_manage->interact_update($json));
		break;
	case 'InteractDel':
		$id = $json['Id'];
		echo json_encode($interact_manage->interact_del($id));
		break;
	case 'InteractOrder':
		$id = $json['Id'];
		$type = trim($json['Type']);
		echo json_encode($interact_manage->interact_order($id,$type));
		break;
	case 'getOPenCity':
		echo json_encode($interact_manage->getOPenCity());
		break;
	case 'InteractConfigList':
		echo json_encode($interact_manage->interactConfigList($json['Cmd']));
		break;
	case 'InteractConfigSave':
		echo json_encode($interact_manage->interactConfigSave($json['Key'],$json['Value'],$json['Descr']));
		break;
	case 'GetInfoByCmd':
		echo json_encode($interact_manage->GetInfoByCmd($json['Cmd']));
		break;
	case 'GetGameInfo':
		echo json_encode($interact_manage->getGameInfo($json['ParentId']));
		break;
	case 'interactConfig':
		echo json_encode($interact_manage->interactConfig($json['Name'],$json['Id']));
		break;
	//礼物
	case 'SaveGiftCate':
		echo json_encode($props_manage->save_gift_cate($json['Name'],$json['Status'],$json['Id'],$json['TplId']));
		break;
	case 'GetGiftCate':
		echo json_encode($props_manage->gift_cate_list($json['Id'],$json['NoPage'],$json['TplId'],$json['IsTricky']));
		break;
    case 'MoveCate':
        echo json_encode($props_manage->gift_cate_move($json['Id'],$json['Direct']));
		break;
	case 'PropsList':
		$id = $json['Id'];
		echo json_encode($props_manage->props_list($json['Data'],$id));
		break;
	case 'PropsAdd':
		echo json_encode($props_manage->props_add($json));
		break;
	case 'PropsUpdate':
		echo json_encode($props_manage->props_update($json));
		break;
	case 'PropsDel':
		$id = $json['Id'];
		echo json_encode($props_manage->props_del($id));
		break;
	case 'PropsOrder':
		echo json_encode($props_manage->props_order($json['Id'],$json['Type']));
		break;
	//分站游戏道具
	case 'GamePropsList':
		$id = $json['Id'];
		echo json_encode($game_props->game_props_list($json['Data'],$id));
		break;
	case 'GamePropsAdd':
		echo json_encode($game_props->game_props_add($json));
		break;
	case 'GamePropsUpdate':
		echo json_encode($game_props->game_props_update($json));
		break;
	case 'GamePropsDel':
		$id = $json['Id'];
		echo json_encode($game_props->game_props_del($id));
		break;
	case 'GamePropsOrder':
		echo json_encode($game_props->game_props_order($json['Id'],$json['Type']));
		break;
	case 'GamePropsConfig':
		echo json_encode($game_props->game_props_config($json['Cmd']));
		break;
	case 'GamePropsConfigModify':
		echo json_encode($game_props->gamePropsConfigModify($json['Data']));
		break;
	case 'GameMoneyBindConfig':
		echo json_encode($game_props->gameMoneyBindConfig($json['Cmd']));
		break;
	case 'GameMoneyBindConfigSet':
		echo json_encode($game_props->gameMoneyBindConfigSet($json['Cmd'],$json['Funduin']));
		break;
	case 'GamePropsInfoByCmd':
		echo json_encode($game_props->gamePropsInfoByCmd($json['Cmd']));
		break;
	//分站功能道具
	case 'FunctionPropsList':
		$id = $json['Id'];
		echo json_encode($function_props->function_props_list($json['Data'],$id));
		break;
	case 'FunctionPropsAdd':
		echo json_encode($function_props->function_props_add($json));
		break;
	case 'FunctionPropsUpdate':
		echo json_encode($function_props->function_props_update($json));
		break;
	case 'FunctionPropsDel':
		$id = $json['Id'];
		echo json_encode($function_props->function_props_del($id));
		break;
	case 'FunctionPropsOrder':
		echo json_encode($function_props->function_props_order($json['Id'],$json['Type']));
		break;
	//分站
	case 'SiteList':
		echo json_encode($site->siteList($json['SearchData']));
		break;
	case 'GetInfo':
		echo json_encode($site->getInfo($json['Id']));
		break;
	case 'SiteAdd':
		echo json_encode($site->siteAdd($json['Data']));
		break;
	case 'SiteUpdate':
		echo json_encode($site->siteUpdate($json['Data'],$json['Id']));
		break;
	case 'SetHot':
		echo json_encode($site->setHot($json['Id']));
		break;
	case 'GetNameByRegion':
		echo json_encode($site->getNameByRegion($json['RegionId']));
		break;
	//分站类别
	case 'SiteCategoryCaseList':
		echo json_encode($site_category->siteCategoryCaseList($json['SearchData']));
		break;
	case 'GetSiteCategoryInfo':
		echo json_encode($site_category->getInfo($json['Id']));
		break;
	case 'SaveCaseUpdate':
		echo json_encode($site_category->saveCaseUpdate($json['Data'],$json['Id']));
		break;
	case 'SaveCaseAdd':
		echo json_encode($site_category->saveCaseAdd($json['Data']));
		break;
	case 'ShowSubSiteCategory':
		echo json_encode($site_category->showSubSiteCategory($json['Id']));
		break;
	case 'CaseUp':
		echo json_encode($site_category->caseUp($json['Id']));
		break;
	case 'CaseDown':
		echo json_encode($site_category->caseDown($json['Id']));
		break;
	case 'CaseTop':
		echo json_encode($site_category->caseTop($json['Id']));
		break;
	case 'SubCategoryInfo':
		echo json_encode($site_category->getSubCategoryInfo($json['CaseId'],$json['Id']));
		break;
	case 'SubCategoryUpdate':
		echo json_encode($site_category->subCategoryUpdate($json['Info'],$json['Id']));
		break;
	case 'SubCategoryAdd':
		echo json_encode($site_category->subCategoryAdd($json['Info']));
		break;
	case 'SubCategoryUp':
		echo json_encode($site_category->subCategoryUp($json['Id'],$json['ParentId']));
		break;
	case 'SubCategoryDown':
		echo json_encode($site_category->subCategoryDown($json['Id'],$json['ParentId']));
		break;
	case 'SubCategoryTop':
		echo json_encode($site_category->subCategoryTop($json['Id'],$json['ParentId']));
		break;
	//分站方言
	case 'DialectList':
		echo json_encode($dialect->getDialectList($json['SearchData']));
		break;
	case 'DialectInfo':
		echo json_encode($dialect->getDialectInfo($json['Id']));
		break;
	case 'DialectAdd':
		echo json_encode($dialect->dialectAdd($json['Data']));
		break;
	case 'DialectUpdate':
		echo json_encode($dialect->dialectUpdate($json['Data'],$json['Id']));
		break;
	//分站广告轮播
	// case 'AdList':
		// echo json_encode($outstation_ad->getAdList($json['SearchData']));
		// break;
	// case 'AdInfo':
		// echo json_encode($outstation_ad->getAdInfo($json['id']));
		// break;
	// case 'AdImg':
		// echo json_encode($outstation_ad->getAdImg($json['RegionId']));
		// break;
	// case 'AdAdjust':
		// echo json_encode($outstation_ad->adjust($json['args']));
		// break;
	default:
		echo json_encode(array('Flag'=>101,'FlagString'=>"region_api '{$json['Tag']}' 404"));
		break;
}
