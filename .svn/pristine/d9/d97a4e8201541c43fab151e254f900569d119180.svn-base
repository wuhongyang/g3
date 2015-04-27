<?php
require_once 'common.php';

$module=empty($_GET['module'])?'cate':$_GET['module'];

//验证是否登陆
$user=checkDpLogin();
//拥有权限
$permisssions=getDpUserPermission($user['Uin']);
$groupId=(int)$permisssions['groupId'];
$permission=(array)$permisssions['permission'];

if(!checkGroupPermission(10587,$permission)){
	alertMsg('无权访问','group.php?module=group_info');
}


switch ($module) {
	case 'sub_cate_list':
		$param = array(
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10587,'ChildId'=>101,'Desc'=>'商品子分类列表读取'),
			'extparam' => array("Tag"=>"SubCateList","GroupId"=>$groupId,'CategoryId'=>$_GET['cate_id'])
		);
		$res = request($param);
		$cateInfo = (array)$res['CateInfo'];
		$subCateList = array();
		if($res['Flag'] == 100){
			$subCateList = $res['SubCateList'];
		}
		unset($res);
		break;
	case 'sub_cate_info':
		$param = array(
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10587,'ChildId'=>101,'Desc'=>'商品子分类详情读取'),
			'extparam' => array("Tag"=>"SubCateInfo","GroupId"=>$groupId,'Id'=>$_GET['id'])
		);
		$info = request($param);
		$info = json_encode($info);
		exit($info);
		break;
	case 'sub_cate_add':
		$_POST['group_id'] = $groupId;
		$param = array(
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10587,'ChildId'=>102,'Desc'=>'商品子分类保存'),
			'extparam' => array("Tag"=>"SubCateAdd",'Data'=>$_POST)
		);
		$rst = request($param);
		$rst = json_encode($rst);
		exit($rst);
		break;
	case 'sub_cate_edit':
		$_POST['group_id'] = $groupId;
		$param = array(
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10587,'ChildId'=>102,'Desc'=>'商品子分类保存'),
			'extparam' => array("Tag"=>"SubCateEdit",'Id'=>$_POST['id'],'Data'=>$_POST)
		);
		$rst = request($param);
		$rst = json_encode($rst);
		exit($rst);
		break;
	case 'sub_cate_move':
		$_POST['group_id'] = $groupId;
		$param = array(
			'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10587,'ChildId'=>103,'Desc'=>'商品子分类排序'),
			'extparam' => array("Tag"=>"SubCateMove",'Data'=>$_POST)
		);
		$rst = request($param);
		$rst = json_encode($rst);
		exit($rst);
		break;
	case 'cate':
		if($_POST){
			$cate_name = htmlspecialchars(addslashes(trim($_POST['cate_name'])));
			if(!$cate_name){
				ShowMsg("分类名称不能为空", -1);
			}
			
			if(mb_strlen($cate_name, "utf-8") > 5 || mb_strlen($cate_name, "utf-8") < 2){
				ShowMsg("分类名称必须在2-5个字之间", -1);
			}
			if($_POST['del_img']){
				$index = "--";
			}else{
				$index = "";
				if($_FILES['img']['tmp_name']){
					$bytes = file_get_contents($_FILES['img']['tmp_name']);
					$index = md5($bytes);
					$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
					$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
				}
			}
			$param = array(
						"param"=>array(
							'BigCaseId'	=> 10006,
							'CaseId'    	=> 10059,
							'ParentId'  	=> 10587, 
							'ChildId'   	=> 102,
							'Desc'			=> '商品分类保存'
						),
						"extparam"=>array(
							"Tag"=>"SaveCate",
							"CateName"=>$cate_name,
							"ImgSrc"=>addslashes(trim($_POST['img_src'])),
							"CateId"=>intval($_POST['cate_id']),
							"State"=>intval($_POST['state']),
							"Id"=>intval($_POST['id']),
							"GroupId"=>$groupId,
							"ImgPath"=>$index,
						)
					);
			$res = request($param);
			ShowMsg($res['FlagString'], "?module=cate");
		}
        
		$param = array(
				"param"=>array(
						'BigCaseId'	=> 10006,
						'CaseId'    	=> 10059,
						'ParentId'  	=> 10587, 
						'ChildId'   	=> 101,
						'Desc'			=> '商品分类列表读取'
				),
				"extparam"=>array(
						"Tag"=>"CateList",
						"GroupId"=>$groupId
				)
		);
		$res = request($param);
		break;
	case 'cate_detail':
		$param = array(
				"param"=>array(
						'BigCaseId'	=> 10006,
						'CaseId'    	=> 10059,
						'ParentId'  	=> 10587, 
						'ChildId'   	=> 101,
						'Desc'			=> '商品分类列表读取'
				),
				"extparam"=>array(
						"Tag"=>"CateDetail",
						"Id"=>intval($_GET['cate_id']),
						"GroupId"=>intval($groupId)
				)
		);
		$res = request($param);
		echo json_encode($res);
		exit;
	case 'cate_move':
		$param = array(
				"param"=>array(
						'BigCaseId'	=> 10006,
						'CaseId'    	=> 10059,
						'ParentId'  	=> 10587, 
						'ChildId'   	=> 103,
						'Desc'			=> '商品分类排序'
				),
				"extparam"=>array(
						"Tag"=>"CateMove",
						"CateId"=>intval($_POST['cate_id']),
						"Option"=>$_POST['option'],
						"GroupId"=>intval($groupId)
				)
		);
		$res = request($param);
		echo json_encode($res);
		exit;
	case 'goods_move':
		$param = array(
				"param"=>array(
						'BigCaseId'	=> 10006,
						'CaseId'    	=> 10059,
						'ParentId'  	=> 10587, 
						'ChildId'   	=> 107,
						'Desc'			=> '商品列表排序'
				),
				"extparam"=>array(
						"Tag"=>"GoodsMove",
						"Id"=>intval($_POST['id']),
						"Option"=>$_POST['option'],
						"GroupId"=>intval($groupId),
						"CateId"=>intval($_POST['cate_id']),
						"SubCateId"=>intval($_POST['sub_cate_id'])
				)
		);
		$res = request($param);
		echo json_encode($res);
		exit;
	case 'goods_list':
		$cate_id = intval($_GET['goods_cate_id']);
		$param = array(
			"param"=>array(
				'BigCaseId'	=> 10006,
				'CaseId'    	=> 10059,
				'ParentId'  	=> 10587, 
				'ChildId'   	=> 104,
				'Desc'			=> '商品列表读取'
			),
			"extparam"=>array(
				"Tag"=>"GoodsList",
				"GroupId"=>$groupId,
				"CateId" => intval($_GET['cate_id']),
				"SubCateId"=>intval($_GET['sub_cate_id'])
			)
		);
		$res = request($param);
		if($res['Flag'] != 100){
			ShowMsg($res['FlagString'], -1);
		}
		$cateInfo = $res['CateInfo'];
		break;
	case 'add_goods':
		if($_POST){
			$commodity_name = addslashes(htmlspecialchars($_POST['commodity_name']));
			$content = addslashes($_POST['content']);
			$len = mb_strlen($commodity_name, "utf-8");
			if(!$_POST['price']){
				ShowMsg("价格不能为空", -1);
			}
			if($len < 2 || $len > 20){
				ShowMsg("商品名称长度在2-20个字之间", -1);
			}
			$param = array(
					"param"=>array(
							'BigCaseId'	=> 10006,
							'CaseId'    	=> 10059,
							'ParentId'  	=> 10587, 
							'ChildId'   	=> 105,
							'Desc'			=> '普通商品添加'
					),
					"extparam"=>array(
							"Tag"=>"SaveGoods",
							"GroupId"=>$groupId,
							"CateId"=>intval($_GET['goods_cate_id']),
							"Data"=>array(
										"CommodityId"=>intval($_POST['commodity_id']),
										"CommodityName"=>$commodity_name,
										"Content"=>$content,
										"Duration"=>intval($_POST['duration']?$_POST['duration']:0),
										"Price"=>intval($_POST['price']),
										"State"=>intval($_POST['state']),
										"IsGift"=>intval($_POST['is_gift']),
										"GiftCateId"=>intval($_POST['gift_cate_id']),
										"GiftGoodsId"=>intval($_POST['gift_goods_id']),
										"SubCateId"=>intval($_POST['sub_cate_id'])
									)
					)
			);
			if($_POST['id']){
				$param['extparam']['Data']['Id'] = intval($_POST['id']);
			}
			$res = request($param);
			if($res['Flag'] == 100){
				ShowMsg($res['FlagString'], "?module=goods_list&cate_id={$_GET['cate_id']}&sub_cate_id={$_GET['sub_cate_id']}&goods_cate_id={$_GET['goods_cate_id']}");
			}else{
				ShowMsg($res['FlagString'], -1);
			}
		}
		if($_GET['id']){
			$param = array(
					"param"=>array(
							'BigCaseId'	=> 10006,
							'CaseId'    	=> 10059,
							'ParentId'  	=> 10587, 
							'ChildId'   	=> 104,
							'Desc'			=> '商品列表读取'
					),
					"extparam"=>array(
							"Tag"=>"GoodsDetials",
							"GroupId"=>$groupId,
							"Id"=>intval($_GET['id'])
					)
			);
			$res = request($param);
			$data = $res['Data'];

			$param = array(
				'param' => array('BigCaseId'=>10006,'CaseId'=>10059,'ParentId'=>10587,'ChildId'=>101,'Desc'=>'商品子分类列表读取'),
				'extparam' => array("Tag"=>"SubCateList","GroupId"=>$groupId,'CategoryId'=>$_GET['goods_cate_id'])
			);
			$subCateList = request($param);
			$subCateList = (array)$subCateList['SubCateList'];
		}
		
		
		$param = array(
				"param"=>array(
						'BigCaseId'	=> 10006,
						'CaseId'    	=> 10059,
						'ParentId'  	=> 10587,
						'ChildId'   	=> 104,
						'Desc'			=> '商品列表读取'
				),
				"extparam"=>array(
						"Tag"=>"AllCateGoods",
						"GroupId"=>$groupId,
						"GoodsId"=>intval($_GET['id'])
				)
		);
		$res2 = request($param);
		$gift_goods_json = json_encode($res2['Data']);
		
		$param = array(
				"param"=>array(
						'BigCaseId'	=> 10006,
						'CaseId'    	=> 10059,
						'ParentId'  	=> 10587, 
						'ChildId'   	=> 104,
						'Desc'			=> '商品列表读取'
				),
				"extparam"=>array(
						"Tag"=>"PackageGoods",
						"GroupId"=>$groupId,
						"CateId"=>intval($_GET['goods_cate_id'])
				)
		);
		$res = request($param);
		$goods_arr = array();
		foreach((array)$res['GoodsName'] as $one){
			$goods_arr[$one['id']] = $one;
		}
		$goods_json = json_encode($goods_arr);
		break;
	case 'add_package':
		
		if($_POST){
			$index = "";
			if($_FILES['img']['tmp_name']){
				$bytes = file_get_contents($_FILES['img']['tmp_name']);
				$index = md5($bytes);
				$opt = array('UPLOAD_KEY'=>OSS_UPLOAD_KEY,'Bytes'=>$bytes,'Type' =>'md5','Index'=>$index);
				$query = json_decode(socket_request(UPLOAD_API_PATH,$opt,true,600),true);
			}
			$package_name = addslashes(htmlspecialchars($_POST['package_name']));
			$content = addslashes($_POST['content']);
			$len = mb_strlen($package_name, "utf-8");
			$cate_id = array_map("intval", (array)$_POST['cate_id']);
			$commodity_id = array_map("intval", (array)$_POST['commodity_id']);
			$value = array_map("intval", (array)$_POST['value']);
			if(!$_POST['price']){
				ShowMsg("价格不能为空", -1);
			}
			if(!$_POST['original_price']){
				ShowMsg("原价不能为空", -1);
			}
			if($len < 2 || $len > 20){
				ShowMsg("商品名称长度在2-20个字之间", -1);
			}
			$param = array(
					"param"=>array(
							'BigCaseId'	=> 10006,
							'CaseId'    	=> 10059,
							'ParentId'  	=> 10587, 
							'ChildId'   	=> 106,
							'Desc'			=> '套餐商品添加'
					),
					"extparam"=>array(
							"Tag"=>"SavePackage",
							"GroupId"=>$groupId,
							"CateId"=>intval($_GET['goods_cate_id']),
							"Data"=>array(
										"Id"=>intval($_POST['id']),
										"PackageName"=>$package_name,
										"Desc"=>$content,
										"Scope"=>intval($_POST['scope']),
										"Price"=>intval($_POST['price']),
										"OriginalPrice"=>intval($_POST['original_price']),
										"State"=>intval($_POST['state']),
										"SubCateId"=>$_GET['sub_cate_id'],
										"CateId" => $cate_id,
										"CommodityId"=>$commodity_id,
										"Value"=>$value,
										"Img"=>$index
									)
					)
			);
			$res = request($param);
			if($res['Flag'] == 100){
				ShowMsg($res['FlagString'], "?module=goods_list&cate_id={$_GET['cate_id']}&sub_cate_id={$_GET['sub_cate_id']}&goods_cate_id={$_GET['goods_cate_id']}");
			}else{
				ShowMsg($res['FlagString'], -1);
			}
		}
		$edit_json = json_encode(array());
		if($_GET['id']){
			$param = array(
					"param"=>array(
							'BigCaseId'	=> 10006,
							'CaseId'    	=> 10059,
							'ParentId'  	=> 10587, 
							'ChildId'   	=> 104,
							'Desc'			=> '商品列表读取'
					),
					"extparam"=>array(
							"Tag"=>"PackageDetail",
							"GroupId"=>$groupId,
							"Id"=>intval($_GET['id'])
					)
			);
			$res = request($param);
			$data = $res['Data'];
			$edit_json = $data['goods'];
		}
		
		$param = array(
				"param"=>array(
						'BigCaseId'	=> 10006,
						'CaseId'    	=> 10059,
						'ParentId'  	=> 10587, 
						'ChildId'   	=> 104,
						'Desc'			=> '商品列表读取'
				),
				"extparam"=>array(
						"Tag"=>"ScopeGoods",
						"GroupId"=>$groupId
				)
		);
		$res = request($param);
		$goods_json = json_encode($res['Data']);
		break;
}

$tpl=template::getInstance();
$tpl->setOptions(get_config('template','group'));
include template('goods/'.$module.'.html',$tpl);