<?php
include_once dirname(__FILE__).'/library/goods_factory.class.php';

$json = $_POST['extparam'];
$param = $_POST['param'];

$gf = GoodsFactory::getInstance($json['Category']);
switch($json['Tag']){
	case 'GoodsDisplay':
		include_once dirname(__FILE__).'/library/goods_common.class.php';
		$gm = new GoodsCommon();
		$array = $gm->goodsDisplay($json['Category'],$json['GroupId'],$json['CategoryId']);
		echo json_encode($array);
		break;
		break;
	case 'Categories' : //获取单个商品详情
		include_once dirname(__FILE__).'/library/goods_common.class.php';
		$gm = new GoodsCommon();
		$state = isset($json['State']) ? intval($json['State']) : 1;
		$array = $gm->categories($json['GroupId'],$json['Category'],$state);
		echo json_encode($array);
		break;
	case 'GetCategoryByCaseId':
		include_once dirname(__FILE__).'/library/goods_common.class.php';
		$gm = new GoodsCommon();
		$array = $gm->getCategoryByCaseId($json['CaseId']);
		echo json_encode($array);
		break;
	case 'SetDefault':
		include_once dirname(__FILE__).'/library/goods_common.class.php';
		$gm = new GoodsCommon();
		$array = $gm->setDefault($json['Uin'],$json['ParentId']);
		echo json_encode($array);
		break;
	case 'GetGoodsOnCat':
		$array = $gf->getGoodsOnCat($json['GroupId'],$json['Category']);
		echo json_encode($array);
		break;
	case 'GetUserStocksOnCategory':  //个人中心
		$array = $gf->getUserStocksOnCategory($param['Uin'],$json['GroupId'],$json['Category']);
		echo json_encode($array);
		break;
	case 'GetCommodityInfo':  //公司后台商品详情
		$array = $gf->getCommodityInfo($json['CommodityId']);
		echo json_encode($array);
		break;
	case 'GetInfo' :  //站后台商品详情
		if(!isset($json['State'])){
			$json['State']=1;
		}
		$array = $gf->getInfo($json['GroupId'],$json['GoodsId'],$json['State']);
		echo json_encode($array);
		break;
	case 'Classifies':  //靓号中的小分类
		$array = $gf->classifies($json['GroupId']);
		echo json_encode($array);
		break;
	case 'GoodsOnCategory' : 
		$state = isset($json['State']) ? intval($json['State']) : 1;
		$array = $gf->goodsOnCategory($json['GroupId'],$json['CategoryId'],$state);
		echo json_encode($array);
		break;
	case 'Buy':
		$data = $json['Data'];
		if($param['GroupId'] > 0){
			$data['group_id'] = intval($param['GroupId']);
		}
		if($param['ChannelId'] > 0){
			$data['roomid'] = intval($param['ChannelId']);
		}
		if($param['Uin'] > 0 && $data['artist']>0){
			$data['uin'] = intval($param['Uin']);
		}
		$array = $gf->buy($data);
		echo json_encode($array);
		break;
	case 'NumberSaled':
		include_once dirname(__FILE__).'/library/liang.class.php';
		$l = new Liang();
		$array = $l->numberSaled($json['GroupId']);
		echo json_encode($array);
		break;
	case 'searchById':
		include_once dirname(__FILE__).'/library/liang.class.php';
		$l = new Liang();
		$array = $l->searchById($param['GroupId'],$json['Name']);
		echo json_encode($array);
		break;
	case 'GetStock':
		$array = $gf->getStock($json['Data']);
		echo json_encode($array);
		break;
	case 'GetActualUin':
		$array = $gf->getActualUin($json['GroupId'],$json['Uin']);
		echo json_encode($array);
		break;
	case 'UseProps':
		include_once dirname(__FILE__).'/library/function_card.class.php';
		$fc = new FunctionCard();
		$array = $fc->useProps($param['Uin'],$param['ParentId']);
		echo json_encode($array);
		break;
	case 'GetSingleGoodsInfo':
		$array = $gf->getSingleGoodsInfo($json['GroupId'],$json['GoodsId']);
		echo json_encode($array);
		break;
}