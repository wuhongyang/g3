<?php
require_once '../library/global.fun.php';
$module = empty($_GET['module']) ? 'act_list' : trim($_GET['module']);
$bigcase_id = $_GET['bigcase_id'];
$case_id = $_GET['case_id'];
$parent_id = $_GET['parent_id'];
$child_id = $_GET['child_id'];
$select_type = $_GET['select_type'];
$uin_type = $_GET['uin_type'];
$uin = $_GET['uin'];
$trade_id = $_GET['trade_id'];
//$key_word = $_GET['key_word'];
$start_date = $_GET['start_date'] ? $_GET['start_date'] : date('Y-m-d 00:00:00');
$end_date = $_GET['end_date'] ? $_GET['end_date'] : date('Y-m-d 23:59:59');

switch($module){
    case 'act_list':
        //当前使用站
        $__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
        $__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

        if(!$_GET['data_group_id']){
            $_GET['data_group_id'] = $__ADMIN_CURGROUP['groupid'];
        }

        if($_GET['data_group_id']){
            $param = array(
                'extparam' => array('Tag'=>'Act_list','BigCaseId'=>$bigcase_id,'CaseId'=>$case_id,'ParentId'=>$parent_id,'ChildId'=>$child_id,'select_type'=>$select_type,'uin_type'=>$uin_type,'uin'=>$uin,'ChannelId'=>$trade_id,'start_date'=>$start_date,'end_date'=>$end_date,'DataGroupId'=> intval($_GET['data_group_id'])),
                'param' => array('BigCaseId'=>10002,'CaseId'=>10008,'ParentId'=>10008,'ChildId'=>101,'Desc'=>'行为流水列表')
            );
            $act_array = request($param);
            $list = $act_array['Result'];
        }
        $template = 'act_list.html';
        break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('act/'.$template,$tpl);
