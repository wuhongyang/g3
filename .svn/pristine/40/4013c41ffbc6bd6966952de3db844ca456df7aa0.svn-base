<?php
include_once('../library/global.fun.php');
include 'config/data.php';

$module = trim($_GET['module']);

$_GET['search']['stime'] = $_GET['search']['stime'] ? $_GET['search']['stime'] : date('Y-m-d');
$_GET['search']['etime'] = $_GET['search']['etime'] ? $_GET['search']['etime'] : date('Y-m-d');

if($module == 'list'){
    $param = array(
        'param'=>array(
            "BigCaseId"  => 10002,
            "CaseId"	 => 10040,
            "ParentId"   => 10254,
            "ChildId"	 => 101,
            "Desc"		 => "广告统计查看"
        ),
        'extparam'=>array(
            "Tag" 		 => "DetailList",
            "SearchData" => $_GET['search']
        )
    );
    $result = request($param);
	$list = (array)$result['Result'];
    $temp = 'detail.html';
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('ad/'.$temp,$tpl);