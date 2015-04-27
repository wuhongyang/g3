<?php
require_once 'common.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'show';
if($module=='show'&&$categoryInfo['cate_id']==4){
	$module='number';
}
//站的金钱数
$money = get_money($user['Uin'],$GroupData['groupid']);
//站信息
$scheme_id = intval($GroupData['scheme_id']);//商城方案
$unit = $GroupData['currency_unit'];//站货币单位
$room_id = intval($_GET['room_id']);

switch ($module) {
	case 'info':
		//获取站商品详情
		$goodsId=intval($_GET['goods_id']);
		$param=array(
			'extparam'=>array('Tag'=>'GetInfo','GroupId'=>$groupId,'GoodsId'=>$goodsId,'Category'=>$categoryInfo['cate_id']),
			'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品详情')
		);
		$goodsInfo=request($param);
		if($goodsInfo['Flag']!=100){ //商品不存在
			alertMsg('商品不存在或已下线',-1);
		}
		$goodsInfo=$goodsInfo['Info'];
		if($goodsInfo['commodity_id']){
			//获取后台商品详情
			$param = array(
				'extparam'=>array('Tag'=>'GetCommodityInfo','CommodityId'=>$goodsInfo['commodity_id']),
				'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品详情')
			);
			$commodityInfo=request($param);
			if($commodityInfo['Flag']!=100){ //商品不存在
				alertMsg('商品不存在或已下线',-1);
			}
			$commodityInfo=$commodityInfo['CommodityInfo'];
			if($commodityInfo['scope'] == 2){ //作用域为房间
				if($room_id < 1){
					alertMsg('该商品需要对应房间号，请去房间点击够买');
				}
				//站下房间
				$param=array(
					'extparam'=>array('Tag'=>'GetGroupRooms','GroupId'=>$groupId),
					'param'=>array('BigCaseId'=>10006,'CaseId'=>10033,'ParentId'=>10260,'ChildId'=>101,'Desc'=>'获取站下房间')
				);		
				$groupRooms=request($param);
				$groupRooms = (array)$groupRooms['roomList'];
				$rooms = array();
				foreach ($groupRooms as $key => $val) {
					if($val['status'] <= 0){
						unset($groupRooms[$key]);
						continue;
					}
					$rooms[$val['id']] = $val['name'];
				}
				unset($groupRooms);
				if($room_id > 0){
					if(!in_array($room_id, (array)array_keys($rooms))){
						alertMsg('房间ID错误');
					}
				}
			}
		}
		//如果有赠品
		if($goodsInfo['is_gift']){
			$giftInfo=json_decode($goodsInfo['gift_detail'],true);
			$param=array(
				'extparam'=>array('Tag'=>'GetInfo','GroupId'=>$groupId,'GoodsId'=>$giftInfo['gift_goods_id'],'State'=>0),
				'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品详情')
			);
			$giftInfo=request($param);
			if($giftInfo['Flag']!=100){ //赠品不存在
				alertMsg('赠品不存在或已下线',-1);
			}
			$giftInfo=$giftInfo['Info'];
		}
		$temp = 'info';
		break;

	case 'buy':
		
		//获取站商品详情
		$goodsId=intval($_GET['goods_id']);
		$uin=(isset($_POST['uin'])&&$_POST['uin']>0)?$_POST['uin']:$user['Uin'];
		$num=intval($_POST['num'])>0?intval($_POST['num']):1;
		if($goodsId<0||!$categoryInfo){
			$rst=array(
				'Flag'=>101,
				'FlagSrting'=>'商品不存在或已下线'
			);
			echo json_encode($rst);
			exit;
		}
		
		$param=array(
			'extparam'=>array('Tag'=>'GetInfo','GroupId'=>$groupId,'GoodsId'=>$goodsId,'Category'=>$categoryInfo['cate_id']),
			'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品详情')
		);
		$goodsInfo=request($param);
		if($goodsInfo['Flag']!=100){ //商品不存在
			$rst=array(
				'Flag'=>101,
				'FlagSrting'=>'商品不存在或已下线'
			);
			echo json_encode($rst);
			exit;
		}
		$goodsInfo=$goodsInfo['Info'];
		//普通商品
		if($goodsInfo['commodity_id']){
			//获取后台商品详情
			$param = array(
				'extparam'=>array('Tag'=>'GetCommodityInfo','CommodityId'=>$goodsInfo['commodity_id']),
				'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品详情')
			);
			$commodityInfo=request($param);
			if($commodityInfo['Flag']!=100){ //商品不存在
				$rst=array(
					'Flag'=>101,
					'FlagSrting'=>'商品不存在或已下线'
				);
				echo json_encode($rst);
				exit;
			}
			$commodityInfo=$commodityInfo['CommodityInfo'];
			$data=array(
				'goods_id'=>$goodsId,
				'uin'=>$uin,
				'pay_uin'=>$user['Uin'],
				'group_id'=>$groupId,
				'roomid'=>intval($_POST['roomid']),
				'num'=>$num
			);
			$param = array(
				'extparam'=>array('Tag'=>'Buy','Data'=>$data,'Category'=>$categoryInfo['cate_id']),
				'param'=>array('BigCaseId'=>$commodityInfo['bigcase_id'],'CaseId'=>$commodityInfo['case_id'],'ParentId'=>$commodityInfo['parent_id'],'ChildId'=>101,'GroupId'=>$groupId,'Uin'=>$user['Uin'],'TargetUin'=>$uin,'Desc'=>$user['Uin'].'在商城购买'.$num.'个'.$goodsInfo['commodity_name'].'给'.$uin.'总金额'.$goodsInfo['price']*$num,'ChannelId'=>1)
			);
		}
		//靓号
		elseif($categoryInfo['cate_id']==4){
			$data=array(
				'goods_id'=>$goodsId,
				'uin'=>$uin,
				'pay_uin'=>$user['Uin'],
				'group_id'=>$groupId,
				'roomid'=>0,
				'num'=>0
			);
			$param = array(
				'extparam'=>array('Tag'=>'Buy','Data'=>$data,'Category'=>$categoryInfo['cate_id']),
				'param'=>array('BigCaseId'=>$goodsInfo['bigcase_id'],'CaseId'=>$goodsInfo['case_id'],'ParentId'=>$goodsInfo['parent_id'],'ChildId'=>101,'GroupId'=>$groupId,'Uin'=>$user['Uin'],'TargetUin'=>$uin,'Desc'=>$user['Uin'].'在商城购买靓号'.$goodsInfo['name'].'给'.$uin.'总金额'.$goodsInfo['price'],'ChannelId'=>1)
			);
		}
		//套餐包
		else{
			$data=array(
				'package_id'=>$goodsId,
				'uin'=>$uin,
				'pay_uin'=>$user['Uin'],
				'group_id'=>$groupId,
				'roomid'=>intval($_POST['roomid']),
				'num'=>$num
			);
			$param = array(
				'extparam'=>array('Tag'=>'Buy','Data'=>$data,'Category'=>$categoryInfo['cate_id']),
				'param'=>array('BigCaseId'=>10005,'CaseId'=>10065,'ParentId'=>10586,'ChildId'=>101,'GroupId'=>$groupId,'Uin'=>$user['Uin'],'TargetUin'=>$uin,'Desc'=>$user['Uin'].'在商城购买'.$num.'个'.$goodsInfo['package_name'].'给'.$uin.'总金额'.$goodsInfo['price']*$num,'ChannelId'=>1)
			);
		}
		
		$rst=request($param);
		$rst['GoodsId']=$goodsId;
		$rst['Uin']=$uin;
		$rst['Num']=$num;
		$rst['Money']=$goodsInfo['price']*$num;
		$rst['Duration']=$goodsInfo['duration']*$num;
		exit(json_encode($rst));
		break;

	case 'success': 
		$info=json_decode($_GET['info'], true);
		$goodsId=$info['GoodsId'];
		//得到昵称
		$row = httpPOST(GROUP_SHOP_API_PATH,array('extparam'=>array('Tag'=>'GetActualUin','Uin'=>$info['Uin'],'GroupId'=>$groupId)));
		$param=array('extparam'=>array('Tag'=>'GetUserInfo','Uin'=>$row['Uin']));
		$result=httpPOST(KBASIC_API_PATH,$param);
		$nick=$result['Flag']==100?$result['Nick']:$row['Uin'];
		//获取站商品详情
		$param=array(
			'extparam'=>array('Tag'=>'GetInfo','GroupId'=>$groupId,'GoodsId'=>$goodsId,'Category'=>$categoryInfo['cate_id']),
			'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品详情')
		);
		$goodsInfo=request($param);
		if($goodsInfo['Flag']!=100){ //商品不存在
			alertMsg('商品不存在或已下线',-1);
		}
		$goodsInfo=$goodsInfo['Info'];
		$temp='success';
		break;

	case 'number':
		if($_POST['action']&&$_POST['keywords']!=''){
			//靓号列表
			$param=array(
				'extparam'=>array('Tag'=>'searchById','Name'=>addslashes($_POST['keywords'])),
				'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'GroupId'=>$groupId,'Desc'=>'获取靓号列表')
			);
			$numberStatus=request($param);
			if($numberStatus['Flag']!=100){
				alertMsg('查询失败',-1);
			}
			
			$serviceQq=$GroupData['EXT']['qq']['value'];
			$temp='number_search';
		}
		else{
			//靓号分类
			$param=array(
				'extparam'=>array('Tag'=>'Classifies','GroupId'=>$groupId,'Category'=>$categoryInfo['cate_id']),
				'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取靓号分类')
			);
			$numberCategory=request($param);
			$numberCategory=$numberCategory['Categories'];
			if(empty($numberCategory)){
				alertMsg('商品不存在或已下线',-1);
			}
			$numCatId=intval($_GET['num_cat_id']);
			if(empty($numCatId)){
				$numCatId=$numberCategory[0]['cate_id'];
			}
			
			//靓号列表
			$param=array(
				'extparam'=>array('Tag'=>'GoodsOnCategory','GroupId'=>$groupId,'CategoryId'=>$numCatId,'Category'=>$categoryInfo['cate_id']),
				'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取靓号列表')
			);
			$numberList=request($param);
			$numberList=$numberList['Goods'];
			
			$temp='number';
		}
		
		//靓号banner
		$param=array(
			'extparam'=>array('Tag'=>'GetBannerImg','GroupId'=>$groupId),
			'param'=>array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10429,'ChildId'=>101,'Desc'=>'商品宣传图片读取')
		);
		$result=request($param);
		$categoryInfo['img_path']=$result['Data']['img_path'];
		$categoryInfo['img_src']=$result['Data']['src'];
		
		//已销售的靓号
		$param=array(
			'extparam'=>array('Tag'=>'NumberSaled','GroupId'=>$groupId,'Category'=>$categoryInfo['cate_id']),
			'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取已售靓号列表')
		);
		$numberSaleListTmp=request($param);
		$numberSaleListTmp=$numberSaleListTmp['Sale'];
		$numberSaleList=array();
		if($numberSaleListTmp){
			foreach($numberSaleListTmp as $val){
				$numberSaleList[$val]=$val;
			}
		}
		unset($numberSaleListTmp);
		break;

	case 'show':

	default:
		if(!$categoryInfo){
			alertMsg('商品不存在或已下线',-1);
		}
		//商品列表
		$param=array(
			'extparam'=>array('Tag'=>'GoodsDisplay','GroupId'=>$groupId,'CategoryId'=>$categoryInfo['id'],'Category'=>$categoryInfo['cate_id']),
			'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品列表')
		);
		$allGoods = request($param);
		$allGoods = (array)$allGoods['Goods'];
		//商品列表
		//$param=array(
		//	'extparam'=>array('Tag'=>'GoodsOnCategory','GroupId'=>$groupId,'CategoryId'=>$categoryInfo['id'],'Category'=>$categoryInfo['cate_id']),
		//	'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品列表')
		//);
		//$goodsList=request($param);
		//$goodsList=$goodsList['Goods'];
		//if($goodsList){
		foreach ($allGoods as $k => $good) {
			$goodsList = (array)$good['goods'];
			foreach($goodsList as $key=>$val){
				if($val['commodity_id']){
					$param=array(
						'extparam'=>array('Tag'=>'GetCommodityInfo','CommodityId'=>$val['commodity_id']),
						'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品详情')
					);
					$commodityInfo=request($param);
					//$goodsList[$key]['commodityInfo']=$commodityInfo['CommodityInfo'];
					$allGoods[$k]['goods'][$key]['commodityInfo'] = $commodityInfo['CommodityInfo'];
				}
				else{
					$goods=json_decode($val['goods'],true);
					foreach($goods as $key2=>$val2){
						//获取站商品详情
						$param=array(
							'extparam'=>array('Tag'=>'GetSingleGoodsInfo','GroupId'=>$groupId,'GoodsId'=>$val2['commodity_id'],'Category'=>$categoryInfo['cate_id']),
							'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品详情')
						);
						$goodsInfo=request($param);
						$goods[$key2]['info']=$goodsInfo['Info'];
						//获取后台商品详情
						$param=array(
							'extparam'=>array('Tag'=>'GetCommodityInfo','CommodityId'=>$goodsInfo['Info']['commodity_id']),
							'param'=>array('BigCaseId'=>10005,'CaseId'=>10057,'ParentId'=>10427,'ChildId'=>101,'Desc'=>'获取商品详情')
						);
						$commodityInfo=request($param);
						$goods[$key2]['commodityInfo']=$commodityInfo['CommodityInfo'];
						if($commodityInfo['CommodityInfo']['scope']==2){
							$goodsList[$key]['in_room']=1;
						}
					}
					//$goodsList[$key]['goods']=$goods;
					$allGoods[$k]['goods'][$key]['goods'] = $goods;
				}
			}
		}

		$goodsCategories = array(-1 => 'package', 1 => 'car', 2 => 'vip', 3 => 'prop', 5 => 'guard', 6 => 'aristocracy');

		if($categoryInfo['cate_id'] == 5){
			alertMsg('请前往房间购买守护',-1);
		}elseif($categoryInfo['cate_id'] == 6){
			$list = (array)$allGoods[0]['goods'];
			$list = array_slice($list, 0, 6);
			$aristCount = count($list);
		}
		$temp = $goodsCategories[$categoryInfo['cate_id']];
		/*
		if($categoryInfo['cate_id']==1){
			$temp='car';
		}
		elseif($categoryInfo['cate_id']==2){
			$temp='vip';
		}
		elseif($categoryInfo['cate_id']==3){
			$temp='prop';
		}
		elseif($categoryInfo['cate_id']==5){
			alertMsg('请前往房间购买守护',-1);
			foreach($goodsList as $key=>$val){
				$goodsList[$val['commodityInfo']['parent_id']]=$val;
				unset($goodsList[$key]);
			}
			$temp='guard';
		}
		elseif($categoryInfo['cate_id']==-1){
			$temp='package';
		}*/
		break;
}
$tmp_config=get_config('template','group_site');
$tmp_config['template_dir'].=$themes.'/tpl/shop/';
$tmp_config['cache_dir'].=$themes.'/tpl/shop/';
$tpl = template::getInstance();
$tpl->setOptions($tmp_config);
include template('group_shop/'.$temp.'.html',$tpl);
