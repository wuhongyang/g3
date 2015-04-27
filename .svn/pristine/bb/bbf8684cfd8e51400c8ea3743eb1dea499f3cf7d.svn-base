<?php
include_once('../library/global.fun.php');
include 'config/data.php';

$module = trim($_GET['module']);

$_GET['search']['stime'] = $_GET['search']['stime'] ? $_GET['search']['stime'] : date('Y-m-d');
$_GET['search']['etime'] = $_GET['search']['etime'] ? $_GET['search']['etime'] : date('Y-m-d');

//当前使用站
$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

if(!$_GET['data_group_id']){
    $_GET['data_group_id'] = $__ADMIN_CURGROUP['groupid'];
}

if($module == 'detailList'){
    $link_array = getLevellink(10002,10008,10038,101);
    
    if($_GET['data_group_id']){
        $param = array(
            'param'=>array(
                "BigCaseId"  => 10002,
                "CaseId"	 => 10008,
                "ParentId"   => 10038,
                "ChildId"	 => 101,
                "Desc"		 => "积分流水查看"
            ),
            'extparam'=>array(
                "Tag" 		 => "DetailList",
                "SearchData" => $_GET['search'],
                'DataGroupId'=> intval($_GET['data_group_id'])
            )
        );
    }
    $result = request($param);
    
    $ruleDefineList = (array)$result['RuleDefineList'];
    $_GET['search']['rule'] = $_GET['search']['rule'] ? $_GET['search']['rule'] :array_shift(array_keys($ruleDefineList));
    $list = (array)$result['Result'];
	$label_name = $result['LabelName'];
	$json_label_name = json_encode($result['LabelName']);
    $temp = 'integrate_detail.html';
}elseif($module == 'summaryList' || $module == 'ajax'){
    $data = (array)$_GET;
    
    if($_GET['data_group_id']){
        $param = array(
            'param'=>array(
                "BigCaseId"  => 10002,
                "CaseId"	 => 10008,
                "ParentId"   => 10038,
                "ChildId"	 => 102,
                "Desc"		 => "积分汇总查看"
            ),
            'extparam'=>array(
                "Tag" 		 => "SummaryList",
                "SearchData" => $data,
                'DataGroupId'=> intval($_GET['data_group_id'])
            )
        );
    }
    $result = request($param);
    $ruleDefineList = (array)$result['RuleDefineList'];
    $_GET['rule'] = $_GET['rule'] ? $_GET['rule'] :array_shift(array_keys($ruleDefineList));
    $list = (array)$result['Result'];
	$label_name = $result['LabelName'];
	$json_label_name = json_encode($result['LabelName']);
    $temp = 'integrate_summary.html';
    $link_array = getLevellink(10002,10008,10038,102);
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('behavior/'.$temp,$tpl);