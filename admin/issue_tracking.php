<?php
require_once '../library/global.fun.php';
$module=empty($_GET['module'])?'issue_list':$_GET['module'];
$user_info = checkLogin();
$status = array(
	"all" => array(
		"1" => "未受理",
		"2" => "已受理",
		"3" => "处理中",
		"4" => "待评价",
		"5" => "已关闭",),
	"undone" => array(1,2,3),
	"done" => array(4,5),
);

switch ($module){
	case "initiate_type_list"://问题类型配置列表
		$link_array = getLevellink(10002,10069,10733,102);
		$template = "initiate_type_list";
		//请求列表
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>102),
        	'extparam'=>array('Tag'=>'InitiateTypeList'),
    	);
    	$res = request($param);
    	break;
	case "initiate_type_save"://问题类型保存
		$template = "initiate_type_save";
		$id = intval($_GET['id']);
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>102),
	        'extparam'=>array('Tag'=>'InitiateTypeInfo','Data'=>array('Id'=>$id)),
	    );
	    $rst = request($param);
	    $info = $rst['Data'];
		if($_POST){
			$param = array(
	       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>101),
	        	'extparam'=>array('Tag'=>'InitiateTypeSave', 'Data'=>$_POST),
	    	);
	    	$res = request($param);
			if($res['Flag'] == 100){
				alertMsg($res['FlagString'],'?module=initiate_type_list');
			}else{
				alertMsg($res['FlagString']);
			}
		}
		break;
	case "initiate_type_del"://问题类型删除
		$id = intval($_GET['id']);
		//请求删除
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>103),
	        'extparam'=>array('Tag'=>'InitiateTypeDel','Data'=>array('Id'=>$id)),
	    );
	    $rst = request($param);
		if($rst['Flag'] == 100){
			alertMsg($rst['FlagString'],'?module=initiate_type_list');
		}else{
			alertMsg($rst['FlagString']);
		}
		break;
	case "level_one_list"://一级划分列表
		$template = "level_one_list";
		$link_array = getLevellink(10002,10069,10734,101);
		//请求列表
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>101),
        	'extparam'=>array('Tag'=>'LevelList', "Data"=>array("level"=>1)),
    	);
    	$res = request($param);
		break;
	case "level_one_save"://一级划分保存
		$template = "level_one_save";
		$id = intval($_GET['id']);
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>101),
	        'extparam'=>array('Tag'=>'LevelInfo','Data'=>array('Id'=>$id)),
	    );
	    $rst = request($param);
	    $info = $rst['Data'];
		if($_POST){
			$data = $_POST;
			$data['level'] = 1;
			$data['p_id'] = 0;
			$param = array(
	       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>102),
	        	'extparam'=>array('Tag'=>'LevelSave', 'Data'=>$data),
	    	);
	    	$res = request($param);
			if($res['Flag'] == 100){
				alertMsg($res['FlagString'],'?module=level_one_list');
			}else{
				alertMsg($res['FlagString']);
			}
		}
		break;
	case "level_one_del"://一级划分删除
		$id = intval($_GET['id']);
		//请求删除
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>103),
	        'extparam'=>array('Tag'=>'LevelDel','Data'=>array('Id'=>$id)),
	    );
	    $rst = request($param);
		if($rst['Flag'] == 100){
			alertMsg($rst['FlagString'],'?module=level_one_list');
		}else{
			alertMsg($rst['FlagString']);
		}
		break;
	case "level_two_list"://二级划分列表
		$template = "level_two_list";
		//请求列表
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>101),
        	'extparam'=>array('Tag'=>'LevelList', 'Data'=>array('p_id'=>$_GET['p_id'], "level"=>2)),
    	);
    	$res = request($param);
		break;
	case "level_two_save"://二级划分保存
		$template = "level_two_save";
		//一级划分列表
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>101),
        	'extparam'=>array('Tag'=>'LevelList', "Data"=>array("no_page"=>true, "status"=>1, "level"=>1)),
    	);
    	$res = request($param);
    	$levelOneList = $res['Data'];
    	//修改
		$id = intval($_GET['id']);
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>101),
	        'extparam'=>array('Tag'=>'LevelInfo','Data'=>array('Id'=>$id)),
	    );
	    $rst = request($param);
	    $info = $rst['Data'];
		if($_POST){
			$data = $_POST;
			$data['level'] = 2;
			$param = array(
	       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>102),
	        	'extparam'=>array('Tag'=>'LevelSave', 'Data'=>$data),
	    	);
	    	$res = request($param);
			if($res['Flag'] == 100){
				alertMsg($res['FlagString'],'?module=level_two_list');
			}else{
				alertMsg($res['FlagString']);
			}
		}
		break;
	case "level_two_del"://二级划分删除
		$id = intval($_GET['id']);
		//请求删除
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>103),
	        'extparam'=>array('Tag'=>'LevelDel','Data'=>array('Id'=>$id)),
	    );
	    $rst = request($param);
		if($rst['Flag'] == 100){
			alertMsg($rst['FlagString'],'?module=level_two_list');
		}else{
			alertMsg($rst['FlagString']);
		}
		break;
	case "collection"://数据统计(区搜索框？？？)
		$template = "collection";
		//问题划分列表
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>101),
        	'extparam'=>array('Tag'=>'LevelList', "Data"=>array("level"=>1, "no_page"=>true)),
    	);
    	$res = request($param);
    	$level_one_list = $res['Data'];
		//搜索条件
		$search = array();
		$search['type'] = $_GET['type']?$_GET['type']:"day";
		if($_GET){
			if($_GET['bg_date']) $search['bg_date'] = strtotime($_GET['bg_date']);
			if($_GET['ed_date']) $search['ed_date'] = strtotime($_GET['ed_date']." 23:59:59");
			if($_GET['area_name']) $search['area_name'] = intval($_GET['area_name']);
			if($_GET['level_id']) $search['level_id'] = intval($_GET['level_id']);
		}
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10732,'ChildId'=>101),
        	'extparam'=>array('Tag'=>'Collection', 'Data'=>$search),
    	);
    	$res = request($param);
    	$level_arr = $res['level_arr'];
    	$list = $res['list'];
		break;
	case "issue_list"://工单列表
		$template = "issue_list";
		$link_array = getLevellink(10002,10069,10731,101);
		//问题类型配置列表
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>101),
        	'extparam'=>array('Tag'=>'InitiateTypeList', 'Data'=>array("no_page"=>true)),
    	);
    	$res = request($param);
    	$initiate_type_list = $res['Data'];
    	//一级划分列表
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>101),
        	'extparam'=>array('Tag'=>'LevelList', "Data"=>array("level"=>1,"no_page"=>true)),
    	);
    	$res = request($param);
    	$level_one_list = $res['Data'];
    	//搜索条件
		$search = array();
		if($_GET['is_completed']) $search['status'] = $status['done'];
		else $search['status'] = $status['undone'];
		if($_GET){
			if($_GET['bg_date']) $search['bg_date'] = strtotime($_GET['bg_date']);
			if($_GET['ed_date']) $search['ed_date'] = strtotime($_GET['ed_date']." 23:59:59");
			if($_GET['initiate_type_id']) $search['initiate_type_id'] = intval($_GET['initiate_type_id']);
			if($_GET['level_id']) $search['level_id'] = intval($_GET['level_id']);
			if($_GET['status']) $search['status'] = array($_GET['status']);
			if($_GET['id']) $search['id'] = $_GET['id'];
		}
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10731,'ChildId'=>101),
        	'extparam'=>array('Tag'=>'IssueList', 'Data'=>$search),
    	);
		$res = request($param);
		//重组问题划分搜索栏
		$level_content = '';
		if($_GET['level_id']){
			$param = array(
	       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>101),
	        	'extparam'=>array('Tag'=>'get_before_level', 'Data'=>array("id"=>$_GET['level_id'])),
	    	);
	    	$level_content = request($param);
		}
		break;
	case "issue_add"://发起工单
		$template = "issue_add";
		//有效站列表
		$param = array(
			'param'=>array("BigCaseId"=>10002,"CaseId"=>10003,"ParentId"=>10339,"ChildId"=>101),
			'extparam'=>array("Tag"=>"List", "GroupId"=>intval($_GET['group_id']), "no_page"=>true)
		);
		$res = request($param);
		$area_arr = $res['List'];
		//问题类型列表
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>102),
        	'extparam'=>array('Tag'=>'InitiateTypeList', "Data"=>array("no_page"=>true)),
    	);
    	$res = request($param);
		$initiate_type_list = $res['Data'];
		//提交工单
		if($_POST){
			//调用上传文件的接口
			foreach($_FILES['img']['name'] as $key=>$val){
				if($_FILES['img']['error'][$key] == 0){
					$ext = explode(".", $_FILES['img']['name'][$key]);
					$count = count($ext);
					$ext = strtolower($ext[$count-1]);
					$bytes = file_get_contents($_FILES['img']['tmp_name'][$key]);
					$index = md5($bytes);
					$opt = array("UPLOAD_KEY"=>OSS_UPLOAD_KEY, "Bytes"=>$bytes, "Type"=>"md5", "Index"=>$index);
					$query = json_decode(socket_request(UPLOAD_API_PATH, $opt, true, 600), true);
					if($query['rst'] != 100){
						echo '图片上传失败';
						exit;
					}
					$img[] = $index;
					$img_ext[] = $ext;
				}
			}
			$_POST['img'] = json_encode($img);
			$_POST['img_ext'] = json_encode($img_ext);
			//处理人
			$record = array();
			$record['发起'][] = array("time"=>time(), "operator_id"=> $user_info['Uin'], "operator_name"=> $user_info['Nick']);
			$_POST['record'] = serialize($record);
			//请求提交
			$param = array(
	       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>102),
	        	'extparam'=>array('Tag'=>'IssueAdd', 'Data'=>$_POST),
	    	);
	    	$res = request($param);
			if($res['Flag'] == 100){
				alertMsg($res['FlagString'],'?module=issue_list');
			}else{
				alertMsg($res['FlagString']);
			}
		}
		break;
	case "issue_receive"://接收工单
		$template = "issue_receive";
		//问题划分列表
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>101),
        	'extparam'=>array('Tag'=>'LevelList', "Data"=>array("level"=>1, "no_page"=>true)),
    	);
    	$res = request($param);
    	$level_one_list = $res['Data'];
    	//工单信息
		$id = intval($_GET['id'])?intval($_GET['id']):intval($_POST['id']);
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10731,'ChildId'=>101),
	        'extparam'=>array('Tag'=>'IssueInfo','Data'=>array('Id'=>$id)),
	    );
	    $rst = request($param);
	    $info = $rst['Data'];
	    $img = json_decode($info['img'], true);
	    $img_ext = json_decode($info['img_ext'], true);
	    if($_POST){
	    	$act = $_POST['act']=="update"?"接收":"关闭";
			$arr = array(
				"level_id" => intval($_POST['level_id']),
				"is_reappear" => intval($_POST['is_reappear']),
				"cusservice_reply" => strtotime($_POST['cusservice_reply']),
				"operation_reply" => strtotime($_POST['operation_reply']),
				"product_reply" => strtotime($_POST['product_reply']),
				"predict_online" => strtotime($_POST['predict_online']),
				"post_time" => time(),
			);
			if($_POST['act'] == "update"){
				$arr['status'] = 2;
			}
			if($_POST['act'] == "close"){
				$arr['status'] = 4;
				$arr['cusservice_resolve'] = strtotime($_POST['cusservice_resolve']);
				$arr['operation_resolve'] = strtotime($_POST['operation_resolve']);
				$arr['bug_resolve'] = strtotime($_POST['bug_resolve']);
				$arr['demand_resolve'] = strtotime($_POST['demand_resolve']);
				$arr['reason'] = addslashes(trim($_POST['reason']));
				$arr['resolve_situation'] = addslashes(trim($_POST['resolve_situation']));
			}
			//record,supply
			$record = unserialize($info['record']);
			$record[$act][] = array("time"=>time(), "operator_id"=> $user_info['Uin'], "operator_name"=> $user_info['Nick']);
			$arr['record'] = serialize($record);
			if(trim($_POST['supply'])){
				$supply[] = array("operator_id"=>$user_info['Uin'], "operator_name"=>$user_info['Nick'], "operator_time"=>time(), "content"=>addslashes(trim($_POST['supply'])));
				$arr['supply'] = serialize($supply);
			}
			$arr['post_time'] = time();
			$arr['id'] = $_POST['id'];
	    	//请求提交
			$param = array(
	       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>102),
	        	'extparam'=>array('Tag'=>'IssueEdit', 'Data'=>$arr),
	    	);
	    	$res = request($param);
			if($res['Flag'] == 100){
				alertMsg($res['FlagString'],'?module=issue_list');
			}else{
				alertMsg($res['FlagString']);
			}
	    }
		break;
	case "issue_edit"://处理工单
		$template = "issue_edit";
		$id = intval($_GET['id'])?intval($_GET['id']):intval($_POST['id']);
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>101),
	        'extparam'=>array('Tag'=>'IssueInfo','Data'=>array('Id'=>$id)),
	    );
	    $rst = request($param);
	    $info = $rst['Data'];
	    $img = json_decode($info['img'], true);
	    $img_ext = json_decode($info['img_ext'], true);
	    $supply = unserialize($info['supply']);
	    $rowspan = count($supply)+1;
	    if($_POST){
	    	$act = $_POST['act']=="update"?"接收":"关闭";
			if($_POST['act'] == "update"){
				$arr['status'] = 3;
			}
			if($_POST['act'] == "close"){
				$arr['status'] = 4;
				$arr['cusservice_resolve'] = strtotime($_POST['cusservice_resolve']);
				$arr['operation_resolve'] = strtotime($_POST['operation_resolve']);
				$arr['bug_resolve'] = strtotime($_POST['bug_resolve']);
				$arr['demand_resolve'] = strtotime($_POST['demand_resolve']);
				$arr['reason'] = addslashes(trim($_POST['reason']));
				$arr['resolve_situation'] = addslashes(trim($_POST['resolve_situation']));
			}
			//record,supply
			$record = unserialize($info['record']);
			$record[$act][] = array("time"=>time(), "operator_id"=> $user_info['Uin'], "operator_name"=> $user_info['Nick']);
			$arr['record'] = serialize($record);
			$supply = unserialize($info['supply']);
	   	 	if(trim($_POST['supply'])){
				$supply[] = array("operator_id"=>$user_info['Uin'], "operator_name"=>$user_info['Nick'], "operator_time"=>time(), "content"=>addslashes(trim($_POST['supply'])));
				$arr['supply'] = serialize($supply);
			}
			$arr['supply'] = serialize($supply);
	    	$arr['post_time'] = time();
			$arr['id'] = $_POST['id'];
	    	//请求提交
			$param = array(
	       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>102),
	        	'extparam'=>array('Tag'=>'IssueEdit', 'Data'=>$arr),
	    	);
	    	$res = request($param);
			if($res['Flag'] == 100){
				alertMsg($res['FlagString'],'?module=issue_list');
			}else{
				alertMsg($res['FlagString']);
			}
	    }
		break;
	case "issue_detail":
		$template = "issue_detail";
		$id = intval($_GET['id']);
		$param = array(
	        'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10733,'ChildId'=>101),
	        'extparam'=>array('Tag'=>'IssueInfo','Data'=>array('Id'=>$id)),
	    );
	    $rst = request($param);
	    $info = $rst['Data'];
	    $img = json_decode($info['img'], true);
	    $img_ext = json_decode($info['img_ext'], true);
	    $supply = unserialize($info['supply']);
	    $rowspan = count($supply);
		break;
	case "get_after_level":
		//问题类型配置列表
		$param = array(
       		'param' => array('BigCaseId'=>10002,'CaseId'=>10069,'ParentId'=>10734,'ChildId'=>101),
        	'extparam'=>array('Tag'=>'get_after_level', 'Data'=>array("p_id"=>$_GET['p_id'])),
    	);
    	$res = request($param);
    	exit(json_encode((array)$res));
		break;
}

$tpl = template::getInstance();
$tpl->setOptions(get_config('template','admin'));
include template('issue_tracking/'.$template.'.html',$tpl);
