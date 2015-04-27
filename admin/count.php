<?php
include_once '../library/global.fun.php';

$module = $_GET['module'];
$_GET['search']['s_time'] = empty($_GET['search']['s_time']) ? date('Y-m-d 00:00:00') : $_GET['search']['s_time'];
$_GET['search']['e_time'] = empty($_GET['search']['e_time']) ? date('Y-m-d H:i:s') : $_GET['search']['e_time'];
if($module == 'all_user_total'){//总在线人数汇总查询
	$param = array(
		'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10152,'ChildId'=>101,'Desc'=>'总在线人数汇总查询'),
		'extparam' => array('Tag'=>'AllUserTotal')
	);
	$result = request($param);
	$list = (array)$result['Result'];
	$page = $result['Page'];
	$link_array = getLevellink(10002,10002,10152,101);
	$temp = 'all_user_total.html';
}elseif($module == 'all_user_history'){//总在线人数明细查询
	$param = array(
		'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10152,'ChildId'=>102,'Desc'=>'总在线人数明细查询'),
		'extparam' => array('Tag'=>'AllUserHistory','Data'=>array('s_time'=>$_GET['search']['s_time'],'e_time'=>$_GET['search']['e_time']))
	);
	$result = request($param);
	$list = $result['Result'];
	$page = $result['Page'];
	$link_array = getLevellink(10002,10002,10152,102);
	$temp = 'all_user_history.html';
}elseif($module == 'rooms_user_total'){//分站在线人数汇总查询
    //当前使用站
	$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
	$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);

    if(!$_GET['data_group_id']){
        $_GET['data_group_id'] = $__ADMIN_CURGROUP['groupid'];
    }
    
    if($_GET['data_group_id']){
    	$param = array(
    		'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10152,'ChildId'=>103,'Desc'=>'分站在线人数汇总查询'),
    		'extparam' => array('Tag'=>'RoomsUserTotal','RegionId'=>$_GET['data_group_id'],'DataGroupId'=>$_GET['data_group_id'])
    	);
    	$result = request($param);
    	$list = (array)$result['Result'];
	    $page = $result['Page'];
    }
    
	$link_array = getLevellink(10002,10002,10152,103);
	$temp = 'rooms_user_total.html';
}elseif($module == 'rooms_user_history'){ //分站在线人数明细查询
    //当前使用站
	$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
	$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);
	
    if(!$_GET['data_group_id']){
        $_GET['data_group_id'] = $__ADMIN_CURGROUP['groupid'];
    }
    
    if($_GET['data_group_id']){
    	$param = array(
    		'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10152,'ChildId'=>104,'Desc'=>'分站在线人数明细查询'),
    		'extparam' => array('Tag'=>'RoomsUserHistory','Data'=>array('RegionId'=>$_GET['data_group_id'],'s_time'=>$_GET['search']['s_time'],'e_time'=>$_GET['search']['e_time']),'DataGroupId'=>$_GET['data_group_id'])
    	);
    	$result = request($param);
    	$list = (array)$result['Result'];
    	$page = $result['Page'];
    }
    
	$link_array = getLevellink(10002,10002,10152,104);
	$temp = 'rooms_user_history.html';
}elseif($module == 'rooms_count_day'){//房间在线人数汇总查询
	//当前使用站
	$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
	$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);
	
    $_GET['data_group_id'] = $__ADMIN_CURGROUP['groupid'];
    $_GET['search']['region_id'] = $__ADMIN_CURGROUP['groupid'];
	
    if($_GET['data_group_id']){
    	$param = array(
    		'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10152,'ChildId'=>105,'Desc'=>'总在线人数明细查询'),
    		'extparam' => array('Tag'=>'RoomsCountDay','Data'=>$_GET['search'],'DataGroupId'=>$_GET['data_group_id'])
    	);
    	$result = request($param);
    	$list = (array)$result['Result'];
	    $page = $result['Page'];
    }
    
	$link_array = getLevellink(10002,10002,10152,105);
	$temp = 'rooms_count_day.html';
}elseif($module == 'rooms_user_info'){
    //当前使用站
	$__ADMIN_CURGROUP_COOKIE = $_COOKIE['__ADMIN_CURGROUP'];
	$__ADMIN_CURGROUP = json_decode($__ADMIN_CURGROUP_COOKIE,true);
	
    $_GET['data_group_id'] = $__ADMIN_CURGROUP['groupid'];
    $_GET['search']['region_id'] = $__ADMIN_CURGROUP['groupid'];
    
    if($_GET['data_group_id']){
    	$param = array(
    		'param' => array('BigCaseId'=>10002,'CaseId'=>10002,'ParentId'=>10152,'ChildId'=>106,'Desc'=>'房间在线人数明细查询'),
    		'extparam' => array('Tag'=>'RoomsUserInfo','Data'=>$_GET['search'],'DataGroupId'=>$_GET['data_group_id'])
    	);
    	$result = request($param);
    	$list = (array)$result['Result'];
    	$page = $result['Page'];
    }
    
	$link_array = getLevellink(10002,10002,10152,106);
	$temp = 'rooms_user_info.html';
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('rooms/'.$temp,$tpl);