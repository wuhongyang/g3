<?php
require_once '../library/global.fun.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'list';

switch ($module) {
	case 'list':
		$param = array(
            'extparam' => array('Tag'=>'CommodityList', 'Search'=>array('tpl_id'=>intval($_GET['tpl_id']))),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>140,'Desc'=>'添加商品')
        );
        $list = request($param);
        $page = $list['Page'];
        $list = (array)$list['List'];
		$template = 'commodity_list';
		break;

	case 'info':
		//编辑的时候得到信息
		if(isset($_GET['id'])){
			$param = array(
	            'extparam' => array('Tag'=>'CommodityInfo','Id'=>$_GET['id']),
	            'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>140,'Desc'=>'商品详情')
	        );
	        $edit = request($param);
	        if($edit['Flag'] != 100){
	        	alertMsg('非法链接',-1);
	        }
	        $edit = $edit['Info'];
	        $image = json_decode($edit['image'], true);
	        $edit['pic_category'] = $image[0];
	        $edit['pic'] = $image[1];
	        $swf = json_decode($edit['flash'], true);
	        $edit['swf_category'] = $swf[0];
	        $edit['swf'] = $swf[1];
	        $room_image = json_decode($edit['room_image'], true);
	        $edit['room_image_category'] = $room_image[0];
	        $edit['room_image'] = $room_image[1];
	        unset($image,$swf);
		}

		//得到商品类别
		$param = array(
            'extparam' => array('Tag'=>'List'),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10009,'ChildId'=>101,'Desc'=>'商品类别列表')
        );
        $commodity_category = request($param);
        $commodity_category = $commodity_category['List'];

        //得到角色
        $search = $_GET['search'];
        $search['tpl_id'] = $_GET['tpl_id'];
        $param = array(
			'param'=>array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>138,"Desc"=>"角色管理列表"),
			'extparam'=>array("Tag"=>"RoleList","SearchData" => $search,'IsNotPage'=>true)
		);
		$result = request($param);
		$roleList = (array)$result['RoleList'];

		//得到图片分类
		$cat = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'CatList','mypost'=>array('state'=>1))));
		$cat = json_encode($cat['lists']);

		//得到图片
		$pic = httpPOST(GRIDFS_API_PATH,array("extparam"=>array('Tag'=>'PicList','mypost'=>array('state'=>1))));
		$pic = json_encode($pic['lists']);

		$template = 'commodity_info';
		break;

	case 'add':
		$param = array(
            'extparam' => array('Tag'=>'CommodityAdd','Data'=>$_POST),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>143,'Desc'=>'添加商品')
        );
        $rst = request($param);
        if($rst['Flag'] == 100){
        	alertMsg($rst['FlagString'],'?module=list&tpl_id='.$_POST['tpl_id']);
        }else{
        	alertMsg($rst['FlagString'],-1);
        }
		break;

	case 'edit':
		$param = array(
            'extparam' => array('Tag'=>'CommodityEdit','Data'=>$_POST,'Id'=>$_POST['id']),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>143,'Desc'=>'编辑商品')
        );
        $rst = request($param);
        if($rst['Flag'] == 100){
        	alertMsg($rst['FlagString'],'?module=list&tpl_id='.$_POST['tpl_id']);
        }else{
        	alertMsg($rst['FlagString'],-1);
        }
		break;

	case 'get_by_category':
        $param = array(
            'extparam' => array('Tag'=>'CommodityGetByCategory','Category'=>$_GET['category']),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10648,'ChildId'=>140,'Desc'=>'商品类别列表')
        );
        $list = request($param);
        echo json_encode((array)$list['Commodity']);
        exit;
        break;
	
	default:
		# code...
		break;
}

$link_array = getLevellink(10002,10069,10648,101);
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('shop/'.$template.'.html',$tpl);