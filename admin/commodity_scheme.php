<?php
require_once '../library/global.fun.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'list';

switch ($module) {
	case 'list':
		$param = array(
            'extparam' => array('Tag'=>'List'),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10190,'ChildId'=>101,'Desc'=>'商品方案列表')
        );
        $list = request($param);
        $list = $list['List'];
		$template = 'scheme_list';
		break;

	case 'info':
		if(isset($_GET['id'])){
			$param = array(
	            'extparam' => array('Tag'=>'Info', 'Id'=>$_GET['id']),
	            'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10190,'ChildId'=>101,'Desc'=>'商品方案详情')
	        );
	        $info = request($param);
	        $info = $info['Info'];
	        $commodities = json_decode($info['content'], true);
	        foreach ($commodities as $key => $commodity) {
	        	foreach ($commodity as $k => $val) {
	        		//得到商品名称
		        	$param = array(
			            'extparam' => array('Tag'=>'CommodityInfo','Id'=>$val),
			            'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10017,'ChildId'=>101,'Desc'=>'添加商品')
			        );
			        $commodityInfo = request($param);
			        $commodities[$key][$k] = array('commodity_id'=>$val,'commodity_name'=>$commodityInfo['Info']['name']);
	        	}
	        }
	        unset($info['content']);
		}
		//商品分类
		$param = array(
            'extparam' => array('Tag'=>'List','Data'=>array('status'=>1)),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10009,'ChildId'=>101,'Desc'=>'商品类别列表')
        );
        $commodity_category = request($param);
        $commodity_category = (array)$commodity_category['List'];
        
		$template = 'scheme_info';
		break;

	case 'add':
		$param = array(
            'extparam' => array('Tag'=>'Add', 'Data'=>$_POST),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10190,'ChildId'=>102,'Desc'=>'商品方案添加')
        );
        $rst = request($param);
        if($rst['Flag'] == 100){
        	alertMsg($rst['FlagString'],'?module=list');
        }else{
        	alertMsg($rst['FlagString'],-1);
        }
		break;

	case 'edit':
		$param = array(
            'extparam' => array('Tag'=>'Edit', 'Data'=>$_POST, 'Id'=>$_POST['id']),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10190,'ChildId'=>102,'Desc'=>'商品方案编辑')
        );
        $rst = request($param);
        if($rst['Flag'] == 100){
        	alertMsg($rst['FlagString'],'?module=list');
        }else{
        	alertMsg($rst['FlagString'],-1);
        }
		break;
	
	default:
		# code...
		break;
}

$link_array = getLevellink(10002,10005,10190,101);
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('shop/'.$template.'.html',$tpl);