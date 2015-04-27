<?php
require_once '../library/global.fun.php';
$module = $_GET['module'];
$link_array = getLevellink(10002,10040,10224,101);
if($module == 'category'){
	$param = array(
		'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10224,'ChildId'=>101,'Desc'=>'帮助分类'),
		'extparam' => array('Tag'=>'ClassifyList','Data'=>array('Status'=>-1))
	);
	$result = request($param);
	$lists = $result['Data'];
	$tpl = 'help/category.html';

}elseif($module == 'category_edit'){
	$id = (int)$_GET['id'];
	if(isset($_POST['category']) && isset($_POST['type'])){
		$childid = $id > 0? 103 : 102;
		$param = array(
			'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10224,'ChildId'=>$childid,'Desc'=>'保存帮助分类'),
			'extparam' => array('Tag'=>'SaveClassify','Data'=>array('Id'=>$id,'Type'=>$_POST['type'],'Name'=>$_POST['category'],'Status'=>$_POST['status']))
		);
		$result = request($param);
		if($result['Flag'] == 100){
			alertMsg($result['FlagString'],'?module=category');
		}else{
			alertMsg($result['FlagString']);
		}
	}
	$edit = array();
	if($id > 0){
		$param = array(
			'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10224,'ChildId'=>101,'Desc'=>'帮助分类'),
			'extparam' => array('Tag'=>'ClassifyList','Data'=>array('Id'=>$id,'Status'=>-1))
		);
		$edit = request($param);
		$edit = $edit['Data'][0];
	}
	$category_type = array('请选择','帮助中心','官方公告','游戏公告');
	$tpl = 'help/category-edit.html';

}elseif($module == 'category_del'){
	$id = (int)$_GET['id'];
	if($id > 0){
		$param = array(
			'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10224,'ChildId'=>104,'Desc'=>'删除帮助分类'),
			'extparam' => array('Tag'=>'DelClassify','Id'=>$id)
		);
		$result = request($param);
		alertMsg($result['FlagString'],'?module=category');
	}else{
		alertMsg('不存在的记录');
	}

}elseif($module == 'article'){
	$cid = (int)$_GET['id'];
	$param = array(
		'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10224,'ChildId'=>105,'Desc'=>'帮助内容'),
		'extparam' => array('Tag'=>'SubstanceList','Data'=>array('ClassifyId'=>$cid))
	);
	$result = request($param);
	$lists = $result['Data'];
	$tpl = 'help/article.html';

}elseif($module == 'article_edit'){
	//添加或修改帮助内容
	$id = (int)$_GET['id'];
	if(!empty($_POST['title']) && !empty($_POST['content']) && $_POST['cid'] > 0){
		$childid = $id > 0? 107 : 106;
		$param = array(
			'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10224,'ChildId'=>$childid,'Desc'=>'保存帮助内容'),
			'extparam' => array('Tag'=>'SaveSubstance','Data'=>array('Id'=>$id,'Title'=>$_POST['title'],'Content'=>$_POST['content'],'ClassifyId'=>$_POST['cid'],'Top'=>$_POST['top']))
		);
		$result = request($param);
		if($result['Flag'] == 100){
			alertMsg($result['FlagString'],'?module=article');
		}else{
			alertMsg($result['FlagString']);
		}
	}
	//查询要修改的帮助内容
	$edit = array();
	if($id > 0){
		$param = array(
			'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10224,'ChildId'=>105,'Desc'=>'帮助内容'),
			'extparam' => array('Tag'=>'SubstanceList','Data'=>array('ClassifyId'=>0,'Id'=>$id))
		);
		$edit = request($param);
		$edit = $edit['Data'][0];
	}
	//获取分类
	$param = array(
		'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10224,'ChildId'=>101,'Desc'=>'帮助分类'),
		'extparam' => array('Tag'=>'ClassifyList','Data'=>array('Status'=>1))
	);
	$result = request($param);
	$category = $result['Data'];
	$tpl = 'help/article-edit.html';

}elseif($module == 'article_del'){
	$id = (int)$_GET['id'];
	if($id > 0){
		$param = array(
			'param' => array('BigCaseId'=>10002,'CaseId'=>10040,'ParentId'=>10224,'ChildId'=>108,'Desc'=>'删除帮助分类'),
			'extparam' => array('Tag'=>'DelSubstance','Id'=>$id)
		);
		$result = request($param);
		alertMsg($result['FlagString'],'?module=article');
	}else{
		alertMsg('不存在的记录');
	}
}

$template = template::getInstance();
$template->setOptions(get_config('template','admin'));
include template("{$tpl}",$template);
