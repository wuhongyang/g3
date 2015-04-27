<?php
require_once '../library/global.fun.php';

$module = isset($_GET['module']) ? $_GET['module'] : 'list';

switch ($module) {
	case 'list':
		$param = array(
            'extparam' => array('Tag'=>'List'),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10009,'ChildId'=>101,'Desc'=>'商品类别列表')
        );
        $list = request($param);
        $list = $list['List'];
		$template = 'cate_list';
		break;

	case 'info':
        if(isset($_GET['id'])){
            $param = array(
                'extparam' => array('Tag'=>'Info','Id'=>$_GET['id']),
                'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10009,'ChildId'=>101,'Desc'=>'商品类别详情')
            );
            $info = request($param);
            $info = $info['Info'];
        }
		
		$template = 'cate_info';
		break;

	case 'add':
		$param = array(
            'extparam' => array('Tag'=>'Add','Data'=>$_POST),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10009,'ChildId'=>102,'Desc'=>'添加商品类别')
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
            'extparam' => array('Tag'=>'Edit','Id'=>$_POST['id'],'Data'=>$_POST),
            'param' => array('BigCaseId'=>10002,'CaseId'=>10005,'ParentId'=>10009,'ChildId'=>102,'Desc'=>'编辑商品类别')
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

$link_array = getLevellink(10002,10005,10009,101);
$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('shop/'.$template.'.html',$tpl);